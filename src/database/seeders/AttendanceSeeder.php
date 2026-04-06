<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $users = User::where('role', 'user')->get();

    foreach (range(0, 6) as $days) {
      $date = Carbon::today()->subDays($days);

      foreach ($users as $user) {
        $attendance = Attendance::factory()->create([
          'user_id' => $user->id,
          'date' => $date->format('Y-m-d'),
          'clock_in' => $date->copy()->setTime(rand(8, 10), rand(0, 59)),
          'clock_out' => $date->copy()->setTime(rand(17, 19), rand(0, 59)),
        ]);

        $attendance->rests()->create([
          'start_time' => $date->copy()->setTime(12, 00),
          'end_time' => $date->copy()->setTime(13, 00),
        ]);
      }
    }
  }
}
