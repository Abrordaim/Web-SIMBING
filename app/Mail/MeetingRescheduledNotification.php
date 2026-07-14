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

class MeetingRescheduledNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Meeting $meeting,
        public readonly User $studentUser,
        public readonly User $lecturerUser,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Mahasiswa Mengajukan Ulang Jadwal Bimbingan');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.meeting-rescheduled');
    }

    public function attachments(): array { return []; }
}
