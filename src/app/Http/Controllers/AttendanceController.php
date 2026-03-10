<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\WorkStatus;
use App\Models\Rest;

class AttendanceController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $today = Carbon::now()->format('Y-m-d');

    $attendance = Attendance::where('user_id', $user->id)
      ->where('date', $today)
      ->first();

    $is_resting = false;
    if ($attendance) {
      $is_resting = Rest::where('attendance_id', $attendance->id)
        ->whereNull('end_time')
        ->exists();
    }

    if (!$attendance) {
      $status_id = 1;
      $status = 'off_duty';
      $status_key = 'off-duty';
    } elseif ($attendance->clock_out) {
      $status_id = 4;
      $status = 'finished';
      $status_key = 'finished';
    } elseif ($is_resting) {
      $status_id = 3;
      $status = 'on_break';
      $status_key = 'on-break';
    } else {
      $status_id = 2;
      $status = 'working';
      $status_key = 'working';
    }

    $workStatus = WorkStatus::find($status_id);
    $status_label = $workStatus->name;

    return view('attendance.punch', [
      'status' => $status,
      'status_key' => $status_key,
      'status_label' => $status_label,
      'today' => Carbon::now()->isoFormat('YYYY年MM月DD日(ddd)'),
      'current_time' => Carbon::now()->format('H:i'),
    ]);
  }

  public function start()
  {
    $user = Auth::user();
    $now = Carbon::now();

    $existinAttendance = Attendance::where('user_id', $user->id)
      ->where('date', $now->format('Y-m-d'))
      ->first();

    if ($existinAttendance) {
      return redirect()->back()->with('error', '今日は既に出勤しています。');
    }

    Attendance::create([
      'user_id' => $user->id,
      'date' => $now->format('Y-m-d'),
      'clock_in' => $now->format('H:i:s'),
    ]);

    return redirect()->back()->with('message', '出勤しました!');
  }

  public function end()
  {
    $user = Auth::user();
    $now = Carbon::now();

    $attendance = Attendance::where('user_id', $user->id)
      ->where('date', $now->format('Y-m-d'))
      ->whereNull('clock_out')
      ->first();

    if (!$attendance) {
      return redirect()->back()->with('error', '出勤データが見つからないか、既に出勤済みです。');
    }

    $attendance->update([
      'clock_out' => $now->format('H:i:s'),
    ]);

    return redirect()->back()->with('message', '退勤しました。お疲れ様でした！');
  }

  public function restStart()
  {
    $user = Auth::user();
    $today = Carbon::now()->format('Y-m-d');

    $attendance = Attendance::where('user_id', $user->id)
      ->where('date', $today)
      ->whereNull('clock_out')
      ->first();

    if ($attendance) {
      Rest::create([
        'attendance_id' => $attendance->id,
        'start_time' => Carbon::now()->format('H:i:s'),
      ]);
    }
    return redirect()->back();
  }

  public function restEnd()
  {
    $user = Auth::user();
    $today = Carbon::now()->format('Y-m-d');

    $attendance = Attendance::where('user_id', $user->id)
      ->where('date', $today)
      ->first();

    if ($attendance) {
      $rest = Rest::where('attendance_id', $attendance->id)
        ->whereNull('end_time')
        ->latest()
        ->first();

      if ($rest) {
        $rest->update([
          'end_time' => Carbon::now()->format('H:i:s'),
        ]);
      }
    }
    return redirect()->back();
  }
}
