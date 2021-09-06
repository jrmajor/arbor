<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocaleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'language' => ['required', Rule::in(config('app.available_locales'))],
        ];
    }

    public function locale(): string
    {
        return $this->validated()['language'];
    }

    public function authorize(): bool
    {
        return true;
    }
}
