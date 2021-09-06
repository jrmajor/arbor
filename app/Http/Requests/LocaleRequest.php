<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LocaleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $supportedLocales = implode(',', config('app.available_locales'));

        return ['language' => ['required', "in:{$supportedLocales}"]];
    }

    public function locale(): string
    {
        return $this->validated()['language'];
    }
}
