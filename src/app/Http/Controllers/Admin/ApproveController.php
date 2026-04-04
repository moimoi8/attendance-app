<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
  public function approveList(Request $request)
  {
    $tab = $request->query('tab', 'pending');
    $query = AttendanceCorrectRequest::with(['user', 'attendance', 'restCorrectRequests']);

    if ($tab === 'approved') {
      $query->where('status', 2);
    } else {
      $query->where('status', 1);
    }

    $applications = $query->orderBy('created_at', 'desc')->get();

    return view('admin.approve.list', compact('applications'));
  }

  public function approveShow($attendance_correct_request_id)
  {
    $application = AttendanceCorrectRequest::with(['user', 'attendance.rests'])->findOrFail($attendance_correct_request_id);
    $attendance = $application->attendance;

    return view('admin.approve.detail', compact('application', 'attendance'));
  }

  public function approveUpdate(Request $request, $attendance_correct_request_id)
  {
    $application = AttendanceCorrectRequest::with('restCorrectRequests')->findOrFail($attendance_correct_request_id);
    $attendance = $application->attendance;

    $attendance->update([
      'clock_in' => $application->requested_clock_in,
      'clock_out' => $application->requested_clock_out,
    ]);

    foreach ($application->restCorrectRequests as $restRequest) {
      if ($restRequest->requested_rest_start && $restRequest->requested_rest_end) {
        $attendance->rests()->updateOrCreate(
          ['id' => $restRequest->rest_id],
          [
            'start_time' => $restRequest->requested_rest_start,
            'end_time' => $restRequest->requested_rest_end,
          ]
        );
      }
    }

    $application->update([
      'status' => 2
    ]);

    return redirect()
      ->route('admin.approve.request_detail', ['attendance_correct_request_id' => $application->id])
      ->with('message', '承認しました');
  }
}
