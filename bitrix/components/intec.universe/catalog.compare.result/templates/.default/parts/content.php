<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var callable $vProperties
 * @var callable $vItems
 * @var callable $vHeader
 */

$vProperties = include(__DIR__.'/properties.php');
$vItems = include(__DIR__.'/items.php');
$vHeader = include(__DIR__.'/header.php');

?>

<?php return function ($arSection) use (&$arResult, &$arVisual, &$arSvg, &$vProperties, &$vItems, &$vHeader) { ?>
    <?php $vHeader($arSection) ?>
    <?php
        $showRightSlider = count($arSection['ITEMS']) > 1;
    ?>
    <div class="catalog-compare-result-products">
        <?= Html::beginTag('div', [
            'class' => [
                'intec-grid' => [
                    '',
                    'nowrap',
                    'a-h-start',
                    'a-v-start',
                    'i-h-10'
                ]
            ]
        ]) ?>
            <div class="intec-grid-item-1 intec-grid-item-768-2">
                <?php $vItems($arSection['ITEMS']) ?>
            </div>
            <div class="catalog-compare-result-mobile-block intec-grid-item-1 intec-grid-item-768-2">
                <?php if ($showRightSlider) { ?>
                    <?php $vItems($arSection['ITEMS'], 'right') ?>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    </div>
    <?= Html::beginTag('div', [
        'class' => 'catalog-compare-result-items-navigation',
        'data' => [
            'role' => 'navigation',
            'fixed' => 'false',
            'state' => 'true'
        ]
    ]) ?>
        <div class="intec-content intec-content-primary intec-content-visible">
            <div class="intec-content-wrapper">
                <div class="catalog-compare-result-items-navigation-wrapper">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-compare-result-items-navigation-button',
                            'intec-cl-border-hover',
                            'intec-cl-background-hover'
                        ],
                        'data' => [
                            'action' => 'next',
                            'role' => 'navigation.button',
                            'state' => 'enabled'
                        ]
                    ]) ?>
                        <i class="fal fa-angle-right"></i>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-compare-result-items-navigation-button',
                            'intec-cl-border-hover',
                            'intec-cl-background-hover'
                        ],
                        'data' => [
                            'action' => 'prev',
                            'role' => 'navigation.button',
                            'state' => 'disabled'
                        ]
                    ]) ?>
                        <i class="fal fa-angle-left"></i>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    <?= Html::endTag('div') ?>
    <div class="catalog-compare-result-different">
        <?= Html::beginTag('label', [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-switch',
                    'scheme-current',
                    'size-2'
                ]
            ],
            'data' => [
                'action' => $arResult['DIFFERENT'] ? $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=N' : $arResult['COMPARE_URL_TEMPLATE'].'DIFFERENT=Y',
                'role' => 'difference'
            ]
        ]) ?>
            <?= Html::checkbox(null, $arResult['DIFFERENT'], [
                'onchange' => 'this.checked = !this.checked'
            ]) ?>
            <span class="intec-ui-part-selector"></span>
            <span class="intec-ui-part-content">
                <?= Loc::getMessage('C_CATALOG_COMPARE_RESULT_DEFAULT_TEMPLATE_DIFFERENCE') ?>
            </span>
        <?= Html::endTag('label') ?>
    </div>
    <?php if ($arVisual['PROPERTIES']['SHOW']) { ?>
        <div class="catalog-compare-result-properties">
            <?php foreach ($arResult['PROPERTIES'] as $arProperty) { ?>
                <?php if (ArrayHelper::isIn($arProperty['ID'], $arSection['PROPERTY_ID'])) { ?>
                    <div class="catalog-compare-result-property">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid' => [
                                    '',
                                    'nowrap',
                                    'a-h-start',
                                    'a-v-start',
                                    'i-h-10'
                                ]
                            ]
                        ]) ?>
                            <div class="intec-grid-item-1 intec-grid-item-768-2">
                                <div class="catalog-compare-result-property-name">
                                    <?= $arProperty['NAME'] ?>
                                </div>
                            </div>
                            <div class="catalog-compare-result-mobile-block intec-grid-item-1 intec-grid-item-768-2">
                                <div class="catalog-compare-result-property-name">
                                    <?php if ($showRightSlider) { ?>
                                        <?= $arProperty['NAME'] ?>
                                    <?php } ?>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid' => [
                                    '',
                                    'nowrap',
                                    'a-h-start',
                                    'a-v-start',
                                    'i-h-10'
                                ]
                            ]
                        ]) ?>
                            <div class="intec-grid-item-1 intec-grid-item-768-2">
                                <?php $vProperties($arSection['ITEMS'], $arProperty) ?>
                            </div>
                            <div class="catalog-compare-result-mobile-block intec-grid-item-1 intec-grid-item-768-2">
                                <?php if ($showRightSlider) { ?>
                                    <?php $vProperties($arSection['ITEMS'], $arProperty, 'right') ?>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } ?>
<?php } ?>
