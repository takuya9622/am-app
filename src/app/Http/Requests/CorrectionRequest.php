<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class CorrectionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'year' => ['required', 'string'],
            'date' => ['required', 'string'],
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'break_start_time.*' => ['required','date_format:H:i'],
            'break_end_time.*' => ['required', 'date_format:H:i'],
            'remarks' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'year.required' => '日付を入力してください',
            'year.date' => '日付は〇年の型で入力してください',
            'date.required' => '日付を入力してください',
            'date.date' => '日付は〇月〇日の型で入力してください',
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '有効な出勤時間を入力してください',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '有効な退勤時間を入力してください',
            'clock_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_start_time.*.required' => '休憩開始時間を入力してください',
            'break_start_time.*.date_format' => '有効な休憩開始時間を入力してください',
            'break_end_time.*.required' => '休憩終了時間を入力してください',
            'break_end_time.*.date_format' => '有効な休憩終了時間を入力してください',
            'remarks.required' => '備考を記入してください',
            'remarks.string' => '備考は文字列で入力してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $clockIn = $this->input('clock_in');
            $clockOut = $this->input('clock_out');
            $breakStartTimes = $this->input('break_start_time', []);
            $breakEndTimes = $this->input('break_end_time', []);

            if ($clockIn && $clockOut) {
                try {
                    $clockInCarbon = Carbon::createFromFormat('H:i', $clockIn);
                    $clockOutCarbon = Carbon::createFromFormat('H:i', $clockOut);

                    foreach ($breakStartTimes as $index => $startTime) {
                        $endTime = $breakEndTimes[$index] ?? null;

                        if (!$startTime || !$endTime) continue;

                        $startTimeCarbon = Carbon::createFromFormat('H:i', $startTime);
                        $endTimeCarbon = Carbon::createFromFormat('H:i', $endTime);
                        $label = $index === 0 ? "休憩" : "休憩" . ($index + 1);

                        if ($startTimeCarbon->lessThan($clockInCarbon) || $startTimeCarbon->greaterThan($clockOutCarbon)) {
                            $validator->errors()->add("break_start_time.$index", "休憩時間が勤務時間外です(" . ($label) . ")");
                        }

                        if ($endTimeCarbon->lessThan($clockInCarbon) || $endTimeCarbon->greaterThan($clockOutCarbon)) {
                            $validator->errors()->add("break_end_time.$index", "休憩時間が勤務時間外です(" . ($label) . ")");
                        }

                        if ($endTimeCarbon->lessThanOrEqualTo($startTimeCarbon)) {
                            $validator->errors()->add("break_end_time.$index", "休憩開始時間もしくは休憩終了時間が不適切な値です(" . ($label) . ")");
                        }
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add("clock_in", "無効な時間形式が指定されています。始業時間または終業時間を確認してください。");
                }
            }
        });
    }

}