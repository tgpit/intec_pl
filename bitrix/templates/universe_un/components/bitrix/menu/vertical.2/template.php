<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (defined('EDITOR'))
    $arResult = [[
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_1'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_2'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_3'),
        'LINK' => null,
        'ITEMS' => []
    ], [
        'SELECTED' => false,
        'ACTIVE' => false,
        'TEXT' => Loc::getMessage('C_MENU_VERTICAL_1_STUB_ITEM_4'),
        'LINK' => null,
        'ITEMS' => []
    ]];

if (empty($arResult))
    return;

$fView = include(__DIR__.'/parts/view.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => Html::cssClassFromArray([
        'ns-bitrix' => true,
        'c-menu' => true,
        'c-menu-vertical-2' => true
    ], true),
    'data' => [
        'role' => 'menu'
    ]
]) ?>
    <div class="menu-items">
        <?php $fView($arResult, 0) ?>
    </div>
<?= Html::endTag('div') ?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var item = $('[data-role="item"]', root);

        item.hover(function () {
            $(this).attr('data-selected', 'true');
            $(this).children('[data-role="menu.items"]').stop().slideDown(500);
        }, function () {
            $(this).attr('data-selected', 'false');
            $(this).children('[data-role="menu.items"]').stop().slideUp(500);
        });
    }, {
        'name': '[Component] bitrix:menu (vertical.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
