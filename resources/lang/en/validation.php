<?php

return [

    'confirmed' => 'The :attribute confirmation does not match.',
    'date_format' => 'The :attribute does not match the format :format.',
    'email' => 'The :attribute must be a valid email address.',
    'integer' => 'The :attribute must be an integer.',
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'min' => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'required' => 'The :attribute field is required.',

    'custom' => [
        /* people */
        'birth_date_from' => [
            'required_with' => 'Birth date range beginning is required when ending is present.',
        ],
        'birth_date_to' => [
            'required_with' => 'Birth date range ending is required when beginning is present.',
            'after_or_equal' => 'Birth date range ending must be a date after or equal to beginning.',
        ],

        'death_date_from' => [
            'required_with' => 'Death date range beginning is required when ending is present.',
        ],
        'death_date_to' => [
            'required_with' => 'Death date range ending is required when beginning is present.',
            'after_or_equal' => 'Death date range ending must be a date after or equal to beginning.',
        ],

        'funeral_date_from' => [
            'required_with' => 'Funeral date range beginning is required when ending is present.',
        ],
        'funeral_date_to' => [
            'required_with' => 'Funeral date range ending is required when beginning is present.',
            'after_or_equal' => 'Funeral date range ending must be a date after or equal to beginning.',
        ],

        'burial_date_from' => [
            'required_with' => 'Burial date range beginning is required when ending is present.',
        ],
        'burial_date_to' => [
            'required_with' => 'Burial date range ending is required when beginning is present.',
            'after_or_equal' => 'Burial date range ending must be a date after or equal to beginning.',
        ],

        /* marriages */
        'first_event_date_from' => [
            'required_with' => 'First event date range beginning is required when ending is present.',
        ],
        'first_event_date_to' => [
            'required_with' => 'First event date range ending is required when beginning is present.',
            'after_or_equal' => 'First event date range ending must be a date after or equal to beginning.',
        ],

        'second_event_date_from' => [
            'required_with' => 'Second event date range beginning is required when ending is present.',
        ],
        'second_event_date_to' => [
            'required_with' => 'Second event date range ending is required when beginning is present.',
            'after_or_equal' => 'Second event date range ending must be a date after or equal to beginning.',
        ],

        'end_date_from' => [
            'required_with' => 'Divorce date range beginning is required when ending is present.',
        ],
        'end_date_to' => [
            'required_with' => 'Divorce date range ending is required when beginning is present.',
            'after_or_equal' => 'Divorce date range ending must be a date after or equal to beginning.',
        ],
    ],

    'attributes' => [
        /* people */
        'id_wielcy' => 'id in wielcy.pl',
        'id_pytlewski' => 'id in pytlewski.pl',

        'birth_date_from' => 'birth date',
        'birth_date_to' => 'birth date',
        'death_date_from' => 'death date',
        'death_date_to' => 'death date',
        'funeral_date_from' => 'funeral date',
        'funeral_date_to' => 'funeral date',
        'burial_date_from' => 'burial date',
        'burial_date_to' => 'burial date',

        /* marriages */
        'woman_order' => 'order in woman marriages',
        'man_order' => 'order in man marriages',

        'first_event_date_from' => 'first event date',
        'first_event_date_to' => 'first event date',
        'second_event_date_from' => 'second event date',
        'second_event_date_to' => 'second event date',
        'end_date_from' => 'divorce date',
        'end_date_to' => 'divorce date',
        'end_cause' => 'divorce cause',

        /* settings */
        'logout_password' => 'password',
    ],

];
