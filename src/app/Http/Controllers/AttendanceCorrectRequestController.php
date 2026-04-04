<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ExemptRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use App\Models\Rest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectRequestController extends Controller
{
  public function update(ExemptRequest $request, $id)
  {
    $attendance = Attendance::findOrFail($id);

    if (in_array($attendance->correctRequest?->status, [1, 2])) {
      return redirect()->back()->with('error', 'この申請は現在変更できません');
    }

    $user = Auth::user();
    $dateStr = $attendance->date->format('Y-m-d');

    $formatTime = function ($time) use ($dateStr) {
      if (!$time) return null;
      $converted = mb_convert_kana($time, 'ka', 'UTF-8');
      $cleanTime = str_replace(' ', '', $converted);
      return $dateStr . ' ' . $cleanTime;
    };

    $application = AttendanceCorrectRequest::updateOrCreate(
      ['attendance_id' => $id, 'user_id' => $user->id],
      [
        'requested_clock_in'  => $formatTime($request->clock_in),
        'requested_clock_out' => $formatTime($request->clock_out),
        'reason' => $request->description,
        'status' => 1,
      ]
    );

    if ($request->has('rests')) {
      foreach ($request->rests as $restId => $restData) {
        if (!empty($restData['start']) && !empty($restData['end'])) {
          $application->restCorrectRequests()->updateOrCreate(
            ['rest_id' => $restId],
            [
              'requested_start_time' => $formatTime($restData['start']),
              'requested_end_time' => $formatTime($restData['end']),
            ]
          );
        }
      }
    }

    if ($request->has('new_rests')) {
      foreach ($request->new_rests as $newRest) {
        if (!empty($newRest['start']) && !empty($newRest['end'])) {
          $application->restCorrectRequests()->create([
            'requested_start_time' => $formatTime($newRest['start']),
            'requested_end_time' => $formatTime($newRest['end']),
            'rest_id' => null,
          ]);
        }
      }
    }

    $attendance->update([
      'clock_in' => $formatTime($request->clock_in),
      'clock_out' => $formatTime($request->clock_out),
    ]);

    if ($request->has('rests')) {
      foreach ($request->rests as $restId => $restData) {
        $rest = Rest::find($restId);
        if ($rest) {
          $rest->update([
            'start_time' => $formatTime($restData['start']),
            'end_time' => $formatTime($restData['end']),
          ]);
        }
      }
    }

    if ($request->has('new_rests')) {
      foreach ($request->new_rests as $newRest) {
        if (!empty($newRest['start']) && !empty($newRest['end'])) {
          Rest::create([
            'attendance_id' => $attendance->id,
            'start_time' => $formatTime($newRest['start']),
            'end_time' => $formatTime($newRest['end']),
          ]);
        }
      }
    }

    return redirect()->route('attendance.edit', ['id' => $id])->with('success', '修正申請を出しました');
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
}
