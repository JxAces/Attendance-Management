<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ECOfficersImport;
use App\Jobs\ProcessECOfficersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\UpdateAttendanceShiftJob;

class ImportECOfficerController extends Controller
{

    public function index()
    {
        return view('admin.import-students');   
    }
    
    public function importECOfficers(Request $request)
    {
        
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,csv',
        ]);

        
        Excel::import(new ECOfficersImport, $request->file('excel_file'));

        dispatch(new UpdateAttendanceShiftJob());

        return redirect()->back()->with('success', 'EC Officers imported successfully!');
        return view('admin.import-students');
        
    }
}