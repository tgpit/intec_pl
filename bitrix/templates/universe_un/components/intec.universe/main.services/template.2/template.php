<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$arForm = [];

if ($arVisual['FORM']['USE']) {
    $arForm = $arVisual['FORM'];
    $arForm['PARAMETERS'] = [
        'id' => $arForm['ID'],
        'template' => $arForm['TEMPLATE'],
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ASK',
            'CONSENT_URL' => $arForm['CONSENT']
        ],
        'settings' => [
            'title' => $arForm['TITLE']
        ],
        'fields' => [
            $arForm['FIELD'] => null
        ]
    ];
}

$iCount = 0;

?>
<div class="widget c-services c-services-template-2" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-all-container' => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'],
                                        'intec-grid-item' => [
                                            'auto' => $arBlocks['HEADER']['SHOW'],
                                            '1' => !$arBlocks['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-all-button',
                                            'intec-cl-text-light-hover',
                                        ],
                                        'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
                                    ])?>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="intec-grid-item-1">
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-start',
                            'a-h-start',
                            'i-7'
                        ]
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        if ($iCount >= 5)
                            $iCount = 0;

                        $iCount++;
                        $arData = $arItem['DATA'];
                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet(
                                $sPicture, [
                                    'width' => 900,
                                    'height' => 900
                                ],
                                BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            );

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        if ($arVisual['VIEW'] === 'mosaic') {
                            $arGrid = [
                                '2' => $iCount <= 2,
                                '3' => $iCount > 2,
                                '1000-2' => $iCount > 2,
                                '600-1' => true
                            ];
                            $sDataGrid = $iCount < 3 ? '2' : '3';
                        } else {
                            $arGrid = [
                                $arVisual['COLUMNS'] => true,
                                '1000-2' => $arVisual['COLUMNS'] > 2,
                                '600-1' => true
                            ];
                            $sDataGrid = $arVisual['COLUMNS'];
                        }

                    ?>
                        <?= Html::beginTag( $arVisual['BUTTON']['SHOW'] ? 'div' : 'a', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => $arGrid,
                            ], true),
                            'data' => [
                                'grid' => $sDataGrid
                            ],
                            'href' => $arVisual['BUTTON']['SHOW'] ? false : $arItem['DETAIL_PAGE_URL']
                        ]) ?>
                            <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                                <?= Html::tag('div', '', [
                                    'class' => 'widget-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                <div class="widget-item-fade"></div>
                                <div class="widget-item-text">
                                    <?= Html::beginTag( $arVisual['BUTTON']['SHOW'] ? 'a' : 'div', [
                                        'class' => Html::cssClassFromArray([
                                            'widget-item-name' => true,
                                        ], true),
                                        'href' => $arVisual['BUTTON']['SHOW'] ? $arItem['DETAIL_PAGE_URL'] : false
                                    ]) ?>
                                        <?= $arItem['NAME'] ?>
                                    <?= Html::endTag($arVisual['BUTTON']['SHOW'] ? 'a' : 'div') ?>
                                    <?php if ($arVisual['PRICE']['SHOW'] && !empty($arData['PRICE']['BASE'])) { ?>
                                        <div class="widget-item-price-container">
                                            <div class="widget-item-price-wrap">
                                                <span class="widget-item-price">
                                                    <?= $arData['PRICE']['BASE'] ?>
                                                </span>
                                                <?php if ($arVisual['PRICE']['SHOW'] && !empty($arData['PRICE']['OLD'])) { ?>
                                                    <span class="widget-item-price-old">
                                                        <?= $arData['PRICE']['OLD'] ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['BUTTON']['SHOW']) { ?>
                                        <div class="widget-item-button-wrapper">
                                            <?php if ($arVisual['BUTTON']['TYPE'] === 'order' && $arForm['USE']) { ?>
                                                <?= Html::beginTag('div', [
                                                    'class' => [
                                                        'widget-item-button',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-button',
                                                            'scheme-current',
                                                            'mod-round-2'
                                                        ]
                                                    ],
                                                    'data' => [
                                                        'role' => 'service.order',
                                                        'name' => $arItem['NAME']
                                                    ]
                                                ]) ?>
                                                    <span class="widget-item-button-content intec-ui-part-content">
                                                        <?= $arVisual['BUTTON']['TEXT'] ?>
                                                    </span>
                                                <?= Html::endTag('div') ?>
                                            <?php } else { ?>
                                                <?= Html::beginTag('a', [
                                                    'class' => [
                                                        'widget-item-button',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-button',
                                                            'scheme-current',
                                                            'mod-round-2'
                                                        ]
                                                    ],
                                                    'href' => $arItem['DETAIL_PAGE_URL']
                                                ]) ?>
                                                    <span class="widget-item-button-content intec-ui-part-content">
                                                        <?= $arVisual['BUTTON']['TEXT'] ?>
                                                    </span>
                                                <?= Html::endTag('a') ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?= Html::endTag($arVisual['BUTTON']['SHOW'] ? 'div' : 'a') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-footer' => true,
                        'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                        'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
                    ], true)
                ]) ?>
                    <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                        <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                            'href' => $arBlocks['FOOTER']['BUTTON']['LINK'],
                            'class' => [
                                'widget-footer-button',
                                'intec-ui' => [
                                    '',
                                    'size-5',
                                    'scheme-current',
                                    'control-button',
                                    'mod' => [
                                        'transparent',
                                        'round-half'
                                    ]
                                ]
                            ]
                        ]) ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
    <?php if ($arVisual['BUTTON']['SHOW'] && $arVisual['BUTTON']['TYPE'] === 'order' && $arVisual['FORM']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>