<?php

use App\Models\Contact;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

test('landing page displays live system counts and working auth links', function () {
    Student::factory()->count(2)->create();
    Course::factory()->count(3)->create();
    Lecturer::factory()->create(['department' => 'Computer Science']);
    Lecturer::factory()->create(['department' => 'Computer Science']);
    Lecturer::factory()->create(['department' => null]);

    $this->get(route('index'))
        ->assertOk()
        ->assertSee('data-stat="students" data-value="2">2</p>', false)
        ->assertSee('data-stat="courses" data-value="3">3</p>', false)
        ->assertSee('data-stat="departments" data-value="1">1</p>', false)
        ->assertSee('data-stat="lecturers" data-value="3">3</p>', false)
        ->assertSee('href="'.route('register').'"', false)
        ->assertSee('href="'.route('login').'"', false);
});

test('contact form stores validated messages and confirms submission', function () {
    $message = [
        'full_name' => 'Alex Student',
        'campus_email' => 'alex@example.com',
        'topic' => 'Registration',
        'message' => 'I need help with course registration.',
    ];

    $this->from(route('contact'))
        ->post(route('contact.store'), $message)
        ->assertRedirect(route('contact'))
        ->assertSessionHas('status', 'Your message has been sent to the Registry.');

    $this->assertDatabaseHas('contacts', $message);
});

test('contact inbox is not available to guests or students', function () {
    $this->get(route('contactlist'))->assertRedirect(route('login'));

    $this->seed(RolePermissionSeeder::class);
    $student = User::factory()->create();
    $student->assignRole('student');

    $this->actingAs($student)->get(route('contactlist'))->assertForbidden();
});

test('administrator can access the contact inbox', function () {
    $this->seed(RolePermissionSeeder::class);
    $administrator = User::factory()->create();
    $administrator->assignRole('administrator');
    Contact::create([
        'full_name' => 'Alex Student',
        'campus_email' => 'alex@example.com',
        'topic' => 'Registration',
        'message' => 'I need help with course registration.',
    ]);

    $this->actingAs($administrator)
        ->get(route('contactlist'))
        ->assertOk()
        ->assertSee('alex@example.com');
});
