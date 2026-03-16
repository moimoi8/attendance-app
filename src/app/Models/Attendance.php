<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    foreach ($this->rests as $rest) {
      if ($rest->start_time && $rest->end_time) {
        $totalMinutes += $rest->start_time->diffInMinutes($rest->end_time);
      }
    }
    $hours = floor($totalMinutes / 60);
    $minutes = $totalMinutes % 60;
    return sprintf('%02d:%02d', $hours, $minutes);
  }

  public function getTotalWorkTimeAttribute()
  {
    if (!$this->clock_in || !$this->clock_out) {
      return '00:00';
    }
    $totalMinutes = $this->clock_in->diffInMinutes($this->clock_out);

    $restMinutes = 0;
    foreach ($this->rests as $rest) {
      if ($rest->start_time && $rest->end_time) {
        $restMinutes += $rest->start_time->diffInMinutes($rest->end_time);
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
