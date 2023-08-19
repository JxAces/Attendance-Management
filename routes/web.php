<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\ImportStudentController;
use App\Http\Controllers\EventsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

use App\Http\Controllers\AttendanceController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

    // Route::get('/', [HomeController::class, 'home']);
	Route::get('dashboard', function () {
		return view('dashboard');
	})->name('dashboard');

	Route::get('billing', function () {
		return view('billing');
	})->name('billing');

	Route::get('profile', function () {
		return view('profile');
	})->name('profile');

	Route::get('user-management', function () {
		return view('laravel-examples/user-management');
	})->name('user-management');

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');


    Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

    Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile', [InfoUserController::class, 'create']);
	Route::post('/user-profile', [InfoUserController::class, 'store']);
    Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	Route::get('/', [StudentController::class, 'showSearchPage'])->name('student.search');
	Route::get('/search', [StudentController::class, 'search'])->name('student.search.submit');	
	Route::get('/student/{id_no}', [StudentController::class, 'getStudentDetails']);
});



Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');

});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');

Route::get('/import-students', [ImportStudentController::class, 'index'])->name('import-students.index');
Route::post('import-students-file', [ImportStudentController::class, 'import'])->name('import.students.file');

Route::get('events', [EventsController::class, 'index'])->name('events.index');
Route::get('events/create', [EventsController::class, 'create'])->name('events.create');
Route::post('events', [EventsController::class, 'store'])->name('events.store');
Route::get('events/{event}', [EventsController::class, 'show'])->name('events.show');
Route::get('events/{event}/edit', [EventsController::class, 'edit'])->name('events.edit');
Route::put('events/{event}', [EventsController::class, 'update'])->name('events.update');
Route::delete('events/{event}', [EventsController::class, 'destroy'])->name('events.destroy');

Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
Route::get('/attendances/export', [AttendanceController::class, 'export'])->name('attendances.export');
Route::get('/attendances/export-data', [AttendanceController::class, 'exportData'])->name('attendances.export-data');
Route::post('/attendances/update', [AttendanceController::class, 'update'])->name('attendances.update');


Route::get('/search/students', [StudentController::class, 'search']);