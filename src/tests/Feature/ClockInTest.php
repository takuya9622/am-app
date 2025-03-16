<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class ClockInTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUserCanClockIn()
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSeeText('出勤');

        $response = $this->post(route('attendance.store'));
        $response->assertRedirect(route('attendance.index'));

        $this->assertDataBaseHas('attendance_records', [
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_AT_WORK,
        ]);
        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('出勤中');
    }

    public function testUserCanOnlyClockInOnceADay()
    {
        $user = $this->createTestUser();

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response->assertDontSeeText('出勤');
    }

    public function testUserCanCheckClockInTime()
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response = $this->post(route('attendance.store'));
        $response->assertRedirect(route('attendance.index'));

        $this->assertDataBaseHas('attendance_records',[
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'work_status' => AttendanceRecord::STATUS_AT_WORK,
        ]);

        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
        ->where('date', now()->toDateString())
        ->firstOrFail();
        $clockInFormatted = Carbon::parse($attendanceRecord->clock_in)->format('H:i');

        $response = $this->get(route('attendance.list'));
        $response->assertSeeText($clockInFormatted);
    }
}
