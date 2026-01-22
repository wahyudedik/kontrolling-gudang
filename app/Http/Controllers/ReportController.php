<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterReportRequest;
use App\Models\DailyReport;
use App\Services\ExcelExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{

    /**
     * Display reports with filters
     */
    public function index(FilterReportRequest $request): View
    {
        $query = DailyReport::with([
            'todoList.createdBy',
            'supervisor',
            'manPower',
            'stockFinishGood',
            'stockRawMaterial',
            'warehouseConditions',
            'suppliers',
        ]);

        // Filter by due date (todo due_date)
        if ($request->filled('due_date')) {
            $query->whereHas('todoList', function ($q) use ($request) {
                $q->whereDate('due_date', $request->due_date);
            });
        }

        // Filter by task/todo list
        if ($request->filled('task_id')) {
            $query->where('todo_list_id', $request->task_id);
        }

        // Filter by supervisor
        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }

        $reports = $query->latest('report_date')->paginate(20);

        // Get data for filter dropdowns
        $todoLists = \App\Models\TodoList::active()->orderBy('title')->get();
        $supervisors = \App\Models\User::where('role', 'supervisor')->orderBy('name')->get();

        return view('reports.index', compact('reports', 'todoLists', 'supervisors'));
    }

    /**
     * Export reports to Excel
     */
    public function export(FilterReportRequest $request)
    {
        $query = DailyReport::with([
            'todoList.createdBy',
            'supervisor',
            'manPower',
            'stockFinishGood',
            'stockRawMaterial',
            'warehouseConditions',
            'suppliers',
        ]);

        // Apply same filters as index
        if ($request->filled('due_date')) {
            $query->whereHas('todoList', function ($q) use ($request) {
                $q->whereDate('due_date', $request->due_date);
            });
        }
        if ($request->filled('task_id')) {
            $query->where('todo_list_id', $request->task_id);
        }
        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }

        $reports = $query->get();

        $excelExportService = new \App\Services\ExcelExportService();
        return $excelExportService->exportReports($reports, $request->all());
    }

    /**
     * Export reports to PDF
     */
    public function exportPdf(FilterReportRequest $request)
    {
        $query = DailyReport::with([
            'todoList.createdBy',
            'supervisor',
            'manPower',
            'stockFinishGood',
            'stockRawMaterial',
            'warehouseConditions',
            'suppliers',
        ]);

        // Apply same filters
        if ($request->filled('due_date')) {
            $query->whereHas('todoList', function ($q) use ($request) {
                $q->whereDate('due_date', $request->due_date);
            });
        }
        if ($request->filled('task_id')) {
            $query->where('todo_list_id', $request->task_id);
        }
        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->supervisor_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('report_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('report_date', '<=', $request->to_date);
        }

        $reports = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('reports'));
        return $pdf->download('daily-reports.pdf');
    }
}
