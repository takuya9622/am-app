<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\BreakRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class MyAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testAttendanceRecordDetailHasUserName(): void
    {
        $user = $this->createTestUser();
        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertStatus(200);
        $response->assertSeeText($user->name);
    }

    public function testAttendanceRecordDetailHasSelectedDate(): void
    {
        $user = $this->createTestUser();
        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
        ]);
        $attendanceRecord->formattedDate = Carbon::parse($attendanceRecord->date)->isoFormat('M月D日');

        $response = $this->actingAs($user)->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertStatus(200);
        $response->assertSee('<option value="' . $attendanceRecord->formattedDate . '" selected>', false);
    }

    public function testAttendanceRecordDetailHasCorrectClockInAndClockOutTime(): void
    {
        $user = $this->createTestUser();
        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
        ]);
        $attendanceRecord->formattedClockIn = $attendanceRecord->clock_in->format('H:i');
        $attendanceRecord->formattedClockOut = $attendanceRecord->clock_out->format('H:i');

        $response = $this->actingAs($user)->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertStatus(200);
        $response->assertSee($attendanceRecord->formattedClockIn);
        $response->assertSee($attendanceRecord->formattedClockOut);
    }

    public function testAttendanceRecordDetailHasCorrectBreakTime(): void
    {
        $user = $this->createTestUser();
        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
        ]);
        $breakRecord = BreakRecord::factory()->create([
            'attendance_record_id' => $attendanceRecord->id,
        ]);
        $breakRecord->formattedStartTime = $breakRecord->start_time->format('H:i');
        $breakRecord->formattedEndTime = $breakRecord->end_time->format('H:i');

        $response = $this->actingAs($user)->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertStatus(200);
        $response->assertSee($breakRecord->formattedStartTime);
        $response->assertSee($breakRecord->formattedEndTime);
    }
}
