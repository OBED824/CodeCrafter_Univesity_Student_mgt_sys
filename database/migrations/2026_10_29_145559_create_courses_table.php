<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Courses belong to one lecturer (lecturer_id), which is why this is
     * a one-to-many relationship: one lecturer teaches many courses,
     * but each course has a single responsible lecturer.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code')->unique(); // e.g. CSC101, unique per course
            $table->string('course_name');
            $table->text('description')->nullable();
            $table->foreignId('lecturer_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
