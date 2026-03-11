<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;

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
  return view('welcome');
});

Route::get('/login', [LoginController::class, 'showUserLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

Route::get('/admin/login', [LoginController::class, 'showAdminLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'store']);


Route::middleware(['auth'])->group(function () {

  Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.punch');
  Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
  Route::post('attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
  Route::post('/rest/start', [AttendanceController::class, 'restStart'])->name('rest.start');
  Route::post('/rest/end', [AttendanceController::class, 'restEnd'])->name('rest.end');
});

Route::get('/attendance/list', function () {
  return view('attendance.list');
})->name('attendance.list');

Route::get('/attendance/detail/{id}', function ($id) {
  return view('attendance.edit');
})->name('attendance.detail');

Route::get('/stamp_correction_request/list', function () {
  return view('attendance.request_list');
})->name('request.list');
