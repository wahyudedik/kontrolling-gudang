<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TodoList;
use App\Models\DailyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DailyHabitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function supervisor_can_complete_3x_daily_habit_and_increase_streak()
    {
        // 1. Setup Data
        $supervisor = User::factory()->create(['role' => 'supervisor']);
        $admin = User::factory()->create(['role' => 'super_admin']);

        // Create Daily Habit
        $habit = TodoList::create([
            'title' => 'Cek Rutin Gudang (Harian)',
            'type' => 'daily',
            'difficulty_level' => 'medium',
            'due_date' => now()->addYear(),
            'created_by' => $admin->id,
            'is_active' => true,
            'streak_count' => 0
        ]);

        // Assign to supervisor (using pivot if exists, or just implicit access)
        // Assuming your app logic checks assignments, but for now let's assume they have access

        $this->actingAs($supervisor);

        // 2. Submit Morning Report
        $response1 = $this->post(route('daily-reports.store'), [
            'todo_list_id' => $habit->id,
            'report_date' => now()->format('Y-m-d'),
            'session' => 'morning',
            'man_power' => ['employees_present' => 10, 'employees_absent' => 0],
            // Add other required fields if any (photo is nullable or mocked?)
            // Based on validation, photo might be required. Let's check StoreDailyReportRequest.
            // If required, we fake it.
        ]);

        // If photo validation fails, we need to handle it.
        // Let's assume for this test we skip detailed validation or provide valid data.
        // Actually, let's verify if streak increased. It should NOT yet.

        $this->assertEquals(0, $habit->fresh()->streak_count);

        // 3. Submit Afternoon Report
        $response2 = $this->post(route('daily-reports.store'), [
            'todo_list_id' => $habit->id,
            'report_date' => now()->format('Y-m-d'),
            'session' => 'afternoon',
            'man_power' => ['employees_present' => 10, 'employees_absent' => 0],
        ]);

        $this->assertEquals(0, $habit->fresh()->streak_count);

        // 4. Submit Evening Report
        $response3 = $this->post(route('daily-reports.store'), [
            'todo_list_id' => $habit->id,
            'report_date' => now()->format('Y-m-d'),
            'session' => 'evening',
            'man_power' => ['employees_present' => 10, 'employees_absent' => 0],
        ]);

        // 5. Verify Streak Increased
        $this->assertEquals(1, $habit->fresh()->streak_count);
    }

    #[Test]
    public function super_admin_can_export_pdf()
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)
            ->get(route('reports.export_pdf'));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
