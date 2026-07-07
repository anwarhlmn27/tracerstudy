<?php

namespace App\Http\Controllers;

use App\Jobs\SendBlastEmailJob;
use App\Models\User;
use Illuminate\Http\Request;

class EmailBlastController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('email.index', compact('users'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:group,individual',
            'group' => 'required_if:target_type,group|in:alumni,atasan',
            'users' => 'required_if:target_type,individual|array',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // max 10MB per file
        ]);

        $recipients = [];

        if ($request->target_type === 'group') {
            $recipients = User::where('role', $request->group)->pluck('email')->toArray();
        } else {
            $recipients = User::whereIn('id', $request->users)->pluck('email')->toArray();
        }

        // Filter out empty emails
        $recipients = array_filter($recipients);

        if (empty($recipients)) {
            return back()->with('error', 'No valid recipients found.');
        }

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $attachmentPaths[] = $file->store('email_attachments', 'public');
            }
        }

        // Dispatch the job
        SendBlastEmailJob::dispatch($recipients, $request->subject, $request->body, $attachmentPaths);

        return back()->with('success', 'Email blast has been queued successfully and will be sent shortly.');
    }
}
