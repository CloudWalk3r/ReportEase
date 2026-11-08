<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Role-aware entry point. Sends the user to the dashboard that matches
     * their persisted role. Used as the post-login / HOME redirect target so
     * the mapping lives in exactly one place.
     */
    public function home(Request $request)
    {
        $roleName = optional(Auth::user()->loadMissing('role')->role)->role_name;

        switch ($roleName) {
            case 'Student':
                return redirect()->route('student.studash');
            case 'Faculty Staff':
                return redirect()->route('facultystaff.dashboard');
            case 'Maintenance Department':
                return redirect()->route('maintenancedep.dashboard');
            case 'Admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect()->route('welcome');
        }
    }

    public function index(Request $request)
    {
        $user = Auth::user()->load('role');

        return view('student.studash', [
            'student' => $user,
            'stats' => $this->statsFor($user, true),
            'recentIssues' => $user->reportedIssues()
                ->orderByDesc('reported_at')
                ->limit(5)
                ->get(),
        ]);
    }

    public function facultyStaffDashboard(Request $request)
    {
        $user = Auth::user()->load('role');

        return view('facultystaff.fsdash', [
            'user' => $user,
            'stats' => $this->statsFor($user, false),
            'recentIssues' => $this->recentGlobalIssues(),
        ]);
    }

    public function maintenanceDashboard(Request $request)
    {
        $user = Auth::user()->load('role');

        return view('maintenancedep.maintenancedepdash', [
            'user' => $user,
            'stats' => $this->statsFor($user, false),
            'recentIssues' => $this->recentGlobalIssues(),
        ]);
    }

    public function adminDashboard(Request $request)
    {
        $user = Auth::user()->load('role');

        return view('admin.admindash', [
            'user' => $user,
            'stats' => $this->statsFor($user, false),
            'recentIssues' => $this->recentGlobalIssues(),
        ]);
    }

    /**
     * Build a small status breakdown used by the dashboard summary cards.
     *
     * @param  \App\Models\User  $user
     * @param  bool  $scopeToUser  When true, only counts issues reported by the user.
     * @return array<string,int>
     */
    protected function statsFor($user, bool $scopeToUser): array
    {
        $base = $scopeToUser
            ? Issue::where('reported_by_user_id', $user->user_id)
            : Issue::query();

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'Pending')->count(),
            'in_progress' => (clone $base)->whereIn('status', ['Under Review', 'Accepted', 'Being Resolved'])->count(),
            'resolved' => (clone $base)->where('status', 'Resolved')->count(),
        ];
    }

    protected function recentGlobalIssues()
    {
        return Issue::with(['user.role'])
            ->orderByDesc('reported_at')
            ->limit(5)
            ->get();
    }
}
