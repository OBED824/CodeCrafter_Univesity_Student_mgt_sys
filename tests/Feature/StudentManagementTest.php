<?php

use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

function adminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('administrator');

    return $user;
}

function studentUserWithProfile(): array
{
    $user = User::factory()->create();
    $user->assignRole('student');
    $student = Student::factory()->create(['user_id' => $user->id]);

    return [$user, $student];
}

test('administrator can create a student', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.students.store'), [
            'student_number' => 'SCS-999-0001-2026',
            'first_name' => 'Test',
            'last_name' => 'Student',
            'email' => 'test.student@example.com',
            'gender' => 'male',
            'program' => 'BSc Computer Science',
        ])
        ->assertRedirect(route('admin.students.index'))
        ->assertSessionHas('status', 'Student created successfully.');

    $this->assertDatabaseHas('students', ['student_number' => 'SCS-999-0001-2026']);
});

test('student number must be unique', function () {
    $existing = Student::factory()->create();

    $this->actingAs(adminUser())
        ->from(route('admin.students.create'))
        ->post(route('admin.students.store'), [
            'student_number' => $existing->student_number,
            'first_name' => 'Dup',
            'last_name' => 'Licate',
            'email' => 'another@example.com',
            'gender' => 'female',
            'program' => 'BSc',
        ])
        ->assertSessionHasErrors('student_number')
        ->assertRedirect(route('admin.students.create'));
});

test('administrator can update and delete a student', function () {
    $student = Student::factory()->create();

    $this->actingAs(adminUser())
        ->put(route('admin.students.update', $student), [
            'student_number' => $student->student_number,
            'first_name' => 'Renamed',
            'last_name' => $student->last_name,
            'email' => $student->email,
            'gender' => $student->gender,
            'program' => $student->program,
        ])
        ->assertRedirect(route('admin.students.show', $student));
    $this->assertDatabaseHas('students', ['id' => $student->id, 'first_name' => 'Renamed']);

    $this->actingAs(adminUser())
        ->delete(route('admin.students.destroy', $student))
        ->assertRedirect(route('admin.students.index'));
    $this->assertDatabaseMissing('students', ['id' => $student->id]);
});

test('student cannot access administrator student management', function () {
    [$user] = studentUserWithProfile();

    $this->actingAs($user)->get(route('admin.students.index'))->assertForbidden();
});

test('administrator can create a course with unique code', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.courses.store'), [
            'course_code' => 'TEST101',
            'course_name' => 'Test Course',
        ])
        ->assertRedirect(route('admin.courses.index'))
        ->assertSessionHas('status', 'Course created successfully.');

    $this->assertDatabaseHas('courses', ['course_code' => 'TEST101']);

    // Duplicate code rejected.
    $this->actingAs(adminUser())
        ->from(route('admin.courses.create'))
        ->post(route('admin.courses.store'), [
            'course_code' => 'TEST101',
            'course_name' => 'Another',
        ])
        ->assertSessionHasErrors('course_code');
});

test('student can register for a course', function () {
    [$user, $student] = studentUserWithProfile();
    $course = Course::factory()->create();

    $this->actingAs($user)
        ->post(route('student.registrations.store', $course))
        ->assertRedirect(route('student.registrations.index'))
        ->assertSessionHas('status', 'Course registered successfully.');

    expect($student->fresh()->courses->pluck('id'))->toContain($course->id);
});

test('student cannot register for the same course twice', function () {
    [$user, $student] = studentUserWithProfile();
    $course = Course::factory()->create();
    $student->courses()->attach($course->id, ['status' => 'approved', 'registered_at' => now()]);

    $this->actingAs($user)
        ->from(route('student.courses'))
        ->post(route('student.registrations.store', $course))
        ->assertRedirect(route('student.courses'))
        ->assertSessionHas('error', 'You are already registered for this course.');

    expect($student->fresh()->courses)->toHaveCount(1);
});

test('a student never sees another student through registration routes', function () {
    [$user, $student] = studentUserWithProfile();
    $other = Student::factory()->create();

    // The student dashboard only ever loads the logged-in user's own record.
    $response = $this->actingAs($user)->get(route('student.dashboard'));
    $response->assertOk()->assertSee($student->student_number)->assertDontSee($other->student_number);
});

test('lecturer cannot open a course that is not assigned to them', function () {
    $lecturerUser = User::factory()->create();
    $lecturerUser->assignRole('lecturer');
    Lecturer::factory()->create(['user_id' => $lecturerUser->id]);

    $foreignCourse = Course::factory()->create(); // belongs to no lecturer / another lecturer

    $this->actingAs($lecturerUser)
        ->get(route('lecturer.courses.students', $foreignCourse))
        ->assertNotFound();
});
