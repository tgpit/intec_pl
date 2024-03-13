<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 * @var bool $bOffers
 */

?>
<?php $vStores = function (&$arStore) use (&$arResult, &$arVisual, &$arSvg) {
    $bMinAmountUse = $arVisual['MIN_AMOUNT']['USE'];
    $sPicture = $arStore['PICTURE'];

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
        'width' => 500,
        'height' => 500
        ], BX_RESIZE_IMAGE_PROPORTIONAL);

        if (!empty($sPicture))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'catalog-store-amount-item' => true,
            'intec-grid-item' => [
                $arVisual['COLUMNS'] => true,
                '1024-3' => $arVisual['COLUMNS'] >= 4,
                '768-2' => true,
                '500-1' => true
            ]
        ], true),
        'data' => [
            'role' => 'store',
            'store-id' => $arStore['ID'],
            'store-state' => $arStore['AMOUNT_STATUS']
        ]
    ]) ?>
        <div class="catalog-store-amount-item-content">
            <?php if ($arVisual['PICTURE']['SHOW'] && !empty($arStore['PICTURE'])) { ?>
                <?= Html::tag('div', null, [
                    'class' => [
                        'catalog-store-amount-item-picture',
                        'intec-image-effect'
                    ],
                    'data' => [
                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                    ],
                    'style' => [
                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                    ]
                ]) ?>
            <?php } ?>
            <div class="catalog-store-amount-item-state-container catalog-store-amount-item-block">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-store-amount-item-state',
                        'intec-grid' => [
                            '',
                            'a-v-center',
                            'i-h-4'
                        ]
                    ],
                    'data' => [
                        'role' => 'store.state'
                    ]
                ]) ?>
                    <div class="intec-grid-item-auto">
                        <?= Html::tag('div', null, [
                            'class' => 'catalog-store-amount-item-state-indicator',
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <?php if (!$bMinAmountUse) { ?>
                            <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_2_TEMPLATE_IN_STOCK'), [
                                'class' => [
                                    'catalog-store-amount-item-state-value',
                                    'catalog-store-amount-item-state-colored'
                                ],
                            ]) ?>
                        <?php } ?>
                        <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                            'class' => 'catalog-store-amount-item-state-value',
                            'data' => [
                                'role' => 'store.quantity'
                            ]
                        ]) ?>
                        <?php if (!$bMinAmountUse) { ?>
                            <?php $sMeasureName = empty($arParams['OFFER_ID']) ? ArrayHelper::getFirstValue($arResult['MEASURES']) : ArrayHelper::getValue($arResult, ['MEASURES', $arParams['OFFER_ID']]) ?>
                            <?= Html::tag('span', $sMeasureName, [
                                'class' => 'catalog-store-amount-item-state-value',
                                'data' => [
                                    'role' => 'store.measure'
                                ]
                            ]) ?>
                        <?php } ?>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
            <?= Html::tag('div', $arStore['TITLE'], [
                'class' => [
                    'catalog-store-amount-item-title',
                    'catalog-store-amount-item-block'
                ],
                'title' => $arStore['TITLE']
            ]) ?>
            <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arStore['SCHEDULE'])) { ?>
                <?= Html::tag('div', $arStore['SCHEDULE'], [
                    'class' => [
                        'catalog-store-amount-item-schedule',
                        'catalog-store-amount-item-block'
                    ]
                ]) ?>
            <?php } ?>
            <?php if ($arVisual['PHONE']['SHOW'] && !empty($arStore['PHONE'])) { ?>
                <div class="catalog-store-amount-item-contact-container catalog-store-amount-item-block">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-store-amount-item-contact',
                            'intec-grid' => [
                                '',
                                'a-v-center',
                                'i-h-4'
                            ]
                        ]
                    ]) ?>
                    <?= Html::tag('div', $arSvg['PHONE'], [
                        'class' => [
                            'intec-grid-item-auto',
                            'catalog-store-amount-item-contact-icon'
                        ]
                    ]) ?>
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <?= Html::tag('a', $arStore['PHONE']['PRINT'], [
                            'class' => [
                                'catalog-store-amount-item-contact-value',
                                'intec-cl-text-hover'
                            ],
                            'href' => 'tel:'.$arStore['PHONE']['HTML'],
                            'title' => $arStore['PHONE']['PRINT'],
                            'data' => [
                                'view' => 'bold'
                            ]
                        ]) ?>
                    </div>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arStore['EMAIL'])) { ?>
                <div class="catalog-store-amount-item-contact-container catalog-store-amount-item-block">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-store-amount-item-contact',
                            'intec-grid' => [
                                '',
                                'a-v-center',
                                'i-h-4'
                            ]
                        ]
                    ]) ?>
                    <?= Html::tag('div', $arSvg['EMAIL'], [
                        'class' => [
                            'intec-grid-item-auto',
                            'catalog-store-amount-item-contact-icon'
                        ]
                    ]) ?>
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <?= Html::tag('a', $arStore['EMAIL'], [
                            'class' => [
                                'catalog-store-amount-item-contact-value',
                                'intec-cl-text',
                                'intec-cl-text-light-hover'
                            ],
                            'href' => 'mailto:'.$arStore['EMAIL'],
                            'title' => $arStore['EMAIL'],
                            'data' => [
                                'view' => 'normal'
                            ]
                        ]) ?>
                    </div>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arStore['DESCRIPTION'])) { ?>
                <?= Html::tag('div', $arStore['DESCRIPTION'], [
                    'class' => [
                        'catalog-store-amount-item-description',
                        'catalog-store-amount-item-block'
                    ],
                    'title' => $arStore['DESCRIPTION']
                ]) ?>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>

<?php
    $vStores($arStore);
    unset($vStores);
?>