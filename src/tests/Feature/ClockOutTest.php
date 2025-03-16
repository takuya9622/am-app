<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUserCanClockOut()
    {
        $user = $this->createTestUser();

        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_AT_WORK,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSee('退勤');

        $response = $this->put(route('attendance.update', $attendanceRecord), [
            'action' => 'endWork',
        ]);
        $response->assertRedirect(route('attendance.index'));

        $this->assertDataBaseHas('attendance_records', [
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
        ]);
        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('退勤済');
    }

    public function testUserCanCheckClockOutTime()
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response = $this->post(route('attendance.store'));
        $response->assertRedirect(route('attendance.index'));

        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
        ->where('date', now()->toDateString())
        ->firstOrFail();

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'work_status' => AttendanceRecord::STATUS_AT_WORK,
        ]);

        $response = $this->put(route('attendance.update', $attendanceRecord), [
            'action' => 'endWork',
        ]);
        $response->assertRedirect(route('attendance.index'));

        $this->assertDataBaseHas('attendance_records', [
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
        ]);

        $attendanceRecord->refresh();
        $clockOutFormatted = Carbon::parse($attendanceRecord->clock_out)->format('H:i');

        $response = $this->get(route('attendance.list'));
        $response->assertSeeText($clockOutFormatted);
    }
}
