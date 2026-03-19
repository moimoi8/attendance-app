<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class ExemptRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'clock_in' => ['required'],
      'clock_out' => ['required'],
      'description' => ['required'],
    ];
  }

  public function messages()
  {
    return [
      'clock_in.required' => '出勤時間もしくは退勤時間が不適切な値です',
      'clock_out.required' => '出勤時間もしくは退勤時間が不適切な値です',
      'description.required' => '備考を記入してください',
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      $clockInStr = $this->clock_in;
      $clockOutStr = $this->clock_out;

      if (!$clockInStr || !$clockOutStr) {
        $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
        return;
      }

      $clockIn = Carbon::createFromFormat('H:i', $clockInStr);
      $clockOut = Carbon::createFromFormat('H:i', $clockOutStr);

      if ($clockIn->greaterThanOrEqualTo($clockOut)) {
        $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
      }

      foreach ($this->all() as $key => $value) {
        if (preg_match('/^rest(\d+)_start$/', $key, $matches)) {
          $index = $matches[1];
          $restStartStr = $value;
          $restEndStr = $this->input("rest{$index}_end");

          if ($restStartStr || $restEndStr) {
            if (!$restStartStr || !$restEndStr) {
              $validator->errors()->add("rest{$index}_start", '休憩時間もしくは退勤時間が不適切な値です');
              continue;
            }

            $restStart = Carbon::createFromFormat('H:i', $restStartStr);
            $restEnd = Carbon::createFromFormat('H:i', $restEndStr);

            if ($restStart->lessThan($clockIn) || $restStart->greaterThan($clockOut)) {
              $validator->errors()->add("rest{$index}_start", '休憩時間もしくは退勤時間が不適切な値です');
            }

            if ($restEnd->greaterThan($clockOut)) {
              $validator->errors()->add("rest{$index}_start", '休憩時間もしくは退勤時間が不適切な値です');
            }

            if ($restStart->greaterThanOrEqualTo($restEnd)) {
              $validator->errors()->add("rest{$index}_start", '休憩時間が不適切です');
            }
          }
        }
      }

      if ($this->has('new_rests')) {
        foreach ($this->new_rests as $index => $rest) {
          $restStartStr = $rest['start'] ?? null;
          $restEndStr = $rest['end'] ?? null;

          if ($restStartStr || $restEndStr) {
            $validator->errors()->add("new_rests.{$index}.start", '休憩時間もしくは退勤時間が不適切な値です');
            continue;
          }

          $restStart = Carbon::createFromFormat('H:i', $restStartStr);
          $restEnd = Carbon::createFromFormat('H:i', $restEndStr);

          if ($restStart->lessThan($clockIn) || $restStart->greaterThan($clockOut)) {
            $validator->errors()->add("new_rests.{$index}.start", '休憩時間もしくは退勤時間が不適切な値です');
          }

          if ($restEnd->greaterThan($clockOut)) {
            $validator->errors()->add("new_rests.{$index}.start", '休憩時間もしくは退勤時間が不適切な値です');
          }

          if ($restStart->greaterThanOrEqualTo($restEnd)) {
            $validator->errors()->add("new_rests.{$index}.start", '休憩時間が不適切です');
          }
        }
      }
    });
  }
}
