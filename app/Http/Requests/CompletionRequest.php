<?php

namespace App\Http\Requests;

use App\Services\HabitProgress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompletionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['date' => ['required', 'string', Rule::in([app(HabitProgress::class)->today()->toDateString()])]];
    }

    public function messages(): array
    {
        return ['required' => 'Tanggal hari ini wajib disertakan.', 'string' => 'Tanggal tidak valid.', 'in' => 'Hari telah berganti atau tanggal tidak valid. Muat ulang halaman untuk mencatat hari ini.'];
    }
}
