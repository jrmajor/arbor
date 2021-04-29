<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBiography extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return ['biography' => ['string', 'max:10000', 'nullable']];
    }

    public function biography(): ?string
    {
        return $this->validated()['biography'];
    }
}
