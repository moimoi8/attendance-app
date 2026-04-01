<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RestCorrectRequests extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('rest_correct_requests', function (Blueprint $table) {
      $table->id();
      $table->foreignId('attendance_correct_request_id')->constrained()->cascadeOnDelete();
      $table->foreignId('rest_id')->nullable()->constrained()->cascadeOnDelete();
      $table->time('requested_start_time');
      $table->time('requested_end_time');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
}
