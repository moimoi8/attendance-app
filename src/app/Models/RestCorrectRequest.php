<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestCorrectRequest extends Model
{
  use HasFactory;

  protected $casts = [
    'requested_start_time' => 'datetime',
    'requested_end_time' => 'datetime',
  ];

  protected $fillable = [
    'attendance_correct_request_id',
    'rest_id',
    'requested_start_time',
    'requested_end_time',
  ];

  public function attendanceCorrectRequest()
  {
    return $this->belongsTo(AttendanceCorrectRequest::class);
  }
}
