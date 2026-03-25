<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\RegisterController;
use App\Models\Attendance;

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

Route::get('/', function () {
  return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showUserLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'store']);

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::middleware(['auth'])->group(function () {

  Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.punch');

  Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');

  Route::post('attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');

  Route::post('/rest/start', [AttendanceController::class, 'restStart'])->name('rest.start');

  Route::post('/rest/end', [AttendanceController::class, 'restEnd'])->name('rest.end');

  Route::get('/attendance/list', [AttendanceController::class, 'attendanceList'])->name('attendance.list');

  Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.edit');

  Route::get('/stamp_correction_request/list', [AttendanceController::class, 'requestList'])->name('attendance.request_list');

  Route::patch('/attendance/update/{id}', [AttendanceController::class, 'update'])->name('attendance.update');


  Route::prefix('admin')->group(function () {
    Route::get('/attendance/list', [AdminController::class, 'index'])->name('admin.attendance.daily');

    Route::get('/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendance.detail');

    Route::get('/staff/list', [AdminController::class, 'staffList'])->name('admin.staff.list');

    Route::get('/attendance/staff/{id}', [AdminController::class, 'userAttendance'])->name('admin.attendance.staff');

    Route::get('/stamp_correction_request/list', [AdminController::class, 'approveList'])->name('admin.approve.list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminController::class, 'approveShow'])->name('admin.approve.request_detail');

    Route::patch('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminController::class, 'approveUpdate'])->name('admin.approve.update');

    Route::patch('/attendance/update/{id}', [AdminController::class, 'update'])->name('admin.attendance.update');

    Route::get('/staff/export/{id}', [AdminController::class, 'exportCsv'])->name('admin.staff.export');
  });
});
