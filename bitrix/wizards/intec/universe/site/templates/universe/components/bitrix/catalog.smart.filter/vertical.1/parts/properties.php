<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arPropertyTypes = [
    'F',
    'G',
    'A',
    'B',
    'H'
];

?>
<?php foreach ($arResult['ITEMS'] as $iKey => $arItem) {

    if (empty($arItem['VALUES']) || ArrayHelper::keyExists('PRICE', $arItem))
        continue;

    $sPropertyType = ArrayHelper::fromRange($arPropertyTypes, $arItem['DISPLAY_TYPE']);

    if ($sPropertyType == 'A' || $sPropertyType == 'B')
        if (!isset($arItem['VALUES']['MAX']['VALUE'], $arItem['VALUES']['MIN']['VALUE']))
            continue;

    $bSearchShow = false;

    if ($arVisual['SEARCH']['SHOW']) {
        if ($arVisual['SEARCH']['MODE'] === 'all') {
            $bSearchShow = true;
        }
        elseif ($arVisual['SEARCH']['MODE'] === 'quantity') {
            if (count($arItem['VALUES']) >= $arVisual['SEARCH']['QUANTITY'])
                $bSearchShow = true;
        }
        elseif ($arVisual['SEARCH']['MODE'] === 'properties') {
            foreach ($arVisual['SEARCH']['PROPERTIES'] as $sProperty) {
                if ($arItem['CODE'] === $sProperty) {
                    $bSearchShow = true;
                    break;
                }
            }

            unset($sProperty);
        }
    }

    if ($sPropertyType === 'A' || $sPropertyType === 'B')
        $bSearchShow = false;

    $bExpanded = ArrayHelper::getValue($arItem, 'DISPLAY_EXPANDED', false) == 'Y';
    $sType = ArrayHelper::getValue($arVisual, ['TYPE', $sPropertyType, 'DATA']);
    $sTypeView = ArrayHelper::getValue($arVisual, ['TYPE', $sPropertyType, 'VIEW']);

?>
    <div class="<?= Html::cssClassFromArray([
        'smart-filter-property' => true,
        'bx-filter-parameters-box' => true,
        'intec-ui-clearfix' => true,
        'bx-active' => $bExpanded
    ], true) ?>">
        <span class="bx-filter-container-modef"></span>
        <div class="smart-filter-property-name" onclick="smartFilter<?= $arVisual['MOBILE'] ? 'Mobile' : null ?>.hideFilterProps(this)">
            <span class="smart-filter-property-name-title intec-cl-text-hover">
                <?= $arItem['NAME'] ?>
            </span>
            <?php if (!empty($arItem['FILTER_HINT'])) { ?>
                <div class="smart-filter-property-hint" data-role="hint">
                    <?= Html::tag('i', '', [
                        'class' => 'fal fa-question-circle',
                        'data-role' => 'hint.icon'
                    ]) ?>
                    <?= Html::tag('div', $arItem['FILTER_HINT'], [
                        'class' => 'smart-filter-property-hint-content',
                        'data' => [
                            'role' => 'hint.content',
                            'state' => 'hidden'
                        ]
                    ]) ?>
                </div>
            <?php } ?>
            <?= Html::tag('i', '', [
                'class' => Html::cssClassFromArray([
                    'smart-filter-property-name-angle' => true,
                    'far fa-angle-down' => true,
                    'property-expanded' => $bExpanded
                ], true),
                'data-role' => 'prop_angle'
            ]) ?>
        </div>
        <?= Html::beginTag('div', [
            'class' => [
                'smart-filter-property-values'
            ],
            'data-role' => 'bx_filter_block',
            'data-property-type' => $sType,
            'data-property-view' => $sTypeView
        ]) ?>
            <?php if ($bSearchShow) { ?>
                <div class="smart-filter-search-wrapper intec-grid intec-grid-a-v-center" data-role="search.holder">
                    <input type="text" class="intec-grid-item" data-role="smart.filter.search" placeholder="<?= Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_SEARCH') ?>">
                    <i class="glyph-icon-loop intec-grid-item-auto" data-role="search.button" data-status="enabled"></i>
                    <i class="fas fa-times intec-grid-item-auto" data-role="search.cancel" data-status="disabled"></i>
                </div>
            <?php } ?>
            <?php if ($sPropertyType !== 'A' && $sPropertyType !== 'B') { ?>
                <div class="scrollbar-inner" data-role="scrollbar" data-status="disabled">
            <?php } ?>
            <?php if ($sPropertyType == 'A' || $sPropertyType == 'B') {

                $sMaxPrice = $arItem['VALUES']['MAX']['VALUE'];
                $sMinPrice = $arItem['VALUES']['MIN']['VALUE'];
                $sKey = $arItem["ENCODED_ID"].time().'_'.$arItem['CODE'];

                if ($arVisual['MOBILE'])
                    $sKey .= '_MOBILE';

                $precision = $arVisual['TYPE'][$sPropertyType]['PRECISION'];
                $precision = $precision <= 0 ? 0 : $precision;

                $bExpanded = ArrayHelper::getValue($arItem, 'DISPLAY_EXPANDED') == 'Y';

                $sMinPriceJS = ArrayHelper::getValue($arItem, ['VALUES', 'MIN', 'HTML_VALUE']);
                $sMinPriceName = $arItem['VALUES']['MIN']['CONTROL_NAME'];
                $sMinPriceId = $arItem['VALUES']['MIN']['CONTROL_ID'];
                $sMaxPriceJS = ArrayHelper::getValue($arItem, ['VALUES', 'MAX', 'HTML_VALUE']);
                $sMaxPriceName = $arItem['VALUES']['MAX']['CONTROL_NAME'];
                $sMaxPriceId = $arItem['VALUES']['MAX']['CONTROL_ID'];

                if ($arVisual['MOBILE']) {
                    $sMinPriceId .= '_mobile';
                    $sMaxPriceId .= '_mobile';
                }

            ?>
                <div class="smart-filter-track-wrapper intec-ui-clearfix">
                    <?php if ($sPropertyType == 'A') { ?>
                        <div class="bx-ui-slider-track-container smart-filter-track-action">
                            <div class="bx-ui-slider-track" id="drag_track_<?= $sKey ?>">
                                <?= Html::tag('div', '', [
                                    'id' => "colorUnavailableActive_$sKey",
                                    'class' => 'bx-ui-slider-pricebar-vd',
                                    'style' => [
                                        'left' => 0,
                                        'right' => 0
                                    ]
                                ]) ?>
                                <?= Html::tag('div', '', [
                                    'id' => "colorAvailableInactive_$sKey",
                                    'class' => 'bx-ui-slider-pricebar-vn',
                                    'style' => [
                                        'left' => 0,
                                        'right' => 0
                                    ]
                                ]) ?>
                                <?= Html::tag('div', '', [
                                    'id' => "colorAvailableActive_$sKey",
                                    'class' => 'bx-ui-slider-pricebar-v intec-cl-background intec-cl-border',
                                    'style' => [
                                        'left' => 0,
                                        'right' => 0
                                    ]
                                ]) ?>
                                <div class="bx-ui-slider-range" id="drag_tracker_<?= $sKey ?>"  style="left: 0; right: 0;">
                                    <?= Html::tag('a', '', [
                                        'id' => "left_slider_$sKey",
                                        'class' => 'bx-ui-slider-handle left',
                                        'href' => 'javascript:void(0)',
                                    ]) ?>
                                    <?= Html::tag('a', '', [
                                        'id' => "right_slider_$sKey",
                                        'class' => 'bx-ui-slider-handle right',
                                        'href' => 'javascript:void(0)',
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="smart-filter-track-value smart-filter-track-min">
                        <label>
                            <?= Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_PRICE_FROM') ?>
                        </label>
                        <?= Html::input('text', $arItem['VALUES']['MIN']['CONTROL_NAME'], $arItem['VALUES']['MIN']['HTML_VALUE'], [
                            'id' => $sMinPriceId,
                            'class' => 'min-price',
                            'style' => [
                                'border' => 'none'
                            ],
                            'placeholder' => $sMinPriceJS ? $sMinPriceJS : $sMinPrice,
                            'onkeyup' => 'smartFilter'.($arItem['VALUES']['MOBILE'] ? 'Mobile' : null).'.keyup(this)'
                        ]) ?>
                    </div>
                    <div class="smart-filter-track-value smart-filter-track-max">
                        <label>
                            <?= Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_PRICE_TO') ?>
                        </label>
                        <?= Html::input('text', $arItem['VALUES']['MAX']['CONTROL_NAME'], $arItem['VALUES']['MAX']['HTML_VALUE'], [
                            'id' => $sMaxPriceId,
                            'class' => 'max-price',
                            'style' => [
                                'border' => 'none'
                            ],
                            'placeholder' => $sMaxPriceJS ? $sMaxPriceJS : $sMaxPrice,
                            'onkeyup' => 'smartFilter'.($arItem['VALUES']['MOBILE'] ? 'Mobile' : null).'.keyup(this)'
                        ]) ?>
                    </div>
                </div>
                <?php if ($sPropertyType == 'A') { ?>
                    <?php $arJsParams = [
                        'variable' => 'smartFilter'.($arVisual['MOBILE'] ? 'Mobile' : null),
                        'leftSlider' => 'left_slider_'.$sKey,
                        'rightSlider' => 'right_slider_'.$sKey,
                        'tracker' => 'drag_tracker_'.$sKey,
                        'trackerWrap' => 'drag_track_'.$sKey,
                        'minInputId' => $sMinPriceId,
                        'maxInputId' => $sMaxPriceId,
                        'minPrice' => $sMinPrice,
                        'maxPrice' => $sMaxPrice,
                        'curMinPrice' => $arItem['VALUES']['MIN']['HTML_VALUE'],
                        'curMaxPrice' => $arItem['VALUES']['MAX']['HTML_VALUE'],
                        'fltMinPrice' => $arItem['VALUES']['MIN']['FILTERED_VALUE'] ? $arItem['VALUES']['MIN']['FILTERED_VALUE'] : $arItem['VALUES']['MIN']['VALUE'] ,
                        'fltMaxPrice' => $arItem['VALUES']['MAX']['FILTERED_VALUE'] ? $arItem['VALUES']['MAX']['FILTERED_VALUE'] : $arItem['VALUES']['MAX']['VALUE'],
                        'precision' => $precision,
                        'colorUnavailableActive' => 'colorUnavailableActive_'.$sKey,
                        'colorAvailableActive' => 'colorAvailableActive_'.$sKey,
                        'colorAvailableInactive' => 'colorAvailableInactive_'.$sKey,
                    ] ?>
                    <script>
                        BX.ready(function () {
                            window['trackBar<?= $sKey ?>'] = new BX.Iblock.SmartFilterVertical1(<?= JavaScript::toObject($arJsParams) ?>);
                        });
                    </script>
                <?php } ?>
            <?php } else if ($sPropertyType == 'G') { ?>
                <?php if ($sTypeView == 'tile') { ?>
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-a-h-start">
                <?php } ?>
                <?php foreach ($arItem['VALUES'] as $arValue) {

                    $sControlId = $arValue['CONTROL_ID'];
                    $sImage = ArrayHelper::getValue($arValue, ['FILE', 'SRC']);

                    if ($arVisual['MOBILE'])
                        $sControlId .= '_mobile';

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid-item-4' => $sTypeView == 'tile',
                            'smart-filter-property-value' => true,
                            'mouse-click-effect' => $sTypeView == 'default',
                        ], true),
                        'data' => [
                            'role' => 'scrollbar.item',
                            'search-value' => $bSearchShow ? $arValue['VALUE'] : null
                        ]
                    ])?>
                        <?= Html::beginTag('label', [
                            'class' => Html::cssClassFromArray([
                                'disabled' => $arValue['DISABLED']
                            ], true),
                            'data-role' => "label_$sControlId",
                            'for' => $sControlId
                        ]) ?>
                            <?= Html::input('checkbox', $arValue['CONTROL_NAME'], $arValue['HTML_VALUE'], [
                                'id' => $sControlId,
                                'checked' => $arValue['CHECKED'] ? 'checked' : null,
                                'onclick' => 'smartFilter'.($arVisual['MOBILE'] ? 'Mobile' : null).'.click(this)'
                            ]) ?>
                            <?= Html::beginTag('span', [
                                'class' => [
                                    'smart-filter-property-value-picture'
                                ],
                                'title' => $arValue['VALUE'],
                                'data-size' => $sTypeView == 'default' ? $arVisual['TYPE']['G']['SIZE'] : null,
                                'style' => [
                                    'background-image' => 'url('.$sImage.')'
                                ]
                            ]) ?>
                                <i class="smart-filter-property-value-icon fal fa-check"></i>
                            <?= Html::endTag('span') ?>
                        <?= Html::endTag('label') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php if ($sTypeView == 'tile') { ?>
                    </div>
                <?php } ?>
            <?php } else if ($sPropertyType == 'H') { ?>
                <?php foreach ($arItem['VALUES'] as $arValue) {

                    $sControlId = $arValue['CONTROL_ID'];
                    $sImage = ArrayHelper::getValue($arValue, ['FILE', 'SRC']);

                    if ($arVisual['MOBILE'])
                        $sControlId .= '_mobile';

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'smart-filter-property-value' => true,
                            'mouse-click-effect' => true
                        ], true),
                        'data' => [
                            'role' => 'scrollbar.item',
                            'search-value' => $bSearchShow ? $arValue['VALUE'] : null
                        ]
                    ])?>
                        <?= Html::beginTag('label', [
                            'class' => Html::cssClassFromArray([
                                'disabled' => $arValue['DISABLED']
                            ], true),
                            'data-role' => "label_$sControlId",
                            'for' => $sControlId
                        ]) ?>
                            <?= Html::input('checkbox', $arValue['CONTROL_NAME'], $arValue['HTML_VALUE'], [
                                'id' => $sControlId,
                                'checked' => $arValue['CHECKED'] ? 'checked' : null,
                                'onclick' => 'smartFilter'.($arVisual['MOBILE'] ? 'Mobile' : null).'.click(this)'
                            ]) ?>
                            <span class="smart-filter-property-value-text-picture">
                                <?= Html::tag('span', '', [
                                    'class' => 'smart-filter-property-value-text-picture-item',
                                    'data' => [
                                        'type' => 'picture'
                                    ],
                                    'style' => [
                                        'background' => !empty($sImage) ? "url($sImage)" : null
                                    ]
                                ]) ?>
                                <?= Html::tag('span', $arValue['VALUE'], [
                                    'class' => 'smart-filter-property-value-text-picture-item',
                                    'data' => [
                                        'type' => 'text'
                                    ]
                                ]) ?>
                            </span>
                        <?= Html::endTag('label') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?php } else { ?>
                <?php if ($sTypeView == 'tile') { ?>
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-a-h-start">
                <?php } ?>
                <?php foreach ($arItem['VALUES'] as $arValue) {

                    $sControlId = $arValue['CONTROL_ID'];

                    if ($arVisual['MOBILE'])
                        $sControlId .= '_mobile';

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'smart-filter-property-value' => true,
                            'mouse-click-effect' => $sTypeView == 'default',
                            'intec-grid-item-2' => $sTypeView == 'tile'
                        ], true),
                        'data' => [
                            'role' => 'scrollbar.item',
                            'search-value' => $bSearchShow ? $arValue['VALUE'] : null
                        ]
                    ])?>
                        <?= Html::beginTag('label', [
                            'class' => Html::cssClassFromArray([
                                'disabled' => $arValue['DISABLED']
                            ], true),
                            'data-role' => "label_$sControlId",
                            'for' => $sControlId
                        ]) ?>
                            <?= Html::input('checkbox', $arValue['CONTROL_NAME'], $arValue['HTML_VALUE'], [
                                'id' => $sControlId,
                                'checked' => $arValue['CHECKED'] ? 'checked' : null,
                                'onclick' => 'smartFilter'.($arVisual['MOBILE'] ? 'Mobile' : null).'.click(this)'
                            ]) ?>
                            <span class="smart-filter-property-value-text">
                                <?= $arValue['VALUE'] ?>
                            </span>
                        <?= Html::endTag('label') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php if ($sTypeView == 'tile') { ?>
                    </div>
                <?php } ?>
            <?php } ?>
                <div class="catalog-smart-filter-search-nothing-found" data-role="nothing.found" data-status="disabled">
                    <?= Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_SEARCH_NOTHING_FOUND') ?>
                </div>
            <?php if ($sPropertyType !== 'A' && $sPropertyType !== 'B') { ?>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
        <?/*= Html::beginTag('div', [
            'class' => [
                'catalog-smart-filter-show-more-button',
                'intec-cl-text'
            ],
            'data' => [
                'expanded' => $arItem['DISPLAY_EXPANDED'] === 'Y' ? 'true' : 'false',
                'status' => 'disabled',
                'role' => 'smart.filter.show.more.button'
            ],
            'onclick' => 'smartFilter.filterShowMoreProps(this)'

        ]) ?>
            <span data-role="show-more-button-text">
                <?php if ($arItem['DISPLAY_EXPANDED'] === 'Y') {
                    echo Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_HIDE');
                } else {
                    echo Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_1_SHOW_ALL');
                }?>
            </span>
            <i class="fas fa-arrow-down"></i>
        <?= Html::endTag('div')*/ ?>
    </div>
<?php } ?>