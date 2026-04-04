<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

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

  protected function prepareForValidation()
  {
    $input = $this->all();

    if ($this->has('clock_in')) {
      $input['clock_in'] = mb_convert_kana($this->clock_in, 'as', 'UTF-8');
    }

    if ($this->has('clock_out')) {
      $input['clock_out'] = mb_convert_kana($this->clock_out, 'as', 'UTF-8');
    }

    if ($this->has('rests')) {
      $rests = [];
      foreach ($this->rests as $id => $times) {
        $rests[$id]['start'] = mb_convert_kana($times['start'] ?? '', 'as', 'UTF-8');
        $rests[$id]['end'] = mb_convert_kana($times['end'] ?? '', 'as', 'UTF-8');
      }
      $input['rests'] = $rests;
    }

    if ($this->has('new_rests')) {
      $newRests = [];
      foreach ($this->new_rests as $index => $rest) {
        $newRests[$index]['start'] = mb_convert_kana($rest['start'] ?? '', 'as', 'UTF-8');
        $newRests[$index]['end'] = mb_convert_kana($rest['end'] ?? '', 'as', 'UTF-8');
      }
      $input['new_rests'] = $newRests;
    }
    $this->merge($input);
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
      try {
        $clockIn = Carbon::createFromFormat('H:i', $this->clock_in);
        $clockOut = Carbon::createFromFormat('H:i', $this->clock_out);
        if ($clockIn->greaterThanOrEqualTo($clockOut)) {
          $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
        }
      } catch (\Exception $e) {
        $validator->errors()->add('clock_in', '出勤時間もしくは退勤時間が不適切な値です');
        return;
      }

      if ($this->has('rests')) {
        foreach ($this->rests as $id => $times) {
          $startStr = $times['start'] ?? '';
          $endStr = $times['end'] ?? '';

          if ($startStr === '' && $endStr === '') continue;

          $errorKey = "rests.{$id}.start";

          if ($startStr === '' || $endStr === '') {
            $validator->errors()->add($errorKey, '休憩時間が不適切な値です');
            continue;
          }

          try {
            $restStart = Carbon::createFromFormat('H:i', $startStr);
            $restEnd = Carbon::createFromFormat('H:i', $endStr);
            $this->validateRestTimes($validator, $restStart, $restEnd, $clockIn, $clockOut, $errorKey);
          } catch (\Exception $e) {
            $validator->errors()->add($errorKey, '休憩時間が不適切な値です');
          }
        }
      }

      if ($this->has('new_rests')) {
        foreach ($this->new_rests as $index => $rest) {
          $startStr = $rest['start'] ?? '';
          $endStr = $rest['end'] ?? '';

          if ($startStr === '' && $endStr === '') continue;
          if ($startStr === '' || $endStr === '') {
            $validator->errors()->add("new_rests.{$index}.start", '休憩時間が不適切な値です');
            continue;
          }

          try {
            $restStart = Carbon::createFromFormat('H:i', $startStr);
            $restEnd = Carbon::createFromFormat('H:i', $endStr);
            $this->validateRestTimes($validator, $restStart, $restEnd, $clockIn, $clockOut, "new_rests.{$index}.start");
          } catch (\Exception $e) {
            $validator->errors()->add("new_rests.{$index}.start", '休憩時間が不適切です');
          }
        }
      }
    });
  }

  public function validateRestTimes($validator, $restStart, $restEnd, $clockIn, $clockOut, $errorKey)
  {
    if ($restStart->lessThan($clockIn) || $restStart->greaterThan($clockOut)) {
      $validator->errors()->add($errorKey, '休憩時間が不適切な値です');
      return;
    }
    if ($restEnd->greaterThan($clockOut)) {
      $validator->errors()->add($errorKey, '休憩時間もしくは退勤時間が不適切な値です');
      return;
    }
    if ($restStart->greaterThanOrEqualTo($restEnd)) {
      $validator->errors()->add($errorKey, '休憩時間が不適切な値です');
    }
  }

  public function getRedirectUrl()
  {
    if ($this->is('admin/*')) {
      return route('admin.attendance.detail', ['id' => $this->route('id')]);
    }

    return parent::getRedirectUrl();
  }
}
