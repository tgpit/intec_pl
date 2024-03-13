<?php

use Bitrix\Main\Localization\Loc;

return [
    'name' => Loc::getMessage('template.layouts.wide.name'),
    'zones' => [[
        'code' => 'header',
        'name' => Loc::getMessage('template.layouts.wide.zones.header.name')
    ], [
        'code' => 'default',
        'name' => Loc::getMessage('template.layouts.wide.zones.default.name')
    ], [
        'code' => 'footer',
        'name' => Loc::getMessage('template.layouts.wide.zones.footer.name')
    ]]
];