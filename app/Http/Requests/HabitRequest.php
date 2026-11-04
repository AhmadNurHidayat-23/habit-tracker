<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HabitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['name' => 'required|string|max:100', 'description' => 'nullable|string|max:500'];
    }

    public function messages(): array
    {
        return ['required' => 'Nama kebiasaan wajib diisi.', 'string' => ':attribute harus berupa teks.', 'max' => ':attribute maksimal :max karakter.'];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama kebiasaan', 'description' => 'Deskripsi'];
    }
}
