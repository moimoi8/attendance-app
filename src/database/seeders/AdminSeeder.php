<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::create([
      'name' => '管理者太郎',
      'email' => 'admin@example.com',
      'password' => Hash::make('password123'),
      'role' => 'admin',
      'email_verified_at' => now(),
    ]);
  }
}
