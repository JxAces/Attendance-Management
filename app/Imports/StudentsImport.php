<?php

namespace App\Imports;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Debug: Log or print the $row array
        //Log::debug($row);
        // or
        //print_r($row);

        // Remove empty timestamp column from the row
        unset($row['created_at']);
        unset($row['updated_at']);

        // Check if the remaining row is empty
        //if (empty(array_filter($row))) {
          //  return null;
        //}


        $deptID = Auth::user()->id;
        $idNo = isset($row['id_no']) ? trim($row['id_no']) : null;

         // Return null if id_no is null
        if ($idNo === null) {
            return null;
        }

        $fullName = isset($row['fullname']) ? $row['fullname'] : null;
        $yearLevel = isset($row['year_level']) ? $row['year_level'] : null;
        $major = isset($row['major']) ? $row['major'] : null;
        $departmentProgram = isset($row['department_program']) ? $row['department_program'] : null;
        $gender = isset($row['gender']) ? $row['gender'] : null;
        $registrationDate = isset($row['registration_date']) ? $row['registration_date'] : null;
        if ($registrationDate) {
            $registrationDate = Carbon::instance(Date::excelToDateTimeObject($registrationDate))->format('Y-m-d');
        }
        $scholarshipStatus = isset($row['scholarship_status']) ? $row['scholarship_status'] : null;
        $address = isset($row['address']) ? $row['address'] : null;
        $gpa = isset($row['gpa']) ? $row['gpa'] : null;
        $totalUnits = isset($row['total_units']) ? $row['total_units'] : null;

        return new Student([
            'id_no' => $idNo,
            'dept_no' => $deptID,
            'full_name' => $fullName,
            'year_level' => $yearLevel,
            'major' => $major,
            'department_program' => $departmentProgram,
            'gender' => $gender,
            'registration_date' => $registrationDate,
            'scholarship_status' => $scholarshipStatus,
            'address' => $address,
            'gpa' => $gpa,
            'total_units' => $totalUnits,
        ]);
    }

    public function rules(): array
    {
        // Your validation rules here
    }

    public function import()
    {
        $import = new StudentsImport();
        $import->skip(6) // Skip the first row (header row)
              ->ignoreEmpty()
              ->import('your-excel-file.xlsx');
    }
}
