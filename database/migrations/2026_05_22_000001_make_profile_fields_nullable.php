<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nim')->nullable()->change();
            $table->string('department')->nullable()->change();
            $table->string('faculty')->nullable()->change();
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('nidn')->nullable()->change();
            $table->string('department')->nullable()->change();
            $table->string('faculty')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('nim')->nullable(false)->change();
            $table->string('department')->nullable(false)->change();
            $table->string('faculty')->nullable(false)->change();
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('nidn')->nullable(false)->change();
            $table->string('department')->nullable(false)->change();
            $table->string('faculty')->nullable(false)->change();
        });
    }
};
