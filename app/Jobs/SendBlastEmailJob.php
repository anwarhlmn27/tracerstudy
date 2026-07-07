<?php

namespace App\Jobs;

use App\Mail\BlastEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendBlastEmailJob implements ShouldQueue
{
    use Queueable;

    public $recipients; // Array of emails
    public $subjectLine;
    public $bodyContent;
    public $attachmentPaths;

    /**
     * Create a new job instance.
     */
    public function __construct(array $recipients, $subjectLine, $bodyContent, $attachmentPaths = [])
    {
        $this->recipients = $recipients;
        $this->subjectLine = $subjectLine;
        $this->bodyContent = $bodyContent;
        $this->attachmentPaths = is_array($attachmentPaths) ? $attachmentPaths : [];
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->recipients as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($email)->send(new BlastEmail($this->subjectLine, $this->bodyContent, $this->attachmentPaths));
            }
        }
    }
}
