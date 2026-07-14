<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervision_id')->constrained('thesis_supervisions')->cascadeOnDelete();
            $table->string('title');
            $table->string('chapter')->nullable();
            $table->enum('type', ['Bab', 'Revisi', 'Proposal'])->default('Bab');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_size')->nullable();
            $table->enum('status', ['pending', 'approved', 'revision', 'rejected'])->default('pending');
            $table->boolean('resolved')->default(false);
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
