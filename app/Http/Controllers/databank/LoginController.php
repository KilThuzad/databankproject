<?php

namespace App\Http\Controllers\databank;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\MemberAgency;
use App\Models\User;
use App\Models\PendingUser;
use App\Mail\VerifyPendingEmail;

class LoginController extends Controller
{
    /**
     * Show login form.
     */
    public function showLoginForm()
    {
        return view('project.login.login');
    }

    /**
     * Show registration form.
     */
    public function showRegistrationForm()
    {
        $memberAgencies = MemberAgency::orderBy('name')->get();
        return view('project.login.registration', compact('memberAgencies'));
    }

    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Your account is not verified. Please check your inbox or contact support.'
                ]);
            }

            $request->session()->regenerate();

            session([
                'user_id'   => $user->id,
                'username'  => $user->username,
                'firstname' => $user->firstname,
                'role'      => $user->role,
            ]);

            return match($user->role) {
                'admin'      => redirect('/dashboard'),
                'researcher' => redirect('/userdashboard'),
                'reviewer'   => redirect('/reviewerDashboard'),
                'staff'      => redirect('/Staff/dashboard'),
                default      => redirect('/'),
            };
        }

        $pendingUser = PendingUser::where('username', $request->username)
                        ->orWhere('email', $request->username)
                        ->first();

        if ($pendingUser) {
            if ($pendingUser->expires_at < now()) {
                $pendingUser->delete();
                return back()->withErrors([
                    'username' => 'Your verification link has expired. Please register again.'
                ]);
            }

            return back()->withErrors([
                'username' => 'Your registration is not yet verified. Please check your email for the verification link.'
            ])->with('pending_email', $pendingUser->email); 
        }

        $legacyUser = User::where('username', $request->username)
                        ->orWhere('email', $request->username)
                        ->whereNull('email_verified_at')
                        ->first();

        if ($legacyUser) {
            return back()->withErrors([
                'username' => 'Your account is not verified. Please contact support to activate your account.'
            ]);
        }

        return back()->withErrors(['username' => 'Invalid credentials.']);
    }

    /**
     * Handle user registration (stores in pending_users).
     */
    public function register(Request $request)
    {
        $request->validate([
            'firstname'    => 'required|string|max:255',
            'lastname'     => 'required|string|max:255',
            'username'     => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check main users table
                    if (User::where('username', $value)->exists()) {
                        $fail('The username has already been taken.');
                        return;
                    }
                    // Check pending_users but allow if expired
                    $pending = PendingUser::where('username', $value)->first();
                    if ($pending && $pending->expires_at > now()) {
                        $fail('The username is already pending verification.');
                    }
                },
            ],
            'email'        => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->exists()) {
                        $fail('The email has already been taken.');
                        return;
                    }
                    $pending = PendingUser::where('email', $value)->first();
                    if ($pending && $pending->expires_at > now()) {
                        $fail('The email is already pending verification.');
                    }
                },
            ],
            'password'     => 'required|string|min:8|confirmed',
            'role'         => 'nullable|string|in:admin,researcher,reviewer,staff',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'member_agencies_id' => 'required|exists:member_agencies,id',
        ]);

        $formatErrors = $this->validateUsernamePasswordFormat($request->username, $request->password);
        if (!empty($formatErrors)) {
            return redirect()->back()->withErrors($formatErrors)->withInput();
        }

        PendingUser::where('username', $request->username)->where('expires_at', '<', now())->delete();
        PendingUser::where('email', $request->email)->where('expires_at', '<', now())->delete();

        $profilePicturePath = $request->hasFile('profile_picture')
            ? $request->file('profile_picture')->store('profile_pictures', 'public')
            : null;

        $token = Str::random(60);

        $pendingUser = PendingUser::create([
            'firstname'          => $request->firstname,
            'lastname'           => $request->lastname,
            'username'           => $request->username,
            'email'              => $request->email,
            'password'           => Hash::make($request->password),
            'role'               => $request->role ?? 'researcher',
            'profile_picture'    => $profilePicturePath,
            'member_agencies_id' => $request->member_agencies_id,
            'verification_token' => $token,
            'expires_at'         => now()->addHours(24),
        ]);

        Mail::to($pendingUser->email)->send(new VerifyPendingEmail($pendingUser));

        return redirect()->route('verification.notice.pending')
            ->with('success', 'Registration initiated! Please check your email to verify your address.');
    }

    /**
     * Handle email verification and create the actual user.
     */
    public function verifyPending($token, $email)
    {
        $pendingUser = PendingUser::where('verification_token', $token)
                        ->where('email', $email)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$pendingUser) {
            return redirect()->route('register')
                ->withErrors(['email' => 'Invalid or expired verification link.']);
        }

        $user = User::create([
            'firstname'          => $pendingUser->firstname,
            'lastname'           => $pendingUser->lastname,
            'username'           => $pendingUser->username,
            'email'              => $pendingUser->email,
            'password'           => $pendingUser->password, 
            'role'               => $pendingUser->role,
            'profile_picture'    => $pendingUser->profile_picture,
            'member_agencies_id' => $pendingUser->member_agencies_id,

        ]);

        $user->email_verified_at = now();
        $user->save();

        $pendingUser->delete();

        Auth::login($user);

        session([
            'user_id'   => $user->id,
            'username'  => $user->username,
            'firstname' => $user->firstname,
            'role'      => $user->role,
        ]);

        return match($user->role) {
            default => redirect('/login')->with('success', 'Email verified! Your account is now active.'),
        };
    }

  
    public function resendPendingVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $pendingUser = PendingUser::where('email', $request->email)->first();

        if (!$pendingUser) {
            return back()->withErrors(['email' => 'No pending registration found for this email.']);
        }

        if ($pendingUser->expires_at < now()) {
            $pendingUser->delete();
            return back()->withErrors(['email' => 'Your verification link has expired. Please register again.']);
        }

        Mail::to($pendingUser->email)->send(new VerifyPendingEmail($pendingUser));

        return back()->with('success', 'Verification email resent. Please check your inbox.');
    }

    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Validate username and password format.
     *
     * @param string $username
     * @param string $password
     * @return array  Associative array of field => error message (empty if valid)
     */
    private function validateUsernamePasswordFormat($username, $password)
    {
        $errors = [];

        if (!preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $username)) {
            $errors['username'] = 'Username must be 3-50 characters and can only contain letters, numbers, underscores, and hyphens.';
        }

        if (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one digit.';
        } elseif (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one special character.';
        }

        return $errors;
    }
}