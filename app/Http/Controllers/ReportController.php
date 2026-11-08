<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of reported issues.
     *
     * NOTE: There is no dedicated "Report" model in this application; issue
     * reports are stored in the Issue model. This controller previously
     * referenced a non-existent App\Models\Report class, which caused a
     * fatal error. It now reads from the Issue model and renders the
     * existing shared views.
     */
    public function index()
    {
        $reports = Issue::with(['user.role', 'assignee.role'])
            ->orderByDesc('reported_at')
            ->paginate(15);

        return view('shared.PostedIssues', compact('reports'));
    }

    public function show($id)
    {
        $issue = Issue::with(['user.role', 'assignee.role', 'images'])
            ->where('issue_id', $id)
            ->firstOrFail();

        return view('shared.viewissues', compact('issue'));
    }
}
