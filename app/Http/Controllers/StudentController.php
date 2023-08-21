<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student; 
use App\Models\Day; 
use App\Models\Event; 
use App\Models\Attendance; 
use DateTime;

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
        $days = Day::all();
        $events = Event::all();
        return view('students.search', compact('days','events'));
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

    public function saveStudent(Request $request)
    {
        // Validate the input
        $request->validate([
            'studentName' => 'required|string|max:255',
        ]);

        // Save the student
        $student = new Student();
        $student->full_name = 'New Student';
        $student->year_level = '1';
        $student->major = 'N/A';
        $student->department_program = 'N/A';
        $student->gender = 'N/A';
        $student->registration_date = '2023-08-22';
        $student->scholarship_status = 'N/A';
        $student->address = 'N/A';
        $student->gpa = 0;
        $student->total_units = 0;
        $student->id_no = $request->input('studentName');
        $student->save();

        $events = Event::get();
        if($events != null){
            foreach($events as $event){
                $days = Day::where('event_id', $event->id)->get();
                if($days != null){
                    // Create attendance records for each student and day combination
                    foreach ($days as $day) {
                        Attendance::create([
                            'day_id' => $day->id,
                            'student_id' => $student->id,
                            'm_in' => false,
                            'm_out' => false,
                            'af_in' => false,
                            'af_out' => false,
                        ]);
                    }
                }
            }
        }

        $requestData = $request->all();

        $event = Event::where('name', $requestData['event_name_new'])->first();
        $student = Student::where('id_no', $requestData['studentName'])->first();
        $dayNumber = intval($requestData['day_number_new']);
        $day = Day::where('event_id', $event->id)
                    ->where('day_number', $dayNumber)
                    ->first();
       
        $attendance = Attendance::where('day_id', $day->id)->where('student_id', $student->id)->first();

        $signInMorning = new DateTime($day->sign_in_morning);
        $endsignInMorning = (clone $signInMorning)->modify('+1 hour');
        $signOutMorning = new DateTime($day->sign_out_morning);
        $endsignOutMorning = (clone $signOutMorning)->modify('+1 hour');
        $signInAfternoon = new DateTime($day->sign_in_afternoon);
        $endsignInAfternoon = (clone $signInAfternoon)->modify('+1 hour');
        $signOutAfternoon = new DateTime($day->sign_out_afternoon);
        $endsignOutAfternoon = (clone $signOutAfternoon)->modify('+1 hour');
        $signTime = new DateTime($requestData['sign_time_new']);
        
        $message = "Already Signed In: " . $student->id_no;

        if ($signTime > $signInMorning && $signTime < $endsignInMorning) {
            if($attendance->m_in->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->m_in = 1;
            }
        } else if ($signTime > $signOutMorning && $signTime < $endsignOutMorning) {
            if($attendance->m_out->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->m_out = 1;
            }
        } else if ($signTime > $signInAfternoon && $signTime < $endsignInAfternoon) {
            if($attendance->af_in->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->af_in = 1;
            }
        } else if ($signTime > $signOutAfternoon && $signTime < $endsignOutAfternoon) {
            if($attendance->af_out->value === 1){
                return redirect()->route('student.search')->with('warning', $message);  
            } else {
                $attendance->af_out = 1;
            }
        } else {
            $message = "No Sign In/Out Scheduled";
            return redirect()->route('student.search')->with('error', $message);  
        }

        $attendance->save();

        $message = "ID No: " . $student->id_no;

        return redirect()->route('student.search')->with('success', $message);        
    }
}
