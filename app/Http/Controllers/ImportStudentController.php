<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;


class ImportStudentController extends Controller
{

    public function index()
    {
        return view('admin.import-students');
    }
    
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('excel_file');

        Excel::import(new StudentsImport, $file);

        // Additional logic after import if needed

        return view('admin.import-students');
    }
}
