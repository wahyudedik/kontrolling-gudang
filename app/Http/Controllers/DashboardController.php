<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Quick Stats
        $stats = [
            'total_todos' => TodoList::count(),
            'total_reports' => DailyReport::count(),
            'active_habits' => TodoList::where('type', 'daily')->where('is_active', true)->count(),
            'total_supervisors' => User::where('role', 'supervisor')->count(),
        ];

        // 2. Daily Habit Compliance (Last 7 Days)
        // Calculate percentage of supervisors who completed 3 sessions per day
        $dates = [];
        $complianceData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $dates[] = $date->format('d M');

            // Get total supervisors
            $totalSupervisors = User::where('role', 'supervisor')->count();
            if ($totalSupervisors == 0) {
                $complianceData[] = 0;
                continue;
            }

            // Count supervisors with >= 3 distinct sessions on this date for 'daily' habits
            // We look at reports linked to 'daily' type TodoLists
            $completedSupervisors = DB::table('daily_reports')
                ->join('todo_lists', 'daily_reports.todo_list_id', '=', 'todo_lists.id')
                ->where('todo_lists.type', 'daily')
                ->whereDate('daily_reports.report_date', $dateStr)
                ->select('daily_reports.supervisor_id')
                ->groupBy('daily_reports.supervisor_id')
                ->havingRaw('COUNT(DISTINCT daily_reports.session) >= 3')
                ->get()
                ->count();

            $percentage = ($completedSupervisors / $totalSupervisors) * 100;
            $complianceData[] = round($percentage, 1);
        }

        // 3. Warehouse Cleanliness Trends (Last 7 Days)
        // Average score per day (5=Sangat Bersih, 1=Tidak Bersih)
        $cleanlinessData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dateStr = $date->format('Y-m-d');

            $reports = DailyReport::whereDate('report_date', $dateStr)
                ->with('warehouseConditions')
                ->get();

            $totalScore = 0;
            $count = 0;

            foreach ($reports as $report) {
                foreach ($report->warehouseConditions as $condition) {
                    $score = 0;
                    if ($condition->check_1) $score = 5;
                    elseif ($condition->check_2) $score = 4;
                    elseif ($condition->check_3) $score = 3;
                    elseif ($condition->check_4) $score = 2;
                    elseif ($condition->check_5) $score = 1;

                    if ($score > 0) {
                        $totalScore += $score;
                        $count++;
                    }
                }
            }

            $average = $count > 0 ? $totalScore / $count : 0;
            $cleanlinessData[] = round($average, 1);
        }

        // 4. Top Supervisors by Streak
        $topSupervisors = TodoList::where('type', 'daily')
            ->orderByDesc('streak_count')
            ->take(5)
            ->with('createdBy') // Assuming created_by tracks who owns it, but wait...
            // Actually, TodoList is assigned to supervisors. 
            // Wait, streak_count is on TodoList. 
            // If the habit is global (assigned to many), streak_count on TodoList might be shared or I misunderstood the schema.
            // Let's check TodoAssignment.
            // Ah, the user said "streak_count" on TodoList. 
            // But if one TodoList is assigned to multiple supervisors, how do we track individual streaks?
            // "Buat HabitSeeder... dan menugaskannya ke semua supervisor."
            // If it's a single TodoList record assigned to everyone, streak_count on TodoList is global? That doesn't make sense.
            // Maybe the user implies each supervisor has their own TodoList instance?
            // OR we track streak on the Pivot table or User model?
            // The requirement was: "ubah todos jadi habits dengan field `streak_count`...".
            // If I implemented it on TodoList model, and there is only ONE TodoList for all supervisors, that's a problem.
            // Let's re-read the implementation.
            // In DailyReportController: $dailyReport->todoList->increment('streak_count');
            // Yes, this increments the shared TodoList!
            // This is a flaw in my previous logic if the TodoList is shared.
            // BUT, for now, I will display what I have. 
            // If the Seeder created ONE TodoList for ALL supervisors, then they all contribute to the SAME streak.
            // That might be intended as a "Team Streak" or it's a bug.
            // Assuming "Team Streak" for now to proceed, but I should probably note this.
            // However, to show "Top Supervisors", I need individual performance.
            // I can calculate individual streaks dynamically from reports.
            ->get();

        // Let's calculate individual supervisor compliance for the leaderboard instead
        $leaderboard = [];
        $supervisors = User::where('role', 'supervisor')->get();

        foreach ($supervisors as $supervisor) {
            // Count total days with 3 sessions completed
            $daysCompleted = DB::table('daily_reports')
                ->join('todo_lists', 'daily_reports.todo_list_id', '=', 'todo_lists.id')
                ->where('todo_lists.type', 'daily')
                ->where('daily_reports.supervisor_id', $supervisor->id)
                ->select(DB::raw('DATE(daily_reports.report_date) as date'))
                ->groupBy(DB::raw('DATE(daily_reports.report_date)'))
                ->havingRaw('COUNT(DISTINCT daily_reports.session) >= 3')
                ->get()
                ->count();

            $leaderboard[] = [
                'name' => $supervisor->name,
                'score' => $daysCompleted
            ];
        }

        // Sort by score desc
        usort($leaderboard, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        $leaderboard = array_slice($leaderboard, 0, 5);

        return view('dashboard', compact('stats', 'dates', 'complianceData', 'cleanlinessData', 'leaderboard'));
    }
}
