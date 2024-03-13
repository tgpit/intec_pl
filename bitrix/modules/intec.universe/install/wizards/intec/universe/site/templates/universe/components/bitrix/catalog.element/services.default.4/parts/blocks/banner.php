<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arSvg
 * @var array $arBlock
 */

$sPicture = !empty($arBlock['PICTURE']) ? $arBlock['PICTURE'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png';
$sTag = $arBlock['TITLE_TAG'];

?>
<?php if (!$arBlock['WIDE']) { ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
<?php } ?>
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-element-banner',
            'intec-content-wrap'
        ],
        'style' => !$arBlock['SPLIT'] ? [
            'background-image' => !$arResult['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
        ] : null,
        'title' => !$arBlock['SPLIT'] ? $arBlock['NAME'] : null,
        'data' => !$arBlock['SPLIT'] ? [
            'split' => 'false',
            'lazyload-use' => $arResult['LAZYLOAD']['USE'] ? 'true' : 'false',
            'original' => $arResult['LAZYLOAD']['USE'] ? $sPicture : null,
            'theme' => $arBlock['THEME'],
            'wide' => ($arBlock['WIDE']) ? 'true' : 'false'
        ] : [
            'split' => 'true'
        ]
    ]) ?>
        <?php if (!$arBlock['SPLIT']) { ?>
            <div class="catalog-element-banner-fade"></div>
        <?php } ?>
        <?php if ($arBlock['SPLIT']) { ?>
            <?= Html::tag('div', null, [
                'class' => 'catalog-element-banner-background',
                'title' => $arBlock['NAME'],
                'data' => [
                    'lazyload-use' => $arResult['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arResult['LAZYLOAD']['USE'] ? $sPicture : null
                ],
                'style' => [
                    'background-image' => !$arResult['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                ]
            ]) ?>
        <?php } ?>
            <?php if ($arBlock['SPLIT']) { ?>
                <div class="catalog-element-banner-picture-wrap">
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-element-banner-picture',
                        'title' => $arBlock['NAME'],
                        'data' => [
                            'lazyload-use' => $arResult['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arResult['LAZYLOAD']['USE'] ? $sPicture : null
                        ],
                        'style' => [
                            'background-image' => !$arResult['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                        ]
                    ]) ?>
                </div>
            <?php } ?>
            <div class="intec-content catalog-element-banner-wrapper">
                <div class="intec-content-wrapper catalog-element-banner-wrapper-2">
                    <div class="catalog-element-banner-content intec-grid intec-grid-1000-wrap intec-grid-a-v-stretch intec-grid-a-h-between">
                        <?php if ($arBlock['SPLIT']) { ?>
                            <div class="intec-grid-item-2 intec-grid-item-1000-1"></div>
                        <?php } ?>
                        <?=Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'catalog-element-banner-information' => true,
                                'intec-grid-item' => [
                                    'auto' => !$arBlock['SPLIT'],
                                    '2' => $arBlock['SPLIT'],
                                    '1000-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <?php if ($arBlock['OVERHEAD']['SHOW'] && !$arBlock['SPLIT']) { ?>
                                <?= Html::tag('div', $arBlock['OVERHEAD']['VALUE'], [
                                    'class' => 'catalog-element-banner-overhead'
                                ]) ?>
                            <?php } ?>
                            <?= Html::tag($sTag, $arBlock['NAME'], [
                                'class' => 'catalog-element-banner-header'
                            ]) ?>
                            <?php if ($arBlock['TEXT']['SHOW']) { ?>
                                <div class="catalog-element-banner-text">
                                    <div class="catalog-element-banner-text-value">
                                        <?= $arBlock['TEXT']['VALUE'] ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arBlock['PRICE']['BASE']['SHOW'] || $arBlock['BUTTON']['SHOW']) { ?>
                                <div class="catalog-element-banner-purchase intec-grid intec-grid-500-wrap intec-grid-a-v-center intec-grid-i-5 intec-grid-i-h-20">
                                    <?php if ($arBlock['BUTTON']['SHOW']) { ?>
                                        <?php
                                        $arFormParameters = [
                                            'id' => $arBlock['FORM']['ID'],
                                            'template' => $arBlock['FORM']['TEMPLATE'],
                                            'parameters' => [
                                                'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
                                                'CONSENT_URL' => $arBlock['FORM']['CONSENT']
                                            ],
                                            'settings' => [
                                                'title' => $arBlock['BUTTON']['TEXT']
                                            ],
                                            'fields' => []
                                        ];

                                        if (!empty($arBlock['FORM']['FIELDS']['SERVICE']))
                                            $arFormParameters['fields'][$arBlock['FORM']['FIELDS']['SERVICE']] = $arBlock['NAME'];
                                        ?>
                                        <div class="catalog-element-banner-purchase-button-wrap intec-grid-item-auto">
                                            <?= Html::tag('a', $arBlock['BUTTON']['TEXT'], [
                                                'class' => [
                                                    'catalog-element-banner-purchase-button',
                                                    'intec-cl-background',
                                                    'intec-cl-background-light-hover',
                                                ],
                                                'onclick' => '(function() {
                                                    template.api.forms.show('.JavaScript::toObject($arFormParameters).');
                                                    template.metrika.reachGoal(\'forms.open\');
                                                    template.metrika.reachGoal('.JavaScript::toObject('forms.'.$arFormParameters['id'].'.open').');
                                                })()'
                                            ]) ?>
                                        </div>
                                        <?php unset($arFormParameters) ?>
                                    <?php } ?>
                                    <?php if ($arBlock['PRICE']['BASE']['SHOW']) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'catalog-element-banner-purchase-holder',
                                                'intec-grid-item-auto'
                                            ]
                                        ]) ?>
                                            <? if(!empty($arBlock['PRICE']['OLD']['VALUE'])) ?>
                                                <div class="catalog-element-banner-purchase-price-old"><?= $arBlock['PRICE']['OLD']['VALUE'] ?></div>
                                            <? if(!empty($arBlock['PRICE']['BASE']['VALUE'])) ?>
                                                <div class="catalog-element-banner-purchase-price"> <?= $arBlock['PRICE']['BASE']['VALUE'] ?></div>
                                        <?= Html::endTag('div') ?>
                                        <div class="intec-grid-item"></div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    </div>

                    <?php if (!$arBlock['SPLIT']) { ?>
                        <?php if ($arBlock['ADDITIONAL']['SHOW'] && !empty($arBlock['ADDITIONAL']['VALUE'])) { ?>
                            <?php $iCountAdditional = count($arBlock['ADDITIONAL']['VALUE']) ?>
                            <div class="catalog-element-banner-additional-wrap">
                                <div class="catalog-element-banner-additional intec-grid intec-grid-wrap intec-grid-a-h-center intec-grid-i-h-25 intec-grid-i-v-10">
                                    <?php foreach ($arBlock['ADDITIONAL']['VALUE'] as $arItem) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'catalog-element-banner-additional-item-wrap' => true,
                                                'intec-grid-item' => [
                                                    '4' => $iCountAdditional >= 4,
                                                    '3' => $iCountAdditional <= 3,
                                                    '1000-3' => $iCountAdditional >= 4,
                                                    '768-2' => true,
                                                    '500-1' => true
                                                ]
                                            ], true)
                                        ]) ?>
                                            <div class="catalog-element-banner-additional-item">
                                                <div class="intec-grid intec-grid-a-v-center catalog-element-banner-additional-item-info">
                                                    <?= Html::tag('div', $arSvg['ADDITIONAL']['LEFT'], [
                                                        'class' => [
                                                            'catalog-element-banner-additional-item-icon',
                                                            'intec-grid-item-auto'
                                                        ]
                                                    ]) ?>
                                                        <div class="catalog-element-banner-additional-item-part intec-grid-item intec-grid-item-shrink-1">
                                                            <?= $arItem ?>
                                                        </div>
                                                    <?= Html::tag('div', $arSvg['ADDITIONAL']['RIGHT'], [
                                                        'class' => [
                                                            'catalog-element-banner-additional-item-icon',
                                                            'intec-grid-item-auto'
                                                        ]
                                                    ]) ?>
                                                </div>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
    <?= Html::endTag('div') ?>
<?php if (!$arBlock['WIDE']) { ?>
        </div>
    </div>
<?php } ?>
<?php

unset($sPicture);