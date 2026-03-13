<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceCorrectRequest;

class AttendanceCorrectRequestSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    AttendanceCorrectRequest::create([
      'user_id' => 2,
      'attendance_id' => 24,
      'requested_clock_in' => '09:00:00',
      'requested_clock_out' => '18:00:00',
      'reason' => 'テスト申請中です',
      'status' => 1,
    ]);
  }
}
