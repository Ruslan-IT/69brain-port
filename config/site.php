<?php

return [

    'name' => 'КБ «Прочность»',

    'enb_password' => env('ENB_PASSWORD', ''),

    'services' => [
        [
            'route' => 'strength',
            'title' => 'Расчёты на прочность и жёсткость',
        ],
        [
            'route' => 'statistics',
            'title' => 'Статистические расчёты',
        ],
        [
            'route' => 'technology',
            'title' => 'Технологические расчёты по листовой штамповке и лазерной резке',
        ],
    ],

];
