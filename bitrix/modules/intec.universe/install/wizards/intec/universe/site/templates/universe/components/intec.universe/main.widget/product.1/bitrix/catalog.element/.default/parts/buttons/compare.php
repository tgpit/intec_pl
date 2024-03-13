<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$sSvg = FileHelper::getFileData(__DIR__.'/../../svg/button.action.compare.svg');

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-action',
        'catalog-element-button-action-add',
        'catalog-element-button-action-compare',
        'intec-cl-border-light-hover',
        'intec-cl-background-light-hover',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'mod-round-2'
        ]
    ],
    'data' => [
        'compare-id' => $arResult['ID'],
        'compare-action' => 'add',
        'compare-code' => 'compare',
        'compare-state' => !defined('EDITOR') ? 'processing' : 'none',
        'compare-iblock' => $arResult['IBLOCK_ID']
    ]
]) ?>
    <div class="catalog-element-button-action-icon intec-ui-part-icon intec-ui-picture">
        <?= $sSvg ?>
    </div>
    <div class="catalog-element-button-action-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ACTION_COMPARE') ?>
    </div>
<?= Html::endTag('div') ?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-action',
        'catalog-element-button-action-added',
        'catalog-element-button-action-compare',
        'intec-cl-border',
        'intec-cl-background',
        'intec-cl-border-light-hover',
        'intec-cl-background-light-hover',
        'intec-ui' => [
            '',
            'control-button',
            'control-basket-button',
            'mod-round-2'
        ]
    ],
    'data' => [
        'compare-id' => $arResult['ID'],
        'compare-action' => 'remove',
        'compare-code' => 'compare',
        'compare-state' => !defined('EDITOR') ? 'processing' : 'none',
        'compare-iblock' => $arResult['IBLOCK_ID']
    ]
]) ?>
    <div class="catalog-element-button-action-icon intec-ui-part-icon intec-ui-picture">
        <?= $sSvg ?>
    </div>
    <div class="catalog-element-button-action-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_ACTION_COMPARED') ?>
    </div>
    <div class="intec-ui-part-effect intec-ui-part-effect-bounce">
        <div class="intec-ui-part-effect-wrapper">
            <i></i>
            <i></i>
            <i></i>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php unset($sSvg) ?>
