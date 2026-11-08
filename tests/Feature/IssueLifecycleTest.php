<?php

namespace Tests\Feature;

use App\Models\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class IssueLifecycleTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    public function test_student_can_create_issue_and_it_defaults_to_pending(): void
    {
        $student = $this->createUser('Student');

        $response = $this->actingAs($student)->post('/shared/CreateIssue', [
            'title' => 'Leaking tap in lab',
            'description' => 'Water is leaking continuously in Lab 3.',
            'location' => 'Lab 3',
        ]);

        $response->assertRedirect(route('previous.reports'));

        $this->assertDatabaseHas('Issues', [
            'title' => 'Leaking tap in lab',
            'status' => 'Pending',
            'reported_by_user_id' => $student->user_id,
        ]);
    }

    public function test_create_issue_requires_title_and_location(): void
    {
        $student = $this->createUser('Student');

        $this->actingAs($student)
            ->from('/shared/CreateIssue')
            ->post('/shared/CreateIssue', [
                'description' => 'Missing title and location.',
            ])
            ->assertSessionHasErrors(['title', 'location']);
    }

    public function test_reporter_can_view_their_issue(): void
    {
        $student = $this->createUser('Student');
        $issue = $this->createIssue(['reported_by_user_id' => $student->user_id]);

        $this->actingAs($student)
            ->get(route('shared.viewissues', $issue->issue_id))
            ->assertStatus(200)
            ->assertSee('Broken projector in NLH');
    }

    public function test_faculty_can_accept_an_issue(): void
    {
        $faculty = $this->createUser('Faculty Staff');
        $issue = $this->createIssue();

        $this->actingAs($faculty)
            ->post('/issues/update/' . $issue->issue_id, ['action' => 'accepted'])
            ->assertRedirect();

        $this->assertEquals('Accepted', $issue->fresh()->status);
    }

    public function test_faculty_cannot_resolve_an_issue(): void
    {
        $faculty = $this->createUser('Faculty Staff');
        $issue = $this->createIssue(['status' => 'Accepted']);

        $this->actingAs($faculty)
            ->post('/issues/update/' . $issue->issue_id, ['action' => 'resolved']);

        // Faculty are limited to early triage; status must not become Resolved.
        $this->assertEquals('Accepted', $issue->fresh()->status);
    }

    public function test_maintenance_can_resolve_an_issue(): void
    {
        $maintenance = $this->createUser('Maintenance Department');
        $issue = $this->createIssue(['status' => 'Being Resolved']);

        $this->actingAs($maintenance)
            ->post('/issues/update/' . $issue->issue_id, ['action' => 'resolved'])
            ->assertRedirect();

        $fresh = $issue->fresh();
        $this->assertEquals('Resolved', $fresh->status);
        $this->assertNotNull($fresh->resolved_at);
    }

    public function test_student_cannot_update_issue_status(): void
    {
        $student = $this->createUser('Student');
        $issue = $this->createIssue();

        $this->actingAs($student)
            ->post('/issues/update/' . $issue->issue_id, ['action' => 'accepted'])
            ->assertStatus(403);

        $this->assertEquals('Pending', $issue->fresh()->status);
    }

    public function test_student_cannot_view_resolved_issue(): void
    {
        $student = $this->createUser('Student');
        $issue = $this->createIssue(['status' => 'Resolved']);

        $this->actingAs($student)
            ->get(route('shared.viewissues', $issue->issue_id))
            ->assertRedirect(route('previous.reports'));
    }

    public function test_student_issue_list_excludes_resolved_issues(): void
    {
        $student = $this->createUser('Student');
        $this->createIssue(['title' => 'Visible pending issue', 'status' => 'Pending']);
        $this->createIssue(['title' => 'Hidden resolved issue', 'status' => 'Resolved']);

        $this->actingAs($student)
            ->get(route('previous.reports'))
            ->assertStatus(200)
            ->assertSee('Visible pending issue')
            ->assertDontSee('Hidden resolved issue');
    }
}
