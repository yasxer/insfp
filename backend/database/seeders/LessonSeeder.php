<?php

namespace Database\Seeders;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::all();

        if ($modules->isEmpty()) {
            $this->command->warn('No modules found. Please run ModuleSeeder first.');
            return;
        }

        // Get first teacher to assign lessons
        $teacher = \App\Models\Teacher::first();
        if (!$teacher) {
            $this->command->warn('No teacher found. Please run TeacherSeeder first.');
            return;
        }

        $lessons = [
            [
                'title' => 'Introduction to Programming Basics',
                'description' => 'Learn the fundamental concepts of programming including variables, data types, and basic operations.',
            ],
            [
                'title' => 'Control Structures and Loops',
                'description' => 'Understanding if-else statements, switch cases, for loops, while loops, and do-while loops.',
            ],
            [
                'title' => 'Functions and Methods',
                'description' => 'How to create reusable code blocks with functions, parameters, return values, and scope.',
            ],
            [
                'title' => 'Object-Oriented Programming Concepts',
                'description' => 'Classes, objects, inheritance, polymorphism, encapsulation, and abstraction explained.',
            ],
            [
                'title' => 'Data Structures: Arrays and Lists',
                'description' => 'Working with arrays, dynamic arrays, linked lists, and their practical applications.',
            ],
            [
                'title' => 'Database Design and SQL Basics',
                'description' => 'Introduction to relational databases, SQL queries, joins, and database normalization.',
            ],
        ];

        $additionalLessons = [
            [
                'title' => 'Web Development Fundamentals',
                'description' => 'HTML, CSS, JavaScript basics and how they work together to create web pages.',
            ],
            [
                'title' => 'Version Control with Git',
                'description' => 'Learn Git commands, branching, merging, and collaboration workflows on GitHub.',
            ],
            [
                'title' => 'Algorithm Complexity and Big O Notation',
                'description' => 'Understanding time and space complexity, analyzing algorithm efficiency.',
            ],
            [
                'title' => 'Design Patterns in Software Engineering',
                'description' => 'Common design patterns: Singleton, Factory, Observer, and more.',
            ],
        ];

        foreach ($modules as $module) {
            $lessonCount = rand(3, 5);
            $moduleLessons = array_slice(array_merge($lessons, $additionalLessons), 0, $lessonCount);

            foreach ($moduleLessons as $lessonData) {
                $fileName = str_replace(' ', '_', strtolower($lessonData['title'])) . '.pdf';
                $filePath = "lessons/{$module->code}/{$fileName}";

                Lesson::create([
                    'module_id' => $module->id,
                    'teacher_id' => $teacher->id,
                    'title' => $lessonData['title'],
                    'description' => $lessonData['description'],
                    'file_path' => $filePath,
                    'file_name' => $fileName,
                    'file_type' => 'application/pdf',
                    'file_size' => rand(500000, 5000000),
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }

        $this->command->info('Lessons seeded successfully!');
    }
}
