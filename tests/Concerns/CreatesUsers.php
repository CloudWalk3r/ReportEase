<?php

namespace Tests\Concerns;

use App\Models\Issue;
use App\Models\User;
use Database\Seeders\RoleSeeder;

trait CreatesUsers
{
    /**
     * Map of role name => role_id as defined by RoleSeeder.
     */
    protected array $roleIds = [
        'Admin' => 1,
        'Faculty Staff' => 2,
        'Maintenance Department' => 3,
        'Student' => 4,
    ];

    protected function seedRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }

    protected function createUser(string $roleName = 'Student', array $overrides = []): User
    {
        $this->seedRoles();

        return User::factory()->create(array_merge([
            'role_id' => $this->roleIds[$roleName],
        ], $overrides));
    }

    protected function createIssue(array $overrides = []): Issue
    {
        $reporter = $overrides['reported_by_user_id'] ?? $this->createUser('Student')->user_id;

        return Issue::create(array_merge([
            'title' => 'Broken projector in NLH',
            'description' => 'The projector does not power on.',
            'location' => 'New Lecture Hall',
            'status' => 'Pending',
            'reported_by_user_id' => $reporter,
            'reported_at' => now(),
            'upVotes' => 0,
        ], $overrides));
    }
}
