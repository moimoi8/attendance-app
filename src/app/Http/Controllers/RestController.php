<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Rest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RestController extends Controller
{
  public function restStart()
  {
    $user = Auth::user();
    /** @var \App\Models\User $user */
    $user->work_status_id = 3;
    $user->save();

    $today = Carbon::now()->format('Y-m-d');
    $attendance = Attendance::where('user_id', $user->id)
      ->whereDate('date', $today)
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
    /** @var \App\Models\User $user */
    $user->work_status_id = 2;
    $user->save();

    $today = Carbon::now()->format('Y-m-d');
    $attendance = Attendance::where('user_id', $user->id)
      ->whereDate('date', $today)
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
