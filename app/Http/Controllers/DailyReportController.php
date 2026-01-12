<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyReportRequest;
use App\Http\Requests\UpdateDailyReportRequest;
use App\Models\DailyReport;
use App\Models\TodoList;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DailyReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $reports = DailyReport::with(['todoList', 'supervisor', 'manPower', 'stockFinishGood', 'stockRawMaterial', 'warehouseConditions', 'suppliers'])
            ->where('supervisor_id', auth()->id())
            ->latest('report_date')
            ->paginate(15);

        return view('daily-reports.index', compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TodoList $todoList): View
    {
        // Ensure the todo is assigned to the current supervisor
        $todoList->load('supervisors');
        if (!$todoList->supervisors->contains(auth()->id())) {
            abort(403, 'To Do List ini tidak ditugaskan kepada Anda.');
        }

        return view('daily-reports.create', compact('todoList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDailyReportRequest $request): RedirectResponse
    {
        try {
            // Check if report already exists for this todo, supervisor, and date
            $existingReport = DailyReport::where('todo_list_id', $request->todo_list_id)
                ->where('supervisor_id', $request->user()->id)
                ->whereDate('report_date', $request->report_date)
                ->first();

            if ($existingReport) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Report untuk todo ini pada tanggal ' . \Carbon\Carbon::parse($request->report_date)->format('d M Y') . ' sudah ada. Silakan edit report yang sudah ada atau pilih tanggal lain.');
            }

            $dailyReport = DailyReport::create([
                'todo_list_id' => $request->todo_list_id,
                'supervisor_id' => $request->user()->id,
                'report_date' => $request->report_date,
                'status' => 'completed',
            ]);

        // Save Man Power
        if ($request->has('man_power')) {
            $dailyReport->manPower()->create($request->man_power);
        }

        // Save Stock Finish Good
        if ($request->has('stock_finish_good') && is_array($request->stock_finish_good)) {
            foreach ($request->stock_finish_good as $item) {
                if (!empty($item['item_name'])) {
                    $dailyReport->stockFinishGood()->create($item);
                }
            }
        }

        // Save Stock Raw Material
        if ($request->has('stock_raw_material') && is_array($request->stock_raw_material)) {
            foreach ($request->stock_raw_material as $item) {
                if (!empty($item['item_name'])) {
                    $dailyReport->stockRawMaterial()->create($item);
                }
            }
        }

        // Save Warehouse Conditions
        if ($request->has('warehouse_conditions') && is_array($request->warehouse_conditions)) {
            foreach ($request->warehouse_conditions as $condition) {
                if (!empty($condition['warehouse'])) {
                    // Convert checkbox values to boolean (mapping from view field names to database field names)
                    $check1 = isset($condition['sangat_bersih']) && $condition['sangat_bersih'] == '1';
                    $check2 = isset($condition['bersih']) && $condition['bersih'] == '1';
                    $check3 = isset($condition['cukup_bersih']) && $condition['cukup_bersih'] == '1';
                    $check4 = isset($condition['kurang_bersih']) && $condition['kurang_bersih'] == '1';
                    $check5 = isset($condition['tidak_bersih']) && $condition['tidak_bersih'] == '1';
                    
                    // Only save if at least one checkbox is checked
                    if ($check1 || $check2 || $check3 || $check4 || $check5) {
                        $conditionData = [
                            'warehouse' => $condition['warehouse'],
                            'check_1' => $check1,
                            'check_2' => $check2,
                            'check_3' => $check3,
                            'check_4' => $check4,
                            'check_5' => $check5,
                            'notes' => $condition['notes'] ?? null,
                        ];
                        $dailyReport->warehouseConditions()->create($conditionData);
                    }
                }
            }
        }

        // Save Suppliers
        if ($request->has('suppliers') && is_array($request->suppliers)) {
            foreach ($request->suppliers as $supplier) {
                if (!empty($supplier['supplier_name'])) {
                    $dailyReport->suppliers()->create($supplier);
                }
            }
        }

            return redirect()->route('supervisor.todos')
                ->with('success', 'Report berhasil disimpan.');
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate entry error specifically
            if ($e->getCode() == 23000 && str_contains($e->getMessage(), 'Duplicate entry')) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Report untuk todo ini pada tanggal ' . \Carbon\Carbon::parse($request->report_date)->format('d M Y') . ' sudah ada. Silakan edit report yang sudah ada atau pilih tanggal lain.');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan report: ' . $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan report: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DailyReport $dailyReport): View
    {
        // Ensure only the supervisor who created the report or super admin can view it
        if ($dailyReport->supervisor_id !== auth()->id() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $dailyReport->load([
            'todoList.createdBy',
            'supervisor',
            'manPower',
            'stockFinishGood',
            'stockRawMaterial',
            'warehouseConditions',
            'suppliers',
        ]);

        return view('daily-reports.show', compact('dailyReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DailyReport $dailyReport): View
    {
        // Ensure only the supervisor who created the report can edit it
        if ($dailyReport->supervisor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $dailyReport->load([
            'todoList',
            'manPower',
            'stockFinishGood',
            'stockRawMaterial',
            'warehouseConditions',
            'suppliers',
        ]);

        return view('daily-reports.edit', compact('dailyReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDailyReportRequest $request, DailyReport $dailyReport): RedirectResponse
    {
        // Ensure only the supervisor who created the report can update it
        if ($dailyReport->supervisor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $dailyReport->update($request->only(['report_date', 'status']));

        // Update Man Power
        if ($request->has('man_power')) {
            $dailyReport->manPower()->updateOrCreate(
                ['daily_report_id' => $dailyReport->id],
                $request->man_power
            );
        }

        // Update Stock Finish Good (delete and recreate)
        if ($request->has('stock_finish_good')) {
            $dailyReport->stockFinishGood()->delete();
            foreach ($request->stock_finish_good as $item) {
                if (!empty($item['item_name'])) {
                    $dailyReport->stockFinishGood()->create($item);
                }
            }
        }

        // Update Stock Raw Material (delete and recreate)
        if ($request->has('stock_raw_material')) {
            $dailyReport->stockRawMaterial()->delete();
            foreach ($request->stock_raw_material as $item) {
                if (!empty($item['item_name'])) {
                    $dailyReport->stockRawMaterial()->create($item);
                }
            }
        }

        // Update Warehouse Conditions (delete and recreate)
        if ($request->has('warehouse_conditions')) {
            $dailyReport->warehouseConditions()->delete();
            foreach ($request->warehouse_conditions as $condition) {
                if (!empty($condition['warehouse'])) {
                    // Convert checkbox values to boolean (mapping from view field names to database field names)
                    $check1 = isset($condition['sangat_bersih']) && $condition['sangat_bersih'] == '1';
                    $check2 = isset($condition['bersih']) && $condition['bersih'] == '1';
                    $check3 = isset($condition['cukup_bersih']) && $condition['cukup_bersih'] == '1';
                    $check4 = isset($condition['kurang_bersih']) && $condition['kurang_bersih'] == '1';
                    $check5 = isset($condition['tidak_bersih']) && $condition['tidak_bersih'] == '1';
                    
                    // Only save if at least one checkbox is checked
                    if ($check1 || $check2 || $check3 || $check4 || $check5) {
                        $conditionData = [
                            'warehouse' => $condition['warehouse'],
                            'check_1' => $check1,
                            'check_2' => $check2,
                            'check_3' => $check3,
                            'check_4' => $check4,
                            'check_5' => $check5,
                            'notes' => $condition['notes'] ?? null,
                        ];
                        $dailyReport->warehouseConditions()->create($conditionData);
                    }
                }
            }
        }

        // Update Suppliers (delete and recreate)
        if ($request->has('suppliers')) {
            $dailyReport->suppliers()->delete();
            foreach ($request->suppliers as $supplier) {
                if (!empty($supplier['supplier_name'])) {
                    $dailyReport->suppliers()->create($supplier);
                }
            }
        }

        return redirect()->route('daily-reports.show', $dailyReport)
            ->with('success', 'Report berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DailyReport $dailyReport): RedirectResponse
    {
        // Ensure only the supervisor who created the report can delete it
        if ($dailyReport->supervisor_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $dailyReport->delete();

        return redirect()->route('daily-reports.index')
            ->with('success', 'Report berhasil dihapus.');
    }
}
