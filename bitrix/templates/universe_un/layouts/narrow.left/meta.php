<?php

use Bitrix\Main\Localization\Loc;

return [
    'name' => Loc::getMessage('template.layouts.narrowLeft.name'),
    'zones' => [[
        'code' => 'header',
        'name' => Loc::getMessage('template.layouts.narrowLeft.zones.header.name')
    ], [
        'code' => 'column',
        'name' => Loc::getMessage('template.layouts.narrowLeft.zones.column.name')
    ], [
        'code' => 'default',
        'name' => Loc::getMessage('template.layouts.narrowLeft.zones.default.name')
    ], [
        'code' => 'footer',
        'name' => Loc::getMessage('template.layouts.narrowLeft.zones.footer.name')
    ]]
];