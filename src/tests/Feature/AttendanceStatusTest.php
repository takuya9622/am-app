<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testCorrectDateAndTime()
    {
        $user = $this->createTestUser();

        $nowDate = now()->isoFormat('YYYY年M月D日(ddd)');
        $nowTime = now()->format('H:i');

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response->assertSeeText($nowDate);
        $response->assertSeeText($nowTime);
    }

    public function testStatusIsOutsideWork()
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response->assertSeeText('勤務外');
    }

    public function testStatusIsAtWork()
    {
        $user = $this->createTestUser();

        $this->createAttendanceRecord($user, AttendanceRecord::STATUS_AT_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response->assertSeeText('出勤中');
    }

    public function testStatusIsOnBreak()
    {
        $user = $this->createTestUser();

        $this->createAttendanceRecord($user, AttendanceRecord::STATUS_ON_BREAK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response->assertSeeText('休憩中');
    }


    public function testStatusIsFinishedWork()
    {
        $user = $this->createTestUser();

        $this->createAttendanceRecord($user, AttendanceRecord::STATUS_FINISHED_WORK);

        $response = $this->actingAs($user)->get(route('attendance.index'));
        $response->assertStatus(200);

        $response->assertSeeText('退勤済');
    }
}