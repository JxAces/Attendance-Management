<?php

namespace App\Jobs;

use App\Models\ECMember;
use App\Models\Student;
use App\Models\Day;
use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAttendanceShiftJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param $day
     * @param $shiftField
     * @param $timeField
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ECMembers = ECMember::all();
        $students = Student::all();
        $days = Day::all();

        $timeFields = [
            "sign_in_morning",
            "sign_out_morning",
            "sign_in_afternoon",
            "sign_out_afternoon",
        ];

        $shiftFields = [
            "m_in",
            "m_out",
            "af_in",
            "af_out",
        ];
    
        foreach($days as $day)
        {
                foreach($timeFields as $timeField)
                {
                    if ($day->{$timeField} !== null) {
                        $attendances = Attendance::where('day_id', $day->id)->get();
                        foreach ($attendances as $attendance) {
                        foreach($shiftFields as $shiftField){
                        $student = $students->where('id', $attendance->student_id)->first();
                    if ($student) {
                        if ($ECMembers->where('id_no', $student->id_no)->isNotEmpty()) {
                            $attendance->{$shiftField} = 6;
                        } elseif ($attendance->{$shiftField}->value != 1 && $attendance->{$shiftField}->value != 2 && $attendance->{$shiftField}->value != 6) {
                            $attendance->{$shiftField} = 5;
                        }
                        $attendance->save();
                    }
                }
                 }
                    } else {
                        Attendance::where('day_id', $day->id)->update([$shiftField => 0]);
                    }
                }
        }
    }
}
