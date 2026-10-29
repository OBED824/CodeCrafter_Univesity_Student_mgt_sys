<?php

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
});

test('guests are redirected from dashboards to the login page', function () {
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/lecturer/dashboard')->assertRedirect('/login');
    $this->get('/student/dashboard')->assertRedirect('/login');
});

test('users can log in with valid credentials', function () {
    $user = User::factory()->create();
    $user->assignRole('student');

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('student.dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('users cannot log in with an invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('users can log out', function () {
    $user = User::factory()->create();
    $user->assignRole('student');

    $this->actingAs($user)->post('/logout')->assertRedirect('/');
    $this->assertGuest();
});

test('a new student can register and is logged in', function () {
    $this->post('/register', [
        'first_name' => 'New',
        'last_name' => 'Student',
        'email' => 'new.student@example.com',
        'gender' => 'female',
        'program' => 'BSc Computer Science',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('student.dashboard'));

    $user = User::where('email', 'new.student@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->hasRole('student'))->toBeTrue()
        ->and($user->student)->not->toBeNull()
        ->and($user->student->student_number)->toStartWith('SCS-214-');

    $this->assertAuthenticatedAs($user);
});

test('registration requires a unique email', function () {
    $existing = User::factory()->create();

    $this->post('/register', [
        'first_name' => 'Dup',
        'last_name' => 'Email',
        'email' => $existing->email,
        'gender' => 'male',
        'program' => 'BSc',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');
});

test('a student cannot access the administrator dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('student');

    $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
});

test('an administrator can access the administrator dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('administrator');

    $this->actingAs($user)->get('/admin/dashboard')->assertOk();
});
