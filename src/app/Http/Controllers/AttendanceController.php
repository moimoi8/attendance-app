<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\WorkStatus;
use App\Models\Rest;
use App\Http\Requests\ExemptRequest;

class AttendanceController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $today = Carbon::now()->format('Y-m-d');
    $attendance = Attendance::where('user_id', $user->id)
      ->where('date', $today)
      ->first();

    $is_clocked_out = $attendance && $attendance->clock_out ? true : false;

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
      'is_clocked_out' => $is_clocked_out,
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

  public function show($id)
  {
    $attendance = Attendance::with(['rests', 'correctRequest'])->findOrFail($id);

    return view('attendance.edit', compact('attendance'));
  }

  public function update(ExemptRequest $request, $id)
  {
    $user = Auth::user();
    $attendance = Attendance::findOrFail($id);
    $formatTime = function ($time) {
      if (!$time) return null;
      $converted = mb_convert_kana($time, 'ka', 'UTF-8');
      return str_replace(' ', '', $converted);
    };
    AttendanceCorrectRequest::updateOrCreate(
      ['attendance_id' => $id, 'user_id' => $user->id],
      [
        'requested_clock_in' => $formatTime($request->clock_in),
        'requested_clock_out' => $formatTime($request->clock_out),
        'reason' => $request->description,
        'status' => 1,
      ]
    );
    foreach ([1, 2] as $index) {
      $start = $formatTime($request->input("rest{$index}_start"));
      $end = $formatTime($request->input("rest{$index}_end"));
      if ($start && $end) {
        $rest = $attendance->rests()->skip($index - 1)->first() ?: new Rest();
        $rest->attendance_id = $attendance->id;
        $rest->start_time = $start;
        $rest->end_time = $end;
        $rest->save();
      }
    }

    return redirect()->route('attendance.edit', ['id' => $id])->with('success', '修正申請を出しました');
  }

  public function attendanceList(Request $request)
  {
    $user = Auth::user();
    $dateStr = $request->query('date', Carbon::now()->format('Y-m-d'));
    $currentDate = Carbon::parse($dateStr);

    $startOfMonth = $currentDate->copy()->startOfMonth();
    $endOfMonth = $currentDate->copy()->endOfMonth();

    $calendarDays = [];
    for ($day = $startOfMonth->copy(); $day->lte($endOfMonth); $day->addDay()) {
      $calendarDays[] = $day->copy();
    }

    if ($user->role === 'admin') {
      $attendances = Attendance::where('date', $currentDate->format('Y-m-d'))
        ->with('user')
        ->get();

      return view('admin.attendance.list', [
        'attendances' => $attendances,
        'date' => $currentDate,
        'calendarDays' => $calendarDays,
      ]);
    } else {
      $attendances = Attendance::where('user_id', $user->id)
        ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
        ->get()
        ->keyBy(function ($item) {
          return Carbon::parse($item->date)->format('Y-m-d');
        });
    }

    return view('attendance.list', [
      'calendarDays' => $calendarDays,
      'attendances' => $attendances,
      'date' => $currentDate,
      'currentDate' => $currentDate,
    ]);
  }

  public function requestList(Request $request)
  {
    $user = Auth::user();
    $tab = $request->query('tab', 'pending');
    $status = ($tab === 'approved') ? 2 : 1;
    $applications = AttendanceCorrectRequest::where('user_id', $user->id)
      ->where('status', $status)
      ->orderBy('created_at', 'desc')
      ->get();

    return view('attendance.request_list', compact('applications'));
  }

  public function edit(Request $request, $id = null)
  {
    $date = $request->query('date');
    $user = Auth::user();
    $attendance = Attendance::where('user_id', $user->id)
      ->where('date', $date)
      ->first();

    if (!$attendance) {
      $attendance = new Attendance(['date' => Carbon::parse($date)]);
    }

    return view('attendance.detail', compact('attendance'));
  }
}
