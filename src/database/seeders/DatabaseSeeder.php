<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    $this->call([
      WorkStatusSeeder::class,
      AdminSeeder::class,
      UserSeeder::class,
      AttendanceSeeder::class,
    ]);
  }
}
