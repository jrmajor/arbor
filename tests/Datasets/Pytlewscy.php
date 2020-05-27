<?php

dataset('pytlewscy', function () {
    yield [
        '556',
        file_get_contents(__DIR__.'/pytlewscy/556.html'),
        [
            'basic_name' => '<b>Major</b> Józef',
            'family_name' => 'Major',
            'last_name' => null,
            'name' => 'Józef',
            'middle_name' => null,
            'birth_date' => '29.11.1893',
            'birth_place' => 'Brodach Iłżeckich',
            'death_date' => '17.07.1951',
            'death_place' => 'Radomiu',
            'mother_id' => null,
            'mother_surname' => 'Gołębiowska',
            'mother_name' => 'Jadwiga',
            'father_id' => null,
            'father_surname' => 'Major',
            'father_name' => 'Jacenty',
            'parents' => '1',
            'photo' => 'http://www.pytlewski.pl/index/drzewo/foto/08202006104940.jpg',
            'total_marriages' => '2',
            'marriages' => [
                [
                    'id' => '1141',
                    'name' => 'Prewysz Kwinto, Anna',
                ],
                [
                    'id' => '555',
                    'name' => 'Trelińska, Maria',
                    'date' => '05.02.1940',
                ],
            ],
            'total_children' => '4',
            'children' => [
                ['name' => 'Jadwiga'],
                ['name' => 'Irena'],
                [
                    'id' => '554',
                    'name' => 'Zbigniew',
                ],
                [
                    'id' => '1140',
                    'name' => 'Bogdan',
                ],
            ],
            'total_siblings' => '4',
            'siblings' => [
                ['name' => 'Jan'],
                ['name' => 'Bronisława'],
                ['name' => 'Stanisław'],
                ['name' => 'Adam'],
            ],
        ],
    ];

    yield [
        '550',
        file_get_contents(__DIR__.'/pytlewscy/550.html'),
        [
            'basic_name' => '<b>Bobińska (Kosela)</b> Iwona',
            'family_name' => 'Kosela',
            'last_name' => 'Bobińska',
            'name' => 'Iwona',
            'middle_name' => null,
            'birth_date' => null,
            'birth_place' => 'Kielcach',
            'death_date' => null,
            'death_place' => null,
            'mother_id' => null,
            'mother_surname' => 'Pytlewska',
            'mother_name' => 'Kornelia',
            'father_id' => '543',
            'father_surname' => 'Kosela',
            'father_name' => 'Franciszek',
            'parents' => '1',
            'photo' => 'http://www.pytlewski.pl/index/drzewo/foto/08202006090640.jpg',
            'total_marriages' => '1',
            'marriages' => [
                [
                    'name' => 'Bobiński, Jarosław',
                    'date' => '20.08.1988',
                    'place' => 'Kielce',
                ],
            ],
            'total_children' => '2',
            'children' => [
                ['name' => 'Patryk'],
                ['name' => 'Chrystian'],
            ],
            'total_siblings' => '1',
            'siblings' => [
                ['name' => 'Wojciech'],
            ],
        ],
    ];

    yield [
        '1928',
        file_get_contents(__DIR__.'/pytlewscy/1928.html'),
        [
            'basic_name' => '<b>Chełmońska (Korwin-Szymanowska)</b> Maria-Ludwika',
            'family_name' => 'Korwin-Szymanowska',
            'last_name' => 'Chełmońska',
            'name' => 'Maria',
            'middle_name' => 'Ludwika',
            'birth_date' => '15.07.1856',
            'birth_place' => 'Żbikowie p.Czechowice',
            'death_date' => '1942',
            'death_place' => 'Gierczycach',
            'mother_id' => null,
            'mother_surname' => 'Sliwińska',
            'mother_name' => 'Elżbieta',
            'father_id' => null,
            'father_surname' => 'Szymanowski',
            'father_name' => 'Józef',
            'parents' => '1',
            'photo' => null,
            'total_marriages' => '1',
            'marriages' => [
                [
                    'id' => '1927',
                    'name' => 'Chełmoński, Józef-Marian',
                    'date' => '16.06.1878',
                    'place' => 'Warszawa',
                ],
            ],
            'total_children' => '7',
            'children' => [
                [
                  'id' => '1953',
                  'name' => 'Jadwiga',
                ],
                [
                  'id' => '1926',
                  'name' => 'Zofia',
                ],
                [
                  'id' => '1956',
                  'name' => 'Józef',
                ],
                [
                  'id' => '1957',
                  'name' => 'Anna',
                ],
                [
                  'id' => '1958',
                  'name' => 'Tadeusz',
                ],
                [
                  'id' => '1959',
                  'name' => 'Maria',
                ],
                [
                  'id' => '1974',
                  'name' => 'Wanda',
                ],
            ],
            'total_siblings' => '0',
            'siblings' => null,
        ],
    ];

    yield [
        '4613',
        file_get_contents(__DIR__.'/pytlewscy/4613.html'),
        [
            'basic_name' => '<b>Białkowska (Kolin)</b> Adolfina',
            'family_name' => 'Kolin',
            'last_name' => 'Białkowska',
            'name' => 'Adolfina',
            'middle_name' => null,
            'birth_date' => 1826,
            'birth_place' => null,
            'death_date' => null,
            'death_place' => null,
            'mother_id' => 634,
            'mother_surname' => null,
            'mother_name' => null,
            'father_id' => null,
            'father_surname' => 'Kolin',
            'father_name' => null,
            'parents' => '1',
            'photo' => null,
            'total_marriages' => '1',
            'marriages' => [
                [
                    'id' => '4612',
                    'name' => 'Białkowski, Wiktor',
                ],
            ],
            'total_children' => '1',
            'children' => [
                [
                    'id' => '4611',
                    'name' => 'Karolina',
                ],
            ],
            'total_siblings' => '0',
            'siblings' => null,
        ],
    ];
});
