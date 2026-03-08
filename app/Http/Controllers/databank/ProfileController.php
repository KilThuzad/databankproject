<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\MemberAgency;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('memberAgency');
        return view('project.profile.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        $memberAgencies = MemberAgency::all();

        return view('project.profile.edit', compact('user', 'memberAgencies'));
    }

    public function update(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'firstname' => 'required|string|max:255',
        'lastname'  => 'required|string|max:255',
        'username'  => 'required|string|max:255|unique:users,username,' . $user->id,
        'email'     => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'member_agencies_id' => 'required|exists:member_agencies,id',
        'current_password' => 'nullable|required_with:new_password',
        'new_password'     => 'nullable|min:8|different:current_password',
        'profile_picture'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    $user->firstname        = $request->firstname;
        $user->lastname         = $request->lastname;
        $user->username         = $request->username;
        $user->email            = $request->email;
        $user->member_agencies_id = $request->member_agencies_id;

        $user->save();

    if ($request->filled('current_password') && $request->filled('new_password')) {
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect');
        }

        $user->update(['password' => Hash::make($request->new_password)]);
    }

    if ($request->hasFile('profile_picture')) {
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $user->update(['profile_picture' => $path]);
    }

    return redirect()->route('profile.show')->with('success', 'Profile updated successfully');
}


    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        if ($request->hasFile('profile_picture')) {
        if ($user->profile_picture) {
            \Storage::disk('public')->delete($user->profile_picture);
        }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->update(['profile_picture' => $path]);
        }

        return back()->with('success', 'Profile picture updated successfully.');
    }
}
