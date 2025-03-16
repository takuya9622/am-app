<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class BreakTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUserCanGoOnABreak()
    {
        $user = $this->createTestUser();

        $attendanceRecord = $this->createAttendanceRecord($user, AttendanceRecord::STATUS_AT_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $this->startBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩中');
    }

    public function testUserCanTakeMultipleBreaks()
    {
        $user = $this->createTestUser();

        $attendanceRecord = $this->createAttendanceRecord($user, AttendanceRecord::STATUS_AT_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $this->startBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩中');
        $response->assertSeeText('休憩戻');

        $this->endBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩入');
    }

    public function testUserCanReturnFromABreak()
    {
        $user = $this->createTestUser();

        $attendanceRecord = $this->createAttendanceRecord($user, AttendanceRecord::STATUS_AT_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $this->startBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩中');
        $response->assertSeeText('休憩戻');

        $this->endBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('出勤中');
    }

    public function
    testUserCanReturnFromMultipleBreaks()
    {
        $user = $this->createTestUser();

        $attendanceRecord = $this->createAttendanceRecord($user, AttendanceRecord::STATUS_AT_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $this->startBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩中');
        $response->assertSeeText('休憩戻');

        $this->endBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩入');

        $this->startBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩戻');
    }

    public function testUserCanCheckBreakStartAndEndTime()
    {
        $user = $this->createTestUser();

        $attendanceRecord = $this->createAttendanceRecord($user, AttendanceRecord::STATUS_AT_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        $this->startBreak($attendanceRecord, $user);

        $response = $this->get(route('attendance.index'));
        $response->assertSeeText('休憩中');
        $response->assertSeeText('休憩戻');

        $this->endBreak($attendanceRecord, $user);

        $attendanceRecord = AttendanceRecord::where('user_id', $user->id)
        ->where('date', now()->toDateString())
        ->firstOrFail();

        $breakRecord = $attendanceRecord->breakRecords()->latest()->firstOrFail();

        $startTimeFormatted = Carbon::parse($breakRecord->start_time)->format('H:i');
        $endTimeFormatted = $breakRecord->end_time
        ? Carbon::parse($breakRecord->end_time)->format('H:i')
        : null;

        $response = $this->get(route('attendance.list'));
        $response->assertSeeText($startTimeFormatted);
        $response->assertSeeText($endTimeFormatted);
    }

    private function startBreak($attendanceRecord, $user)
    {
        $response = $this->put(route('attendance.update', $attendanceRecord), [
            'action' => 'startBreak',
        ]);
        $response->assertRedirect(route('attendance.index'));

        $attendanceRecord->refresh();
        $this->assertDataBaseHas('attendance_records', [
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_ON_BREAK,
        ]);
    }

    private function endBreak($attendanceRecord, $user)
    {
        $response = $this->put(route('attendance.update', $attendanceRecord), [
            'action' => 'endBreak',
        ]);
        $response->assertRedirect(route('attendance.index'));

        $attendanceRecord->refresh();
        $this->assertDataBaseHas('attendance_records', [
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_AT_WORK,
        ]);
    }
}
