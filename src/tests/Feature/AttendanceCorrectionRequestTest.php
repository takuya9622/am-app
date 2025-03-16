<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class AttendanceCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testClockInTimeCanNotBeAfterClockOutTime(): void
    {
        $user = $this->createTestUser();

        $date = now()->format('Y-m-d');
        $clockIn = Carbon::parse($date . ' 08:00');
        $clockOut = (clone $clockIn)->addHours(8);
        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
        ]);

        BreakRecord::factory()->create([
            'attendance_record_id' => $attendanceRecord->id,
            'start_time' => (clone $clockIn)->addHour(1),
            'end_time' => (clone $clockIn)->addHour(2),
        ]);

        $attendanceRecord->formattedClockIn = $attendanceRecord->clock_in->format('H:i');
        $attendanceRecord->formattedClockOut = $attendanceRecord->clock_out->format('H:i');

        $response = $this->actingAs($user)->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertStatus(200);
        $response->assertSeeText($attendanceRecord->formattedClockIn);
        $response->assertSee($attendanceRecord->formattedClockOut);

        $newClockIn = [
            'clock_in' => (clone $clockOut)->addHour()->format('H:i'),
        ];

        $attendanceRecord->formattedClockIn = $newClockIn['clock_in'];

        $response = $this->post(route('attendance.correct', ['attendanceId' => $attendanceRecord->id]), $newClockIn);
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['clock_out']);
        $response->assertRedirect(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response = $this->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertSee($attendanceRecord->formattedClockIn);
        $response->assertSee($attendanceRecord->formattedClockOut);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    // public function testBreakStartTimeCannotBeAfterClockOutTime(): void
    // {
    //     $user = $this->createTestUser();

    //     $date = now()->format('Y-m-d');
    //     $clockIn = Carbon::parse($date . ' 08:00');
    //     $clockOut = (clone $clockIn)->addHours(8);
    //     $attendanceRecord = AttendanceRecord::factory()->create([
    //         'user_id' => $user->id,
    //         'date' => $date,
    //         'clock_in' => $clockIn,
    //         'clock_out' => $clockOut,
    //     ]);

    //     BreakRecord::factory()->create([
    //         'attendance_record_id' => $attendanceRecord->id,
    //         'start_time' => (clone $clockIn)->addHour(1),
    //         'end_time' => (clone $clockIn)->addHour(2),
    //     ]);

    //     $attendanceRecord->formattedClockIn = $attendanceRecord->clock_in->format('H:i');
    //     $attendanceRecord->formattedClockOut = $attendanceRecord->clock_out->format('H:i');

    //     $response = $this->actingAs($user)->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
    //     $response->assertStatus(200);
    //     $response->assertSee($attendanceRecord->formattedClockIn);
    //     $response->assertSee($attendanceRecord->formattedClockOut);

    //     $newBreakEnd = [
    //         'break_end_time[0]' => (clone $clockOut)->addHour()->format('H:i'),
    //     ];

    //     $attendanceRecord->formattedClockIn = $newBreakEnd['end_time'];

    //     $response = $this->post(route('attendance.correct', ['attendanceId' => $attendanceRecord->id]), $newBreakEnd);
    //     $response->assertStatus(302);
    //     $response->assertSessionHasErrors(['clock_out']);
    //     $response->assertRedirect(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
    //     $response = $this->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
    //     $response->assertSee($attendanceRecord->formattedClockIn);
    //     $response->assertSee($attendanceRecord->formattedClockOut);
    //     $response->assertSeeText('休憩時間が勤務時間外です');
    // }

    // public function testBreakEndTimeCannotBeAfterClockOutTime(): void
    // {
    //     $user = $this->createTestUser();

    //     $response->assertSeeText('出勤時間もしくは退勤時間が不適切な値です');
    // }

    // public function testRemarksFieldIsRequired(): void
    // {
    //     $user = $this->createTestUser();

    //     $response->assertSeeText('備考を記入してください');
    // }

    // public function testCorrectionRequestIsProcessed(): void
    // {
    //     $user = $this->createTestUser();

    //     $response->assertStatus(200);
    // }

    // public function testPendingRequestsAreDisplayedForLoggedInUser(): void
    // {
    //     $user = $this->createTestUser();

    //     $response->assertStatus(200);
    // }

    // public function testApprovedRequestsAreDisplayedForAdmin(): void
    // {
    //     $user = $this->createTestUser();

    //     $response->assertStatus(200);
    // }

    // public function testRequestDetailButtonNavigatesToDetailPage(): void
    // {
    //     $user = $this->createTestUser();

    //     $response->assertStatus(200);
    // }
}
