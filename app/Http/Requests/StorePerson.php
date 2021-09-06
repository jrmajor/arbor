<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerson extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_wielcy' => 'string|max:20|nullable',
            'id_pytlewski' => 'integer|nullable',

            'sex' => 'in:xx,xy|nullable',
            'name' => 'required|string|max:100',
            'middle_name' => 'string|max:100|nullable',
            'family_name' => 'required|string|max:100',
            'last_name' => 'string|max:100|nullable',

            'mother_id' => 'integer|exists:people,id|nullable',
            'father_id' => 'integer|exists:people,id|nullable',

            'birth_date_from' => [
                'date_format:Y-m-d',
                'required_with:birth_date_to',
                'nullable',
            ],
            'birth_date_to' => [
                'date_format:Y-m-d',
                'required_with:birth_date_from',
                'after_or_equal:birth_date_from',
                'nullable',
            ],
            'birth_place' => 'string|max:100|nullable',

            'dead' => 'boolean',
            'death_date_from' => [
                'date_format:Y-m-d',
                'required_with:death_date_to',
                'nullable',
            ],
            'death_date_to' => [
                'date_format:Y-m-d',
                'required_with:death_date_from',
                'after_or_equal:death_date_from',
                'nullable',
            ],
            'death_place' => 'string|max:100|nullable',
            'death_cause' => 'string|max:100|nullable',

            'funeral_date_from' => [
                'date_format:Y-m-d',
                'required_with:funeral_date_to',
                'nullable',
            ],
            'funeral_date_to' => [
                'date_format:Y-m-d',
                'required_with:funeral_date_from',
                'after_or_equal:funeral_date_from',
                'nullable',
            ],
            'funeral_place' => 'string|max:100|nullable',

            'burial_date_from' => [
                'date_format:Y-m-d',
                'required_with:burial_date_to',
                'nullable',
            ],
            'burial_date_to' => [
                'date_format:Y-m-d',
                'required_with:burial_date_from',
                'after_or_equal:burial_date_from',
                'nullable',
            ],
            'burial_place' => 'string|max:100|nullable',

            'sources' => 'array|nullable',
            'sources.*' => 'string|max:256|nullable',
        ];
    }
}
