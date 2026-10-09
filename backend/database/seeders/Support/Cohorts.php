<?php

namespace Database\Seeders\Support;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/** Database lookups shared by the demo seeders once cohorts exist. */
class Cohorts
{
    /** Approved students of a cohort (see DemoData::COHORTS), ordered as created. */
    public static function students(string $key): Collection
    {
        $c = DemoData::COHORTS[$key];
        $sessionId = DB::table('sessions')->where('month', $c['session'][0])->where('year', $c['session'][1])->value('id');
        $specialtyId = DB::table('specialties')->where('code', $c['specialty'])->value('id');

        return DB::table('students')
            ->join('session_specialties', 'session_specialties.id', '=', 'students.session_specialty_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->where('session_specialties.session_id', $sessionId)
            ->where('session_specialties.specialty_id', $specialtyId)
            ->where('session_specialties.study_type', $c['type'])
            ->where('users.is_approved', true)
            ->orderBy('students.id')
            ->select('students.*')
            ->get();
    }

    /** Modules of a specialty semester, keyed by code suffix, with teacher id. */
    public static function modules(string $code, int $semester): Collection
    {
        $catalog = collect(DemoData::specialties()[$code]['modules'][$semester])->keyBy(0);
        $teachers = DB::table('teachers')->get()->keyBy(fn ($t) => DemoData::slug($t->last_name));

        return DB::table('modules')->where('code', 'like', "$code-S$semester-%")->orderBy('id')->get()
            ->keyBy(fn ($m) => substr($m->code, strrpos($m->code, '-') + 1))
            ->map(function ($m, $suffix) use ($catalog, $teachers) {
                $m->teacher_id = $teachers[$catalog[$suffix][4]]->id;
                $m->room_type = $catalog[$suffix][5];
                return $m;
            });
    }

    /** First day of the cohort's semester $k: semesters follow the Février / Septembre periods. */
    public static function semesterStart(string $key, int $k): Carbon
    {
        [$month, $year] = DemoData::COHORTS[$key]['session'];
        for ($i = 1; $i < $k; $i++) {
            [$month, $year] = $month === 9 ? [2, $year + 1] : [9, $year];
        }
        return Carbon::create($year, $month, 1);
    }

    /** Move a date to the next teaching day (Sunday–Thursday). */
    public static function teachingDay(Carbon $date): Carbon
    {
        while (in_array($date->dayOfWeek, [Carbon::FRIDAY, Carbon::SATURDAY], true)) {
            $date->addDay();
        }
        return $date;
    }
}
