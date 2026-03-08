<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResearchProject;
use App\Models\User;
use App\Models\Category;
use App\Models\ProjectTeam;
use App\Models\MemberAgency;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ResearchProjectController extends Controller
{
    public function index(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id');
        $category       = $request->input('category'); 
        $sort           = $request->input('sort', 'created_at'); 
        $order          = $request->input('order', 'desc');    

        $projectsQuery = ResearchProject::with(['user.memberAgency']);

        if ($memberAgencyId) {
            $projectsQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId);
            });
        }

        if ($category) {
            $projectsQuery->where('category', $category);
        }

        $allowedSorts = ['category', 'deadline', 'created_at', 'title'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }

        $allowedOrders = ['asc', 'desc'];
        if (!in_array($order, $allowedOrders)) {
            $order = 'desc';
        }

        $projects = $projectsQuery->orderBy($sort, $order)->get();

        $memberAgencies = MemberAgency::orderBy('name')->get();
        $categories     = ResearchProject::select('category')->distinct()->pluck('category');

        $projectsByStatusQuery = ResearchProject::query();
        if ($memberAgencyId) {
            $projectsByStatusQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId);
            });
        }
        if ($category) {
            $projectsByStatusQuery->where('category', $category);
        }
        $projectsByStatus = $projectsByStatusQuery
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $projectsByCategoryQuery = ResearchProject::query();
        if ($memberAgencyId) {
            $projectsByCategoryQuery->whereHas('user', function ($query) use ($memberAgencyId) {
                $query->where('member_agencies_id', $memberAgencyId);
            });
        }
        if ($category) {
            $projectsByCategoryQuery->where('category', $category);
        }
        $projectsByCategory = $projectsByCategoryQuery
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        return view('project.research-project.index', compact(
            'projects',
            'memberAgencies',
            'memberAgencyId',
            'categories',
            'category',
            'projectsByStatus',
            'projectsByCategory'
        ));
    }


    public function create()
    {
        return view('project.research-project.create', [
            'categories' => Category::all(),
            'researchers' => User::where('role', 'researcher')->get(),
            'memberAgencies' => MemberAgency::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', 'unique:research_projects,title'],
            'description' => 'required|string',
            'category' => 'required|string',
            'file_path' => 'required|file|mimes:pdf,doc,docx|max:20240',
            'leader_id' => ['required', 'exists:users,id'],
            'member_ids' => ['required', 'array', 'size:2'],
        ]);

        if (count($validated['member_ids']) !== count(array_unique($validated['member_ids']))) {
            return back()->withErrors([
                'member_ids' => 'This user is already assigned to this project and cannot be added twice.'
            ])->withInput();
        }

        $allTeamMembers = array_merge([$validated['leader_id']], $validated['member_ids']);

        if (count($allTeamMembers) !== count(array_unique($allTeamMembers))) {
            return back()->withErrors([
                'member_ids' => 'This user already has a role in this project.'
            ])->withInput();
        }

        $filePath = $request->file('file_path')->store('research_files', 'public');

        DB::transaction(function () use ($validated, $filePath) {
            $project = ResearchProject::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'file_path' => $filePath,
                'category' => $validated['category'],
                'submitted_by' => auth()->id(),
                'status' => 'pending_review',
            ]);

            $project->team()->attach([
                $validated['leader_id'] => ['role' => 'leader'],
                $validated['member_ids'][0] => ['role' => 'member'],
                $validated['member_ids'][1] => ['role' => 'member'],
            ]);
        });

        return redirect()->route('research_projects.index')
            ->with('success', 'Successfully submitted a research project with your team!');
    }

    public function edit($id)
    {
        $project = ResearchProject::findOrFail($id);
        $categories = Category::all();
        $memberAgencies = MemberAgency::all();
        return view('project.research-project.edit', compact('project', 'categories', 'memberAgencies'));
    }

    public function update(Request $request, $id)
    {
        $project = ResearchProject::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'status' => 'required|in:pending_review,needs_revision,complete',
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

        return redirect()->route('research_projects.index')->with('success', 'Successfully updated the project!');
    }


    public function destroy($id)
    {
        $project = ResearchProject::findOrFail($id);

        $project->comments()->delete();

        $project->team()->detach(); 

        if ($project->file_path && file_exists(storage_path('app/public/' . $project->file_path))) {
            unlink(storage_path('app/public/' . $project->file_path));
        }

        $project->delete();

        return redirect()->route('research_projects.index')
                        ->with('success', 'Successfully deleted the project!');
    }


    public function deleteMember($projectId, $memberId)
    {
        $project = ResearchProject::findOrFail($projectId);
        $project->team()->detach($memberId);

        return redirect()->back()->with('success', 'Team member removed successfully.');
    }

    public function show($id, Request $request)
    {
        $project = ResearchProject::with([
            'user.memberAgency',
            'team' => function ($q) {
                $q->select('users.id', 'username', 'firstname', 'lastname', 'email');
            }
        ])->findOrFail($id);

        $assignedUserIds = ProjectTeam::pluck('user_id')->toArray();

        $availableMembers = User::select('id', 'firstname', 'lastname', 'email', 'role')
            ->whereIn('role', ['researcher'])
            ->whereNotIn('id', $assignedUserIds)
            ->orderBy('firstname')
            ->get();

        return view('project.research-project.project-details', compact('project', 'availableMembers'));
    }

    // public function bulkDeadline(Request $request)
    // {
    //     $request->validate([
    //         'categories' => 'required|array',
    //         'deadline' => 'required|date',
    //     ]);

    //     ResearchProject::whereIn('category', $request->categories)
    //         ->update(['deadline' => $request->deadline]);

    //     return back()->with('success', 'Deadlines updated for selected categories.');
    // }

    public function bulkDeadline(Request $request)
    {
        $request->validate([
            'project_ids' => 'required|array', 
            'deadline' => 'required|date',
        ]);

        ResearchProject::whereIn('id', $request->project_ids)
            ->update(['deadline' => $request->deadline]);

        return back()->with('success', 'Deadlines updated for selected projects.');
    }


    public function addMember(Request $request, $id)
    {
        $project = ResearchProject::findOrFail($id);

        $selectedMembers = $request->input('selected_members', []);
        $roles = $request->input('role', []);

        $existingRoles = $project->team->pluck('pivot.role')->toArray();
        $currentMembersCount = count(array_filter($existingRoles, fn($r) => $r === 'member'));
        $hasLeader = in_array('leader', $existingRoles);

        foreach ($selectedMembers as $memberId) {
            $role = $roles[$memberId] ?? 'member';

            if ($role === 'member' && $currentMembersCount >= 2) {
                return back()->with('error', 'You can only add up to 2 members.');
            }

            if ($role === 'leader' && $hasLeader) {
                return back()->with('error', 'This project already has a leader.');
            }

            $project->team()->attach($memberId, ['role' => $role]);

            if ($role === 'member') $currentMembersCount++;
            if ($role === 'leader') $hasLeader = true;
        }

        return back()->with('success', 'Member(s) added successfully!');
    }

    public function changeMember(Request $request, $projectId)
    {
        $request->validate([
            'selected_member' => 'required|exists:users,id',
            'old_member_id' => 'required|exists:users,id',
        ]);

        $project = ResearchProject::with('team')->findOrFail($projectId);

        if ($project->team()->where('user_id', $request->selected_member)->exists()) {
            return redirect()->back()->with('error', 'This user is already part of the team.');
        }

        $oldMember = $project->team()->where('user_id', $request->old_member_id)->first();

        if (!$oldMember) {
            return redirect()->back()->with('error', 'Old member not found in this project.');
        }

        $role = $oldMember->pivot->role;

        $project->team()->detach($oldMember->id);
        $project->team()->attach($request->selected_member, ['role' => $role]);

        return redirect()->back()->with('success', 'Member changed successfully!');
    }

   public function download($id)
{
    $project = ResearchProject::findOrFail($id);
    $filePath = storage_path('app/public/' . $project->file_path);

    if (!file_exists($filePath)) {
        return abort(404, 'File not found.');
    }

    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    $safeTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $project->title);

    return response()->download(
        $filePath,
        $safeTitle . '.' . $extension,
        [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]
    );
}


    public function viewFile($filename)
    {
        $path = 'research_files/' . $filename;

        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($path);
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $pdfFormats = ['pdf'];
        $officeFormats = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

        $publicUrl = asset('storage/research_files/' . $filename);

        if (in_array($extension, $pdfFormats)) {
            return view('project.research-project.syncfusion-viewer', [
                'fileUrl' => $publicUrl,
                'type' => 'pdf'
            ]);
        } elseif (in_array($extension, $officeFormats)) {
            return view('project.research-project.syncfusion-viewer', [
                'fileUrl' => $publicUrl,
                'type' => 'office',
                'extension' => $extension
            ]);
        } else {
            return response()->file($fullPath, [
                'Content-Type' => Storage::disk('public')->mimeType($path),
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }
    }
}
