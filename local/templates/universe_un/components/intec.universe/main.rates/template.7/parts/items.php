<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$items) use (&$arVisual, &$arSvg, &$sTemplateId, &$APPLICATION, &$component) { ?>
    <div class="widget-items-container" data-role="container">
        <?= Html::beginTag('div', [
            'class' => [
                'widget-items',
                'owl-carousel'
            ],
            'data-role' => 'slider'
        ]) ?>
            <?php foreach ($items as $item) {

                $sId = $sTemplateId.'_'.$item['ID'];
                $sAreaId = $this->GetEditAreaId($sId);
                $this->AddEditAction($sId, $item['EDIT_LINK']);
                $this->AddDeleteAction($sId, $item['DELETE_LINK']);

                $sPicture = null;

                if (!empty($item['DATA']['PICTURE'])) {
                    $arPicture = CFile::ResizeImageGet($item['DATA']['PICTURE'], [
                        'width' => 530,
                        'height' => 300
                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    if (!empty($arPicture))
                        $sPicture = $arPicture['src'];

                    unset($arPicture);
                }

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            ?>
                <?= Html::beginTag('div', [
                    'id' => $sAreaId,
                    'class' => [
                        'widget-item',
                        'intec-grid' => [
                            '',
                            'o-vertical',
                            'a-h-between'
                        ]
                    ],
                    'data-role' => 'item'
                ]) ?>
                    <div class="intec-grid-item-auto">
                        <div class="widget-item-block">
                            <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', null, [
                                'class' => Html::cssClassFromArray([
                                    'widget-item-picture' => true,
                                    'intec-image-effect' => true,
                                    'owl-lazy' => $arVisual['LAZYLOAD']['USE']
                                ], true),
                                'href' => $arVisual['LINK']['USE'] ? $item['DETAIL_PAGE_URL'] : null,
                                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                'data-src' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                'style' => [
                                    'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$sPicture.'\')'
                                ]
                            ]) ?>
                            <?php if ($item['DATA']['MARKS']['SHOW']) { ?>
                                <div class="widget-item-marks">
                                    <?php $APPLICATION->IncludeComponent(
                                        'intec.universe:main.markers',
                                        'template.2', [
                                            'NEW' => $item['DATA']['MARKS']['VALUES']['NEW'],
                                            'HIT' => $item['DATA']['MARKS']['VALUES']['HIT'],
                                            'RECOMMEND' => $item['DATA']['MARKS']['VALUES']['RECOMMEND'],
                                            'SHARE' => $item['DATA']['MARKS']['VALUES']['SHARE'],
                                            'ORIENTATION' => 'horizontal'
                                        ],
                                        $component,
                                        ['HIDE_ICONS' => 'Y']
                                    ) ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="widget-item-block widget-item-content">
                            <?php if ($item['DATA']['HEADER']['SHOW']) { ?>
                                <div class="widget-item-header">
                                    <?= $item['DATA']['HEADER']['VALUE'] ?>
                                </div>
                            <?php } ?>
                            <div class="widget-item-name">
                                <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'span', $item['NAME'], [
                                    'class' => Html::cssClassFromArray([
                                        'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                    ], true),
                                    'href' => $arVisual['LINK']['USE'] ? $item['DETAIL_PAGE_URL'] : null,
                                    'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                ]) ?>
                            </div>
                            <?php if ($item['DATA']['PROPERTIES']['SHOW']) { ?>
                                <div class="widget-item-properties">
                                    <?php foreach ($item['DATA']['PROPERTIES']['VALUES'] as $property) { ?>
                                        <div class="widget-item-property">
                                            <span class="widget-item-property-name">
                                                <?= $property['NAME'] ?>
                                            </span>
                                            <span>
                                                -
                                            </span>
                                            <span class="widget-item-property-value">
                                                <?php if (Type::isArray($property['DISPLAY_VALUE'])) { ?>
                                                    <?= implode(', ', $property['DISPLAY_VALUE']) ?>
                                                <?php } else { ?>
                                                    <?= $property['DISPLAY_VALUE'] ?>
                                                <?php } ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($item['DATA']['ADVANTAGES']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item-advantages',
                                    'data' => [
                                        'role' => 'item.advantages',
                                        'interactive' => $item['DATA']['ADVANTAGES']['EXPAND'] ? 'true' : 'false',
                                        'expanded' => $item['DATA']['ADVANTAGES']['EXPAND'] ? 'false' : 'true'
                                    ]
                                ]) ?>
                                    <div class="widget-item-advantages-content" data-role="item.advantages.content">
                                        <?php foreach ($item['DATA']['ADVANTAGES']['VALUES'] as $property) { ?>
                                            <div class="widget-item-advantage">
                                                <?= $property ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <?php if ($item['DATA']['ADVANTAGES']['EXPAND']) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'widget-item-advantages-expand',
                                                'intec-grid' => [
                                                    '',
                                                    'inline',
                                                ]
                                            ],
                                            'data-role' => 'item.advantages.activator'
                                        ]) ?>
                                            <div class="intec-grid-item-auto">
                                                <?= Html::beginTag('div', [
                                                    'class' => [
                                                        'widget-item-advantages-expand-text',
                                                        'intec-cl-text',
                                                        'intec-cl-text-light-hover'
                                                    ]
                                                ]) ?>
                                                    <span data-state="collapsed">
                                                        <?= Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TEMPLATE_ITEM_ADVANTAGES_EXPAND') ?>
                                                    </span>
                                                    <span data-state="expanded">
                                                        <?= Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TEMPLATE_ITEM_ADVANTAGES_COLLAPSE') ?>
                                                    </span>
                                                    <span class="widget-item-advantages-expand-decoration"></span>
                                                <?= Html::endTag('div') ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if (
                        $item['DATA']['PRICE']['SHOW'] ||
                        $item['DATA']['FORM']['ORDER']['USE'] ||
                        $arVisual['LINK']['USE']
                    ) { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'intec-grid-auto',
                            'data-visible' => (
                                !$item['DATA']['PRICE']['SHOW'] &&
                                !$item['DATA']['FORM']['ORDER']['USE'] &&
                                $arVisual['LINK']['USE']
                            ) ? 'mobile' : 'desktop'
                        ]) ?>
                            <div class="widget-item-footer">
                                <?php if ($item['DATA']['PRICE']['SHOW']) { ?>
                                    <div class="widget-item-price">
                                        <?php if (
                                            $item['DATA']['PRICE']['DISCOUNT']['SHOW'] ||
                                            $item['DATA']['PRICE']['DIFFERENCE']['SHOW']
                                        ) { ?>
                                            <div class="widget-item-price-difference">
                                                <?= Html::beginTag('div', [
                                                    'class' => [
                                                        'widget-item-price-difference-content',
                                                        'intec-grid' => [
                                                            '',
                                                            'inline',
                                                            'a-v-center'
                                                        ]
                                                    ]
                                                ]) ?>
                                                    <?php if ($item['DATA']['PRICE']['DISCOUNT']['SHOW']) { ?>
                                                        <?= Html::beginTag('div', [
                                                            'class' => [
                                                                'widget-item-price-difference-container',
                                                                'widget-item-price-difference-percent-container',
                                                                'intec-grid-item'
                                                            ]
                                                        ]) ?>
                                                            <div class="widget-item-price-difference-percent widget-item-price-difference-value">
                                                                <?= '-'.$item['DATA']['PRICE']['DISCOUNT']['PRINT'] ?>
                                                            </div>
                                                        <?= Html::endTag('div') ?>
                                                    <?php } ?>
                                                    <?php if ($item['DATA']['PRICE']['DIFFERENCE']['SHOW']) { ?>
                                                        <div class="widget-item-price-difference-container intec-grid-item">
                                                            <div class="widget-item-price-difference-value">
                                                                <?= $item['DATA']['PRICE']['DIFFERENCE']['PRINT'] ?>
                                                            </div>
                                                        </div>
                                                        <div class="widget-item-price-difference-icon intec-ui-picture">
                                                            <?= $arSvg['PRICE']['DIFFERENCE'] ?>
                                                        </div>
                                                    <?php } ?>
                                                <?= Html::endTag('div') ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($item['DATA']['PRICE']['BASE']['SHOW']) { ?>
                                            <div class="widget-item-price-discount">
                                                <span class="widget-item-price-discount-current">
                                                    <?= $item['DATA']['PRICE']['BASE']['PRINT'] ?>
                                                </span>
                                                <?php if (!empty($item['DATA']['PRICE']['CURRENCY'])) { ?>
                                                    <span class="widget-item-price-discount-currency">
                                                        <?= $item['DATA']['PRICE']['CURRENCY'] ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <div class="widget-item-price-current">
                                            <span class="widget-item-price-current-value">
                                                <?= $item['DATA']['PRICE']['TOTAL']['PRINT'] ?>
                                            </span>
                                            <?php if (!empty($item['DATA']['PRICE']['CURRENCY'])) { ?>
                                                <span class="widget-item-price-current-currency">
                                                    <?= $item['DATA']['PRICE']['CURRENCY'] ?>
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($item['DATA']['FORM']['ORDER']['USE']) { ?>
                                    <?= Html::tag('div', Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TEMPLATE_ITEM_ORDER_TEXT_DEFAULT'), [
                                        'class' => [
                                            'widget-item-order',
                                            'intec-cl-text',
                                            'intec-cl-background-hover',
                                            'intec-cl-border-hover',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'size-3',
                                                'mod-block',
                                                'mod-round-2'
                                            ]
                                        ],
                                        'data' => [
                                            'role' => 'order',
                                            'name' => $item['NAME']
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <?php if ($arVisual['LINK']['USE']) { ?>
                                    <div class="widget-item-detail">
                                        <?= Html::tag('a', Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_TEMPLATE_ITEM_DETAIL'), [
                                            'class' => [
                                                'intec-cl-text',
                                                'intec-cl-text-light-hover',
                                            ],
                                            'href' => $item['DETAIL_PAGE_URL'],
                                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                        ]) ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
        <div class="widget-navigation" data-role="container.navigation"></div>
        <?php if ($arVisual['SLIDER']['DOTS']) { ?>
            <div class="widget-dots-container">
                <div class="widget-dots intec-ui-align" data-role="container.dots"></div>
            </div>
        <?php } ?>
    </div>
<?php } ?>