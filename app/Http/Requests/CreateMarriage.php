<?php

namespace App\Http\Requests;

class CreateMarriage extends EditMarriage
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'woman_id' => ['required', 'integer', 'exists:people,id'],
            'man_id' => ['required', 'integer', 'exists:people,id'],
        ];
    }
}
