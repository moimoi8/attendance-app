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
    $month = $request->query('month', now()->format('Y-m'));
    $date = Carbon::parse($month . '-01');

    $prevMonth = $date->copy()->subMonth()->format('Y-m');
    $nextMonth = $date->copy()->addMonth()->format('Y-m');

    $attendances = Attendance::where('user_id', $id)
      ->where('date', 'like', "$month%")
      ->orderBy('date', 'asc')
      ->get();

    return view('admin.attendance.staff', compact(
      'user',
      'attendances',
      'month',
      'date',
      'prevMonth',
      'nextMonth',
    ));
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

    if (!$attendance) {
      abort(404);
    }

    return view('admin.attendance.detail', compact('attendance'));
  }

  public function exportCsv(Request $request, $id)
  {
    $user = User::findOrFail($id);
    $month = $request->query('month', now()->format('Y-m'));

    $attendances = Attendance::where('user_id', $id)
      ->where('date', 'like', "$month%")
      ->orderBy('date', 'asc')
      ->get();

    $response = new StreamedResponse(function () use ($user, $attendances) {

      $handle = fopen('php://output', 'w');
      stream_filter_append($handle, 'convert.iconv.UTF-8/CP932');
      fputcsv($handle, ['日付', '出勤', '退勤', '休憩合計', '勤務合計']);

      foreach ($attendances as $attendance) {
        fputcsv($handle, [
          $attendance->date->format('Y/m/d'),
          $attendance->clock_in ? $attendance->clock_in->format('H:i') : '',
          $attendance->clock_out ? $attendance->clock_out->format('H:i') : '',
          $attendance->total_rest_time,
          $attendance->total_work_time,
        ]);
      }
      fclose($handle);
    });
    $fileName = "{$user->name}さん勤怠_{$month}.csv";
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="' . rawurlencode($fileName) . '"');

    return $response;
  }

  public function approveShow($attendance_correct_request_id)
  {
    $application = AttendanceCorrectRequest::with(['user', 'attendance.rests'])->findOrFail($attendance_correct_request_id);

    return view('admin.approve.detail', compact('application'));
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
