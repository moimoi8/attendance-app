<?php

namespace App\Http\Requests;

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
      'description' => ['required', 'string', 'max:255'],
    ];
  }

  public function messages()
  {
    return [
      'clock_in.required' => '出勤時間を入力してください',
      'clock_out.required' => '退勤時間を入力してください',
      'description.required' => '備考を記入してください',
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      $clock_in = $this->clock_in ? date('H:i', strtotime($this->clock_in)) : null;
      $clock_out = $this->clock_out ? date('H:i', strtotime($this->clock_out)) : null;

      $all_inputs = $this->all();

      if ($clock_in && $clock_out && $clock_in >= $clock_out) {
        $validator->errors()->add('clock_out', '出勤時間もしくは退勤時間が不適切な値です');
      }

      foreach ($all_inputs as $key => $value) {
        if (preg_match('/rest(\d+)_start/', $key, $matches)) {
          $i = $matches[1];
          $start_raw = $value;
          $end_raw = $this->input("rest{$i}_end");

          if (empty($start) || empty($end)) {
            continue;
        }

        $start = date('H:i', strtotime($start_raw));
        $end = date('H:i', strtotime($end_raw));

        if ($start < $clock_in || $start > $clock_out) {
          $validator->errors()->add("rest{$i}_start", '休憩時間が不適切な値です');
        }

        if ($end > $clock_out) {
          $validator->errors()->add("rest{$i}_end", '休憩時間もしくは退勤時間が不適切な値です');
        }

        if ($start >= $end) {
          $validator->errors()->add("rest{$i}_end", '休憩時間が不適切な値です');
        }
      }
    });
  }
}
