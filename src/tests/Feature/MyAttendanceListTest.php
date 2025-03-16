<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\CreatesTestData;
use Tests\TestCase;

class MyAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testUserCanCheckAllAttendanceRecords(): void
    {
        $user = $this->createTestUser();

        $attendanceRecords = $this->createMultipleAttendanceRecords($user);

        foreach ($attendanceRecords as $attendanceRecord) {
            $this->assertDatabaseHas('attendance_records', [
                'id' => $attendanceRecord->id,
                'user_id' => $user->id,
                'date' => Carbon::parse($attendanceRecord->date)->format('Y-m-d'),
                'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
            ]);

            $this->assertDatabaseHas('break_records', [
                'attendance_record_id' => $attendanceRecord->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('attendance.list'));
        $response->assertStatus(200);

        foreach ($attendanceRecords as $attendanceRecord) {
            $attendanceRecord->refresh();
            $formattedDate = Carbon::parse($attendanceRecord->date)->isoFormat('MM/DD(ddd)');
            $response->assertSeeText($formattedDate);
        }
    }

    public function testUserCanCheckOwnAttendanceRecordsThisMonth(): void
    {
        $user = $this->createTestUser();

        $attendanceRecords = $this->createMultipleAttendanceRecords($user);
        $thisMonth = now()->format('Y/m');

        foreach ($attendanceRecords as $attendanceRecord) {
            $this->assertDatabaseHas('attendance_records', [
                'id' => $attendanceRecord->id,
                'user_id' => $user->id,
                'date' => Carbon::parse($attendanceRecord->date)->format('Y-m-d'),
                'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
            ]);

            $this->assertDatabaseHas('break_records', [
                'attendance_record_id' => $attendanceRecord->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('attendance.list'));
        $response->assertStatus(200);

        $response->assertSeeText($thisMonth);
        foreach ($attendanceRecords as $attendanceRecord) {
            $attendanceRecord->refresh();
            $formattedDate = Carbon::parse($attendanceRecord->date)->isoFormat('MM/DD(ddd)');
            $response->assertSeeText($formattedDate);
        }
    }

    public function testUserCanCheckOwnAttendanceRecordsPreviousMonth(): void
    {
        $user = $this->createTestUser();

        $attendanceRecords = $this->createMultipleAttendanceRecords($user, true, false);
        $previousMonth = now()->subMonth()->format('Y/m');

        foreach ($attendanceRecords as $attendanceRecord) {
            $this->assertDatabaseHas('attendance_records', [
                'id' => $attendanceRecord->id,
                'user_id' => $user->id,
                'date' => Carbon::parse($attendanceRecord->date)->format('Y-m-d'),
                'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
            ]);

            $this->assertDatabaseHas('break_records', [
                'attendance_record_id' => $attendanceRecord->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('attendance.list'));
        $response->assertStatus(200);
        $response = $this->get(route('attendance.list', ['month' => $previousMonth]));
        $response->assertStatus(200);

        $response->assertSeeText($previousMonth);
        foreach ($attendanceRecords as $attendanceRecord) {
            $attendanceRecord->refresh();
            $formattedDate = Carbon::parse($attendanceRecord->date)->isoFormat('MM/DD(ddd)');
            $response->assertSeeText($formattedDate);
        }
    }

    public function testUserCanCheckOwnAttendanceRecordsNextMonth(): void
    {
        $user = $this->createTestUser();

        $attendanceRecords = $this->createMultipleAttendanceRecords($user, false, true);
        $nextMonth = now()->addMonth()->format('Y/m');

        foreach ($attendanceRecords as $attendanceRecord) {
            $this->assertDatabaseHas('attendance_records', [
                'id' => $attendanceRecord->id,
                'user_id' => $user->id,
                'date' => Carbon::parse($attendanceRecord->date)->format('Y-m-d'),
                'work_status' => AttendanceRecord::STATUS_FINISHED_WORK,
            ]);

            $this->assertDatabaseHas('break_records', [
                'attendance_record_id' => $attendanceRecord->id,
            ]);
        }

        $response = $this->actingAs($user)->get(route('attendance.list'));
        $response->assertStatus(200);
        $response = $this->get(route('attendance.list', ['month' => $nextMonth]));
        $response->assertStatus(200);

        $response->assertSeeText($nextMonth);
        foreach ($attendanceRecords as $attendanceRecord) {
            $attendanceRecord->refresh();
            $formattedDate = Carbon::parse($attendanceRecord->date)->isoFormat('MM/DD(ddd)');
            $response->assertSeeText($formattedDate);
        }
    }

    public function testUserCanAccessAttendanceRecordDetail(): void
    {
        $user = $this->createTestUser();

        $attendanceRecord = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'work_status' => AttendanceRecord::STATUS_AT_WORK,
        ]);

        $attendanceRecord->formattedYear = Carbon::parse($attendanceRecord->date)->format('Y');
        $attendanceRecord->formattedDate = Carbon::parse($attendanceRecord->date)->isoFormat('M月D日');

        $response = $this->actingAs($user)->get(route('attendance.list'));
        $response->assertStatus(200);

        $response = $this->get(route('attendance.detail', ['attendanceId' => $attendanceRecord->id]));
        $response->assertStatus(200);
        $response->assertSee('<option value="' . $attendanceRecord->formattedYear . '" selected>', false);
        $response->assertSee('<option value="' . $attendanceRecord->formattedDate . '" selected>', false);
    }
}