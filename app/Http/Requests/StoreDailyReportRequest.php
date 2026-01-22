<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDailyReportRequest extends FormRequest
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
        $todoList = \App\Models\TodoList::find($this->input('todo_list_id'));
        $todoType = $todoList?->type;

        $rules = [
            'todo_list_id' => ['required', 'uuid', 'exists:todo_lists,id'],
            'report_date' => ['required', 'date'],
            'session' => ['nullable', 'in:morning,afternoon,evening'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'],
        ];

        // Conditional validation based on todo type
        if ($todoType === 'man_power' || $todoType === 'daily') {
            $rules['man_power.employees_present'] = ['required', 'integer', 'min:0'];
            $rules['man_power.employees_absent'] = ['required', 'integer', 'min:0'];
        }

        if ($todoType === 'finish_good' || $todoType === 'daily') {
            $rules['stock_finish_good'] = ['sometimes', 'array'];
            $rules['stock_finish_good.*.item_name'] = ['required_with:stock_finish_good.*.quantity', 'string', 'max:255'];
            $rules['stock_finish_good.*.quantity'] = ['required_with:stock_finish_good.*.item_name', 'integer', 'min:0'];
        }

        if ($todoType === 'raw_material' || $todoType === 'daily') {
            $rules['stock_raw_material'] = ['sometimes', 'array'];
            $rules['stock_raw_material.*.item_name'] = ['required_with:stock_raw_material.*.quantity', 'string', 'max:255'];
            $rules['stock_raw_material.*.quantity'] = ['required_with:stock_raw_material.*.item_name', 'numeric', 'min:0'];
        }

        if ($todoType === 'gudang' || $todoType === 'daily') {
            $rules['warehouse_conditions'] = ['sometimes', 'array'];
            $rules['warehouse_conditions.*.warehouse'] = ['required_with:warehouse_conditions', 'in:cs1,cs2,cs3,cs4,cs5,cs6'];
            $rules['warehouse_conditions.*.sangat_bersih'] = ['sometimes', 'boolean'];
            $rules['warehouse_conditions.*.bersih'] = ['sometimes', 'boolean'];
            $rules['warehouse_conditions.*.cukup_bersih'] = ['sometimes', 'boolean'];
            $rules['warehouse_conditions.*.kurang_bersih'] = ['sometimes', 'boolean'];
            $rules['warehouse_conditions.*.tidak_bersih'] = ['sometimes', 'boolean'];
            $rules['warehouse_conditions.*.notes'] = ['nullable', 'string'];
            $rules['warehouse_conditions.*.photo'] = ['nullable', 'image', 'mimes:jpeg,png', 'max:2048'];
        }

        if ($todoType === 'supplier_datang' || $todoType === 'daily') {
            $rules['suppliers'] = ['sometimes', 'array'];
            $rules['suppliers.*.supplier_name'] = ['required_with:suppliers', 'string', 'max:255'];
            $rules['suppliers.*.jenis_barang'] = ['nullable', 'string', 'max:255'];
        }

        if ($todoType === 'daily') {
            $rules['session'] = ['required', 'in:morning,afternoon,evening'];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'todo_list_id.required' => 'To Do List wajib dipilih.',
            'todo_list_id.exists' => 'To Do List yang dipilih tidak valid.',
            'report_date.required' => 'Tanggal report wajib diisi.',
            'report_date.date' => 'Format tanggal tidak valid.',
            'man_power.employees_present.required' => 'Jumlah karyawan masuk wajib diisi.',
            'man_power.employees_absent.required' => 'Jumlah karyawan tidak masuk wajib diisi.',
        ];
    }
}
