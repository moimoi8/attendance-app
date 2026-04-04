<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
  use HasFactory;

  protected $fillable = [
    'user_id',
    'date',
    'clock_in',
    'clock_out',
    'description',
  ];

  protected $casts = [
    'date' => 'date',
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function rests()
  {
    return $this->hasMany(Rest::class);
  }

  public function getTotalRestTimeAttribute()
  {
    $totalMinutes = 0;
    if ($this->correctRequest && $this->correctRequest->status == 1) {
      foreach ($this->correctRequest->restCorrectRequests as $restReq) {
        $start = Carbon::parse($restReq->requested_start_time);
        $end = Carbon::parse($restReq->requested_end_time);
        $totalMinutes += $start->diffInMinutes($end);
      }
    } else {
      foreach ($this->rests as $rest) {
        if ($rest->start_time && $rest->end_time) {
          $start = Carbon::parse($rest->start_time);
          $end = Carbon::parse($rest->end_time);
          $totalMinutes += $start->diffInMinutes($end);
        }
      }
    }
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    return sprintf('%02d:%02d', $hours, $minutes);
  }

  public function getTotalWorkTimeAttribute()
  {
    if ($this->correctRequest && $this->correctRequest->status == 1) {
      $clockIn = Carbon::parse($this->correctRequest->requested_clock_in);
      $clockOut = Carbon::parse($this->correctRequest->requested_clock_out);
    } else {
      $clockIn = $this->clock_in;
      $clockOut = $this->clock_out;
    }

    if (!$clockIn || !$clockOut) {
      return '00:00';
    }

    $totalMinutes = $clockIn->diffInMinutes($clockOut);

    $restMinutes = 0;
    if ($this->correctRequest && $this->correctRequest->status == 1) {
      foreach ($this->correctRequest->restCorrectRequests as $restReq) {
        $start = Carbon::parse($restReq->requested_start_time);
        $end = Carbon::parse($restReq->requested_end_time);
        $restMinutes += $start->diffInMinutes($end);
      }
    } else {
      foreach ($this->rests as $rest) {
        if ($rest->start_time && $rest->end_time) {
          $start = Carbon::parse($rest->start_time);
          $end = Carbon::parse($rest->end_time);
          $restMinutes += $start->diffInMinutes($end);
        }
      }
    }

    $workMinutes = $totalMinutes - $restMinutes;
    if ($workMinutes < 0) $workMinutes = 0;

    $hours = floor($workMinutes / 60);
    $minutes = $workMinutes % 60;
    return sprintf('%02d:%02d', $hours, $minutes);
  }

  public function correctRequest()
  {
    return $this->hasOne(AttendanceCorrectRequest::class);
  }
}
