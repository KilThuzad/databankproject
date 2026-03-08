<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\University;
use App\Models\User;
use App\Models\MemberAgency;

class AccountController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $memberAgencyId = $request->input('member_agencies_id');

        $usersQuery = User::with(['memberAgency']);


        if ($memberAgencyId) {
            $usersQuery->where('member_agencies_id', $memberAgencyId);
        }

        $users = $usersQuery->get();

        $accountsByRoleQuery = User::query();

        if ($memberAgencyId) {
            $accountsByRoleQuery->where('member_agencies_id', $memberAgencyId);
        }

        $accountsByRole = $accountsByRoleQuery
            ->select('role', DB::raw('count(*) as count'))
            ->groupBy('role')
            ->get()
            ->pluck('count', 'role');

        $memberAgencies = MemberAgency::orderBy('name')->get();

        return view('project.accounts.accounts', compact(
            'users',
            'memberAgencies',
            'memberAgencyId',
            'accountsByRole'
        ));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $universities = University::orderBy('name')->get();
        $memberAgencies = MemberAgency::orderBy('name')->get();

        return view('project.accounts.edit-user', compact('user', 'universities', 'memberAgencies'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username,' . $id,
            'email'     => 'required|email|max:255|unique:users,email,' . $id,
            'role'      => 'required|string',
            'member_agencies_id' => 'required|exists:member_agencies,id',
            'profile_picture'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->update(['profile_picture' => $path]);
        }

        $user->firstname        = $request->firstname;
        $user->lastname         = $request->lastname;
        $user->username         = $request->username;
        $user->email            = $request->email;
        $user->role             = $request->role;
        $user->member_agencies_id = $request->member_agencies_id;

        $user->save();

        return redirect()->route('all.users')->with('success', 'User updated successfully!');
    }

    public function deleteUser($id)
    {
        User::destroy($id);
        return redirect()->route('all.users')->with('delete', 'Deleted Successfully');
    }
}
