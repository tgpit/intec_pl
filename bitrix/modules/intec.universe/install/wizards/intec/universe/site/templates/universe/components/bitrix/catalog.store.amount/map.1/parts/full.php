<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var Closure $vQuantity(&$arStore)
 */

?>
<div class="store-amount-full" data-role="store.full" data-active="false">
    <?php foreach ($arResult['STORES'] as $arStore) { ?>
        <?= Html::beginTag('div', [
            'class' => 'store-amount-full-item',
            'data' => [
                'role' => 'store.full.item',
                'active' => 'false',
                'store-id' => $arStore['ID'],
            ]
        ]) ?>
            <div class="store-amount-full-item-content scrollbar-outer" data-scroll>
                <div class="store-amount-full-item-content-wrapper">
                    <?php if ($arVisual['PICTURE']['SHOW'] && !empty($arStore['PICTURE'])) { ?>
                        <?= Html::tag('div', null, [
                            'class' => [
                                'store-amount-full-item-picture',
                                'intec-image-effect'
                            ],
                            'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'data-original' => $arVisual['LAZYLOAD']['USE'] ? $arStore['PICTURE']['SRC'] : null,
                            'style' => [
                                'background-image' => $arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arVisual['LAZYLOAD']['STUB'].'\')' : 'url(\''.$arStore['PICTURE']['SCR'].'\')'
                            ]
                        ]) ?>
                    <?php } ?>
                    <div class="store-amount-full-item-name store-amount-full-item-part">
                        <?= $arStore['TITLE'] ?>
                    </div>
                    <div class="store-amount-full-item-part">
                        <?php $vQuantity($arStore) ?>
                    </div>
                    <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arStore['SCHEDULE'])) { ?>
                        <div class="store-amount-full-item-schedule store-amount-full-item-part">
                            <?= $arStore['SCHEDULE'] ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['PHONE']['SHOW'] && !empty($arStore['PHONE'])) { ?>
                        <div class="store-amount-full-item-contact store-amount-full-item-part">
                            <div class="store-amount-full-item-contact-content">
                                <?= Html::tag('div', $arSvg['FULL']['PHONE'], [
                                    'class' => [
                                        'store-amount-full-item-contact-icon',
                                        'store-amount-full-item-contact-part'
                                    ]
                                ]) ?>
                                <div class="store-amount-full-item-contact-part">
                                    <?= Html::tag('a', $arStore['PHONE']['PRINT'], [
                                        'class' => [
                                            'store-amount-full-item-contact-phone',
                                            'store-amount-full-item-contact-value',
                                            'intec-cl-text-hover'
                                        ],
                                        'href' => 'tel:'.$arStore['PHONE']['HTML']
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arStore['EMAIL'])) { ?>
                        <div class="store-amount-full-item-contact store-amount-full-item-part">
                            <div class="store-amount-full-item-contact-content">
                                <?= Html::tag('div', $arSvg['FULL']['EMAIL'], [
                                    'class' => [
                                        'store-amount-full-item-contact-icon',
                                        'store-amount-full-item-contact-part'
                                    ]
                                ]) ?>
                                <div class="store-amount-full-item-contact-part">
                                    <?= Html::tag('a', $arStore['EMAIL'], [
                                        'class' => [
                                            'store-amount-full-item-contact-email',
                                            'store-amount-full-item-contact-value',
                                            'intec-cl-text',
                                            'intec-cl-text-light-hover'
                                        ],
                                        'href' => 'mailto:'.$arStore['EMAIL']
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arStore['DESCRIPTION'])) { ?>
                        <?= Html::tag('div', $arStore['DESCRIPTION'], [
                            'class' => [
                                'store-amount-full-item-description',
                                'store-amount-full-item-part'
                            ]
                        ]) ?>
                    <?php } ?>
                    <?= Html::tag('div', $arSvg['FULL']['CLOSE'], [
                        'class' => 'store-amount-full-item-close',
                        'data-role' => 'store.full.item.close'
                    ]) ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
