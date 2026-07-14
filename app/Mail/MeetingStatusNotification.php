<?php

namespace App\Mail;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetingStatusNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Meeting $meeting,
        public readonly string $status, // 'confirmed' | 'cancelled'
        public readonly User $studentUser,
        public readonly User $lecturerUser,
        public readonly string $rejectReason = '', // alasan penolakan, jika ada
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->status === 'confirmed'
            ? 'Jadwal Bimbingan Dikonfirmasi'
            : 'Jadwal Bimbingan Ditolak';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.meeting-status');
    }

    public function attachments(): array { return []; } 
}
