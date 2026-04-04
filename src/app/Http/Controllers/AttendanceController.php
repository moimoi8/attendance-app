<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Rest;
use App\Models\WorkStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
  public function index()
  {
    $user = Auth::user();
    $today = Carbon::now()->format('Y-m-d');
    $attendance = Attendance::where('user_id', $user->id)
      ->whereDate('date', $today)
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
    /** @var App\Models\User $user */
    $user->work_status_id = 2;
    $user->save();

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

    $user->update(['work_status_id' => 2]);

    return redirect()->back()->with('message', '出勤しました!');
  }

  public function end()
  {
    $user = Auth::user();
    /** @var App\Models\User $user */
    $user->work_status_id = 4;
    $user->save();

    $now = Carbon::now();
    $attendance = Attendance::where('user_id', $user->id)
      ->whereDate('date', $now->format('Y-m-d'))
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

  public function show($id)
  {
    $attendance = Attendance::with([
      'rests',
      'correctRequest.restCorrectRequests'
    ])->findOrFail($id);

    return view('attendance.edit', compact('attendance'));
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
        ->with([
          'rests',
          'correctRequest' => function ($query) {
            $query->where('status', 1);
          },
          'correctRequest.restCorrectRequests'
        ])
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

  public function edit(Request $request, $id = null)
  {
    $date = $request->query('date');
    $user = Auth::user();
    $attendance = Attendance::with(['rests', 'correctRequest.restCorrectRequests'])
      ->where('user_id', $user->id)
      ->where('date', $date)
      ->first();

    if (!$attendance) {
      $attendance = new Attendance(['date' => Carbon::parse($date)]);
    }

    return view('attendance.detail', compact('attendance'));
  }
}
