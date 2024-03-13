<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
    'settingsDisplay' => [
        'type' => 'list',
        'name' => Loc::getMessage('intec.universe.settings.settingsDisplay'),
        'default' => 'admin',
        'values' => [
            'none' => Loc::getMessage('intec.universe.settings.settingsDisplay.none'),
            'admin' => Loc::getMessage('intec.universe.settings.settingsDisplay.admin'),
            'all' => Loc::getMessage('intec.universe.settings.settingsDisplay.all')
        ]
    ],
    'yandexMetrikaUse' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaUse'),
        'default' => 0
    ],
    'yandexMetrikaId' => [
        'type' => 'string',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaId'),
        'default' => ''
    ],
    'yandexMetrikaClickMap' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaClickMap'),
        'default' => 1
    ],
    'yandexMetrikaEcommerce' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaEcommerce'),
        'default' => 1
    ],
    'yandexMetrikaTrackHash' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaTrackHash'),
        'default' => 1
    ],
    'yandexMetrikaTrackLinks' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaTrackLinks'),
        'default' => 1
    ],
    'yandexMetrikaWebvisor' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.yandexMetrikaWebvisor'),
        'default' => 0
    ],
    'googleTagUse' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.googleTagUse'),
        'default' => 0
    ],
    'googleTagId' => [
        'type' => 'string',
        'name' => Loc::getMessage('intec.universe.settings.googleTagId'),
        'default' => ''
    ],
    'optimizationUse' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.universe.settings.optimizationUse'),
        'default' => 1
    ]
];