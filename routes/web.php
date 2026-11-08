<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ResetPasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes (no authentication required)
Route::get('/', [AuthController::class, 'Welcome'])->name('welcome');

// Guest-only routes (already-authenticated users are redirected away)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login-custom', [AuthController::class, 'LoginCustom'])->name('login.custom');
    Route::get('/register', [AuthController::class, 'Register'])->name('register');
    Route::post('/register-custom', [AuthController::class, 'RegisterCustom'])->name('register.custom');
    Route::get('/forget-password', [AuthController::class, 'showForgetPasswordForm'])->name('password.request');
    Route::post('/forget-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/recovery-email-sent', function () {
        return view('auth.recovery');
    })->name('password.recovery');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

// Protected routes (authentication required)
Route::middleware(['auth'])->group(function () {

    // Logout (needs to be authenticated to logout)
    Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'Logout'])->name('logout');

    // Role-aware landing route (post-login / HOME target)
    Route::get('/dashboard', [DashboardController::class, 'home'])->name('dashboard');

    // Role-specific dashboards (each locked to its own role)
    Route::get('/student/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:Student')
        ->name('student.studash');
    Route::get('/facultystaff/dashboard', [DashboardController::class, 'facultyStaffDashboard'])
        ->middleware('role:Faculty Staff')
        ->name('facultystaff.dashboard');
    Route::get('/maintenancedep/dashboard', [DashboardController::class, 'maintenanceDashboard'])
        ->middleware('role:Maintenance Department')
        ->name('maintenancedep.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])
        ->middleware('role:Admin')
        ->name('admin.dashboard');

    // Issue Management routes (viewing is available to every authenticated user)
    Route::get('/PostedIssues', [IssueController::class, 'index'])->name('previous.reports');
    Route::get('/issues/{id}', [IssueController::class, 'show'])->name('issues.show');
    Route::get('/shared/viewissues/{id}', [IssueController::class, 'showViewIssues'])->name('shared.viewissues');

    // Create issues (reporters only — maintenance staff resolve, they don't report)
    Route::middleware('role:Student,Faculty Staff,Admin')->group(function () {
        Route::get('shared/CreateIssue', [IssueController::class, 'create'])->name('issue.create');
        Route::post('shared/CreateIssue', [IssueController::class, 'store'])->name('issues.store');
    });

    // Update issue status (triage roles only; also enforced in the controller)
    Route::post('/issues/update/{id}', [IssueController::class, 'UpdateIssueStatus'])
        ->middleware('role:Admin,Faculty Staff,Maintenance Department')
        ->name('issues.update');

    // Reports (admin overview)
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:Admin')
        ->name('report');
    Route::get('/report/{id}', [ReportController::class, 'show'])
        ->middleware('role:Admin')
        ->name('report.show');

    // Settings/Profile routes
    Route::get('/settings', [AuthController::class, 'showSettings'])->name('settings');
    Route::post('/settings', [AuthController::class, 'updateSettings'])->name('settings.update');

});
