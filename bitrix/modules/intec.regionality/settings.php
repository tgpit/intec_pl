<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return [
    'crossSiteUse' => [
        'type' => 'boolean',
        'name' => Loc::getMessage('intec.regionality.settings.crossSiteUse'),
        'default' => 0
    ]
];