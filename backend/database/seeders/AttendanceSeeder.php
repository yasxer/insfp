<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Schedule;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::where('current_semester', 1)->get();
        $schedules = Schedule::where('semester', 1)->with('teacher')->get();

        $this->command->info("Found {$students->count()} students");
        $this->command->info("Found {$schedules->count()} schedules");

        if ($students->isEmpty() || $schedules->isEmpty()) {
            $this->command->warn('No students or schedules found for semester 1');
            return;
        }

        $statuses = ['present', 'absent', 'late', 'excused'];

        // Generate attendance for last 15 days (simplified)
        $attendanceCount = 0;

        foreach ($students as $student) {
            // Get schedules for student's specialty
            $studentSchedules = $schedules->where('specialty_id', $student->specialty_id);

            if ($studentSchedules->isEmpty()) {
                continue;
            }

            // Generate 10-15 random attendance records per student
            $recordsToCreate = rand(10, 15);
            $createdDates = []; // Track created dates to avoid duplicates

            for ($i = 0; $i < $recordsToCreate; $i++) {
                $schedule = $studentSchedules->random();
                $daysAgo = rand(1, 30);
                $date = Carbon::now()->subDays($daysAgo)->format('Y-m-d');

                // Check if this combination already exists
                $key = "{$student->id}-{$schedule->id}-{$date}";
                if (in_array($key, $createdDates)) {
                    continue;
                }
                $createdDates[] = $key;

                // Check if record exists in database
                if (Attendance::where('student_id', $student->id)
                    ->where('schedule_id', $schedule->id)
                    ->where('attendance_date', $date)
                    ->exists()) {
                    continue;
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'schedule_id' => $schedule->id,
                    'teacher_id' => $schedule->teacher_id,
                    'attendance_date' => $date,
                    'status' => $statuses[array_rand($statuses)],
                    'notes' => null,
                ]);

                $attendanceCount++;
            }
        }

        $this->command->info("Created {$attendanceCount} attendance records");
    }
}
