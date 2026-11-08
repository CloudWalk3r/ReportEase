<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/student/dashboard')->assertRedirect(route('login'));
    }

    public function test_student_can_access_their_own_dashboard(): void
    {
        $user = $this->createUser('Student');
        $this->actingAs($user)->get('/student/dashboard')->assertStatus(200);
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $user = $this->createUser('Student');
        $this->actingAs($user)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_student_cannot_access_faculty_dashboard(): void
    {
        $user = $this->createUser('Student');
        $this->actingAs($user)->get('/facultystaff/dashboard')->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $user = $this->createUser('Admin');
        $this->actingAs($user)->get('/admin/dashboard')->assertStatus(200);
    }

    public function test_each_role_lands_on_its_own_dashboard(): void
    {
        $cases = [
            'Student' => route('student.studash'),
            'Faculty Staff' => route('facultystaff.dashboard'),
            'Maintenance Department' => route('maintenancedep.dashboard'),
            'Admin' => route('admin.dashboard'),
        ];

        foreach ($cases as $role => $expected) {
            $user = $this->createUser($role);
            $this->actingAs($user)->get('/dashboard')->assertRedirect($expected);
        }
    }

    public function test_maintenance_staff_cannot_open_create_issue_form(): void
    {
        $user = $this->createUser('Maintenance Department');
        $this->actingAs($user)->get('/shared/CreateIssue')->assertStatus(403);
    }

    public function test_student_can_open_create_issue_form(): void
    {
        $user = $this->createUser('Student');
        $this->actingAs($user)->get('/shared/CreateIssue')->assertStatus(200);
    }

    public function test_non_admin_cannot_access_reports_overview(): void
    {
        $user = $this->createUser('Faculty Staff');
        $this->actingAs($user)->get('/reports')->assertStatus(403);
    }

    public function test_admin_can_access_reports_overview(): void
    {
        $user = $this->createUser('Admin');
        $this->actingAs($user)->get('/reports')->assertStatus(200);
    }
}
