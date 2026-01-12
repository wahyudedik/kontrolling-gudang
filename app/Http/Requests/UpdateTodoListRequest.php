<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTodoListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['man_power', 'finish_good', 'raw_material', 'gudang', 'supplier_datang'])],
            'date' => ['nullable', 'date'],
            'due_date' => ['required', 'date'],
            'is_active' => ['sometimes', 'boolean'],
            'supervisor_ids' => ['required', 'array', 'min:1'],
            'supervisor_ids.*' => ['required', 'uuid', 'exists:users,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul To Do List wajib diisi.',
            'type.required' => 'Tipe To Do List wajib dipilih.',
            'type.in' => 'Tipe To Do List tidak valid.',
            'date.date' => 'Format tanggal tidak valid.',
            'due_date.required' => 'Due Date wajib diisi.',
            'due_date.date' => 'Format Due Date tidak valid.',
        ];
    }
}
