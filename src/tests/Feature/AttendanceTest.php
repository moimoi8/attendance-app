<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\WorkStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AttendanceTest extends TestCase
{
  /**
   * A basic feature test example.
   *
   * @return void
   */

  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
    Carbon::setTestNow('2026-04-01 10:00:00');
  }

  public function tearDown(): void
  {
    Carbon::setTestNow();
    parent::tearDown();
  }

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

  public function test_attendance_list_shows_today_record()
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

    $response = $this->get(route('attendance.list'));
    $response->assertSee(now()->format('Y/m'));
  }

  public function test_attendance_list_can_change_month()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    foreach ([1 => '勤務外', 2 => '出勤中'] as $id => $name) {
      $status = new WorkStatus();
      $status->id = $id;
      $status->name = $name;
      $status->save();
    }

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user']);

    $lastMonthDate = now()->subMonth()->format('Y-m-d');
    $lastMonthLabel = now()->subMonth()->format('Y/m');
    $response = $this->actingAs($user)->get(route('attendance.list', ['date' => $lastMonthDate]));
    $response->assertSee($lastMonthLabel);

    $nextMonthDate = now()->addMonth()->format('Y-m-d');
    $nextMonthLabel = now()->addMonth()->format('Y/m');

    $response = $this->actingAs($user)->get(route('attendance.list', ['date' => $nextMonthDate]));
    $response->assertSee($nextMonthLabel);
  }

  public function test_attendance_detail_page_displays_correct_info()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    $this->seed();

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['role' => 'user', 'name' => 'テスト太郎']);

    $attendance = Attendance::create([
      'user_id' => $user->id,
      'date' => now()->format('Y-m-d'),
      'clock_in' => '09:00:00',
      'clock_out' => '18:00:00',
      'work_status_id' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('attendance.edit', ['id' => $attendance->id]));
    $response->assertStatus(200);
    $response->assertSee('テスト太郎');
    $response->assertSee(now()->format('Y年'));
    $response->assertSee(now()->format('n月j日'));
    $response->assertSee('09:00');
    $response->assertSee('18:00');
  }

  public function test_user_can_submit_correction_request()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    /** @var \App\Models\User $user */
    $user = User::factory()->create(['name' => 'テスト太郎']);
    $attendance = Attendance::create([
      'user_id' => $user->id,
      'date' => now()->format('Y-m-d'),
      'clock_in' => '09:00:00',
      'clock_out' => '18:00:00',
      'work_status_id' => 1,
    ]);

    $response = $this->actingAs($user)->patch(route('attendance.update', ['id' => $attendance->id]), [
      'clock_in' => '08:30',
      'clock_out' => '17:30',
      'description' => '電車遅延のため修正します',
    ]);
    $response->assertStatus(302);

    $this->assertDatabaseHas('attendance_correct_requests', [
      'attendance_id' => $attendance->id,
      'user_id' => $user->id,
      'status' => 1,
      'reason' => '電車遅延のため修正します',
      'requested_clock_in' => now()->format('Y-m-d') . ' 08:30:00',
      'requested_clock_out' => now()->format('Y-m-d') . ' 17:30:00',
    ]);
  }

  public function test_user_can_see_their_own_pending_requests()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    /** @var \App\Models\User $user */
    $user = User::factory()->create();

    $attendance = Attendance::create([
      'user_id' => $user->id,
      'date' => now()->format('Y-m-d'),
      'clock_in' => '09:00:00',
      'clock_out' => '18:00:00',
      'work_status_id' => 1,
    ]);

    $request = AttendanceCorrectRequest::create([
      'attendance_id' => $attendance->id,
      'user_id' => $user->id,
      'requested_clock_in' => now()->format('Y-m-d') . ' 08:30:00',
      'requested_clock_out' => now()->format('Y-m-d') . ' 17:30:00',
      'reason' => '表示確認用のテスト理由です',
      'status' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('attendance.request_list'));
    $response->assertStatus(200);
    $response->assertSee('表示確認用のテスト理由です');
    $response->assertSee('承認待ち');
  }

  public function test_admin_can_approve_correction_request()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    /** @var \App\Models\User $admin */
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create();

    $attendance = Attendance::create([
      'user_id' => $user->id,
      'date' => '2026-03-27',
      'clock_in' => '09:00:00',
      'clock_out' => '18:00:00',
      'work_status_id' => 1,
    ]);

    $correctionRequest = AttendanceCorrectRequest::create([
      'attendance_id' => $attendance->id,
      'user_id' => $user->id,
      'requested_clock_in' => '2026-03-27 08:30:00',
      'requested_clock_out' => '2026-03-27 17:30:00',
      'reason' => '承認テストです',
      'status' => 1,
    ]);

    $response = $this->actingAs($admin)
      ->patch(route('admin.approve.update', [
        'attendance_correct_request_id' => $correctionRequest->id
      ]));
    $response->assertStatus(302);

    $this->assertDatabaseHas('attendance_correct_requests', [
      'id' => $correctionRequest->id,
      'status' => 2,
    ]);

    $targetDate = '2026-03-27';
    $this->assertDatabaseHas('attendances', [
      'id' => $attendance->id,
      'clock_in' => $targetDate . ' 08:30:00',
      'clock_out' => $targetDate . ' 17:30:00',
    ]);
  }

  public function test_user_can_see_their_own_approved_requests()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    /** @var \App\Models\User $user */
    $user = User::factory()->create([
      'role' => 'admin',
    ]);

    $attendance = Attendance::create([
      'user_id' => $user->id,
      'date' => now()->format('Y-m-d'),
      'clock_in' => '09:00:00',
      'clock_out' => '18:00:00',
      'work_status_id' => 1,
    ]);

    $approveRequest = AttendanceCorrectRequest::create([
      'attendance_id' => $attendance->id,
      'user_id' => $user->id,
      'requested_clock_in' => now()->format('Y-m-d') . ' 08:30:00',
      'requested_clock_out' => now()->format('Y-m-d') . ' 17:30:00',
      'reason' => '承認済みタブの表示テストです',
      'status' => 2,
    ]);

    $response = $this->actingAs($user)->get(route('admin.approve.request_detail', [
      'attendance_correct_request_id' => $approveRequest->id
    ]));
    $response->assertStatus(200);
    $response->assertSee('承認済みタブの表示テストです');
    $response->assertSee('承認済み');
  }

  public function test_admin_can_see_all_users_attendance_list()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    /** @var \App\Models\User $admin */
    $admin = User::factory()->create(['role' => 'admin']);
    $user1 = User::factory()->create(['name' => '太郎']);
    $user2 = User::factory()->create(['name' => '花子']);

    $today = '2026-03-27';

    Attendance::create([
      'user_id' => $user1->id,
      'date' => $today,
      'clock_in' => $today . ' 08:00:00',
      'clock_out' => $today . ' 18:00:00',
    ]);

    Attendance::create([
      'user_id' => $user2->id,
      'date' => $today,
      'clock_in' => $today . ' 08:00:00',
      'clock_out' => $today . ' 18:00:00',
    ]);

    $this->assertDatabaseHas('attendances', [
      'user_id' => $user1->id,
      'date' => $today . ' 00:00:00'
    ]);

    $response = $this->actingAs($admin)->get(route('admin.attendance.daily', ['date' => $today]));
    $response->assertStatus(200);
    $response->assertSee('太郎');
    $response->assertSee('花子');
  }

  public function test_admin_can_see_specific_staff_attendance_list()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    /** @var \App\Models\User $admin */
    $admin = User::factory()->create(['role' => 'admin']);
    $staff = User::factory()->create(['name' => 'テストスタッフ']);

    $targetDate = '2026-03-27';
    Attendance::create([
      'user_id' => $staff->id,
      'date' => $targetDate,
      'clock_in' => $targetDate . ' 09:00:00',
      'clock_out' => $targetDate . ' 18:00:00',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.attendance.staff', [
      'id' => $staff->id,
      'date' => '2026-03-01'
    ]));
    $response->assertStatus(200);
    $response->assertSee('テストスタッフ');
    $response->assertSee('09:00');
    $response->assertSee('18:00');
  }
}
