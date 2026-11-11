<?php

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Mark;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Spatie\Permission\Models\Permission;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('administrator can update a student status and track a transfer in the lifecycle history', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $student = Student::factory()->create();

    $this->actingAs($administrator)
        ->put(route('admin.students.update', $student), [
            'student_number' => $student->student_number,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'gender' => $student->gender,
            'program' => 'Bachelor of Information Systems',
            'status' => 'transferred',
            'lifecycle_notes' => 'Approved inter-program transfer.',
        ])
        ->assertRedirect(route('admin.students.show', $student))
        ->assertSessionHas('status', 'Student updated successfully.');

    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'program' => 'Bachelor of Information Systems',
        'status' => 'transferred',
    ]);
    $this->assertDatabaseHas('student_lifecycle_events', [
        'student_id' => $student->id,
        'event_type' => 'transferred',
        'previous_program' => $student->program,
        'new_program' => 'Bachelor of Information Systems',
        'new_status' => 'transferred',
        'notes' => 'Approved inter-program transfer.',
    ]);

    $this->get(route('admin.students.show', $student))
        ->assertOk()
        ->assertSee('Student lifecycle history')
        ->assertSee('Approved inter-program transfer.');
});

test('administrator can review and correct central attendance and marks', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $student = Student::factory()->create();
    $course = Course::factory()->create();
    $attendance = Attendance::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'recorded_at' => '2026-11-10 09:00:00',
    ]);
    $mark = Mark::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'assessment_name' => 'Midterm',
        'score' => 12,
        'maximum_score' => 20,
    ]);

    $this->actingAs($administrator)
        ->get(route('admin.attendance.index', ['course_id' => $course->id, 'student_id' => $student->id, 'date' => '2026-11-10']))
        ->assertOk()
        ->assertSee($student->student_number)
        ->assertSee($course->course_code);

    $this->patch(route('admin.attendance.update', $attendance), [
        'recorded_at' => '2026-11-10 10:30:00',
    ])->assertRedirect();

    $this->assertDatabaseHas('attendances', [
        'id' => $attendance->id,
        'recorded_at' => '2026-11-10 10:30:00',
    ]);

    $this->get(route('admin.marks.index', ['course_id' => $course->id, 'student_id' => $student->id]))
        ->assertOk()
        ->assertSee('Midterm')
        ->assertSee('12.00');

    $this->patch(route('admin.marks.update', $mark), [
        'assessment_name' => 'Midterm',
        'score' => 17,
        'maximum_score' => 20,
    ])->assertRedirect();

    $this->assertDatabaseHas('marks', [
        'id' => $mark->id,
        'score' => 17,
        'maximum_score' => 20,
    ]);
});

test('administrator can assign a student account and change role permissions', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $student = Student::factory()->create();

    $this->actingAs($administrator)
        ->post(route('admin.users.store'), [
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'role' => 'student',
            'student_id' => $student->id,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('admin.users.index'));

    $studentUser = User::where('email', $student->email)->firstOrFail();
    expect($studentUser->hasRole('student'))->toBeTrue();
    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'user_id' => $studentUser->id,
    ]);

    $registerPermission = Permission::findByName('register courses', 'web');

    $this->get(route('admin.roles.index'))
        ->assertOk()
        ->assertSee('Role Permissions')
        ->assertSee('Manage grades');

    $this->put(route('admin.roles.update', 'student'), [
        'permissions' => [$registerPermission->id],
    ])
        ->assertRedirect(route('admin.roles.index'))
        ->assertSessionHas('status', 'Student permissions updated.');

    $this->actingAs($studentUser)
        ->get(route('student.marks.index'))
        ->assertForbidden();
});

test('administrator can change an account role and provision its lecturer profile', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    $account = User::factory()->create();
    $account->assignRole('student');
    $student = Student::factory()->for($account, 'user')->create();

    $this->actingAs($administrator)
        ->get(route('admin.users.edit', $account))
        ->assertOk()
        ->assertSee('Edit User Access');

    $this->patch(route('admin.users.update', $account), [
        'first_name' => 'Taylor',
        'last_name' => 'Lecturer',
        'email' => 'taylor.lecturer@example.com',
        'role' => 'lecturer',
        'staff_number' => 'STF-450',
        'department' => 'Science',
    ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'User account updated.');

    expect($account->fresh()->hasRole('lecturer'))->toBeTrue();
    $this->assertDatabaseHas('lecturers', [
        'user_id' => $account->id,
        'staff_number' => 'STF-450',
        'email' => 'taylor.lecturer@example.com',
    ]);
    $this->assertDatabaseHas('students', [
        'id' => $student->id,
        'user_id' => null,
    ]);
});

test('administrator can generate filtered academic reports and download a csv', function () {
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
    Attendance::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'recorded_at' => now(),
    ]);
    Mark::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
        'assessment_name' => 'Quiz',
        'score' => 8,
        'maximum_score' => 10,
    ]);

    $query = [
        'start_date' => today()->toDateString(),
        'end_date' => today()->toDateString(),
        'course_id' => $course->id,
    ];

    $this->actingAs($administrator)
        ->get(route('admin.reports.index', $query))
        ->assertOk()
        ->assertSee('Institutional Reports')
        ->assertSee($course->course_code)
        ->assertSee('80.00%');

    $this->get(route('admin.reports.export', $query))
        ->assertDownload('institutional-report.csv');
});

test('administrator cannot demote their own account', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');

    $this->actingAs($administrator)
        ->patch(route('admin.users.update', $administrator), [
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => $administrator->email,
            'role' => 'student',
            'student_id' => Student::factory()->create()->id,
        ])
        ->assertSessionHasErrors([
            'role' => 'You cannot remove your own administrator access.',
        ]);

    expect($administrator->fresh()->hasRole('administrator'))->toBeTrue();
});

test('graduated students cannot enroll in courses or record attendance', function () {
    $studentUser = User::factory()->create();
    $studentUser->assignRole('student');
    $student = Student::factory()->for($studentUser, 'user')->create([
        'status' => 'graduated',
    ]);
    $course = Course::factory()->create();

    $this->actingAs($studentUser)
        ->from(route('student.courses'))
        ->post(route('student.registrations.store', $course))
        ->assertRedirect(route('student.courses'))
        ->assertSessionHas('error', 'Only active students can register for courses.');

    $this->post(route('student.attendance.store', $course))
        ->assertForbidden();

    $this->assertDatabaseCount('course_registrations', 0);
    $this->assertDatabaseCount('attendances', 0);
});
