<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WorkAssignmentApprovalMail extends Mailable
{
    public array $workflow;
    public string $token;
    public string $role;
    protected string $pdfPath;

    public function __construct(array $workflow, string $token, string $role, string $pdfPath)
    {
        $this->workflow = $workflow;
        $this->token = $token;
        $this->role = $role;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $link = $this->role === 'mo'
            ? route('approval.mo', $this->token)
            : route('approval.mf', $this->token);

        $subject = $this->role === 'mo'
            ? 'Approval Work Assignment - Manager Operasi'
            : 'Approval Work Assignment - Manager Finance';

        return $this->subject($subject)
            ->view('emails.work_assignment_approval_single')
            ->with([
                'workflow' => $this->workflow,
                'link'     => $link,
                'role'     => strtoupper($this->role),
            ])
            ->attach($this->pdfPath);
    }
}
