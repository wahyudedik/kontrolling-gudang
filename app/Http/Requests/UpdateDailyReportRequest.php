<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDailyReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isSupervisor() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'report_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:draft,completed'],
            
            // Man Power
            'man_power.employees_present' => ['sometimes', 'integer', 'min:0'],
            'man_power.employees_absent' => ['sometimes', 'integer', 'min:0'],
            
            // Stock Finish Good
            'stock_finish_good' => ['sometimes', 'array'],
            'stock_finish_good.*.item_name' => ['required_with:stock_finish_good', 'string', 'max:255'],
            'stock_finish_good.*.quantity' => ['required_with:stock_finish_good', 'integer', 'min:0'],
            
            // Stock Raw Material
            'stock_raw_material' => ['sometimes', 'array'],
            'stock_raw_material.*.item_name' => ['required_with:stock_raw_material', 'string', 'max:255'],
            'stock_raw_material.*.quantity' => ['required_with:stock_raw_material', 'numeric', 'min:0'],
            
            // Warehouse Condition
            'warehouse_conditions' => ['sometimes', 'array'],
            'warehouse_conditions.*.warehouse' => ['required_with:warehouse_conditions', 'in:cs1,cs2,cs3,cs4,cs5,cs6'],
            'warehouse_conditions.*.sangat_bersih' => ['sometimes', 'boolean'],
            'warehouse_conditions.*.bersih' => ['sometimes', 'boolean'],
            'warehouse_conditions.*.cukup_bersih' => ['sometimes', 'boolean'],
            'warehouse_conditions.*.kurang_bersih' => ['sometimes', 'boolean'],
            'warehouse_conditions.*.tidak_bersih' => ['sometimes', 'boolean'],
            'warehouse_conditions.*.notes' => ['nullable', 'string'],
            
            // Suppliers
            'suppliers' => ['sometimes', 'array'],
            'suppliers.*.supplier_name' => ['required_with:suppliers', 'string', 'max:255'],
            'suppliers.*.jenis_barang' => ['nullable', 'string', 'max:255'],
        ];
    }
}
