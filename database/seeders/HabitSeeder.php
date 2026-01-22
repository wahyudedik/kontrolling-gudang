<?php

namespace Database\Seeders;

use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Seeder;

class HabitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cari atau buat Super Admin untuk menjadi creator
        $admin = User::where('role', 'super_admin')->first();

        if (!$admin) {
            // Fallback jika tidak ada admin, ambil user pertama
            $admin = User::first();
        }

        // Cek apakah sudah ada Master Habit
        $habit = TodoList::where('title', 'Cek Rutin Gudang (Harian)')->first();

        if (!$habit && $admin) {
            $habit = TodoList::create([
                'title' => 'Cek Rutin Gudang (Harian)',
                'type' => 'daily', // Asumsi ada tipe ini atau string bebas
                'date' => now(),
                'due_date' => '2030-12-31', // Berlaku selamanya
                'created_by' => $admin->id,
                'is_active' => true,
                'difficulty_level' => 'medium',
                'streak_count' => 0,
            ]);

            // Buat item default
            $defaultItems = [
                ['item_type' => 'man_power', 'order' => 1],
                ['item_type' => 'stock_finish_good', 'order' => 2],
                ['item_type' => 'stock_raw_material', 'order' => 3],
                ['item_type' => 'warehouse_condition', 'order' => 4],
                ['item_type' => 'supplier', 'order' => 5],
            ];

            foreach ($defaultItems as $item) {
                $habit->items()->create($item);
            }

            $this->command->info('Master Habit "Cek Rutin Gudang (Harian)" berhasil dibuat.');
        } else {
            $this->command->info('Master Habit sudah ada.');
        }

        // Assign ke SEMUA supervisor yang ada
        if ($habit) {
            $supervisors = User::where('role', 'supervisor')->get();
            $habit->supervisors()->syncWithoutDetaching($supervisors->pluck('id'));
            $this->command->info('Master Habit ditugaskan ke semua supervisor.');
        }
    }
}
