<?php

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Mark;
use App\Models\Note;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('a student sees assignments for enrolled courses and only their own marks', function () {
    $studentUser = User::factory()->create();
    $studentUser->assignRole('student');
    $student = Student::factory()->for($studentUser, 'user')->create();
    $otherStudent = Student::factory()->create();
    $course = Course::factory()->create();
    $otherCourse = Course::factory()->create();

    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);
    CourseRegistration::create([
        'student_id' => $otherStudent->id,
        'course_id' => $otherCourse->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);

    Assignment::create([
        'course_id' => $course->id,
        'title' => '<script>alert(1)</script>',
        'description' => 'Read the assigned chapter.',
    ]);
    Assignment::create([
        'course_id' => $otherCourse->id,
        'title' => 'Another student assignment',
    ]);
    Note::create([
        'course_id' => $course->id,
        'title' => 'Course introduction',
        'content' => 'Welcome to the course.',
    ]);
    Note::create([
        'course_id' => $otherCourse->id,
        'title' => 'Private course notes',
        'content' => 'Notes for another course.',
    ]);
    Mark::create([
        'course_id' => $course->id,
        'student_id' => $student->id,
        'assessment_name' => 'Quiz 1',
        'score' => 8,
        'maximum_score' => 10,
    ]);
    Mark::create([
        'course_id' => $course->id,
        'student_id' => $otherStudent->id,
        'assessment_name' => 'Private assessment',
        'score' => 3,
        'maximum_score' => 10,
    ]);

    $this->actingAs($studentUser)
        ->get(route('student.assignments.index'))
        ->assertOk()
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertDontSee('Another student assignment');

    $this->get(route('student.marks.index'))
        ->assertOk()
        ->assertSee('Quiz 1')
        ->assertDontSee('Private assessment');

    $this->get(route('student.notes.index'))
        ->assertOk()
        ->assertSee('Course introduction')
        ->assertDontSee('Private course notes');
});

test('a student can record multiple attendance entries only for registered courses', function () {
    $studentUser = User::factory()->create();
    $studentUser->assignRole('student');
    $student = Student::factory()->for($studentUser, 'user')->create();
    $course = Course::factory()->create();
    $unregisteredCourse = Course::factory()->create();

    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);

    $this->actingAs($studentUser)
        ->post(route('student.attendance.store', $course))
        ->assertRedirect(route('student.attendance.index'))
        ->assertSessionHas('status', 'Attendance recorded for '.$course->course_code.'.');

    $this->post(route('student.attendance.store', $course))
        ->assertRedirect(route('student.attendance.index'));

    $this->post(route('student.attendance.store', $unregisteredCourse))
        ->assertNotFound();

    $this->assertDatabaseCount('attendances', 2);
    $this->assertDatabaseHas('attendances', [
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);
    $this->assertDatabaseMissing('attendances', [
        'student_id' => $student->id,
        'course_id' => $unregisteredCourse->id,
    ]);

    $this->get(route('student.attendance.index'))
        ->assertOk()
        ->assertSee($course->course_code)
        ->assertSee('Attendance history');
});

test('a lecturer can post assignments and record or update marks for registered students', function () {
    $lecturerUser = User::factory()->create();
    $lecturerUser->assignRole('lecturer');
    $lecturer = Lecturer::factory()->for($lecturerUser, 'user')->create();
    $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
    $student = Student::factory()->create();

    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);

    $this->actingAs($lecturerUser)
        ->post(route('lecturer.courses.assignments.store', $course), [
            'title' => 'Lab report',
            'description' => 'Submit the completed report.',
            'due_at' => '2026-12-01T10:00',
        ])
        ->assertRedirect(route('lecturer.courses.assignments', $course))
        ->assertSessionHas('status', 'Assignment posted successfully.');

    $this->assertDatabaseHas('assignments', [
        'course_id' => $course->id,
        'title' => 'Lab report',
    ]);

    $this->post(route('lecturer.courses.notes.store', $course), [
        'title' => 'Week one notes',
        'content' => 'Introduction to the course content.',
    ])
        ->assertRedirect(route('lecturer.courses.notes', $course))
        ->assertSessionHas('status', 'Course note posted successfully.');

    $this->assertDatabaseHas('notes', [
        'course_id' => $course->id,
        'title' => 'Week one notes',
        'content' => 'Introduction to the course content.',
    ]);

    $markInput = [
        'student_id' => $student->id,
        'assessment_name' => 'Midterm',
        'score' => 16,
        'maximum_score' => 20,
    ];

    $this->post(route('lecturer.courses.marks.store', $course), $markInput)
        ->assertRedirect(route('lecturer.courses.marks', $course))
        ->assertSessionHas('status', 'Mark saved successfully.');

    $markInput['score'] = 18;

    $this->post(route('lecturer.courses.marks.store', $course), $markInput)
        ->assertRedirect(route('lecturer.courses.marks', $course));

    $this->assertDatabaseCount('marks', 1);
    $this->assertDatabaseHas('marks', [
        'course_id' => $course->id,
        'student_id' => $student->id,
        'assessment_name' => 'Midterm',
        'score' => 18,
        'maximum_score' => 20,
    ]);
});

test('a lecturer cannot manage another lecturers course or mark an unregistered student', function () {
    $courseOwnerUser = User::factory()->create();
    $courseOwnerUser->assignRole('lecturer');
    $courseOwner = Lecturer::factory()->for($courseOwnerUser, 'user')->create();
    $otherLecturerUser = User::factory()->create();
    $otherLecturerUser->assignRole('lecturer');
    $course = Course::factory()->create(['lecturer_id' => $courseOwner->id]);
    $student = Student::factory()->create();

    $this->actingAs($otherLecturerUser)
        ->get(route('lecturer.courses.assignments', $course))
        ->assertNotFound();

    $this->actingAs($courseOwnerUser)
        ->post(route('lecturer.courses.marks.store', $course), [
            'student_id' => $student->id,
            'assessment_name' => 'Midterm',
            'score' => 12,
            'maximum_score' => 20,
        ])
        ->assertSessionHasErrors([
            'student_id' => 'Choose a student registered for this course.',
        ]);

    $this->assertDatabaseCount('marks', 0);
});

test('a lecturer cannot submit a score higher than the maximum mark', function () {
    $lecturerUser = User::factory()->create();
    $lecturerUser->assignRole('lecturer');
    $lecturer = Lecturer::factory()->for($lecturerUser, 'user')->create();
    $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
    $student = Student::factory()->create();

    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);

    $this->actingAs($lecturerUser)
        ->from(route('lecturer.courses.marks', $course))
        ->post(route('lecturer.courses.marks.store', $course), [
            'student_id' => $student->id,
            'assessment_name' => 'Midterm',
            'score' => 21,
            'maximum_score' => 20,
        ])
        ->assertSessionHasErrors('score');

    $this->assertDatabaseCount('marks', 0);
});

test('non-students cannot access student academic pages', function () {
    $lecturer = User::factory()->create();
    $lecturer->assignRole('lecturer');

    $this->actingAs($lecturer)
        ->get(route('student.marks.index'))
        ->assertForbidden();
});
