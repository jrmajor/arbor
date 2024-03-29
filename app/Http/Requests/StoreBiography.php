<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBiography extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return ['biography' => ['string', 'max:10000', 'nullable']];
    }

    public function biography(): ?string
    {
        return $this->validated()['biography'];
    }
}
