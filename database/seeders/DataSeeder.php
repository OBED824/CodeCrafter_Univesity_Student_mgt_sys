<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sample data for the coursework demo.
 *
 * Everything uses firstOrCreate on a natural unique key (student_number,
 * staff_number, course_code, email) so re-running this seeder is safe
 * and never creates duplicate records.
 *
 * Sample login credentials (documented in the README):
 *   admin@example.com    / password123  (administrator)
 *   lecturer@example.com / password123  (lecturer)
 *   student@example.com  / password123  (student)
 */
class DataSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users + roles -----------------------------------------------

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'System Administrator', 'password' => 'password123'],
        );
        $adminUser->syncRoles(['administrator']);

        $lecturerUser = User::firstOrCreate(
            ['email' => 'lecturer@example.com'],
            ['name' => 'Dr. Aisha Nabirye', 'password' => 'password123'],
        );
        $lecturerUser->syncRoles(['lecturer']);

        $studentUser = User::firstOrCreate(
            ['email' => 'student@example.com'],
            ['name' => 'Brian Okello', 'password' => 'password123'],
        );
        $studentUser->syncRoles(['student']);

        // --- Lecturers ------------------------------------------------------

        $lecturer = Lecturer::firstOrCreate(
            ['staff_number' => 'STF-001'],
            [
                'first_name' => 'Aisha',
                'last_name' => 'Nabirye',
                'email' => 'lecturer@example.com',
                'phone' => '+256700000001',
                'department' => 'Computer Science',
                'user_id' => $lecturerUser->id,
            ],
        );

        $secondLecturer = Lecturer::firstOrCreate(
            ['staff_number' => 'STF-002'],
            [
                'first_name' => 'Peter',
                'last_name' => 'Wasswa',
                'email' => 'peter.wasswa@example.com',
                'phone' => '+256700000002',
                'department' => 'Business Administration',
            ],
        );

        // --- Courses (assigned to lecturers via lecturer_id) ---------------

        $coursesData = [
            ['course_code' => 'CSC101', 'course_name' => 'Introduction to Programming', 'description' => 'Fundamentals of programming using PHP and Laravel.', 'lecturer_id' => $lecturer->id],
            ['course_code' => 'CSC201', 'course_name' => 'Database Systems', 'description' => 'Relational database design, SQL and Eloquent ORM.', 'lecturer_id' => $lecturer->id],
            ['course_code' => 'CSC301', 'course_name' => 'Web Application Development', 'description' => 'Building secure, responsive web applications with MVC architecture.', 'lecturer_id' => $lecturer->id],
            ['course_code' => 'BUS110', 'course_name' => 'Principles of Management', 'description' => 'Introduction to management theory and practice.', 'lecturer_id' => $secondLecturer->id],
        ];

        $courses = collect($coursesData)->map(function ($courseData) {
            return Course::firstOrCreate(['course_code' => $courseData['course_code']], $courseData);
        });

        // --- Students (at least 5, one linked to the sample login) ---------

        $studentsData = [
            ['student_number' => 'SCS-214-0001-2026', 'first_name' => 'Brian', 'last_name' => 'Okello', 'email' => 'student@example.com', 'phone' => '+256701111111', 'gender' => 'male', 'date_of_birth' => '2005-03-14', 'program' => 'Bachelor of Science in Computer Science', 'user_id' => $studentUser->id],
            ['student_number' => 'SCS-214-0002-2026', 'first_name' => 'Aisha', 'last_name' => 'Namutebi', 'email' => 'aisha.namutebi@example.com', 'phone' => '+256702222222', 'gender' => 'female', 'date_of_birth' => '2004-11-02', 'program' => 'Bachelor of Science in Computer Science'],
            ['student_number' => 'SCS-214-0003-2026', 'first_name' => 'David', 'last_name' => 'Ssebuggwala', 'email' => 'david.s@example.com', 'phone' => '+256703333333', 'gender' => 'male', 'date_of_birth' => '2005-06-21', 'program' => 'Bachelor of Science in Computer Science'],
            ['student_number' => 'SCB-220-0004-2026', 'first_name' => 'Grace', 'last_name' => 'Achan', 'email' => 'grace.achan@example.com', 'phone' => '+256704444444', 'gender' => 'female', 'date_of_birth' => '2004-01-30', 'program' => 'Bachelor of Business Administration'],
            ['student_number' => 'SCB-220-0005-2026', 'first_name' => 'Emmanuel', 'last_name' => 'Tumusiime', 'email' => 'emmanuel.t@example.com', 'phone' => '+256705555555', 'gender' => 'male', 'date_of_birth' => '2005-09-09', 'program' => 'Bachelor of Business Administration'],
        ];

        $students = collect($studentsData)->map(function ($studentData) {
            return Student::firstOrCreate(['student_number' => $studentData['student_number']], $studentData);
        });

        // --- Course registrations (students <-> courses) -------------------

        // Brian (the sample student login) is registered for CSC101 and CSC201.
        $registrations = [
            ['student' => $students[0], 'course' => $courses[0]],
            ['student' => $students[0], 'course' => $courses[1]],
            ['student' => $students[1], 'course' => $courses[0]],
            ['student' => $students[1], 'course' => $courses[2]],
            ['student' => $students[2], 'course' => $courses[1]],
            ['student' => $students[3], 'course' => $courses[3]],
            ['student' => $students[4], 'course' => $courses[3]],
        ];

        foreach ($registrations as $registration) {
            CourseRegistration::firstOrCreate([
                'student_id' => $registration['student']->id,
                'course_id' => $registration['course']->id,
            ], [
                'status' => 'approved',
                'registered_at' => now(),
            ]);
        }
    }
}
