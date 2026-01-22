<?php

namespace Database\Seeders;

use App\Models\DailyReport;
use App\Models\ReportManPower;
use App\Models\ReportStockFinishGood;
use App\Models\ReportStockRawMaterial;
use App\Models\ReportSupplier;
use App\Models\ReportWarehouseCondition;
use App\Models\TodoItem;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WarehouseSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Users
        $this->command->info('Creating users...');
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $supervisors = [];
        for ($i = 1; $i <= 5; $i++) {
            $supervisors[] = User::firstOrCreate(
                ['email' => "supervisor{$i}@gmail.com"],
                [
                    'name' => "Supervisor {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'supervisor',
                ]
            );
        }

        // 2. Create Todo Lists with different types
        $this->command->info('Creating todo lists...');
        $todoTypes = [
            'man_power',
            'finish_good',
            'raw_material',
            'gudang',
            'supplier_datang',
            'daily',
        ];

        $todoLists = [];
        $today = now();

        // Create multiple todo lists for each type (2-3 per type)
        foreach ($todoTypes as $type) {
            $count = rand(2, 3);
            for ($i = 0; $i < $count; $i++) {
                $todoList = TodoList::create([
                    'title' => $this->getTodoTitle($type) . ($i > 0 ? ' - ' . ($i + 1) : ''),
                    'type' => $type,
                    'date' => $today->copy()->subDays(rand(0, 30)),
                    'due_date' => $today->copy()->addDays(rand(1, 14)),
                    'created_by' => $superAdmin->id,
                    'is_active' => rand(0, 10) < 9, // 90% active
                    'difficulty_level' => $type === 'daily' ? ['easy', 'medium', 'hard'][rand(0, 2)] : 'medium',
                    'streak_count' => $type === 'daily' ? rand(0, 30) : 0,
                ]);

                // Create default todo items
                $defaultItems = [
                    ['item_type' => 'man_power', 'order' => 1],
                    ['item_type' => 'stock_finish_good', 'order' => 2],
                    ['item_type' => 'stock_raw_material', 'order' => 3],
                    ['item_type' => 'warehouse_condition', 'order' => 4],
                    ['item_type' => 'supplier', 'order' => 5],
                ];

                foreach ($defaultItems as $item) {
                    TodoItem::create([
                        'todo_list_id' => $todoList->id,
                        'item_type' => $item['item_type'],
                        'order' => $item['order'],
                    ]);
                }

                // Assign to random supervisors
                $assignedSupervisors = collect($supervisors)->random(rand(1, min(3, count($supervisors))));
                $todoList->supervisors()->attach($assignedSupervisors->pluck('id'));

                $todoLists[] = $todoList;
            }
        }

        // 3. Create Daily Reports
        $this->command->info('Creating daily reports...');
        $reportDates = [];
        for ($i = 0; $i < 30; $i++) {
            $reportDates[] = $today->copy()->subDays($i);
        }

        foreach ($reportDates as $reportDate) {
            // Create 3-8 reports per day
            $reportsPerDay = rand(3, 8);

            for ($j = 0; $j < $reportsPerDay; $j++) {
                $todoList = collect($todoLists)->random();

                // Get supervisors assigned to this todo list
                $assignedSupervisors = $todoList->supervisors;
                if ($assignedSupervisors->isEmpty()) {
                    continue;
                }

                $supervisor = $assignedSupervisors->random();

                $session = $todoList->type === 'daily' ? ['morning', 'afternoon', 'evening'][rand(0, 2)] : 'morning';

                // Check if report already exists for this todo and supervisor on this date and session
                $existingReport = DailyReport::where('todo_list_id', $todoList->id)
                    ->where('supervisor_id', $supervisor->id)
                    ->where('report_date', $reportDate->format('Y-m-d'))
                    ->where('session', $session)
                    ->first();

                if ($existingReport) {
                    continue;
                }

                $dailyReport = DailyReport::create([
                    'todo_list_id' => $todoList->id,
                    'supervisor_id' => $supervisor->id,
                    'report_date' => $reportDate,
                    'status' => rand(0, 1) ? 'completed' : 'draft',
                    'session' => $session,
                ]);

                // 4. Create Report Man Power
                ReportManPower::create([
                    'daily_report_id' => $dailyReport->id,
                    'employees_present' => rand(15, 30),
                    'employees_absent' => rand(0, 5),
                ]);

                // 5. Create Report Stock Finish Good (2-5 items)
                $finishGoodItems = [
                    'Produk A - Karton',
                    'Produk B - Karton',
                    'Produk C - Karton',
                    'Produk D - Karton',
                    'Produk E - Karton',
                ];

                $selectedFinishGood = collect($finishGoodItems)->random(rand(2, 5));
                foreach ($selectedFinishGood as $item) {
                    ReportStockFinishGood::create([
                        'daily_report_id' => $dailyReport->id,
                        'item_name' => $item,
                        'quantity' => rand(50, 500),
                    ]);
                }

                // 6. Create Report Stock Raw Material (2-5 items)
                $rawMaterialItems = [
                    'Bahan Baku A',
                    'Bahan Baku B',
                    'Bahan Baku C',
                    'Bahan Baku D',
                    'Bahan Baku E',
                ];

                $selectedRawMaterial = collect($rawMaterialItems)->random(rand(2, 5));
                foreach ($selectedRawMaterial as $item) {
                    ReportStockRawMaterial::create([
                        'daily_report_id' => $dailyReport->id,
                        'item_name' => $item,
                        'quantity' => round(rand(100, 2000) / 10, 2), // Decimal with 2 decimals
                    ]);
                }

                // 7. Create Report Warehouse Condition (for all 6 warehouses)
                $warehouses = ['cs1', 'cs2', 'cs3', 'cs4', 'cs5', 'cs6'];
                foreach ($warehouses as $warehouse) {
                    // Randomly select one condition (only one should be true)
                    $conditionIndex = rand(0, 4);

                    ReportWarehouseCondition::create([
                        'daily_report_id' => $dailyReport->id,
                        'warehouse' => $warehouse,
                        'check_1' => $conditionIndex === 0,
                        'check_2' => $conditionIndex === 1,
                        'check_3' => $conditionIndex === 2,
                        'check_4' => $conditionIndex === 3,
                        'check_5' => $conditionIndex === 4,
                        'notes' => rand(0, 1) ? "Catatan untuk {$warehouse}" : null,
                    ]);
                }

                // 8. Create Report Suppliers (1-4 suppliers)
                $supplierNames = [
                    'PT Supplier A',
                    'PT Supplier B',
                    'PT Supplier C',
                    'PT Supplier D',
                    'PT Supplier E',
                    'CV Supplier F',
                    'CV Supplier G',
                ];

                $selectedSuppliers = collect($supplierNames)->random(rand(1, 4));
                foreach ($selectedSuppliers as $supplierName) {
                    ReportSupplier::create([
                        'daily_report_id' => $dailyReport->id,
                        'supplier_name' => $supplierName,
                    ]);
                }
            }
        }

        $this->command->info('Seeder completed successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Super Admin');
        $this->command->info('- ' . count($supervisors) . ' Supervisors');
        $this->command->info('- ' . count($todoLists) . ' Todo Lists');
        $this->command->info('- ' . DailyReport::count() . ' Daily Reports');
    }

    /**
     * Get title for todo list based on type
     */
    private function getTodoTitle(string $type): string
    {
        $titles = [
            'man_power' => 'Laporan Man Power Harian',
            'finish_good' => 'Laporan Stock Finish Good',
            'raw_material' => 'Laporan Stock Raw Material',
            'gudang' => 'Laporan Kondisi Gudang',
            'supplier_datang' => 'Laporan Supplier Datang',
            'daily' => 'Cek Rutin Gudang (Harian)',
        ];

        return $titles[$type] ?? 'Todo List ' . ucfirst(str_replace('_', ' ', $type));
    }
}
