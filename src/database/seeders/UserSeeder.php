<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::create([
      'name' => 'テスト花子',
      'email' => 'user@example.com',
      'password' => Hash::make('password123'),
      'work_status_id' => 1,
      'email_verified_at' => now(),
      'role' => 'user',
    ]);
  }
}
