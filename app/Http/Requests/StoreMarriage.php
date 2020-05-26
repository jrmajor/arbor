<?php

namespace App\Http\Requests;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
use App\Marriage;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Enum\Laravel\Rules\EnumRule;

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
            'rite' => [new EnumRule(MarriageRiteEnum::class), 'nullable'],

            'first_event_type' => [new EnumRule(MarriageEventTypeEnum::class), 'nullable'],
            'first_event_date_from' => [
                'date_format:Y-m-d',
                'required_with:first_event_date_to',
                'nullable'
            ],
            'first_event_date_to' => [
                'date_format:Y-m-d',
                'required_with:first_event_date_from',
                'after_or_equal:first_event_date_from',
                'nullable'
            ],
            'first_event_place' => 'string|max:100|nullable',

            'second_event_type' => [new EnumRule(MarriageEventTypeEnum::class), 'nullable'],
            'second_event_date_from' => [
                'date_format:Y-m-d',
                'required_with:second_event_date_to',
                'nullable'
            ],
            'second_event_date_to' => [
                'date_format:Y-m-d',
                'required_with:second_event_date_from',
                'after_or_equal:second_event_date_from',
                'nullable'
            ],
            'second_event_place' => 'string|max:100|nullable',

            'ended' => 'boolean',
            'end_cause' => 'string|max:100|nullable',
            'end_date_from' => [
                'date_format:Y-m-d',
                'required_with:end_date_to',
                'nullable'
            ],
            'end_date_to' => [
                'date_format:Y-m-d',
                'required_with:first_event_date_from',
                'after_or_equal:end_date_from',
                'nullable'
            ],
        ];
    }
}
