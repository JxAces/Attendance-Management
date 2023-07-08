<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;


class StudentsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Student([
            "id_no" => $row['ID No.'],
            "dept_no" => Auth::user()->id,
            "full_name" => $row['Fullname'],
            "year_level" => $row['Year Level'],
            "major" => $row['Major'],
            "department_program" => $row['Department Program'],
            "gender" => $row['Gender	Gender'],
            "registration_date" => $row['Registration Date'],
            "scholarship_status" => $row['Scholarship Status'],
            "address" => $row['Address'],
            "gpa" => $row['GPA	GPA'],
            "total_units" => $row['Total Units']

        ]);
    }
}
