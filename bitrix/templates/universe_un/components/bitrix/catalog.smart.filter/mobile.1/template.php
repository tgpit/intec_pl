<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arSvg = [
    'HEADER' => [
        'TITLE' => FileHelper::getFileData(__DIR__.'/svg/header.title.svg'),
        'RESET' => FileHelper::getFileData(__DIR__.'/svg/header.reset.svg')
    ],
    'PROPERTY' => [
        'ARROW' => FileHelper::getFileData(__DIR__.'/svg/property.arrow.svg'),
        'DROPDOWN' => [
            'ARROW' => FileHelper::getFileData(__DIR__.'/svg/property.dropdown.arrow.svg')
        ]
    ]
];

$renderProperty = include(__DIR__.'/parts/properties.php');

?>
<div class="ns-bitrix c-catalog-smart-filter c-catalog-smart-filter-mobile-1" id="<?= $sTemplateId ?>">
    <?= Html::beginForm($arResult['FORM_ACTION'], 'get', [
        'class' => 'catalog-smart-filter-form',
        'name' => $arResult['FILTER_NAME'].'_form',
        'data-role' => 'filter.form'
    ]) ?>
        <div class="catalog-smart-filter-form-content intec-grid intec-grid-o-vertical">
            <div class="catalog-smart-filter-header intec-grid-item-auto">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-12">
                    <div class="intec-grid-item-auto">
                        <div class="catalog-smart-filter-title">
                            <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-6">
                                <div class="intec-grid-item">
                                    <div class="catalog-smart-filter-title-icon intec-ui-picture">
                                        <?= $arSvg['HEADER']['TITLE'] ?>
                                    </div>
                                </div>
                                <div class="intec-grid-item">
                                    <div class="catalog-smart-filter-title-text">
                                        <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_TITLE_DEFAULT') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::beginTag('button', [
                            'id' => 'delete-filter-mobile',
                            'class' => 'catalog-smart-filter-reset',
                            'name' => 'delete-filter-mobile',
                            'type' => 'submit'
                        ]) ?>
                            <span class="intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                                <span class="intec-grid-item-auto">
                                    <span class="catalog-smart-filter-reset-icon intec-ui-picture">
                                        <?= $arSvg['HEADER']['RESET'] ?>
                                    </span>
                                </span>
                                <span class="intec-grid-item-auto">
                                    <span class="catalog-smart-filter-reset-text">
                                        <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_RESET_DEFAULT') ?>
                                    </span>
                                </span>
                            </span>
                        <?= Html::endTag('button') ?>
                    </div>
                </div>
            </div>
            <div class="catalog-smart-filter-content intec-grid-item">
                <div class="catalog-smart-filter-properties">
                    <?php if (!empty($arResult['HIDDEN'])) { ?>
                        <?php foreach ($arResult['HIDDEN'] as $arControl) { ?>
                            <?= Html::hiddenInput($arControl['CONTROL_NAME'], $arControl['HTML_VALUE'], [
                                'id' => $arControl['CONTROL_ID']
                            ]) ?>
                        <?php } ?>
                        <?php unset($arControl) ?>
                    <?php } ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {
                        if (empty($arItem['PRICE']) || !$arItem['PRICE'])
                            continue;

                        $renderProperty($arItem);
                    } ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {
                        if (!empty($arItem['PRICE']) || $arItem['PRICE'])
                            continue;

                        $renderProperty($arItem);
                    } ?>
                </div>
            </div>
            <div class="catalog-smart-filter-footer intec-grid-item-auto">
                <?= Html::beginTag('button', [
                    'id' => 'set-filter-mobile',
                    'class' => [
                        'catalog-smart-filter-apply',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-block'
                        ]
                    ],
                    'name' => 'set_filter',
                    'type' => 'submit'
                ]) ?>
                    <span>
                        <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_APPLY_DEFAULT') ?>
                    </span>
                    <span id="quantity-filter-mobile"></span>
                <?= Html::endTag('button') ?>
            </div>
        </div>
    <?= Html::endForm() ?>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
