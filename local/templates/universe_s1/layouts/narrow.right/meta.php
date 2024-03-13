<?php

use Bitrix\Main\Localization\Loc;

return [
    'name' => Loc::getMessage('template.layouts.narrowRight.name'),
    'zones' => [[
        'code' => 'header',
        'name' => Loc::getMessage('template.layouts.narrowRight.zones.header.name')
    ], [
        'code' => 'column',
        'name' => Loc::getMessage('template.layouts.narrowRight.zones.column.name')
    ], [
        'code' => 'default',
        'name' => Loc::getMessage('template.layouts.narrowRight.zones.default.name')
    ], [
        'code' => 'footer',
        'name' => Loc::getMessage('template.layouts.narrowRight.zones.footer.name')
    ]]
];