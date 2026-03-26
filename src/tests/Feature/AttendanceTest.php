<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\WorkStatus;
use Illuminate\Support\Facades\DB;


class AttendanceTest extends TestCase
{
  /**
   * A basic feature test example.
   *
   * @return void
   */

  use RefreshDatabase;

  public function test_current_date_is_displayed_correctly()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    $status = new WorkStatus();
    $status->id = 1;
    $status->name = '勤務外';
    $status->save();

    /** @var \App\Models\User $user*/
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('attendance.punch'));
    $response->assertStatus(200);
    $response->assertSee(now()->format('Y年m月d日'));
  }

  public function test_status_is_off_work_initially()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    $status = new WorkStatus();
    $status->id = 1;
    $status->name = '勤務外';
    $status->save();
    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->get(route('attendance.punch'));

    $response->assertSee('勤務外');
  }

  public function test_user_can_punch_in()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    $status1 = new WorkStatus();
    $status1->id = 1;
    $status1->name = '勤務外';
    $status1->save();

    $status2 = new WorkStatus();
    $status2->id = 2;
    $status2->name = '出勤中';
    $status2->save();

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)->post(route('attendance.start'));

    $this->assertDatabaseHas('attendances', [
      'user_id' => $user->id,
      'date' => now()->format('Y-m-d 00:00:00'),
    ]);

    $response->assertStatus(302);
  }

  public function test_status_changes_to_working_after_punch_in()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    $status1 = new WorkStatus();
    $status1->id = 1;
    $status1->name = '勤務外';
    $status1->save();

    $status2 = new WorkStatus();
    $status2->id = 2;
    $status2->name = '出勤中';
    $status2->save();

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->post(route('attendance.start'));
    $user->refresh();

    $response = $this->get(route('attendance.punch'));
    $response->assertSee('出勤中');
  }

  public function test_punch_in_button_is_disabled_while_working()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    $status1 = new WorkStatus();
    $status1->id = 1;
    $status1->name = '勤務外';
    $status1->save();

    $status2 = new WorkStatus();
    $status2->id = 2;
    $status2->name = '出勤中';
    $status2->save();

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->post(route('attendance.start'));

    $response = $this->get(route('attendance.punch'));
    $response->assertDontSee('<button class="punch__button punch__button--black">出勤</button>', false);
    $response->assertSee('退勤');
  }

  public function test_status_changes_to_finished_after_punch_out()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 4 => '退勤済'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('attendance.end'));
    $user->refresh();

    $response = $this->actingAs($user)->get(route('attendance.punch'));
    $response->assertSee('退勤済');
  }

  public function test_punch_out_button_is_disabled_after_punch_out()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 4 => '退勤済'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('attendance.end'));

    $response = $this->get(route('attendance.punch'));
    $response->assertDontSee('<button class="punch__button punch__button--black">退勤</button>', false);
    $response->assertDontSee('<button class="punch__button punch__button--white">休憩入</button>', false);
    $response->assertSee('お疲れ様でした。');
  }

  public function test_punch_out_button_is_disabled_while_on_break()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 3 => '休憩中'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('rest.start'));

    $response = $this->get(route('attendance.punch'));
    $response->assertDontSee('<button class="punch__button punch__button--black">退勤</button>', false);
    $response->assertSee('休憩戻');
  }

  public function test_rest_start_time_is_saved_after_punch_in_rest()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 3 => '休憩中'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('rest.start'));

    $this->assertDatabaseHas('rests', [
      'start_time' => now()->format('Y-m-d H:i:s'),
    ]);
  }

  public function test_status_changes_to_resting_after_punch_in_rest()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 3 => '休憩中'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('rest.start'));
    $user->refresh();

    $response = $this->actingAs($user)->get(route('attendance.punch'));
    $response->assertSee('休憩中');
  }

  public function test_rest_end_time_is_saved_and_status_returns_to_working()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 3 => '休憩中'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('rest.start'));
    $this->actingAs($user)->post(route('rest.end'));
    $this->assertDatabaseHas('rests', [
      'end_time' => now()->format('Y-m-d H:i:s'),
    ]);
    $user->refresh();

    $response = $this->actingAs($user)->get(route('attendance.punch'));
    $response->assertSee('出勤中');
  }

  public function test_rest_end_button_is_disabled_after_rest_end()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中', 3 => '休憩中'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->post(route('attendance.start'));
    $this->actingAs($user)->post(route('rest.start'));
    $this->actingAs($user)->post(route('rest.end'));

    $response = $this->get(route('attendance.punch'));
    $response->assertDontSee('<button class="punch__button punch__button--white">休憩戻</button>', false);
    $response->assertSee('休憩入');
  }
}
