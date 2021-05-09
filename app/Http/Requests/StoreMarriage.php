<?php

namespace App\Http\Requests;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreMarriage extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'woman_id' => 'required|integer|exists:people,id',
            'woman_order' => 'integer|nullable',
            'man_id' => 'required|integer|exists:people,id',
            'man_order' => 'integer|nullable',
            'rite' => [MarriageRiteEnum::toRule(), 'nullable'],

            'first_event_type' => [MarriageEventTypeEnum::toRule(), 'nullable'],
            'first_event_date_from' => [
                'date_format:Y-m-d',
                'required_with:first_event_date_to',
                'nullable',
            ],
            'first_event_date_to' => [
                'date_format:Y-m-d',
                'required_with:first_event_date_from',
                'after_or_equal:first_event_date_from',
                'nullable',
            ],
            'first_event_place' => 'string|max:100|nullable',

            'second_event_type' => [MarriageEventTypeEnum::toRule(), 'nullable'],
            'second_event_date_from' => [
                'date_format:Y-m-d',
                'required_with:second_event_date_to',
                'nullable',
            ],
            'second_event_date_to' => [
                'date_format:Y-m-d',
                'required_with:second_event_date_from',
                'after_or_equal:second_event_date_from',
                'nullable',
            ],
            'second_event_place' => 'string|max:100|nullable',

            'divorced' => 'boolean',
            'divorce_date_from' => [
                'date_format:Y-m-d',
                'required_with:divorce_date_to',
                'nullable',
            ],
            'divorce_date_to' => [
                'date_format:Y-m-d',
                'required_with:divorce_date_from',
                'after_or_equal:divorce_date_from',
                'nullable',
            ],
            'divorce_place' => 'string|max:100|nullable',
        ];
    }
}
