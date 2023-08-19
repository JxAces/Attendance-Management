<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student; 

class StudentController extends Controller
{
    // public function search(Request $request)
    // {
    //     $query = $request->input('query');

    //     // Perform the database query to find matching students
    //     // For example, search by full_name column
    //     $results = Student::where('full_name', 'like', '%'.$query.'%')
    //                   ->orWhere('id_no', 'like', '%'.$query.'%')
    //                   ->get();

    //     // Return the search results as JSON response
    //     return response()->json($results);
    // }

    public function showSearchPage()
    {
        return view('students.search');
    }

    public function search(Request $request)
    {
        try {
            $searchTerm = $request->input('q');
            $students = Student::where('id_no', 'like', '%' . $searchTerm . '%')->get();
            return response()->json($students);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'An error occurred while processing your request.'], 500);
        }
    }
    
    // Add a new method in StudentController.php
    public function getStudentDetails($id_no)
    {
        try {
            $student = Student::where('id_no', $id_no)->firstOrFail();
            return response()->json($student);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['error' => 'Student details not found.'], 404);
        }
    }
}
