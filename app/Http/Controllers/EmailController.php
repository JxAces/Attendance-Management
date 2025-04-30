<?php

namespace App\Http\Controllers;

use App\Mail\StudentEmail;
use App\Models\Student;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function sendStudentEmails()
    {
        $students = Student::all(); // Fetch all students
        $mailers = ['gmail1', 'gmail2']; // List of Gmail accounts
        $mailerIndex = 0;

        $batchSize = 500; // Gmail's daily limit per account
        $batches = $students->chunk($batchSize); // Split students into chunks

        foreach ($batches as $batch) {
            $currentMailer = $mailers[$mailerIndex];

            foreach ($batch as $student) {
                try {
                    $studentEmail = new StudentEmail($student);
                    $emailAddress = $studentEmail->generateEmail();

                    // Log student details
                    Log::info("Processing student:", [
                        'id_no' => $student->id_no,
                        'full_name' => $student->full_name,
                        'email' => $emailAddress,
                        'mailer' => $currentMailer,
                    ]);

                    // Send email
                    Mail::mailer($currentMailer)
                        ->to($emailAddress)
                        ->send($studentEmail);

                    // Log success
                    Log::info("Email successfully sent to {$emailAddress}.");
                } catch (\Exception $e) {
                    // Log error for failed email
                    Log::error('Email failed for ' . $student->full_name . ': ' . $e->getMessage());
                }
            }

            // Rotate mailer
            $mailerIndex = ($mailerIndex + 1) % count($mailers);
        }

        return response()->json(['message' => 'Student emails have been sent successfully!']);
    }

    public function sendSingleEmail (Request $request){
        $studentIds = $request->input('student_ids'); // e.g., "1,2,3"
    
        // Split the input into an array of IDs
        $studentIdsArray = explode(',', $studentIds);
    
        // Fetch specific students based on the given IDs
        $students = Student::whereIn('id_no', $studentIdsArray)->get();

        Log::info('Received student IDs: ', ['ids' => $studentIdsArray]);
    
        if ($students->isEmpty()) {
            return response()->json(['message' => 'No students found with the provided IDs.'], 404);
        }
    
        // Initialize mailers
        $mailers = ['gmail1', 'gmail2']; // List of Gmail accounts
        $mailerIndex = 0;
        $batchSize = 500; // Gmail's daily limit per account
        $batches = $students->chunk($batchSize); // Split students into chunks
    
        foreach ($batches as $batch) {
            $currentMailer = $mailers[$mailerIndex];
    
            foreach ($batch as $student) {
                try {
                    // Prepare the email for the student
                    $studentEmail = new StudentEmail($student);
                    $emailAddress = $studentEmail->generateEmail();
    
                    // Log student details
                    Log::info("Processing student:", [
                        'id_no' => $student->id_no,
                        'full_name' => $student->full_name,
                        'email' => $emailAddress,
                        'mailer' => $currentMailer,
                    ]);
    
                    // Send email
                    Mail::mailer($currentMailer)
                        ->to($emailAddress)
                        ->send($studentEmail);
    
                    // Log success
                    Log::info("Email successfully sent to {$emailAddress}.");
                } catch (\Exception $e) {
                    // Log error for failed email
                    Log::error('Email failed for ' . $student->full_name . ': ' . $e->getMessage());
                }
            }
    
            // Rotate mailer
            $mailerIndex = ($mailerIndex + 1) % count($mailers);
        }
    
        return redirect()->route('student.search'); 
    }
}
