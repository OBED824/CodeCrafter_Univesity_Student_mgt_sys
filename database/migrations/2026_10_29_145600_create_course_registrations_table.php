<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This is the pivot table connecting students and courses
     * (many-to-many). It is a full model instead of a plain pivot
     * because it stores registration information (status, dates).
     * The composite unique index makes a duplicate registration
     * impossible at the database level.
     */
    public function up(): void
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'withdrawn'])->default('approved');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            // A student can never register for the same course twice.
            $table->unique(['student_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};
