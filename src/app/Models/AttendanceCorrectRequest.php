<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectRequest extends Model
{
  use HasFactory;

  protected $table = 'attendance_correct_requests';

  protected $fillable = [
    'user_id',
    'attendance_id',
    'requested_clock_in',
    'requested_clock_out',
    'reason',
    'status'
  ];

  protected $casts = [
    'requested_clock_in' => 'datetime',
    'requested_clock_out' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function attendance()
  {
    return $this->belongsTo(Attendance::class);
  }
}
