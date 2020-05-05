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
            'birth_date_from' => ['date_format:Y-m-d', 'required_with:birth_date_to', 'nullable'],
            'birth_date_to' => ['date_format:Y-m-d', 'after_or_equal:birth_date_from', 'nullable'],
            'birth_place' => 'string|max:100|nullable',
            'dead' => 'boolean',
            'death_date_from' => ['date_format:Y-m-d', 'required_with:death_date_to', 'nullable'],
            'death_date_to' => ['date_format:Y-m-d', 'after_or_equal:death_date_from', 'nullable'],
            'death_place' => 'string|max:100|nullable',
            'death_cause' => 'string|max:100|nullable',
            'funeral_date_from' => ['date_format:Y-m-d', 'required_with:funeral_date_to', 'nullable'],
            'funeral_date_to' => ['date_format:Y-m-d', 'after_or_equal:funeral_date_from', 'nullable'],
            'funeral_place' => 'string|max:100|nullable',
            'burial_date_from' => ['date_format:Y-m-d', 'required_with:burial_date_to', 'nullable'],
            'burial_date_to' => ['date_format:Y-m-d', 'after_or_equal:burial_date_from', 'nullable'],
            'burial_place' => 'string|max:100|nullable',
        ];
    }

    protected function prepareForValidation()
    {
        if (
            $this['birth_date_from'] != null
            && $this['birth_date_to'] == null
        ) {
            $this['birth_date_to'] = $this['birth_date_from'];
        }

        if (
            $this['death_date_from'] != null
            && $this['death_date_to'] == null
        ) {
            $this['death_date_to'] = $this['death_date_from'];
        }

        if (
            $this['funeral_date_from'] != null
            && $this['funeral_date_to'] == null
        ) {
            $this['funeral_date_to'] = $this['funeral_date_from'];
        }

        if (
            $this['burial_date_from'] != null
            && $this['burial_date_to'] == null
        ) {
            $this['burial_date_to'] = $this['burial_date_from'];
        }
    }
}
