<?php

namespace App\Http\Controllers\reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\MemberAgency;
use App\Helpers\ActivityLogger;

class ReviewerProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('memberAgency');
        return view('project.reviewer.profile.profile', compact('user'));
    }

 
    public function edit()
    {
        $user = Auth::user();
        $memberAgencies = MemberAgency::all();
        return view('project.reviewer.profile.edit', compact('user', 'memberAgencies'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'member_agencies_id' => 'required|exists:member_agencies,id',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|different:current_password',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'member_agencies_id' => $request->member_agencies_id,
        ]);

        ActivityLogger::log(
            'updated profile',
            'User',
            $user->id,
            'Updated basic information and member agency'
        );

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (Hash::check($request->current_password, $user->password)) {
                $user->update(['password' => Hash::make($request->new_password)]);

                ActivityLogger::log(
                    'changed password',
                    'User',
                    $user->id,
                    'Password updated'
                );
            } else {
                return redirect()->back()->with([
                    'status' => 'error',
                    'message' => 'Current password is incorrect'
                ]);
            }
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->update(['profile_picture' => $path]);

            ActivityLogger::log(
                'updated profile picture',
                'User',
                $user->id,
                'Profile picture changed'
            );
        }

        return redirect()->route('reviewer.profile')->with([
            'status' => 'success',
            'message' => 'Profile updated successfully'
        ]);
    }

    public function updatePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = auth()->user();

        $path = $request->file('profile_picture')
                        ->store('profile_photos', 'public');

        $user->update([
            'profile_picture' => $path,
        ]);

        return back()->with('success', 'Profile photo updated successfully.');
    }

}
