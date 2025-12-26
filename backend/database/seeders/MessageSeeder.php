<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'administration')->first();
        $teacherUser = User::where('role', 'teacher')->first();
        $specialties = Specialty::all();
        $students = User::where('role', 'student')->limit(5)->get();

        // Broadcast Messages (All Students)
        Message::create([
            'sender_id' => $adminUser->id,
            'recipient_type' => 'all',
            'recipient_id' => null,
            'subject' => 'Important: End of Semester Exams Schedule',
            'body' => "Dear Students,\n\nThe end of semester exams will take place from January 15 to January 30, 2025.\n\nPlease check your individual schedules on the platform and prepare accordingly.\n\nGood luck!\n\nAdministration",
        ]);

        Message::create([
            'sender_id' => $adminUser->id,
            'recipient_type' => 'all',
            'recipient_id' => null,
            'subject' => 'Holiday Notice: Winter Break',
            'body' => "Dear All,\n\nPlease note that the institute will be closed for winter break from December 27, 2025 to January 5, 2026.\n\nClasses will resume on January 6, 2026.\n\nHappy holidays!\n\nINSFP Administration",
        ]);

        Message::create([
            'sender_id' => $adminUser->id,
            'recipient_type' => 'all',
            'recipient_id' => null,
            'subject' => 'COVID-19 Safety Protocols Update',
            'body' => "Dear Students and Staff,\n\nWe remind everyone to follow the safety protocols:\n- Wear masks in common areas\n- Maintain social distancing\n- Sanitize hands regularly\n\nYour safety is our priority.\n\nStay safe!",
        ]);

        // Individual Messages to Students
        foreach ($students as $index => $student) {
            Message::create([
                'sender_id' => $adminUser->id,
                'recipient_type' => 'individual',
                'recipient_id' => $student->id,
                'subject' => 'Personal: Document Request',
                'body' => "Dear {$student->name},\n\nWe have received your request for a school enrollment certificate.\n\nYour document is ready and can be collected from the administration office during working hours (09:00 - 16:00).\n\nBest regards,\nINSFP Administration",
            ]);

            if ($index === 0) {
                Message::create([
                    'sender_id' => $teacherUser->id,
                    'recipient_type' => 'individual',
                    'recipient_id' => $student->id,
                    'subject' => 'Excellent Work on Last Assignment!',
                    'body' => "Dear {$student->name},\n\nI wanted to personally congratulate you on your excellent performance on the last assignment.\n\nYour approach was innovative and your code was well-structured. Keep up the great work!\n\nIf you're interested, I'd like to discuss some advanced topics with you during office hours.\n\nBest regards,\nYour Teacher",
                ]);
            }

            if ($index === 1) {
                Message::create([
                    'sender_id' => $adminUser->id,
                    'recipient_type' => 'individual',
                    'recipient_id' => $student->id,
                    'subject' => 'Reminder: Tuition Payment Due',
                    'body' => "Dear {$student->name},\n\nThis is a friendly reminder that your tuition payment for the current semester is due by January 15, 2026.\n\nPlease contact the finance office to complete your payment.\n\nThank you for your cooperation.",
                ]);
            }
        }

        $this->command->info('Messages seeded successfully!');
    }
}
