<?php

namespace App\Imports;

use App\Models\ECMember;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Student;
use App\Models\Day;
use App\Models\Event;
use App\Models\Attendance;

class ECOfficersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        
        $idNo = isset($row['id_no']) ? trim($row['id_no']) : null;
        $fullName = isset($row['fullname']) ? $row['fullname'] : null;

        // Return null if either id_no or full_name is missing
        if ($idNo === null || $fullName === null) {
            return null;
        }

        // Check if an EC officer with the same id_no already exists
        $existingECOfficer = ECMember::where('id_no', $idNo)->first();

        if ($existingECOfficer) {
            // Update the existing EC officer's full_name
            $existingECOfficer->update(['full_name' => $fullName]);
        } else {
            // Create a new EC officer
            $ECMember = new ECMember([
                'id_no' => $idNo,
                'full_name' => $fullName,
            ]);

            $ECMember -> save();

        }

        $newECAttendances = Attendance::where('student_id', $existingECOfficer->id_no)->get();
        foreach($newECAttendances as $newECAttendance){
                $newECAttendance ->m_in = 6;
                $newECAttendance ->m_out = 6;
                $newECAttendance ->af_in = 6;
                $newECAttendance ->af_out = 6;
                $newECAttendance->save();
        }

        return;
        
    
    }
}
