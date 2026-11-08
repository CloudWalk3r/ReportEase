<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    public function test_welcome_page_loads_for_guests(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_student_can_register(): void
    {
        $this->seedRoles();

        $response = $this->post('/register-custom', [
            'first_name' => 'Samanalee',
            'last_name' => 'Fernando',
            'email' => 'samanalee@std.appsc.sab.ac.lk',
            'registration_number' => '21CIS004',
            'phone_number' => '0771234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->roleIds['Student'],
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('Users', [
            'email' => 'samanalee@std.appsc.sab.ac.lk',
            'full_name' => 'Samanalee Fernando',
            'ID' => '21CIS004',
            'phone_number' => '0771234567', // stored as string, leading zero kept
            'role_id' => $this->roleIds['Student'],
        ]);
    }

    public function test_visitor_cannot_self_register_as_admin(): void
    {
        $this->seedRoles();

        $response = $this->from('/register')->post('/register-custom', [
            'first_name' => 'Mallory',
            'last_name' => 'Hacker',
            'email' => 'mallory@example.com',
            'registration_number' => 'HACK001',
            'phone_number' => '0770000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->roleIds['Admin'],
        ]);

        $response->assertSessionHasErrors('role_id');
        $this->assertDatabaseMissing('Users', ['email' => 'mallory@example.com']);
    }

    public function test_visitor_cannot_self_register_as_maintenance(): void
    {
        $this->seedRoles();

        $this->from('/register')->post('/register-custom', [
            'first_name' => 'Mal',
            'last_name' => 'Ory',
            'email' => 'maint@example.com',
            'registration_number' => 'MNT001',
            'phone_number' => '0770000001',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_id' => $this->roleIds['Maintenance Department'],
        ])->assertSessionHasErrors('role_id');

        $this->assertDatabaseMissing('Users', ['email' => 'maint@example.com']);
    }

    public function test_registration_rejects_short_password(): void
    {
        $this->seedRoles();

        $this->from('/register')->post('/register-custom', [
            'first_name' => 'Short',
            'last_name' => 'Pass',
            'email' => 'short@example.com',
            'registration_number' => 'SHORT1',
            'phone_number' => '0771111111',
            'password' => 'short',
            'password_confirmation' => 'short',
            'role_id' => $this->roleIds['Student'],
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('Users', ['email' => 'short@example.com']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = $this->createUser('Student', [
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login-custom', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->createUser('Student', [
            'email' => 'login2@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->from('/login')->post('/login-custom', [
            'email' => 'login2@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->createUser('Student');

        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authenticated_user_is_redirected_away_from_login(): void
    {
        $user = $this->createUser('Student');

        $this->actingAs($user)->get('/login')->assertRedirect(route('dashboard'));
    }
}
