<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ApproveController;
use App\Http\Controllers\AttendanceCorrectRequestController;
use App\Http\Controllers\RestController;
use Illuminate\Support\Facades\Route;

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
  if (auth()->check()) {
    return auth()->user()->role === 'admin'
      ? redirect()->route('admin.attendance.daily')
      : redirect()->route('attendance.punch');
  }
  return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
  Route::prefix('admin')->group(function () {
    Route::get('/attendance/list', [AdminController::class, 'index'])->name('admin.attendance.daily');

    Route::get('/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendance.detail');

    Route::get('/staff/list', [AdminController::class, 'staffList'])->name('admin.staff.list');

    Route::get('/attendance/staff/{id}', [AdminController::class, 'userAttendance'])->name('admin.attendance.staff');

    Route::get('/stamp_correction_request/list', [ApproveController::class, 'approveList'])->name('admin.approve.list');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request_id}', [ApproveController::class, 'approveShow'])->name('admin.approve.request_detail');

    Route::patch('/stamp_correction_request/approve/{attendance_correct_request_id}', [ApproveController::class, 'approveUpdate'])->name('admin.approve.update');

    Route::patch('/attendance/update/{id}', [AdminController::class, 'update'])->name('admin.attendance.update');

    Route::get('/staff/export/{id}', [AdminController::class, 'exportCsv'])->name('admin.staff.export');
  });
});

Route::middleware(['auth', 'verified'])->group(function () {

  Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.punch');

  Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');

  Route::post('attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');

  Route::post('/rest/start', [RestController::class, 'restStart'])->name('rest.start');

  Route::post('/rest/end', [RestController::class, 'restEnd'])->name('rest.end');

  Route::get('/attendance/list', [AttendanceController::class, 'attendanceList'])->name('attendance.list');

  Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.edit');

  Route::get('/stamp_correction_request/list', [AttendanceCorrectRequestController::class, 'requestList'])->name('attendance.request_list');

  Route::patch('/attendance/update/{id}', [AttendanceCorrectRequestController::class, 'update'])->name('attendance.update');
});
