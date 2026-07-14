<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\ThesisSupervision;
use App\Models\Submission;
use App\Models\SubmissionDecision;
use App\Models\Comment;
use App\Models\Meeting;
use App\Models\TimelineEvent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // =============================================
        // USERS
        // =============================================

        // Dosen
        $dosenUser = User::create([
            'name' => 'Dr. Ahmad Susanto',
            'email' => 'ahmad.susanto@university.ac.id',
            'password' => 'password',
            'role' => 'lecturer',
            'phone' => '+62 812-9876-5432',
        ]);

        // Mahasiswa
        $sarahUser = User::create([
            'name' => 'Sarah Wijaya',
            'email' => 'sarah.wijaya@student.ac.id',
            'password' => 'password',
            'role' => 'student',
            'phone' => '+62 812-3456-7890',
        ]);

        $budiUser = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@student.ac.id',
            'password' => 'password',
            'role' => 'student',
            'phone' => '+62 812-9876-5432',
        ]);

        $rinaUser = User::create([
            'name' => 'Rina Kusuma',
            'email' => 'rina.kusuma@student.ac.id',
            'password' => 'password',
            'role' => 'student',
            'phone' => '+62 813-5566-7788',
        ]);

        $ahmadUser = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'ahmad.fauzi@student.ac.id',
            'password' => 'password',
            'role' => 'student',
            'phone' => '+62 812-4455-6677',
        ]);

        // =============================================
        // LECTURER
        // =============================================
        $lecturer = Lecturer::create([
            'user_id' => $dosenUser->id,
            'nidn' => '0312058901',
            'department' => 'Teknik Informatika',
            'faculty' => 'Fakultas Teknik',
            'specialization' => 'Machine Learning & Data Science',
        ]);

        // =============================================
        // STUDENTS
        // =============================================
        $sarah = Student::create([
            'user_id' => $sarahUser->id,
            'nim' => '2001010234',
            'semester' => 8,
            'department' => 'Teknik Informatika',
            'faculty' => 'Fakultas Teknik',
        ]);

        $budi = Student::create([
            'user_id' => $budiUser->id,
            'nim' => '2001010156',
            'semester' => 8,
            'department' => 'Teknik Informatika',
            'faculty' => 'Fakultas Teknik',
        ]);

        $rina = Student::create([
            'user_id' => $rinaUser->id,
            'nim' => '2001010089',
            'semester' => 9,
            'department' => 'Teknik Informatika',
            'faculty' => 'Fakultas Teknik',
        ]);

        $ahmad = Student::create([
            'user_id' => $ahmadUser->id,
            'nim' => '2001010312',
            'semester' => 8,
            'department' => 'Teknik Informatika',
            'faculty' => 'Fakultas Teknik',
        ]);

        // =============================================
        // THESIS SUPERVISIONS
        // =============================================
        $supSarah = ThesisSupervision::create([
            'student_id' => $sarah->id,
            'lecturer_id' => $lecturer->id,
            'title' => 'Implementasi Machine Learning untuk Deteksi Spam Email',
            'progress' => 65,
            'status' => 'active',
            'start_date' => '2026-02-15',
        ]);

        $supBudi = ThesisSupervision::create([
            'student_id' => $budi->id,
            'lecturer_id' => $lecturer->id,
            'title' => 'Sistem Informasi Manajemen Perpustakaan Berbasis Web',
            'progress' => 80,
            'status' => 'active',
            'start_date' => '2026-01-20',
        ]);

        $supRina = ThesisSupervision::create([
            'student_id' => $rina->id,
            'lecturer_id' => $lecturer->id,
            'title' => 'Aplikasi Mobile E-Commerce dengan Flutter',
            'progress' => 45,
            'status' => 'warning',
            'start_date' => '2025-10-01',
        ]);

        $supAhmad = ThesisSupervision::create([
            'student_id' => $ahmad->id,
            'lecturer_id' => $lecturer->id,
            'title' => 'Analisis Sentimen Media Sosial menggunakan Deep Learning',
            'progress' => 90,
            'status' => 'active',
            'start_date' => '2026-01-15',
        ]);

        // =============================================
        // SUBMISSIONS — Sarah Wijaya
        // =============================================
        $sarahSub1 = Submission::create([
            'supervision_id' => $supSarah->id,
            'title' => 'BAB 3 - Metodologi Penelitian',
            'chapter' => 'BAB 3',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab3.pdf',
            'file_size' => '2.8 MB',
            'status' => 'approved',
            'resolved' => true,
            'submitted_at' => '2026-04-24 10:00:00',
        ]);

        $sarahSub2 = Submission::create([
            'supervision_id' => $supSarah->id,
            'title' => 'BAB 2 - Tinjauan Pustaka',
            'chapter' => 'BAB 2',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab2.pdf',
            'file_size' => '2.3 MB',
            'status' => 'revision',
            'resolved' => false,
            'submitted_at' => '2026-04-20 10:15:00',
        ]);

        $sarahSub3 = Submission::create([
            'supervision_id' => $supSarah->id,
            'title' => 'BAB 1 - Pendahuluan',
            'chapter' => 'BAB 1',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab1.pdf',
            'file_size' => '1.5 MB',
            'status' => 'pending',
            'resolved' => false,
            'submitted_at' => '2026-04-18 11:00:00',
        ]);

        $sarahSub4 = Submission::create([
            'supervision_id' => $supSarah->id,
            'title' => 'Proposal Penelitian',
            'chapter' => null,
            'type' => 'Proposal',
            'file_path' => '/pdfs/proposal.pdf',
            'file_size' => '3.1 MB',
            'status' => 'approved',
            'resolved' => true,
            'submitted_at' => '2026-03-10 09:00:00',
        ]);

        // =============================================
        // SUBMISSIONS — Budi Santoso
        // =============================================
        $budiSub1 = Submission::create([
            'supervision_id' => $supBudi->id,
            'title' => 'BAB 4 - Implementasi Sistem',
            'chapter' => 'BAB 4',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab4.pdf',
            'file_size' => '4.2 MB',
            'status' => 'pending',
            'resolved' => false,
            'submitted_at' => '2026-04-22 10:00:00',
        ]);

        $budiSub2 = Submission::create([
            'supervision_id' => $supBudi->id,
            'title' => 'BAB 3 - Perancangan Sistem',
            'chapter' => 'BAB 3',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab3.pdf',
            'file_size' => '3.5 MB',
            'status' => 'approved',
            'resolved' => true,
            'submitted_at' => '2026-04-15 09:00:00',
        ]);

        // =============================================
        // SUBMISSIONS — Rina Kusuma
        // =============================================
        $rinaSub1 = Submission::create([
            'supervision_id' => $supRina->id,
            'title' => 'Revisi BAB 2 - Tinjauan Pustaka',
            'chapter' => 'BAB 2',
            'type' => 'Revisi',
            'file_path' => '/pdfs/bab2.pdf',
            'file_size' => '2.1 MB',
            'status' => 'pending',
            'resolved' => false,
            'submitted_at' => '2026-04-21 10:30:00',
        ]);

        $rinaSub2 = Submission::create([
            'supervision_id' => $supRina->id,
            'title' => 'BAB 2 - Tinjauan Pustaka',
            'chapter' => 'BAB 2',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab2.pdf',
            'file_size' => '1.9 MB',
            'status' => 'revision',
            'resolved' => false,
            'submitted_at' => '2026-04-14 09:00:00',
        ]);

        // =============================================
        // SUBMISSIONS — Ahmad Fauzi
        // =============================================
        $ahmadSub1 = Submission::create([
            'supervision_id' => $supAhmad->id,
            'title' => 'BAB 5 - Kesimpulan & Saran',
            'chapter' => 'BAB 5',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab5.pdf',
            'file_size' => '1.2 MB',
            'status' => 'pending',
            'resolved' => false,
            'submitted_at' => '2026-04-27 08:45:00',
        ]);

        $ahmadSub2 = Submission::create([
            'supervision_id' => $supAhmad->id,
            'title' => 'BAB 4 - Hasil & Pembahasan',
            'chapter' => 'BAB 4',
            'type' => 'Bab',
            'file_path' => '/pdfs/bab4.pdf',
            'file_size' => '5.1 MB',
            'status' => 'approved',
            'resolved' => true,
            'submitted_at' => '2026-04-22 08:45:00',
        ]);

        // =============================================
        // SUBMISSION DECISIONS
        // =============================================
        SubmissionDecision::create([
            'submission_id' => $sarahSub1->id,
            'lecturer_id' => $lecturer->id,
            'decision' => 'approved',
            'feedback' => 'Sudah bagus, metodologi penelitian sudah sesuai. Lanjutkan ke BAB 4.',
            'decided_at' => '2026-04-24 14:30:00',
        ]);

        SubmissionDecision::create([
            'submission_id' => $sarahSub2->id,
            'lecturer_id' => $lecturer->id,
            'decision' => 'revision_minor',
            'feedback' => 'Perlu tambahan referensi jurnal internasional terbaru (2024–2026). Minimal 3 referensi.',
            'decided_at' => '2026-04-20 14:20:00',
        ]);

        SubmissionDecision::create([
            'submission_id' => $sarahSub4->id,
            'lecturer_id' => $lecturer->id,
            'decision' => 'approved',
            'feedback' => 'Proposal disetujui. Silahkan mulai pengerjaan BAB 1.',
            'decided_at' => '2026-03-10 14:00:00',
        ]);

        SubmissionDecision::create([
            'submission_id' => $budiSub2->id,
            'lecturer_id' => $lecturer->id,
            'decision' => 'approved',
            'feedback' => 'Perancangan sistem sudah sangat baik. ERD dan DFD sudah sesuai.',
            'decided_at' => '2026-04-15 14:00:00',
        ]);

        SubmissionDecision::create([
            'submission_id' => $rinaSub2->id,
            'lecturer_id' => $lecturer->id,
            'decision' => 'revision_major',
            'feedback' => 'Referensi Flutter masih kurang. Tambahkan dokumentasi resmi dan studi kasus.',
            'decided_at' => '2026-04-14 14:00:00',
        ]);

        SubmissionDecision::create([
            'submission_id' => $ahmadSub2->id,
            'lecturer_id' => $lecturer->id,
            'decision' => 'approved',
            'feedback' => 'Sangat baik! Analisis hasil sudah komprehensif.',
            'decided_at' => '2026-04-22 13:00:00',
        ]);

        // =============================================
        // COMMENTS — Sarah BAB 3 (approved)
        // =============================================
        Comment::create([
            'submission_id' => $sarahSub1->id,
            'user_id' => $dosenUser->id,
            'text' => 'Sudah saya review BAB 3. Metodologi yang dipilih sudah sesuai dengan permasalahan penelitian. Bagus!',
            'created_at' => '2026-04-24 14:30:00',
        ]);
        Comment::create([
            'submission_id' => $sarahSub1->id,
            'user_id' => $sarahUser->id,
            'text' => 'Terima kasih atas feedbacknya Pak. Apakah ada yang perlu diperbaiki?',
            'created_at' => '2026-04-24 14:45:00',
        ]);
        Comment::create([
            'submission_id' => $sarahSub1->id,
            'user_id' => $dosenUser->id,
            'text' => 'Untuk saat ini sudah oke. Silahkan lanjutkan ke BAB 4 – Implementasi.',
            'created_at' => '2026-04-24 15:00:00',
        ]);

        // COMMENTS — Sarah BAB 2 (revision)
        Comment::create([
            'submission_id' => $sarahSub2->id,
            'user_id' => $sarahUser->id,
            'text' => 'Pak, saya sudah upload BAB 2 revisi. Mohon reviewnya.',
            'created_at' => '2026-04-20 10:15:00',
        ]);
        Comment::create([
            'submission_id' => $sarahSub2->id,
            'user_id' => $dosenUser->id,
            'text' => 'Masih perlu penambahan referensi. Terutama jurnal internasional terbaru (2024–2026). Minimal tambahkan 3 referensi.',
            'created_at' => '2026-04-20 14:20:00',
        ]);

        // COMMENTS — Sarah BAB 1 (pending)
        Comment::create([
            'submission_id' => $sarahSub3->id,
            'user_id' => $sarahUser->id,
            'text' => 'Selamat siang Pak, saya sudah upload BAB 1 untuk review. Terima kasih.',
            'created_at' => '2026-04-18 11:00:00',
        ]);

        // COMMENTS — Budi BAB 4 (pending)
        Comment::create([
            'submission_id' => $budiSub1->id,
            'user_id' => $budiUser->id,
            'text' => 'Pak, saya sudah upload BAB 4 implementasi lengkap.',
            'created_at' => '2026-04-22 10:00:00',
        ]);

        // COMMENTS — Budi BAB 3 (approved)
        Comment::create([
            'submission_id' => $budiSub2->id,
            'user_id' => $budiUser->id,
            'text' => 'Berikut BAB 3 perancangan sistem Pak.',
            'created_at' => '2026-04-15 09:00:00',
        ]);
        Comment::create([
            'submission_id' => $budiSub2->id,
            'user_id' => $dosenUser->id,
            'text' => 'Perancangan sistem sudah sangat baik. Lanjutkan ke BAB 4.',
            'created_at' => '2026-04-15 14:00:00',
        ]);

        // COMMENTS — Rina Revisi BAB 2
        Comment::create([
            'submission_id' => $rinaSub1->id,
            'user_id' => $rinaUser->id,
            'text' => 'Pak, ini revisi BAB 2 saya. Sudah saya tambahkan referensi Flutter terbaru.',
            'created_at' => '2026-04-21 10:30:00',
        ]);

        // COMMENTS — Ahmad BAB 5
        Comment::create([
            'submission_id' => $ahmadSub1->id,
            'user_id' => $ahmadUser->id,
            'text' => 'Pak, BAB 5 sudah saya upload.',
            'created_at' => '2026-04-27 08:45:00',
        ]);

        // COMMENTS — Ahmad BAB 4 (approved)
        Comment::create([
            'submission_id' => $ahmadSub2->id,
            'user_id' => $ahmadUser->id,
            'text' => 'Pak, BAB 4 hasil dan pembahasan sudah saya upload.',
            'created_at' => '2026-04-22 08:45:00',
        ]);
        Comment::create([
            'submission_id' => $ahmadSub2->id,
            'user_id' => $dosenUser->id,
            'text' => 'Sangat baik Ahmad! Lanjutkan ke BAB 5.',
            'created_at' => '2026-04-22 13:00:00',
        ]);

        // =============================================
        // MEETINGS
        // =============================================

        // Sarah's meetings
        Meeting::create([
            'supervision_id' => $supSarah->id,
            'title'          => 'Konsultasi Progress BAB 4',
            'date'           => '2026-04-28',
            'time_start'     => '10:00',
            'location'       => 'Ruang Dosen 301',
            'type'           => 'offline',
            'status'         => 'confirmed',
            'notes'          => 'Siapkan draft BAB 4 untuk didiskusikan',
        ]);

        Meeting::create([
            'supervision_id' => $supSarah->id,
            'title'          => 'Diskusi Metodologi',
            'date'           => '2026-04-29',
            'time_start'     => '14:00',
            'location'       => 'Google Meet',
            'type'           => 'online',
            'status'         => 'confirmed',
            'notes'          => '',
        ]);

        Meeting::create([
            'supervision_id' => $supSarah->id,
            'title'          => 'Review Keseluruhan Draft',
            'date'           => '2026-05-05',
            'time_start'     => '14:00',
            'location'       => 'Google Meet',
            'type'           => 'online',
            'status'         => 'pending',
            'notes'          => 'Menunggu konfirmasi dosen',
        ]);

        Meeting::create([
            'supervision_id' => $supSarah->id,
            'title'          => 'Bimbingan BAB 3',
            'date'           => '2026-04-24',
            'time_start'     => '09:00',
            'location'       => 'Ruang Dosen 301',
            'type'           => 'offline',
            'status'         => 'completed',
            'notes'          => '',
        ]);

        Meeting::create([
            'supervision_id' => $supSarah->id,
            'title'          => 'Konsultasi Proposal',
            'date'           => '2026-04-15',
            'time_start'     => '10:00',
            'location'       => 'Ruang Dosen 301',
            'type'           => 'offline',
            'status'         => 'completed',
            'notes'          => '',
        ]);

        Meeting::create([
            'supervision_id' => $supSarah->id,
            'title'          => 'Revisi BAB 2',
            'date'           => '2026-04-20',
            'time_start'     => '13:00',
            'location'       => 'Ruang Dosen 301',
            'type'           => 'offline',
            'status'         => 'cancelled',
            'notes'          => 'Dibatalkan karena dosen ada keperluan mendadak',
        ]);

        // Budi's meetings
        Meeting::create([
            'supervision_id' => $supBudi->id,
            'title'          => 'Review BAB 4 Implementasi',
            'date'           => '2026-04-30',
            'time_start'     => '10:00',
            'location'       => 'Ruang Dosen 301',
            'type'           => 'offline',
            'status'         => 'confirmed',
            'notes'          => 'Bawa laptop untuk demo implementasi',
        ]);

        // Rina's meetings
        Meeting::create([
            'supervision_id' => $supRina->id,
            'title'          => 'Diskusi Revisi BAB 2',
            'date'           => '2026-04-25',
            'time_start'     => '13:00',
            'location'       => 'Google Meet',
            'type'           => 'online',
            'status'         => 'pending',
            'notes'          => '',
        ]);

        // =============================================
        // TIMELINE EVENTS
        // =============================================

        // Sarah
        TimelineEvent::create(['supervision_id' => $supSarah->id, 'event' => 'BAB 3 disetujui', 'type' => 'approved', 'event_date' => '2026-04-24']);
        TimelineEvent::create(['supervision_id' => $supSarah->id, 'event' => 'BAB 2 perlu revisi', 'type' => 'revision', 'event_date' => '2026-04-20']);
        TimelineEvent::create(['supervision_id' => $supSarah->id, 'event' => 'BAB 1 dikirim, menunggu review', 'type' => 'pending', 'event_date' => '2026-04-18']);
        TimelineEvent::create(['supervision_id' => $supSarah->id, 'event' => 'Proposal disetujui', 'type' => 'approved', 'event_date' => '2026-03-10']);
        TimelineEvent::create(['supervision_id' => $supSarah->id, 'event' => 'Bimbingan dimulai', 'type' => 'info', 'event_date' => '2026-02-15']);

        // Budi
        TimelineEvent::create(['supervision_id' => $supBudi->id, 'event' => 'BAB 4 dikirim, menunggu review', 'type' => 'pending', 'event_date' => '2026-04-22']);
        TimelineEvent::create(['supervision_id' => $supBudi->id, 'event' => 'BAB 3 disetujui', 'type' => 'approved', 'event_date' => '2026-04-15']);
        TimelineEvent::create(['supervision_id' => $supBudi->id, 'event' => 'Bimbingan dimulai', 'type' => 'info', 'event_date' => '2026-01-20']);

        // Rina
        TimelineEvent::create(['supervision_id' => $supRina->id, 'event' => 'Revisi BAB 2 dikirim', 'type' => 'pending', 'event_date' => '2026-04-21']);
        TimelineEvent::create(['supervision_id' => $supRina->id, 'event' => 'BAB 2 perlu revisi', 'type' => 'revision', 'event_date' => '2026-04-14']);
        TimelineEvent::create(['supervision_id' => $supRina->id, 'event' => 'Bimbingan dimulai', 'type' => 'info', 'event_date' => '2025-10-01']);

        // Ahmad
        TimelineEvent::create(['supervision_id' => $supAhmad->id, 'event' => 'BAB 5 dikirim, menunggu review', 'type' => 'pending', 'event_date' => '2026-04-27']);
        TimelineEvent::create(['supervision_id' => $supAhmad->id, 'event' => 'BAB 4 disetujui', 'type' => 'approved', 'event_date' => '2026-04-22']);
        TimelineEvent::create(['supervision_id' => $supAhmad->id, 'event' => 'Bimbingan dimulai', 'type' => 'info', 'event_date' => '2026-01-15']);
    }
}
