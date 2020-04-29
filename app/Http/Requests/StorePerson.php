<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerson extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_wielcy' => 'string|max:20|nullable',
            'id_pytlewski' => 'integer|nullable',
            'sex' => ['regex:/x(y|x)/', 'nullable'],
            'name' => 'required|string|max:100',
            'middle_name' => 'string|max:100|nullable',
            'family_name' => 'required|string|max:100',
            'last_name' => 'string|max:100|nullable',
            'mother_id' => 'integer|exists:people,id|nullable',
            'father_id' => 'integer|exists:people,id|nullable',
            'birth_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
            'birth_place' => 'string|max:100|nullable',
            'dead' => 'boolean',
            'death_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
            'death_place' => 'string|max:100|nullable',
            'death_cause' => 'string|max:100|nullable',
            'funeral_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
            'funeral_place' => 'string|max:100|nullable',
            'burial_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
            'burial_place' => 'string|max:100|nullable',
        ];
    }
}
