<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$items) use (&$sTemplateId, &$arResult, &$arVisual, &$arBlocks, &$arSvg, &$APPLICATION, &$component) {

    $bSlider = count($items) > 1;

?>
    <div class="widget-items" data-role="collections">
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'widget-items-content' => true,
                'owl-carousel' => $bSlider
            ], true),
            'data-role' => $bSlider ? 'collections.slider' : null
        ]) ?>
            <?php foreach ($items as $item) {

                $sId = $sTemplateId.'_'.$item['ID'];
                $sAreaId = $this->GetEditAreaId($sId);
                $this->AddEditAction($sId, $item['EDIT_LINK']);
                $this->AddDeleteAction($sId, $item['DELETE_LINK']);

            ?>
                <?= Html::beginTag('div', [
                    'id' => $sAreaId,
                    'class' => 'widget-item',
                    'data-products' => $item['DATA']['PRODUCTS']['SHOW'] ? 'true' : 'false'
                ]) ?>
                    <div class="widget-item-container">
                        <div class="intec-grid intec-grid-768-wrap intec-grid-a-v-stretch">
                            <div class="intec-grid-item intec-grid-item-768-1">
                                <div class="widget-item-content">
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid' => [
                                                '' => true,
                                                'a-v-stretch' => true,
                                                'i-h-20' => true,
                                                'i-v-16' => true,
                                                '768-wrap' => $item['DATA']['PRODUCTS']['SHOW'],
                                                '600-wrap' => !$item['DATA']['PRODUCTS']['SHOW']
                                            ]
                                        ], true)
                                    ]) ?>
                                        <?php if ($item['DATA']['PICTURE']['SHOW']) {

                                            $sPicture = CFile::ResizeImageGet($item['DATA']['PICTURE']['VALUE'], [
                                                'width' => 440,
                                                'height' => 440
                                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                            if (!empty($sPicture))
                                                $sPicture = $sPicture['src'];
                                            else
                                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                        ?>
                                            <?= Html::beginTag('div', [
                                                'class' => Html::cssClassFromArray([
                                                    'intec-grid-item' => [
                                                        'auto' => true,
                                                        '768-1' => $item['DATA']['PRODUCTS']['SHOW'],
                                                        '600-1' => !$item['DATA']['PRODUCTS']['SHOW']
                                                    ]
                                                ], true)
                                            ]) ?>
                                                <div class="widget-item-picture intec-ui-picture">
                                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                                        'class' => 'intec-image-effect',
                                                        'title' => $item['NAME'],
                                                        'alt' => $item['NAME'],
                                                        'data' => [
                                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                        ]
                                                    ]) ?>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'intec-grid-item' => [
                                                    '' => true,
                                                    '768-1' => $item['DATA']['PRODUCTS']['SHOW']
                                                ]
                                            ], true)
                                        ]) ?>
                                            <div class="widget-item-name">
                                                <?= Html::tag($arVisual['DETAIL']['SHOW'] ? 'a' : 'span', $item['NAME'], [
                                                    'class' => Html::cssClassFromArray([
                                                        'intec-cl-text-hover' => $arVisual['DETAIL']['SHOW']
                                                    ], true),
                                                    'href' => $arVisual['DETAIL']['SHOW'] ? $item['DETAIL_PAGE_URL'] : null,
                                                    'target' => $arVisual['DETAIL']['SHOW'] && $arVisual['DETAIL']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                            </div>
                                            <div class="widget-item-information scrollbar-inner" data-role="collections.information">
                                                <?php if ($item['DATA']['PREVIEW']['SHOW']) { ?>
                                                    <div class="widget-item-description">
                                                        <?= $item['DATA']['PREVIEW']['VALUE'] ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($item['DATA']['DISPLAY']['SHOW']) { ?>
                                                    <div class="widget-item-properties">
                                                        <?php foreach ($item['DATA']['DISPLAY']['VALUES'] as $value) { ?>
                                                            <div class="widget-item-properties-item widget-item-block">
                                                                <span class="widget-item-properties-name">
                                                                    <?= $value['NAME'] ?>
                                                                </span>
                                                                <span class="widget-item-properties-separator">
                                                                    &#8212;
                                                                </span>
                                                                <span class="widget-item-properties-value">
                                                                    <?= $value['VALUE'] ?>
                                                                </span>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php if ($item['DATA']['PREVIEW']['SHOW'] || $item['DATA']['DISPLAY']['SHOW']) { ?>
                                                <div class="widget-item-information-toggle" data-role="information.toggle" data-state="show">
                                                    <!--noindex-->
                                                        <span data-code="show"><?= Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TEMPLATE_INFO_SHOW') ?></span>
                                                        <span data-code="hide"><?= Loc::getMessage('C_MAIN_IMAGES_TEMPLATE_1_TEMPLATE_INFO_HIDE') ?></span>
                                                    <!--/noindex-->
                                                </div>
                                            <?php } ?>
                                            <?php if ($arVisual['DETAIL']['SHOW']) { ?>
                                                <div class="widget-item-footer">
                                                    <div class="widget-item-footer-block widget-item-block">
                                                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-4">
                                                            <div class="intec-grid-item-auto">
                                                                <?= Html::tag('a', $arVisual['DETAIL']['TEXT'], [
                                                                    'class' => [
                                                                        'widget-item-footer-button',
                                                                        'intec-ui' => [
                                                                            '',
                                                                            'control-button',
                                                                            'scheme-current',
                                                                            'mod-block',
                                                                            'mod-round-2'
                                                                        ]
                                                                    ],
                                                                    'href' => $item['DETAIL_PAGE_URL'],
                                                                    'target' => $arVisual['DETAIL']['BLANK'] ? '_blank' : null
                                                                ]) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?= Html::endTag('div') ?>
                                    <?= Html::endTag('div') ?>
                                </div>
                            </div>
                            <?php if ($item['DATA']['PRODUCTS']['SHOW']) {

                                $GLOBALS[$arResult['PRODUCTS']['FILTER_NAME']] = [
                                    'ID' => $item['DATA']['PRODUCTS']['VALUES']
                                ];

                            ?>
                                <div class="intec-grid-item-4 intec-grid-item-768-1">
                                    <div class="widget-item-products">
                                        <div class="widget-item-products-scroll scrollbar-inner" data-role="collections.scroll">
                                            <?php $APPLICATION->IncludeComponent(
                                                'bitrix:catalog.section',
                                                'images.list.1',
                                                $arResult['PRODUCTS'],
                                                $component
                                            ) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    </div>
<?php } ?>