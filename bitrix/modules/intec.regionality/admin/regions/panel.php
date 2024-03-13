<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arUrlTemplates
 */

Loc::loadMessages(__FILE__);

if (empty($arUrlTemplates))
    include(Core::getAlias('@intec/regionality/module/admin/url.php'));

return new CAdminContextMenu([[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['regions']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['regions.add']
]]);