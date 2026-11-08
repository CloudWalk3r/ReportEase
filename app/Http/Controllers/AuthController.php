<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Roles a visitor is allowed to self-register as. Privileged roles
     * (Admin, Maintenance Department) must be provisioned by an administrator
     * to prevent self-serve privilege escalation.
     */
    protected const SELF_REGISTER_ROLES = ['Student', 'Faculty Staff'];

    // return login view
    public function Login()
    {
        return view('auth.login');
    }

    // validating login
    public function LoginCustom(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (auth()->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Store user role/type in session for quick access in views
            $user = auth()->user()->load('role');
            $roleName = optional($user->role)->role_name;

            session([
                'user_role' => $roleName,
                'user_role_id' => $user->role_id ?? null,
                'user_id' => $user->user_id ?? null,
            ]);

            // Role mapping lives in DashboardController@home (single source of truth)
            return redirect()->intended(route('dashboard'))
                ->withSuccess('You have successfully logged in');
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password'])
            ->withInput($request->only('email'));
    }

    // return registration view
    public function Register()
    {
        return view('auth.register', [
            'roles' => $this->selfRegisterRoles(),
        ]);
    }

    // Show forget password form
    public function showForgetPasswordForm()
    {
        return view('auth.forgetpassword');
    }

    // validating registration
    public function RegisterCustom(Request $request)
    {
        $allowedRoleIds = $this->selfRegisterRoles()->pluck('role_id')->all();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:Users,email',
            'registration_number' => 'required|string|max:255|unique:Users,ID',
            'phone_number' => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/', 'unique:Users,phone_number'],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => ['required', Rule::in($allowedRoleIds)],
        ], [
            'role_id.in' => 'Please select a valid role. Admin and Maintenance accounts are created by administrators.',
            'phone_number.regex' => 'Please enter a valid phone number.',
        ]);

        $this->create($validated);

        return redirect()->route('login')->withSuccess('You have successfully registered. Please log in.');
    }

    protected function create(array $data): User
    {
        return User::create([
            'full_name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'ID' => $data['registration_number'],
            'password' => bcrypt($data['password']),
            'role_id' => $data['role_id'],
            'phone_number' => $data['phone_number'],
        ]);
    }

    /**
     * Roles presented on the public registration form.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function selfRegisterRoles()
    {
        try {
            $roles = Role::whereIn('role_name', self::SELF_REGISTER_ROLES)->get();
            if ($roles->isNotEmpty()) {
                return $roles;
            }
        } catch (\Throwable $e) {
            // Database not ready — fall through to a safe default list.
        }

        return collect([
            (object) ['role_id' => 4, 'role_name' => 'Student'],
            (object) ['role_id' => 2, 'role_name' => 'Faculty Staff'],
        ]);
    }

    // logout function
    public function Logout(Request $request)
    {
        $request->session()->forget(['user_role', 'user_role_id', 'user_id']);
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withSuccess('You have successfully logged out');
    }

    // send reset link
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = \Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    // routes to welcomepage
    public function Welcome()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('welcome');
    }

    // show settings page
    public function showSettings()
    {
        $user = auth()->user()->load('role');

        return view('settings.settings', compact('user'));
    }

    // update user settings
    public function updateSettings(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/', Rule::unique('Users', 'phone_number')->ignore($user->user_id, 'user_id')],
            'email' => ['required', 'email', 'max:255', Rule::unique('Users', 'email')->ignore($user->user_id, 'user_id')],
        ], [
            'phone_number.regex' => 'Please enter a valid phone number.',
        ]);

        $user->update([
            'full_name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
            'phone_number' => $validated['phone_number'],
            'email' => $validated['email'],
        ]);

        return redirect()->route('settings')->with('success', 'Profile updated successfully!');
    }
}
