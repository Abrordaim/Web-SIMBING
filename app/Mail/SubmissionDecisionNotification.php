<?php

namespace App\Mail;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionDecisionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Submission $submission,
        public readonly string $decisionType,
        public readonly string $feedbackText,
        public readonly User $studentUser,
        public readonly User $lecturerUser,
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'approved'       => 'Pengajuan Anda Disetujui!',
            'revision_minor' => 'Pengajuan Perlu Revisi Minor',
            'revision_major' => 'Pengajuan Perlu Revisi Mayor',
            'rejected'       => 'Pengajuan Anda Ditolak',
        ];

        return new Envelope(
            subject: $subjects[$this->decisionType] ?? 'Update Status Pengajuan Skripsi',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.submission-decision');
    }

    public function attachments(): array { return []; }
}
