<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthTest extends TestCase
{
  /**
   * A basic feature test example.
   *
   * @return void
   */
  use RefreshDatabase;

  public function test_name_is_required()
  {
    $response = $this->post('/register', [
      'name' => '',
      'email' => 'test@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors(['name']);
  }

  public function test_password_length_check()
  {
    $response = $this->post('/register', [
      'name' => 'テスト太郎',
      'email' => 'test@example.com',
      'password' => '1234567',
      'password_confirmation' => '1234567',
    ]);

    $response->assertSessionHasErrors(['password']);
  }

  public function test_password_confirmation_check()
  {
    $response = $this->post('/register', [
      'name' => 'テスト太郎',
      'email' => 'test@example.com',
      'password' => 'password123',
      'password_confirmation' => 'different_password',
    ]);

    $response->assertSessionHasErrors(['password']);
  }

  public function test_user_can_register()
  {
    DB::statement('PRAGMA foreign_keys = OFF');

    $response = $this->post('/register', [
      'name' => 'テスト太郎',
      'email' => 'success@example.com',
      'password' => 'password123',
      'password_confirmation' => 'password123',
    ]);

    $this->assertDatabaseHas('users', [
      'email' => 'success@example.com',
    ]);

    DB::statement('PRAGMA foreign_keys = ON');
  }

  public function test_login_validation_check()
  {
    $response = $this->post('/login', ['email' => '', 'password' => 'password123']);
    $response->assertSessionHasErrors(['email']);
    $response = $this->post('/login', ['email' => 'test@example.com', 'password' => '']);
    $response->assertSessionHasErrors(['password']);
  }

  public function test_login_fails_with_wrong_credentials()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    $user = User::create([
      'name' => 'テスト会員',
      'email' => 'login-test@example.com',
      'password' => bcrypt('password123'),
      'role' => 'user',
    ]);
    DB::statement('PRAGMA foreign_keys = ON');

    $response = $this->post('/login', [
      'email' => 'login-test@example.com',
      'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors(['email']);
  }

  public function test_user_can_login()
  {
    DB::statement('PRAGMA foreign_keys = OFF');
    $user = User::create([
      'name' => 'ログイン成功ユーザー',
      'email' => 'success-login@example.com',
      'password' => bcrypt('password123'),
      'role' => 'user',
    ]);
    DB::statement('PRAGMA foreign_keys = ON');

    $response = $this->post('/login', [
      'email' => 'success-login@example.com',
      'password' => 'password123',
    ]);

    $this->assertAuthenticatedAs($user);

    $response->assertStatus(302);
  }
}
