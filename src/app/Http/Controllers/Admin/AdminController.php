<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\AttendanceCorrectRequest;

class AdminController extends Controller
{
  public function index(Request $request)
  {
    $date = $request->query('date', Carbon::today()->format('Y-m-d'));
    $attendances = Attendance::with('user')->where('date', $date)->get();

    return view('admin.attendance.daily', compact('date', 'attendances'));
  }

  public function staffList()
  {
    $users = User::all();

    return view('admin.staff.list', compact('users'));
  }

  public function userAttendance(Request $request, $id)
  {
    $user = User::findOrFail($id);
    $dateParam = $request->query('date', now()->format('Y-m-d'));
    $date = Carbon::parse($dateParam)->startOfMonth();

    $calendarDays = [];
    $daysInMonth = $date->daysInMonth;
    for ($i = 0; $i < $daysInMonth; $i++) {
      $calendarDays[] = $date->copy()->addDays($i);
    }

    $attendances = Attendance::where('user_id', $id)
      ->whereBetween('date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
      ->get()
      ->keyBy(function ($item) {
        return $item->date->format('Y-m-d');
      });

    return view('admin.attendance.staff', [
      'user' => $user,
      'attendances' => $attendances,
      'calendarDays' => $calendarDays,
      'date' => $date->format('Y-m-d'),
    ]);
  }

  public function approveList(Request $request)
  {
    $tab = $request->query('tab', 'pending');
    $query = AttendanceCorrectRequest::with(['user', 'attendance']);

    if ($tab === 'approved') {
      $query->where('status', 2);
    } else {
      $query->where('status', 1);
    }

    $applications = $query->orderBy('created_at', 'desc')->get();

    return view('admin.approve.list', compact('applications'));
  }

  public function show($id)
  {
    $attendance = Attendance::with(['user', 'rests'])->findOrFail($id);

    return view('admin.attendance.detail', compact('attendance'));
  }

  public function exportCsv(Request $request, $id)
  {
    $user = User::findOrFail($id);
    $dateParam = $request->query('date') ?? $request->query('month') ?? now()->format('Y-m-d');
    $date = Carbon::parse($dateParam)->startOfMonth();
    $monthStr = $date->format('Y-m');

    $attendances = Attendance::where('user_id', $id)
      ->whereBetween('date', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
      ->get()
      ->keyBy(function ($item) {
        return $item->date->format('Y-m-d');
      });

    $response = new StreamedResponse(function () use ($user, $attendances, $date) {
      $handle = fopen('php://output', 'w');
      stream_filter_append($handle, 'convert.iconv.UTF-8/CP932//TRANSLIT');
      fputcsv($handle, ['日付', '出勤', '退勤', '休憩合計', '勤務合計']);

      $daysInMonth = $date->daysInMonth;
      for ($i = 0; $i < $daysInMonth; $i++) {
        $currentDay = $date->copy()->addDays($i);
        $currentDayStr = $currentDay->format('Y-m-d');
        $attendance = $attendances->get($currentDayStr);

        fputcsv($handle, [
          $currentDay->format('Y/m/d'),
          $attendance?->clock_in ? $attendance->clock_in->format('H:i') : '',
          $attendance?->clock_out ? $attendance->clock_out->format('H:i') : '',
          $attendance?->total_rest_time ?? '',
          $attendance?->total_work_time ?? '',
        ]);
      }
      fclose($handle);
    });

    $fileName = "{$user->name}さん勤怠_{$monthStr}.csv";

    foreach (
      [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename*=UTF-8''" . rawurlencode($fileName),
      ] as $key => $value
    ) {
      $response->headers->set($key, $value);
    }

    return $response;
  }

  public function update(Request $request, $id)
  {
    $attendance = Attendance::findOrFail($id);

    $attendance->update([
      'clock_in' => $request->clock_in,
      'clock_out' => $request->clock_out,
      'description' => $request->description,
    ]);

    if ($request->has('rests')) {
      foreach ($request->rests as $restId => $restData) {
        $rest = $attendance->rests()->find($restId);
        if ($rest) {
          $rest->update([
            'start_time' => $restData['start'],
            'end_time' => $restData['end'],
          ]);
        }
      }
    }

    if ($request->filled('new_rests.0.start')) {
      $attendance->rests()->create([
        'start_time' => $request->new_rests[0]['start'],
        'end_time' => $request->new_rests[0]['end'],
      ]);
    }

    return redirect()
      ->route('admin.attendance.show', ['id' => $attendance->id])
      ->with('message', '勤怠情報を修正しました');
  }

  public function approveShow($attendance_correct_request_id)
  {
    $application = AttendanceCorrectRequest::with(['user', 'attendance.rests'])->findOrFail($attendance_correct_request_id);
    $attendance = $application->attendance;

    return view('admin.approve.detail', compact('application', 'attendance'));
  }

  public function approveUpdate(Request $request, $attendance_correct_request_id)
  {
    $application = AttendanceCorrectRequest::findOrFail($attendance_correct_request_id);

    $attendance = $application->attendance;

    $attendance->update([
      'clock_in' => $application->requested_clock_in,
      'clock_out' => $application->requested_clock_out,
    ]);

    $application->update([
      'status' => 2
    ]);

    return redirect()
      ->route('admin.approve.request_detail', ['attendance_correct_request_id' => $application->id])
      ->with('message', '承認しました');
  }
}
