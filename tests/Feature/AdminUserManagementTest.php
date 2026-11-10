<?php

use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('administrator can create a lecturer account with its own course access', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');

    $this->actingAs($administrator)
        ->get(route('admin.users.create'))
        ->assertOk()
        ->assertSee('Lecturer')
        ->assertSee('Administrator');

    $this->actingAs($administrator)
        ->post(route('admin.users.store'), [
            'first_name' => 'Aisha',
            'last_name' => 'Nabirye',
            'email' => 'aisha.nabirye@example.com',
            'role' => 'lecturer',
            'staff_number' => 'STF-204',
            'department' => 'Computer Science',
            'phone' => '+256700000204',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('status', 'Lecturer account created for Aisha Nabirye.');

    $lecturer = User::where('email', 'aisha.nabirye@example.com')->firstOrFail();
    expect($lecturer->roles->pluck('name')->all())->toBe(['lecturer']);
    $this->assertDatabaseHas('lecturers', [
        'user_id' => $lecturer->id,
        'staff_number' => 'STF-204',
        'department' => 'Computer Science',
    ]);

    $this->actingAs($administrator)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee($lecturer->name)
        ->assertSee('STF-204');

    $course = Course::factory()->create(['lecturer_id' => $lecturer->lecturer->id]);
    $student = Student::factory()->create();
    $student->courses()->attach($course->id, [
        'status' => 'approved',
        'registered_at' => now(),
    ]);

    $this->post(route('logout'));
    $this->post(route('login.attempt'), [
        'email' => $lecturer->email,
        'password' => 'password123',
    ])->assertRedirect(route('lecturer.dashboard'));

    $this->get(route('lecturer.dashboard'))
        ->assertOk()
        ->assertSee($course->course_code);

    $this->get(route('lecturer.courses.students', $course))
        ->assertOk()
        ->assertSee($student->full_name);

    $this->get(route('admin.dashboard'))->assertForbidden();
    $this->get(route('student.dashboard'))->assertForbidden();
});

test('administrator can create an administrator account without a lecturer profile', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');

    $this->actingAs($administrator)
        ->post(route('admin.users.store'), [
            'first_name' => 'Grace',
            'last_name' => 'Achan',
            'email' => 'grace.achan@example.com',
            'role' => 'administrator',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('admin.users.index'));

    $newAdministrator = User::where('email', 'grace.achan@example.com')->firstOrFail();
    expect($newAdministrator->roles->pluck('name')->all())->toBe(['administrator'])
        ->and($newAdministrator->lecturer)->toBeNull();

    $this->post(route('logout'));
    $this->post(route('login.attempt'), [
        'email' => $newAdministrator->email,
        'password' => 'password123',
    ])->assertRedirect(route('admin.dashboard'));

    $this->get(route('admin.dashboard'))->assertOk();
    $this->get(route('lecturer.dashboard'))->assertForbidden();
});

test('students cannot access staff account management', function () {
    $student = User::factory()->create();
    $student->assignRole('student');

    $this->actingAs($student)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    $this->post(route('admin.users.store'), [
        'first_name' => 'New',
        'last_name' => 'Administrator',
        'email' => 'new.admin@example.com',
        'role' => 'administrator',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertForbidden();

    $this->assertDatabaseMissing('users', ['email' => 'new.admin@example.com']);
});

test('public registration always assigns the student role', function () {
    $this->post(route('register.store'), [
        'first_name' => 'New',
        'last_name' => 'Student',
        'email' => 'new.student@example.com',
        'gender' => 'female',
        'program' => 'Computer Science',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'administrator',
    ])->assertRedirect(route('student.dashboard'));

    $student = User::where('email', 'new.student@example.com')->firstOrFail();
    expect($student->roles->pluck('name')->all())->toBe(['student']);
});

test('lecturer accounts require a unique staff number', function () {
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');

    $this->actingAs($administrator)
        ->from(route('admin.users.create'))
        ->post(route('admin.users.store'), [
            'first_name' => 'Aisha',
            'last_name' => 'Nabirye',
            'email' => 'missing.number@example.com',
            'role' => 'lecturer',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertSessionHasErrors('staff_number')
        ->assertRedirect(route('admin.users.create'));

    $this->assertDatabaseMissing('users', ['email' => 'missing.number@example.com']);
});
