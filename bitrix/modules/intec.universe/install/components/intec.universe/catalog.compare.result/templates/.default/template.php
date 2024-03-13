<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(false);

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$vContent = include(__DIR__.'/parts/content.php');
$arSvg = [
    'FIXED' => FileHelper::getFileData(__DIR__.'/svg/fix.svg'),
    'REMOVE' => FileHelper::getFileData(__DIR__.'/svg/remove.svg')
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-compare-result',
        'c-catalog-compare-result-default'
    ]
]) ?>
    <?php if ($arResult['AJAX']) $APPLICATION->RestartBuffer() ?>
        <div class="intec-content intec-content-visible">
            <div class="intec-content-wrapper">
                <?php if (!empty($arResult['ITEMS'])) { ?>
                    <div class="catalog-compare-result-wrapper">
                        <?php if (count($arResult['SECTIONS']) > 2) { ?>
                            <div class="catalog-compare-result-tabs scroll-mod-hiding scrollbar-inner">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'intec-grid' => [
                                            '',
                                            'wrap',
                                            'a-v-center',
                                            'a-h-start',
                                            '768-nowrap'
                                        ],
                                        'scroll' => [
                                            'mod-hiding',
                                            'inner',
                                            'content',
                                            'scrolly_visible'
                                        ],
                                        'scrollbar-inner' => [
                                            ''
                                        ]
                                    ],
                                    'data' => [
                                        'role' => 'scroll',
                                        'ui-control' => 'tabs'
                                    ]
                                ]) ?>
                                    <?php $bActive = true ?>
                                    <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                                        <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'catalog-compare-result-tab',
                                                'intec-grid' => [
                                                    'item-auto',
                                                    '',
                                                    'nowrap',
                                                    'a-v-center'
                                                ]
                                            ],
                                            'data' => [
                                                'active' => $bActive ? 'true' : 'false'
                                            ]
                                        ]) ?>
                                            <?= Html::a($arSection['NAME'], '#compares-'.$sTemplateId.'-section-'.$arSection['ID'], [
                                                'class' => [
                                                    'catalog-compare-result-tab-link',
                                                    'intec-grid-item-auto'
                                                ],
                                                'data' => [
                                                    'role' => 'compare.tab.control',
                                                    'type' => 'tab'
                                                ]
                                            ]) ?>
                                            <?= Html::tag('span', count($arSection['ITEMS']), [
                                                'class' => [
                                                    'catalog-compare-result-tab-link-count',
                                                    'intec-grid-item-auto'
                                                ]
                                            ]) ?>
                                        <?= Html::endTag('div') ?>
                                        <?php $bActive = false ?>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                        <div class="catalog-compare-result-tabs-content intec-ui intec-ui-control-tabs-content intec-ui-clearfix">
                            <?php $bActive = true ?>
                            <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                                <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
                                <?= Html::beginTag('div', [
                                    'id' => 'compares-'.$sTemplateId.'-section-'.$arSection['ID'],
                                    'class' => [
                                        'intec-ui-part-tab',
                                        'catalog-compare-result-tabs-section'
                                    ],
                                    'data' => [
                                        'role' => 'compare.tab.section',
                                        'initialize' => 'false',
                                        'active' => $bActive ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <?php $vContent($arSection) ?>
                                <?= Html::endTag('div') ?>
                                <?php $bActive = false ?>
                            <?php } ?>
                            <?php unset($bActive) ?>
                        </div>
                    </div>
                    <?php include(__DIR__.'/parts/script.php') ?>
                <?php } else { ?>
                    <p>
                        <span class="notetext">
                            <?= Loc::getMessage('C_CATALOG_COMPARE_RESULT_DEFAULT_TEMPLATE_EMPTY') ?>
                        </span>
                    </p>
                <?php } ?>
            </div>
        </div>
    <?php if ($arResult['AJAX']) exit() ?>
<?= Html::endTag('div') ?>