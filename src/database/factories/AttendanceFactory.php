<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class AttendanceFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    $start = $this->faker->dateTimeBetween('08:00:00', '10:00:00');
    $end = (clone $start)->modify('+' . rand(8, 9) . ' hours ' . rand(0, 50) . ' minutes ');

    return [
      'user_id' => User::factory(),
      'clock_in' => $start,
      'clock_out' => $end,
    ];
  }
}
