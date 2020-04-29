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
            'first_event_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
            'second_event_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
            'first_event_place' => 'string|max:100|nullable',
            'second_event_place' => 'string|max:100|nullable',
            'ended' => 'boolean',
            'end_cause' => 'string|max:100|nullable',
            'end_date' => ['regex:/^[0-9]{3,4}(-[0-9]{2}(-[0-9]{2})?)?$/m', 'nullable'],
        ];
    }
}
