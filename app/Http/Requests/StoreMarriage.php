<?php

namespace App\Http\Requests;

use App\Enums\MarriageEventTypeEnum;
use App\Enums\MarriageRiteEnum;
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
            'woman_id' => 'integer|exists:people,id',
            'woman_order' => 'integer|nullable',
            'man_id' => 'integer|exists:people,id',
            'man_order' => 'integer|nullable',
            'rite' => [new EnumRule(MarriageRiteEnum::class), 'nullable'],
            'first_event_type' => [new EnumRule(MarriageEventTypeEnum::class), 'nullable'],
            'second_event_type' => [new EnumRule(MarriageEventTypeEnum::class), 'nullable'],
            'first_event_date_from' => ['date_format:Y-m-d', 'required_with:first_event_date_to', 'nullable'],
            'first_event_date_to' => ['date_format:Y-m-d', 'after_or_equal:first_event_date_from', 'nullable'],
            'second_event_date_from' => ['date_format:Y-m-d', 'required_with:second_event_date_to', 'nullable'],
            'second_event_date_to' => ['date_format:Y-m-d', 'after_or_equal:second_event_date_from', 'nullable'],
            'first_event_place' => 'string|max:100|nullable',
            'second_event_place' => 'string|max:100|nullable',
            'ended' => 'boolean',
            'end_cause' => 'string|max:100|nullable',
            'end_date_from' => ['date_format:Y-m-d', 'required_with:end_date_to', 'nullable'],
            'end_date_to' => ['date_format:Y-m-d', 'after_or_equal:end_date_from', 'nullable'],
        ];
    }

    protected function prepareForValidation()
    {
        if (
            $this['first_event_date_from'] != null
            && $this['first_event_date_to'] == null
        ) {
            $this['first_event_date_to'] = $this['first_event_date_from'];
        }

        if (
            $this['second_event_date_from'] != null
            && $this['second_event_date_to'] == null
        ) {
            $this['second_event_date_to'] = $this['second_event_date_from'];
        }

        if (
            $this['end_date_from'] != null
            && $this['end_date_to'] == null
        ) {
            $this['end_date_to'] = $this['end_date_from'];
        }
    }
}
