<?php

return [
    'enum' => 'The :attribute field is not a valid :enum.',
    'enum_index' => 'The :attribute field is not a valid index of :enum.',
    'enum_name' => 'The :attribute field is not a valid name of :enum.',
    'enum_value' => 'The :attribute field is not a valid value of :enum.',

    'enums' => [
        \App\Enums\MarriageTypeEnum::class => [
            'slugged_name' => 'translated value',
            'slugged_other_name' => 'translated other value',
        ],
    ],
];
