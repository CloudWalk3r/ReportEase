<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\IssueImage;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class IssueController extends Controller
{
    /**
     * Resolve the current user's role from the persisted relationship, falling
     * back to the synced session value. Using the database record keeps
     * authorization decisions robust even if the session copy is stale.
     */
    protected function currentRole(): ?string
    {
        $user = auth()->user();

        if ($user) {
            $roleName = optional($user->loadMissing('role')->role)->role_name;
            if ($roleName) {
                return $roleName;
            }
        }

        return session('user_role');
    }

    // Show all issues with role-based filtering
    public function index()
    {
        $role = $this->currentRole();

        $query = Issue::with(['user.role', 'assignee.role']);

        // Students can see all issues except resolved ones.
        if ($role === 'Student') {
            $query->where('status', '!=', 'Resolved');
        }

        $reports = $query->orderByDesc('reported_at')->paginate(15);

        return view('shared.PostedIssues', compact('reports'));
    }

    // Show create issue form
    public function create()
    {
        return view('shared.CreateIssue');
    }

    // Store a newly created issue
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'location' => 'required|string|max:255',
            'evidence' => 'nullable|array|max:3',
            'evidence.*' => 'image|mimes:jpg,jpeg,png|max:2048', // up to 2MB per image
        ]);

        // Persist the issue first. New reports always start at "Pending"
        // (the first stage of the documented triage workflow).
        $issue = new Issue();
        $issue->title = $validated['title'];
        $issue->description = $validated['description'];
        $issue->location = $validated['location'];
        $issue->status = 'Pending';
        $issue->reported_by_user_id = auth()->id();
        $issue->reported_at = now();
        $issue->upVotes = 0;
        $issue->save();

        // Handle multiple image uploads
        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $image) {
                // Store original image
                $originalPath = $image->store('evidence', 'public');

                // Generate a 200x200 thumbnail (keeping aspect ratio)
                $thumbnail = Image::make($image)->resize(200, 200, function ($constraint) {
                    $constraint->aspectRatio();
                });

                // Random filename for security
                $thumbnailPath = 'evidence/thumbnails/' . Str::random(40) . '.' . $image->extension();
                Storage::disk('public')->put($thumbnailPath, (string) $thumbnail->encode());

                IssueImage::create([
                    'issue_id' => $issue->issue_id,
                    'original_path' => $originalPath,
                    'thumbnail_path' => $thumbnailPath,
                ]);
            }
        }

        return redirect()
            ->route('previous.reports')
            ->with('success', 'Your issue has been submitted successfully.');
    }

    // Show a specific issue
    public function show($id)
    {
        $issue = Issue::with(['user.role', 'assignee.role', 'images'])
            ->where('issue_id', $id)
            ->firstOrFail();

        $role = $this->currentRole();

        // Students cannot view resolved issues.
        if ($role === 'Student' && $issue->status === 'Resolved') {
            return redirect()->route('previous.reports')
                ->with('error', 'You cannot view resolved issues.');
        }

        return view('shared.viewissues', compact('issue'));
    }

    // Alias used by the /shared/viewissues/{id} route
    public function showViewIssues($id)
    {
        return $this->show($id);
    }

    // Update Issue Status
    public function UpdateIssueStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|string|in:accepted,under_review,being_resolved,resolved,rejected',
        ]);

        // Authorization: only triage roles may update status.
        $role = $this->currentRole();
        $allowedRoles = ['Admin', 'Faculty Staff', 'Maintenance Department'];
        if (! in_array($role, $allowedRoles, true)) {
            return redirect()->back()->with('error', 'You are not authorized to update status.');
        }

        // Faculty Staff are limited to the early triage actions.
        $action = $request->input('action');
        if ($role === 'Faculty Staff' && ! in_array($action, ['accepted', 'under_review', 'rejected'], true)) {
            return redirect()->back()->with('error', 'Faculty Staff can only Accept, set to Under Review, or Reject issues.');
        }

        $issue = Issue::where('issue_id', $id)->firstOrFail();

        $statusMap = [
            'accepted' => 'Accepted',
            'under_review' => 'Under Review',
            'being_resolved' => 'Being Resolved',
            'resolved' => 'Resolved',
            'rejected' => 'Rejected',
        ];

        $issue->status = $statusMap[$action];
        $issue->resolved_at = $action === 'resolved' ? now() : null;
        $issue->save();

        return redirect()->back()->with('success', 'Issue status updated successfully.');
    }
}
