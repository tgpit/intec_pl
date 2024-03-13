<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\Type;
use intec\regionality\Module;
use intec\regionality\models\Region;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$APPLICATION->SetTitle(Loc::getMessage('title'));

$pricesTypesUse = Loader::includeModule('catalog') || Loader::includeModule('intec.startshop');
$storesUse = Loader::includeModule('catalog');

$variables = [[
    'name' => Loc::getMessage('fields.domain.name'),
    'type' => 'field',
    'macros' => '#'.Module::VARIABLE.'_DOMAIN#',
    'session' => '$_SESSION[\''.Module::VARIABLE.'\'][\'DOMAIN\']'
]];

$fields = Region::getFields(false, false);

foreach ($fields as $key => $name) {
    $variables[] = [
        'name' => $name,
        'type' => 'region.field',
        'macros' => '#'.Module::VARIABLE.'_'.Region::VARIABLE.'_'.$key.'#',
        'session' => '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\''.$key.'\']'
    ];
}

if ($pricesTypesUse) {
    $variables[] = [
        'name' => Loc::getMessage('regions.fields.pricesTypes.name'),
        'type' => 'region.field',
        'macros' => null,
        'session' => [
            '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'PRICES\'][\'ID\']',
            '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'PRICES\'][\'CODE\']'
        ]
    ];
}

if ($storesUse) {
    $variables[] = [
        'name' => Loc::getMessage('regions.fields.stores.name'),
        'type' => 'region.field',
        'macros' => null,
        'session' => [
            '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'STORES\'][\'ID\']',
            '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'STORES\'][\'CODE\']'
        ]
    ];
}

$properties = Region::getProperties();

foreach ($properties as $property) {
    $variables[] = [
        'name' => $property['LABEL'],
        'type' => 'region.property',
        'macros' => [
            '#'.Module::VARIABLE.'_'.Region::VARIABLE.'_PROPERTIES_'.$property['FIELD_CODE'].'_DISPLAY#',
            '#'.Module::VARIABLE.'_'.Region::VARIABLE.'_PROPERTIES_'.$property['FIELD_CODE'].'_RAW#'
        ],
        'session' => [
            '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'PROPERTIES\'][\''.$property['FIELD_CODE'].'\'][\'DISPLAY\']',
            '$_SESSION[\''.Module::VARIABLE.'\'][\''.Region::VARIABLE.'\'][\'PROPERTIES\'][\''.$property['FIELD_CODE'].'\'][\'RAW\']'
        ]
    ];
}

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<div class="adm-info-message-wrap">
    <div class="adm-info-message">
        <?= Loc::getMessage('description') ?>
    </div>
</div>
<div class="adm-list-table-wrap">
    <table class="adm-list-table">
        <thead>
            <tr class="adm-list-table-header">
                <th class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">
                        <?= Loc::getMessage('list.headers.name') ?>
                    </div>
                </th>
                <th class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">
                        <?= Loc::getMessage('list.headers.type') ?>
                    </div>
                </th>
                <th class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">
                        <?= Loc::getMessage('list.headers.macros') ?>
                    </div>
                </th>
                <th class="adm-list-table-cell">
                    <div class="adm-list-table-cell-inner">
                        <?= Loc::getMessage('list.headers.session') ?>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($variables as $variable) { ?>
                <tr class="adm-list-table-row">
                    <td class="adm-list-table-cell">
                        <?= $variable['name'] ?>
                    </td>
                    <td class="adm-list-table-cell">
                        <?= Loc::getMessage('list.rows.types.'.$variable['type']) ?>
                    </td>
                    <td class="adm-list-table-cell">
                        <?php if (!empty($variable['macros'])) { ?>
                            <?php if (Type::isArray($variable['macros'])) { ?>
                                <?= implode('<br />', $variable['macros']) ?>
                            <?php } else { ?>
                                <?= $variable['macros'] ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= '('.Loc::getMessage('list.rows.answers.no').')' ?>
                        <?php } ?>
                    </td>
                    <td class="adm-list-table-cell">
                        <?php if (!empty($variable['session'])) { ?>
                            <?php if (Type::isArray($variable['session'])) { ?>
                                <?= implode('<br />', $variable['session']) ?>
                            <?php } else { ?>
                                <?= $variable['session'] ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= '('.Loc::getMessage('list.rows.answers.no').')' ?>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>