<?php

namespace App\Services;

use App\Models\DailyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExportService
{
    public function exportReports($reports, array $filters = [])
    {
        $export = new ReportsExport($reports, $filters);
        
        $filename = 'reports_' . date('Y-m-d_His') . '.xlsx';
        
        return Excel::download($export, $filename);
    }
}

class ReportsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $reports;
    protected $filters;

    public function __construct($reports, array $filters = [])
    {
        $this->reports = $reports;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'Tanggal Report',
            'To Do List',
            'Supervisor',
            'Karyawan Masuk',
            'Karyawan Tidak Masuk',
            'Stock Finish Good',
            'Stock Raw Material',
            'Kondisi Gudang',
            'Supplier',
        ];
    }

    public function map($report): array
    {
        // Stock Finish Good
        $stockFinishGood = $report->stockFinishGood->map(function ($item) {
            return $item->item_name . ' (' . $item->quantity . ' karton)';
        })->implode(', ');

        // Stock Raw Material
        $stockRawMaterial = $report->stockRawMaterial->map(function ($item) {
            return $item->item_name . ' (' . $item->quantity . ' kg)';
        })->implode(', ');

        // Warehouse Conditions
        $checkLabels = [
            1 => 'Sangat Bersih',
            2 => 'Bersih',
            3 => 'Cukup Bersih',
            4 => 'Kurang Bersih',
            5 => 'Tidak Bersih'
        ];
        
        $warehouseConditions = $report->warehouseConditions->map(function ($condition) use ($checkLabels) {
            $checks = [];
            if ($condition->check_1) $checks[] = $checkLabels[1];
            if ($condition->check_2) $checks[] = $checkLabels[2];
            if ($condition->check_3) $checks[] = $checkLabels[3];
            if ($condition->check_4) $checks[] = $checkLabels[4];
            if ($condition->check_5) $checks[] = $checkLabels[5];
            return strtoupper($condition->warehouse) . ': ' . implode(', ', $checks);
        })->implode(' | ');

        // Suppliers
        $suppliers = $report->suppliers->map(function ($supplier) {
            $text = $supplier->supplier_name;
            if ($supplier->jenis_barang) {
                $text .= ' (' . $supplier->jenis_barang . ')';
            }
            return $text;
        })->implode(', ');

        return [
            $report->report_date->format('Y-m-d'),
            $report->todoList->title,
            $report->supervisor->name,
            $report->manPower->employees_present ?? 0,
            $report->manPower->employees_absent ?? 0,
            $stockFinishGood ?: '-',
            $stockRawMaterial ?: '-',
            $warehouseConditions ?: '-',
            $suppliers ?: '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

