<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php $vItems = function ($arItems = [], $bOther = false) use (&$arVisual, &$arSvg) { ?>
    <?php if (empty($arItems)) return ?>
    <?php foreach ($arItems as $arItem) {

        if (!$arItem['CAN_BUY'])
            continue;

        $sPicture = $arItem['DETAIL_PICTURE'];

        if (empty($sPicture))
            $sPicture = $arItem['PREVIEW_PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet($sPicture, [
                'width' => 40,
                'height' => 38
            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';

    ?>
        <?= Html::beginTag('div', [
            'class' => 'constructor-main-dynamic-item',
            'data' => [
                'role' => 'small.item',
                'small-id' => $arItem['ID'],
                'selected' => !$bOther ? 'true' : 'false'
            ]
        ]) ?>
            <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                <div class="intec-grid-item">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-4">
                        <div class="constructor-main-dynamic-item-picture-container intec-grid-item-auto intec-grid-item-a-v-start">
                            <?= Html::beginTag('a', [
                                'class' => 'constructor-main-dynamic-item-picture',
                                'href' => $arItem['DETAIL_PAGE_URL'],
                                'target' => '_blank'
                            ]) ?>
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                    'class' => 'intec-image-effect',
                                    'alt' => $arItem['NAME'],
                                    'title' => $arItem['NAME'],
                                    'loading' => 'lazy',
                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]) ?>
                            <?= Html::endTag('a') ?>
                        </div>
                        <div class="intec-grid-item intec-grid-item-a-v-start intec-grid-item-500-1">
                            <div class="constructor-main-dynamic-item-name">
                                <?= Html::tag('a', $arItem['NAME'], [
                                    'class' => [
                                        'intec-cl-text',
                                        'intec-cl-text-light-hover'
                                    ],
                                    'href' => $arItem['DETAIL_PAGE_URL'],
                                    'target' => '_blank'
                                ]) ?>
                            </div>
                        </div>
                        <div class="intec-grid-item-auto intec-grid-item-500-1">
                            <div class="constructor-main-dynamic-item-price">
                                <?php if ($arItem['PRICE_VALUE'] !== $arItem['PRICE_DISCOUNT_VALUE']) { ?>
                                    <?= Html::tag('div', $arItem['PRICE_PRINT_VALUE'], [
                                        'class' => [
                                            'constructor-main-dynamic-item-price-old',
                                            'constructor-main-dynamic-item-price-item'
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <?= Html::tag('div', $arItem['PRICE_PRINT_DISCOUNT_VALUE'], [
                                    'class' => [
                                        'constructor-main-dynamic-item-price-current',
                                        'constructor-main-dynamic-item-price-item'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-grid-item-auto">
                    <?= Html::tag('div', $arSvg['DYNAMIC']['REMOVE'], [
                        'class' => 'constructor-main-dynamic-item-remove',
                        'title' => Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_DYNAMIC_REMOVE'),
                        'data' => [
                            'set-action' => 'remove',
                            'set-action-id' => $arItem['ID']
                        ]
                    ]) ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>
<div class="constructor-main-dynamic">
    <div class="constructor-main-dynamic-container intec-cl-border">
        <div class="constructor-main-dynamic-content">
            <?php $vItems($arResult['SET_ITEMS']['DEFAULT']) ?>
            <?php $vItems($arResult['SET_ITEMS']['OTHER'], true) ?>
            <div class="constructor-main-dynamic-item" data-role="small.empty" data-selected="false">
                <div class="constructor-main-dynamic-item-message-empty">
                    <?= Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_DYNAMIC_EMPTY') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="constructor-main-dynamic-decoration constructor-main-dynamic-plus intec-cl-background">+</div>
    <div class="constructor-main-dynamic-decoration constructor-main-dynamic-result intec-cl-background">=</div>
</div>
