<?php

namespace Database\Seeders;

use App\Models\WorkStatus;
use Illuminate\Database\Seeder;

class WorkStatusSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $statuses = [
      ['id' => 1, 'name' => '勤務外'],
      ['id' => 2, 'name' => '出勤中'],
      ['id' => 3, 'name' => '休憩中'],
      ['id' => 4, 'name' => '退勤済'],
    ];

    foreach ($statuses as $status) {
      WorkStatus::create($status);
    }
  }
}
