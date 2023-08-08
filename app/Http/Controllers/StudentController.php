<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student; // Assuming your model is named "Student"

class StudentController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Perform the database query to find matching students
        // For example, search by full_name column
        $results = Student::where('full_name', 'like', '%'.$query.'%')->get();

        // Return the search results as JSON response
        return response()->json($results);
    }
}
