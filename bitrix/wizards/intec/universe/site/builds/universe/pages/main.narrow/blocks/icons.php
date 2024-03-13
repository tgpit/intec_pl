<?php

return [
    'type' => 'simple',
    'skip' => true,
    'properties' => [
        'margin' => [
            'bottom' => ['value' => 50, 'measure' => 'px']
        ]
    ],
    'widget' => [
        'code' => 'intec.constructor:icons',
        'template' => 'intec.both',
        'properties' => [
            'header' => [
                'show' => ''
            ],
            'caption' => [
                'style' => [
                    'bold' => '',
                    'italic' => '',
                    'underline' => ''
                ],
                'text' => [
                    'align' => [
                        'value' => 'left'
                    ],
                    'size' => [
                        'value' => 14,
                        'measure' => 'px'
                    ],
                    'color' => '#000000',
                ],
                'opacity' => 0
            ],
            'description' => [
                'style' => [
                    'bold' => '',
                    'italic' => '',
                    'underline' => ''
                ],
                'text' => [
                    'align' => [
                        'value' => 'center'
                    ],
                    'size' => [
                        'value' => 14,
                        'measure' => 'px'
                    ]
                ],
                'opacity' => 0,
            ],
            'background' => [
                'show' => '',
                'color' => '#f0f0f0',
                'rounding' => [
                    'value' => 100,
                    'measure' => 'px',
                    'shared' => '',
                    'top' => [
                        'value' => null,
                        'measure' => 'px'
                    ],
                    'right' => [
                        'value' => null,
                        'measure' => 'px'
                    ],
                    'bottom' => [
                        'value' => null,
                        'measure' => 'px'
                    ],
                    'left' => [
                        'value' => null,
                        'measure' => 'px'
                    ]
                ],
                'opacity' => 0
            ],
            'items' => [[
                'name' => 'Акции и скидки для постоянных клиентов',
                'image' => '#TEMPLATE#/images/gallery/1253846-200.png'
            ], [
                'name' => 'Качественные услуги и сервис',
                'image' => '#TEMPLATE#/images/gallery/842988-200.png'
            ], [
                'name' => 'Широкий ассортимент товаров',
                'image' => '#TEMPLATE#/images/gallery/1272607-200.png'
            ]],
            'count' => 3
        ]
    ]
];
