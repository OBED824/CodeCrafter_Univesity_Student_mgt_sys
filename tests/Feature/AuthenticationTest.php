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
