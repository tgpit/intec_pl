<?php

use Bitrix\Main\Localization\Loc;

return [
    'name' => Loc::getMessage('template.layouts.narrow.name'),
    'zones' => [[
        'code' => 'header',
        'name' => Loc::getMessage('template.layouts.narrow.zones.header.name')
    ], [
        'code' => 'default',
        'name' => Loc::getMessage('template.layouts.narrow.zones.default.name')
    ], [
        'code' => 'footer',
        'name' => Loc::getMessage('template.layouts.narrow.zones.footer.name')
    ]]
];