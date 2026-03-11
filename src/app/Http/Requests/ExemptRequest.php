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
      'break_start' => ['required'],
      'break_end' => ['required'],
      'remarks' => ['required', 'string', 'max:255'],
    ];
  }

  public function messages()
  {
    return [
      'remarks.required' => '備考を記入してください',
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      if ($this->clock_in >= $this->clock_out) {
        $validator->errors()->add('clock_out', '出勤時間もしくは退勤時間が不適切な値です');
      }

      if ($this->break_start < $this->clock_in || $this->break_start > $this->clock_out) {
        $validator->errors()->add('break_start', '休憩時間が不適切な値です');
      }

      if ($this->break_end && $this->clock_out) {
        if ($this->break_end > $this->clock_out) {
          $validator->errors()->add('break_end', '休憩時間もしくは退勤時間が不適切な値です');
        }
      }
    });
  }
}
