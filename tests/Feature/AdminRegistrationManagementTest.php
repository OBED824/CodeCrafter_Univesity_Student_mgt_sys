<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('administrator can create a course registration', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $student = Student::factory()->create();
    $course = Course::factory()->create();

    $this->actingAs($administrator)
        ->get(route('admin.registrations.create'))
        ->assertOk()
        ->assertSee($student->full_name)
        ->assertSee($course->course_code);

    $this->post(route('admin.registrations.store'), [
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
    ])->assertRedirect(route('admin.registrations.index'))
        ->assertSessionHas('status', 'Course registration created.');

    $this->assertDatabaseHas('course_registrations', [
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
    ]);
});

test('administrator cannot create a duplicate course registration', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $student = Student::factory()->create();
    $course = Course::factory()->create();
    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);

    $this->actingAs($administrator)
        ->from(route('admin.registrations.create'))
        ->post(route('admin.registrations.store'), [
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'approved',
        ])
        ->assertSessionHasErrors('course_id')
        ->assertRedirect(route('admin.registrations.create'));

    $this->assertDatabaseCount('course_registrations', 1);
});

test('administrator can review, update, and delete course registrations', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $student = Student::factory()->create();
    $course = Course::factory()->create();
    $registration = CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'status' => 'pending',
        'registered_at' => now(),
    ]);

    $this->actingAs($administrator)
        ->get(route('admin.registrations.index'))
        ->assertOk()
        ->assertSee($student->full_name)
        ->assertSee($course->course_code)
        ->assertSee('pending');

    $this->patch(route('admin.registrations.update', $registration), [
        'status' => 'approved',
    ])->assertRedirect(route('admin.registrations.index'))
        ->assertSessionHas('status', 'Registration status updated.');

    $this->assertDatabaseHas('course_registrations', [
        'id' => $registration->id,
        'status' => 'approved',
    ]);

    $this->delete(route('admin.registrations.destroy', $registration))
        ->assertRedirect(route('admin.registrations.index'))
        ->assertSessionHas('status', 'Registration deleted.');

    $this->assertDatabaseMissing('course_registrations', ['id' => $registration->id]);
});

test('only administrators can manage registrations', function () {
    $student = User::factory()->create();
    $student->assignRole('student');
    $registration = CourseRegistration::create([
        'student_id' => Student::factory()->create()->id,
        'course_id' => Course::factory()->create()->id,
        'status' => 'pending',
        'registered_at' => now(),
    ]);

    $this->actingAs($student)
        ->get(route('admin.registrations.index'))
        ->assertForbidden();

    $this->patch(route('admin.registrations.update', $registration), [
        'status' => 'approved',
    ])->assertForbidden();

    $this->delete(route('admin.registrations.destroy', $registration))
        ->assertForbidden();

    $this->assertModelExists($registration);
});

test('registration status must be one of the supported states', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $registration = CourseRegistration::create([
        'student_id' => Student::factory()->create()->id,
        'course_id' => Course::factory()->create()->id,
        'status' => 'pending',
        'registered_at' => now(),
    ]);

    $this->actingAs($administrator)
        ->from(route('admin.registrations.index'))
        ->patch(route('admin.registrations.update', $registration), [
            'status' => 'cancelled',
        ])
        ->assertSessionHasErrors('status')
        ->assertRedirect(route('admin.registrations.index'));

    $this->assertDatabaseHas('course_registrations', [
        'id' => $registration->id,
        'status' => 'pending',
    ]);
});

test('lecturer can only see active registrations for assigned courses', function () {
    $lecturerUser = User::factory()->create();
    $lecturerUser->assignRole('lecturer');
    $lecturer = Lecturer::factory()->create(['user_id' => $lecturerUser->id]);
    $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
    $activeStudent = Student::factory()->create();
    $withdrawnStudent = Student::factory()->create();

    CourseRegistration::create([
        'student_id' => $activeStudent->id,
        'course_id' => $course->id,
        'status' => 'approved',
        'registered_at' => now(),
    ]);
    CourseRegistration::create([
        'student_id' => $withdrawnStudent->id,
        'course_id' => $course->id,
        'status' => 'withdrawn',
        'registered_at' => now(),
    ]);

    $this->actingAs($lecturerUser)
        ->get(route('lecturer.dashboard'))
        ->assertOk()
        ->assertSee('1 registered student(s)');

    $this->get(route('lecturer.courses.students', $course))
        ->assertOk()
        ->assertSee($activeStudent->full_name)
        ->assertDontSee($withdrawnStudent->full_name);
});

test('lecturers and students cannot manage student, course, or user accounts', function () {
    $lecturer = User::factory()->create();
    $lecturer->assignRole('lecturer');
    $student = User::factory()->create();
    $student->assignRole('student');

    $this->actingAs($lecturer)
        ->get(route('admin.students.index'))
        ->assertForbidden();

    $this->get(route('admin.courses.index'))->assertForbidden();
    $this->get(route('admin.users.index'))->assertForbidden();

    $this->actingAs($student)
        ->get(route('admin.students.index'))
        ->assertForbidden();

    $this->get(route('admin.courses.index'))->assertForbidden();
    $this->get(route('admin.users.index'))->assertForbidden();
});
