<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$arForm = $arResult['FORM'];
$arForm['PARAMETERS'] = [
    'id' => $arForm['ID'],
    'template' => $arForm['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
        'CONSENT_URL' => $arForm['CONSENT']
    ],
    'settings' => [
        'title' => $arForm['TITLE']
    ],
    'fields' => [
        $arForm['FIELD'] => null
    ]
];

if (empty($arForm['BUTTON']))
    $arForm['BUTTON'] = Loc::getMessage('C_MAIN_RATES_TEMPLATE_6_ORDER_BUTTON_DEFAULT')

?>
<div class="widget c-rates c-rates-template-6" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="widget-items-wrap scrollbar-inner" data-role="scrollbar">
                    <div class="widget-items">
                        <div class="widget-item widget-item-head">
                            <div class="widget-item-head-property-name"></div>
                            <?php foreach($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);
                            ?>
                                <div class="widget-item-head-property" id="<?= $sAreaId ?>">
                                    <?= $arItem['NAME'] ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php foreach($arResult['PROPERTIES'] as $arProperty) { ?>
                            <div class="widget-item">
                                <div class="widget-item-property-name">
                                    <?= $arProperty['NAME'] ?>
                                </div>
                                <?php foreach ($arResult['ITEMS'] as $arItem) {

                                    $sPropertyValueCode = ArrayHelper::getValue($arItem, ['DISPLAY_PROPERTIES', $arProperty['CODE'], 'VALUE_XML_ID']);
                                    $mPropertyValue = ArrayHelper::getValue($arItem, ['DISPLAY_PROPERTIES', $arProperty['CODE'], 'DISPLAY_VALUE']);

                                ?>
                                    <div class="widget-item-property">
                                        <div class="widget-item-head-mobile">
                                            <?= $arItem['NAME'] ?>
                                        </div>
                                        <div class="widget-item-text">
                                            <?php if (!empty($mPropertyValue)) { ?>
                                                <?php if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C' || Type::isArray($mPropertyValue)) { ?>
                                                    <?php if (!Type::isArray($mPropertyValue)) { ?>
                                                        <?= $mPropertyValue ?>
                                                    <?php } else { ?>
                                                        <?= implode(', ', $mPropertyValue) ?>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <?php if ($sPropertyValueCode === 'Y') { ?>
                                                        <span class="icon-available"></span>
                                                    <?php } else if ($sPropertyValueCode === 'N') { ?>
                                                        <span class="icon-unavailable"></span>
                                                    <?php } else { ?>
                                                        <?= $mPropertyValue ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <span class="icon-unavailable"></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['PRICE']['SHOW'] || $arForm['USE']) { ?>
                            <div class="widget-item widget-item-price-block-wrap">
                                <div class="widget-item-property-name"></div>
                                <?php foreach($arResult['ITEMS'] as $arItem) {
                                    $arData = $arItem['DATA'];
                                ?>
                                    <div class="widget-item-property">
                                        <?php if ($arVisual['PRICE']['SHOW']) { ?>
                                            <div class="widget-item-price-wrap">
                                                <?php if ($arVisual['DISCOUNT']['SHOW']) { ?>
                                                    <div class="widget-item-discount-wrap intec-grid intec-grid-a-v-center">
                                                        <?php if (!empty($arData['PRICE']['OLD']) && $arData['PRICE']['OLD'] > 0) { ?>
                                                            <div class="intec-grid-item">
                                                                <div class="widget-item-discount">
                                                                    <div class="widget-item-discount-value">
                                                                        <?php if (!empty($arData['DISCOUNT']['VALUE'])) { ?>
                                                                            <?php
                                                                            $iOffset = StringHelper::position('.', $arData['PRICE']['OLD']);

                                                                            $iPrecision = 0;

                                                                            if ($iOffset)
                                                                                $iPrecision = StringHelper::length(
                                                                                    StringHelper::cut($arData['PRICE']['OLD'], $iOffset + 1)
                                                                                );

                                                                            $sQuantity = number_format(
                                                                                $arData['PRICE']['OLD'],
                                                                                $iPrecision,
                                                                                '.',
                                                                                ' '
                                                                            );

                                                                            unset($iOffset, $iPrecision);

                                                                            ?>
                                                                            <?= $sQuantity ?>
                                                                            <?php if (!empty($arData['PRICE']['CURRENCY'])) { ?>
                                                                                <?= $arData['PRICE']['CURRENCY'] ?>
                                                                            <?php } ?>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                        <?php if ($arVisual['DISCOUNT']['SHOW'] && !empty($arData['DISCOUNT']['VALUE'])) { ?>
                                                            <div class="intec-grid-item-auto widget-item-sticker-wrap">
                                                                <div class="widget-item-sticker intec-cl-background">
                                                                    <?php if ($arData['DISCOUNT']['TYPE'] !== 'value') { ?>
                                                                        <?= '-'.$arData['DISCOUNT']['VALUE'].'%' ?>
                                                                    <?php } else { ?>
                                                                        <?= '-'.$arData['DISCOUNT']['VALUE'].' '.$arData['PRICE']['CURRENCY'] ?>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="intec-grid-item-auto">&nbsp;</div>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($arVisual['PRICE']['SHOW']) { ?>
                                                    <div class="widget-item-price">
                                                        <div class="widget-item-price-value">
                                                            <?php if ($arData['PRICE']['NEW'] > 0) { ?>
                                                                <?php
                                                                $iOffset = StringHelper::position('.', $arData['PRICE']['NEW']);

                                                                $iPrecision = 0;

                                                                if ($iOffset)
                                                                    $iPrecision = StringHelper::length(
                                                                        StringHelper::cut($arData['PRICE']['NEW'], $iOffset + 1)
                                                                    );

                                                                $sQuantity = number_format(
                                                                    $arData['PRICE']['NEW'],
                                                                    $iPrecision,
                                                                    '.',
                                                                    ' '
                                                                );

                                                                unset($iOffset, $iPrecision);

                                                                ?>
                                                                <?= $sQuantity ?>
                                                                <?php if (!empty($arData['PRICE']['CURRENCY'])) { ?>
                                                                    <?= $arData['PRICE']['CURRENCY'] ?>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                &nbsp;
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($arForm['USE']) { ?>
                                            <div class="widget-item-button-wrap">
                                                <?= Html::tag('div', Html::stripTags($arForm['BUTTON']), [
                                                    'class' => [
                                                        'widget-item-button',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-button',
                                                            'mod-transparent',
                                                            'mod-block',
                                                        ]
                                                    ],
                                                    'data' => [
                                                        'role' => 'rate.button',
                                                        'value' => $arItem['NAME']
                                                    ]
                                                ]) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>