<?php

return [

    'date_format' => ':Attribute nie jest podana w formacie :format.',
    'email' => ':Attribute musi być poprawnym adresem email.',
    'integer' => ':Attribute musi być liczbą całkowitą.',
    'max' => [
        'string' => ':Attribute nie może mieć więcej niż :max znaków.',
    ],
    'min' => [
        'string' => ':Attribute musi mieć co najmniej :min znaków.',
    ],
    'required' => ':Attribute nie może być puste.',

    'custom' => [
        /* user accounts and logins */
        'password' => [
            'confirmed' => 'Podane hasła nie są identyczne.',
        ],

        /* people */
        'birth_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty urodzenia nie może być puste jeśli podano zakończenie.',
        ],
        'birth_date_to' => [
            'required_with' => 'Zakończenie zakresu daty urodzenia nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty urodzenia musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],

        'death_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty śmierci nie może być puste jeśli podano zakończenie.',
        ],
        'death_date_to' => [
            'required_with' => 'Zakończenie zakresu daty śmierci nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty śmierci musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],

        'funeral_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty pogrzebu nie może być puste jeśli podano zakończenie.',
        ],
        'funeral_date_to' => [
            'required_with' => 'Zakończenie zakresu daty pogrzebu nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty pogrzebu musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],

        'burial_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty pochówku nie może być puste jeśli podano zakończenie.',
        ],
        'burial_date_to' => [
            'required_with' => 'Zakończenie zakresu daty pochówku nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty pochówku musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],

        /* marriages */
        'first_event_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty pierwszego wydarzenia nie może być puste jeśli podano zakończenie.',
        ],
        'first_event_date_to' => [
            'required_with' => 'Zakończenie zakresu daty pierwszego wydarzenia nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty pierwszego wydarzenia musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],

        'second_event_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty drugiego wydarzenia nie może być puste jeśli podano zakończenie.',
        ],
        'second_event_date_to' => [
            'required_with' => 'Zakończenie zakresu daty drugiego wydarzenia nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty drugiego wydarzenia musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],

        'divorce_date_from' => [
            'required_with' => 'Rozpoczęcie zakresu daty rozwodu nie może być puste jeśli podano zakończenie.',
        ],
        'divorce_date_to' => [
            'required_with' => 'Zakończenie zakresu daty rozwodu nie może być puste jeśli podano rozpoczęcie.',
            'after_or_equal' => 'Zakończenie zakresu daty rozwodu musi być datą taką samą lub późniejszą niż rozpoczęcie.',
        ],
    ],

    'attributes' => [
        /* user accounts and logins */
        'password' => 'hasło',

        /* people */
        'id_wielcy' => 'id w wielcy.pl',
        'id_pytlewski' => 'id w pytlewski.pl',

        'sex' => 'płeć',
        'name' => 'imię',
        'middle_name' => 'drugie imię',
        'family_name' => 'nazwisko rodowe',
        'last_name' => 'zmienione nazwisko',

        'birth_date_from' => 'data urodzenia',
        'birth_date_to' => 'data urodzenia',
        'birth_place' => 'miejsce urodzenia',

        'death_date_from' => 'data śmierci',
        'death_date_to' => 'data śmierci',
        'death_place' => 'miejsce śmierci',
        'death_cause' => 'powód śmierci',

        'funeral_date_from' => 'data pogrzebu',
        'funeral_date_to' => 'data pogrzebu',
        'funeral_place' => 'miejsce pogrzebu',

        'burial_date_from' => 'data pochówku',
        'burial_date_to' => 'data pochówku',
        'burial_place' => 'miejsce pochówku',

        'sources.*' => 'źródło',

        'biography' => 'biografia',

        /* marriages */
        'woman_order' => 'kolejność w związkach kobiety',
        'man_order' => 'kolejność w związkach mężczyzny',

        'first_event_date_from' => 'data pierwszego wydarzenia',
        'first_event_date_to' => 'data pierwszego wydarzenia',
        'second_event_date_from' => 'data drugiego wydarzenia',
        'second_event_date_to' => 'data drugiego wydarzenia',
        'divorce_date_from' => 'data rozwodu',
        'divorce_date_to' => 'data rozwodu',

        'first_event_place' => 'miejsce pierwszego wydarzenia',
        'second_event_place' => 'miejsce drugiego wydarzenia',
        'divorce_place' => 'miejsce rozwodu',

        /* settings */
        'logout_password' => 'hasło',
    ],

];
