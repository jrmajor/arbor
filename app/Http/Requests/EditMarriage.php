<?php

namespace App\Http\Requests;

use App\Enums\MarriageEventType;
use App\Enums\MarriageRite;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class EditMarriage extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'woman_order' => ['integer', 'nullable'],
            'man_order' => ['integer', 'nullable'],
            'rite' => [new Enum(MarriageRite::class), 'nullable'],

            'first_event_type' => [new Enum(MarriageEventType::class), 'nullable'],
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
            'first_event_place' => ['string', 'max:100', 'nullable'],

            'second_event_type' => [new Enum(MarriageEventType::class), 'nullable'],
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
            'second_event_place' => ['string', 'max:100', 'nullable'],

            'divorced' => ['boolean'],
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
            'divorce_place' => ['string', 'max:100', 'nullable'],
        ];
    }
}
