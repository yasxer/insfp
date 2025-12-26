<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        // Get first administration user
        $admin = \App\Models\Administration::first();
        if (!$admin) {
            $this->command->warn('No administration found. Please run AdministrationSeeder first.');
            return;
        }

        // General Documents (Available to all students)
        $generalDocuments = [
            [
                'title' => 'Student Handbook 2025-2026',
                'description' => 'Complete guide for students including rules, regulations, academic policies, and campus facilities information.',
                'file_name' => 'student_handbook_2025_2026.pdf',
                'category' => 'General',
            ],
            [
                'title' => 'Academic Calendar 2025-2026',
                'description' => 'Important dates: semester start/end dates, exam periods, holidays, registration deadlines, and academic events.',
                'file_name' => 'academic_calendar_2025_2026.pdf',
                'category' => 'General',
            ],
            [
                'title' => 'Exam Regulations and Guidelines',
                'description' => 'Official examination rules, procedures, grading system, retake policies, and student conduct during exams.',
                'file_name' => 'exam_regulations.pdf',
                'category' => 'Academic',
            ],
            [
                'title' => 'Library Access and Resources Guide',
                'description' => 'How to access the library, borrow books, use online resources, and research databases available to students.',
                'file_name' => 'library_guide.pdf',
                'category' => 'Resources',
            ],
            [
                'title' => 'Campus Safety and Emergency Procedures',
                'description' => 'Safety protocols, emergency contacts, evacuation procedures, and health services information.',
                'file_name' => 'campus_safety_guide.pdf',
                'category' => 'Safety',
            ],
            [
                'title' => 'Internship Program Guidelines',
                'description' => 'Internship requirements, application process, evaluation criteria, and partner companies list.',
                'file_name' => 'internship_guidelines.pdf',
                'category' => 'Career',
            ],
            [
                'title' => 'Scholarship and Financial Aid Information',
                'description' => 'Available scholarships, eligibility criteria, application procedures, and financial assistance options.',
                'file_name' => 'scholarship_info.pdf',
                'category' => 'Financial',
            ],
            [
                'title' => 'Code of Conduct and Ethics',
                'description' => 'Expected student behavior, academic integrity policies, disciplinary procedures, and appeal processes.',
                'file_name' => 'code_of_conduct.pdf',
                'category' => 'General',
            ],
            [
                'title' => 'Graduation Requirements Checklist',
                'description' => 'Complete list of requirements for graduation including credits, mandatory courses, and final project guidelines.',
                'file_name' => 'graduation_requirements.pdf',
                'category' => 'Academic',
            ],
            [
                'title' => 'Career Services and Job Placement Guide',
                'description' => 'Resume writing tips, interview preparation, job search strategies, and alumni network access.',
                'file_name' => 'career_services_guide.pdf',
                'category' => 'Career',
            ],
        ];

        foreach ($generalDocuments as $docData) {
            Document::create([
                'administration_id' => $admin->id,
                'title' => $docData['title'],
                'description' => $docData['description'],
                'category' => $docData['category'],
                'file_path' => 'documents/general/' . $docData['file_name'],
                'file_name' => $docData['file_name'],
                'is_public' => true,
                'created_at' => now()->subDays(rand(5, 60)),
            ]);
        }

        $this->command->info('Documents seeded successfully!');
    }
}
