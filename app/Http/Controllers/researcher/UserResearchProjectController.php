<?php

namespace App\Http\Controllers\researcher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\Category;
use App\Models\ProjectTeam;
use App\Models\ProjectComment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class UserResearchProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $sortColumn = $request->query('sort', 'category');
        $sortDirection = $request->query('direction', 'asc');

        $projects = ResearchProject::with(['team', 'user'])
            ->where('submitted_by', $user->id)
            ->orWhereHas('team', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->orderBy($sortColumn, $sortDirection)
            ->get();

        return view('project.researcher.research-project.index', compact('projects', 'sortColumn', 'sortDirection'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255|unique:research_projects,title',
            'description'  => 'nullable|string',
            'category'     => 'required|string',
            'file_path'    => 'required|file|mimes:pdf,doc,docx|max:20240',
            'leader_id'    => 'nullable|exists:users,id',
            'member_ids'   => 'nullable|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $submitterId = auth()->id();

        $leaderId = $validated['leader_id'] ?? $submitterId;

        $memberIds = $validated['member_ids'] ?? [];

        $memberIds = array_filter($memberIds, fn($id) => $id != $leaderId);

        if ($submitterId != $leaderId) {
            $memberIds[] = $submitterId;
        }

        $memberIds = array_unique($memberIds);

        $filePath = $request->file('file_path')->store('research_files', 'public');

        DB::transaction(function () use ($validated, $filePath, $leaderId, $memberIds) {

            $project = ResearchProject::create([
                'title'        => $validated['title'],
                'description'  => $validated['description'],
                'file_path'    => $filePath,
                'category'     => $validated['category'],
                'submitted_by' => auth()->id(),
                'status'       => 'pending_review',
            ]);

            $project->team()->attach($leaderId, ['role' => 'leader']);

            foreach ($memberIds as $memberId) {
                $project->team()->attach($memberId, ['role' => 'member']);
            }

            ActivityLogger::log(
                'created',
                'ResearchProject',
                $project->id,
                'submitted project ' . $project->title
            );
        });

        return redirect()
            ->route('userresearchproject.index')
            ->with('success', 'Research project submitted successfully!');
    }

    public function create()
    {
        return view('project.researcher.research-project.create', [
            'categories' => Category::all(),
            'researchers' => User::where('role', 'researcher')->get(),
            'advisers' => User::where('role', 'reviewer')->get()
        ]);
    }

    public function show($id) 
    { 
        $project = ResearchProject::with([
            'user', 
            'team' => function ($q) { 
                $q->select('users.id', 'firstname', 'lastname', 'email'); 
            },
            'reviewAssignment.reviewer' 
        ])->findOrFail($id); 

        $assignedUserIds = $project->team->pluck('id')->toArray(); 
        
        $availableMembers = User::where('role', 'researcher')
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('firstname')
            ->get(); 
        
        return view(
            'project.researcher.research-project.project-details', 
            compact('project', 'availableMembers') 
        ); 
    }
    
    public function edit($id)
    {
        $project = ResearchProject::findOrFail($id);
        $categories = DB::table('categories')->get();
        return view('project.researcher.research-project.edit', compact('project', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $project = ResearchProject::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = $request->only('title', 'description', 'category', 'status');

        if ($request->hasFile('file')) {
            if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
                Storage::disk('public')->delete($project->file_path);
            }
            $filePath = $request->file('file')->store('research_files', 'public');
            $data['file_path'] = $filePath;
        }

        $project->update($data);

        ActivityLogger::log(
            'updated',
            'ResearchProject',
            $project->id,
            'Updated ' . $project->title
        );

        return redirect()->route('userresearchproject.index')->with('success', 'Successfully updated the project!');
    }

    public function destroy($id)
    {
        $project = ResearchProject::findOrFail($id);
        $title = $project->title;
        $project->delete();

        ActivityLogger::log(
            'deleted',
            'ResearchProject',
            $id,
            'deleted project ' . $title
        );

        return redirect()->route('userresearchproject.index')->with('success', 'Successfully deleted the project!');
    }

    public function deleteMember($projectId, $memberId)
    {
        $project = ResearchProject::findOrFail($projectId);
        $member = $project->team()->where('users.id', $memberId)->first();
        $project->team()->detach($memberId);

        ActivityLogger::log(
            'removed member',
            'ResearchProject',
            $projectId,
            "removed member {$member->firstname} {$member->lastname} from project {$project->title}"
        );

        return redirect()->back()->with('success', 'Team member removed successfully.');
    }

    public function addMember(Request $request, ResearchProject $project)
    {
        $request->validate([
            'member_ids' => 'required|array',
            'member_ids.*' => 'exists:users,id',
        ]);

        $existingIds = $project->team->pluck('id')->toArray();

        $newIds = User::where('role', 'researcher')
            ->whereIn('id', $request->member_ids)
            ->whereNotIn('id', $existingIds)
            ->pluck('id')
            ->toArray();

        if (empty($newIds)) {
            return back()->with('error', 'No valid researchers selected to add.');
        }

        $attachData = [];
        foreach ($newIds as $id) {
            $attachData[$id] = ['role' => 'member'];
        }

        $project->team()->attach($attachData);

        $newMembers = User::whereIn('id', $newIds)->get();

        $names = $newMembers->map(function ($user) {
            return $user->firstname . ' ' . $user->lastname;
        })->implode(', ');

        ActivityLogger::log(
            'added member(s)',
            'ResearchProject',
            $project->id,
            "added member {$names} to project {$project->title}"
        );

        return back()->with('success', 'Researcher(s) added successfully.');
    }

    public function changeMember(Request $request, ResearchProject $project)
    {
        $request->validate([
            'old_member_id' => 'required|exists:users,id',
            'selected_member_id' => 'required|exists:users,id',
            'new_role' => 'required|in:leader,member',
        ]);

        $oldId = $request->old_member_id;
        $newId = $request->selected_member_id;
        $newRole = $request->new_role;

        $oldUser = User::findOrFail($oldId);
        $newUser = User::findOrFail($newId);

        if (
            $newUser->role !== 'researcher' ||
            $project->team->contains($newId)
        ) {
            return back()->with('error', 'Invalid researcher selected.');
        }

        DB::transaction(function () use ($project, $oldUser, $newUser, $newRole) {

            $currentLeader = $project->team()
                ->wherePivot('role', 'leader')
                ->first();

            if ($newRole === 'leader' && $currentLeader) {
                $project->team()->updateExistingPivot(
                    $currentLeader->id,
                    ['role' => 'member']
                );
            }

            $project->team()->detach($oldUser->id);

            $project->team()->attach($newUser->id, ['role' => $newRole]);

            ActivityLogger::log(
                'changed member',
                'ResearchProject',
                $project->id,
                auth()->user()->role .
                " replaced member {$oldUser->firstname} {$oldUser->lastname} with {$newUser->firstname} {$newUser->lastname} as {$newRole} in project {$project->title}"
            );
        });

        return back()->with('success', 'Project member updated successfully.');
    }


    public function changeRole(Request $request, $projectId) 
    {
        $project = ResearchProject::findOrFail($projectId);

        $memberId = $request->input('selected_member_id'); 
        $member = User::findOrFail($memberId);
        $newRole = $request->input('new_role');

        if ($newRole === 'leader') {
            $currentLeader = $project->team()->wherePivot('role', 'leader')->first();

            if ($currentLeader && $currentLeader->id != $member->id) {
                $project->team()->updateExistingPivot($currentLeader->id, ['role' => 'member']);
            }
        }

        $project->team()->updateExistingPivot($member->id, ['role' => $newRole]);

        ActivityLogger::log(
            'changed role',
            'ResearchProject',
            $project->id,
            "updated a member role in project {$project->title}"
        );

        return back()->with('success', 'Member role updated successfully.');
    }

    public function assignNewLeader(Request $request, $projectId)
    {
        $project = ResearchProject::findOrFail($projectId);

        $demotedMemberId = $request->input('demoted_member_id'); 
        $newLeaderId = $request->input('new_leader_id'); 

        if (!$newLeaderId) {
            return back()->with('error', 'You must select a new leader before proceeding.');
        }

        if ($demotedMemberId) {
            $project->team()->updateExistingPivot($demotedMemberId, ['role' => 'member']);
        }

        $project->team()->updateExistingPivot($newLeaderId, ['role' => 'leader']);

        ActivityLogger::log(
            'assigned leader',
            'ResearchProject',
            $project->id,
            "assigned a new leader for project {$project->title}"
        );

        return back()->with('success', 'Project leader updated successfully.');
    }



    public function storecomment(Request $request, ResearchProject $project)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = $project->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        ActivityLogger::log(
            'added comment',
            'ProjectComment',
            $comment->id,
            "added comment to {$project->title}" 
        );

        return back()->with('success', 'Comment added successfully.');
    }

    public function destroyComment(ResearchProject $project, $commentId)
    {
        $comment = $project->comments()->findOrFail($commentId);

        if ($comment->user_id !== auth()->id()) {
            return back()->with('error', 'Unauthorized action.');
        }

        $comment->delete();

        ActivityLogger::log(
            'deleted comment',
            'ProjectComment',
            $commentId,
            "deleted a comment to {$project->title}" 
        );

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function updateComment(Request $request, $projectId, $commentId)
    {
        $comment = ProjectComment::findOrFail($commentId);

        if (auth()->id() !== $comment->user_id) {
            return back()->with('error', 'Unauthorized');
        }

        $request->validate(['comment' => 'required|string|max:2000']);
        $comment->update(['comment' => $request->comment]);

        $project = $comment->project;

        ActivityLogger::log(
            'updated comment',
            'ProjectComment',
            $commentId,
            'edited a comment on project "' . $project->title . '"'
        );

        return back()->with('success', 'Comment updated successfully.');
    }


    public function download($id)
    {
        $project = ResearchProject::findOrFail($id);
        $filePath = storage_path('app/public/' . $project->file_path);

        if (!file_exists($filePath)) {
            return abort(404, 'File not found.');
        }

        ActivityLogger::log(
            'downloaded file',
            'ResearchProject',
            $project->id,
            'downloaded the file ' . $project->title 
        );

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        return response()->download(
            $filePath,
            $project->title . '.' . $extension,
            [
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]
        );
    }

    public function uploadDocument(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $project = ResearchProject::findOrFail($id);

        if ($project->file_path && Storage::disk('public')->exists($project->file_path)) {
            Storage::disk('public')->delete($project->file_path);
        }

        $path = $request->file('document')->store('research_files', 'public');

        $project->file_path = $path;
        $project->save();


        return back()->with('success', 'Document updated successfully!');
    }
}
