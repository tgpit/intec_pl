<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use Bitrix\Main\Localization\Loc;
use intec\Core;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!CModule::IncludeModule('intec.startshop'))
    return;

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arFastOrder = [];
$arFastOrder['SHOW'] = $arParams['USE_BUTTON_FAST_ORDER'] === 'Y';
$arFastOrder['TEMPLATE'] = ArrayHelper::getValue($arParams, 'FAST_ORDER_TEMPLATE');
$arFastOrder['PREFIX'] = 'FAST_ORDER_';
$arFastOrder['PARAMETERS'] = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $arFastOrder['PREFIX']))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($arFastOrder['PREFIX']));
    $arFastOrder['PARAMETERS'][$sKey] = $sValue;
}

$arFastOrder['AJAX_MODE'] = 'Y';
$arFastOrder['AJAX_OPTION_ADDITIONAL'] = $sTemplateId.'-order-fast';

if (empty($arFastOrder['TEMPLATE']))
    $arFastOrder['SHOW'] = false;

unset($sKey);
unset($sValue);

$oRequest = Core::$app->request;

if ($oRequest->getIsAjax()) {
    $bIsAjax = $oRequest->get('basket');
    $bIsAjax = ArrayHelper::getValue($bIsAjax, 'ajax') === 'Y';
}

?>
<?= Html::beginTag('div', [
    'class' => [
        'ns-intec',
        'c-startshop-basket-basket',
        'c-startshop-basket-basket-default'
    ],
    'id' => $sTemplateId
]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div data-role="content.wrapper">
                <?php
                    if ($bIsAjax) {
                        $APPLICATION->RestartBuffer();
                    }

                    include(__DIR__.'/parts/preloader.php');
                ?>
                <?php if (!empty($arResult['ITEMS'])) { ?>
                    <div class="startshop-basket-basket-filter-wrapper intec-grid intec-grid-650-wrap intec-grid-a-h-650-between intec-grid-i-v-15">
                        <div class="intec-grid-item-auto intec-grid-item-650-1">
                            <div class="startshop-basket-basket-filter-input-wrapper">
                                <?= Html::tag('input', '', [
                                        'id' => 'basket-filter-input',
                                        'class' => [
                                            'startshop-basket-basket-filter-input',
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'view-2',
                                                'size-5'
                                            ]
                                        ],
                                        'placeholder' => Loc::getMessage('SBB_DEFAULT_FILTER_INPUT')
                                ]) ?>
                                <i class="startshop-basket-basket-filter-loop glyph-icon-loop intec-cl-text"></i>
                            </div>
                        </div>
                        <div class="intec-grid-item"></div>
                        <div class="intec-grid-item-auto startshop-basket-basket-print-wrapper">
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'startshop-basket-basket-print-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-5',
                                        'size-2'
                                    ]
                                ],
                                'data-role' => 'print.button'
                            ]) ?>
                                <i class="intec-ui-part-icon basket-print-icon"></i>
                            <?= Html::endTag('a') ?>
                        </div>
                        <?php if ($arParams['USE_BUTTON_CLEAR'] == 'Y') { ?>
                            <div class="startshop-basket-basket-clear-wrapper intec-grid-item-auto">
                                <?= Html::beginTag('div', [
                                        'class' => [
                                            'startshop-basket-basket-clear-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'mod-transparent',
                                                'mod-round-5',
                                                'size-2'
                                            ]
                                        ],
                                        'data-role' => 'clear.button'
                                ]) ?>
                                    <i class="intec-ui-part-icon basket-delete-icon"></i>
                                    <span class="intec-ui-part-content">
                                        <?= Loc::GetMessage('SBB_DEFAULT_BUTTON_CLEAR') ?>
                                    </span>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="startshop-basket-basket-content">
                        <div class="startshop-basket-basket-table-wrapper">
                            <div class="startshop-basket-basket-table" data-role="table">
                                <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                                    <?php
                                        $arSection = ArrayHelper::getValue($arItem, ['SECTION_INFO'], []);
                                    ?>
                                        <?= Html::beginTag('div', [
                                            'id' => 'basket-item-'.$arItem['ID'],
                                            'class' => 'startshop-basket-basket-table-row',
                                            'data' => [
                                                'id' => $arItem['ID'],
                                                'role' => 'item',
                                                'data' => $arItem['DATA'],
                                                'currency' => $arItem['STARTSHOP']['BASKET']['PRICE']['CURRENCY'],
                                                'price' => $arItem['STARTSHOP']['BASKET']['PRICE']['VALUE']
                                            ]
                                        ]) ?>
                                        <?php if ($arParams['USE_ITEMS_PICTURES'] == 'Y') { ?>
                                            <div class="startshop-basket-basket-table-cell startshop-basket-column-picture">
                                                <div class="startshop-basket-cell-picture intec-image-effect">
                                                    <?= Html::beginTag($arResult['QUICK_VIEW']['USE'] ? 'div' : 'a', [
                                                        'class' => 'startshop-image',
                                                        'href' => $arItem['DETAIL_PAGE_URL']
                                                    ]) ?>
                                                        <span class="startshop-aligner-vertical"></span>
                                                        <?= Html::tag('img', '', [
                                                            'loading' => 'lazy',
                                                            'src' => $arItem['PICTURE']['SRC'],
                                                            'alt' => $arItem['NAME'],
                                                            'title' => $arItem['NAME']
                                                        ]) ?>
                                                        <?php if ($arResult['QUICK_VIEW']['USE']) {
                                                            include(__DIR__.'/images/quick.view.button.icon.svg');
                                                        } ?>
                                                    <?= Html::endTag($arResult['QUICK_VIEW']['USE'] ? 'div' : 'a') ?>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                        <div class="startshop-basket-basket-table-cell startshop-basket-column-name">
                                            <div class="startshop-basket-cell">
                                                <?php if (!empty($arSection)) { ?>
                                                    <div class="startshop-basket-basket-section">
                                                        <?= Html::tag('a', $arSection['NAME'], [
                                                            'class' => 'startshop-basket-basket-section-link',
                                                            'href' => $arSection['SECTION_PAGE_URL']
                                                        ]) ?>
                                                    </div>
                                                <?php } ?>
                                                <div class="startshop-basket-basket-product-name">
                                                    <?= Html::tag('a', $arItem['NAME'], [
                                                        'class' => [
                                                            'startshop-basket-basket-product-name-link',
                                                            'intec-cl-text-hover'
                                                        ],
                                                        'href' => $arItem['DETAIL_PAGE_URL'],
                                                        'data-role' => 'item.name.link'
                                                    ]) ?>
                                                </div>
                                                <?php if ($arItem['STARTSHOP']['OFFER']['OFFER']) { ?>
                                                    <div class="startshop-basket-basket-mobile-offers">
                                                        <?php foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty) { ?>
                                                            <?php if ($arProperty['TYPE'] == 'TEXT') { ?>
                                                                <div class="startshop-basket-basket-property startshop-basket-basket-property-text">
                                                                    <div class="startshop-basket-basket-property-name">
                                                                        <?= $arProperty['NAME'] ?>:
                                                                    </div>
                                                                    <div class="startshop-basket-basket-property-value">
                                                                        <?= $arProperty['VALUE']['TEXT'] ?>
                                                                    </div>
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="startshop-basket-basket-property startshop-basket-basket-property-picture">
                                                                    <div class="startshop-basket-basket-property-name">
                                                                        <?= $arProperty['NAME'] ?>:
                                                                    </div>
                                                                    <div class="startshop-basket-basket-property-value">
                                                                        <div class="startshop-basket-basket-property-value-wrapper">
                                                                            <?= Html::tag('img', '', [
                                                                                'src' => $arProperty['VALUE']['PICTURE'],
                                                                                'alt' => $arProperty['VALUE']['TEXT'],
                                                                                'title' => $arProperty['VALUE']['TEXT']
                                                                            ]) ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="startshop-basket-basket-table-cell startshop-basket-column-offers">
                                            <?php if ($arItem['STARTSHOP']['OFFER']['OFFER']) { ?>
                                                <div class="startshop-basket-cell">
                                                    <?php foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty) { ?>
                                                        <?php if ($arProperty['TYPE'] == 'TEXT') { ?>
                                                            <div class="startshop-basket-basket-property startshop-basket-basket-property-text">
                                                                <div class="startshop-basket-basket-property-name">
                                                                    <?= $arProperty['NAME'] ?>:
                                                                </div>
                                                                <div class="startshop-basket-basket-property-value">
                                                                    <?= $arProperty['VALUE']['TEXT'] ?>
                                                                </div>
                                                            </div>
                                                        <?php } else { ?>
                                                            <div class="startshop-basket-basket-property startshop-basket-basket-property-picture">
                                                                <div class="startshop-basket-basket-property-name">
                                                                    <?= $arProperty['NAME'] ?>:
                                                                </div>
                                                                <div class="startshop-basket-basket-property-value">
                                                                    <div class="startshop-basket-basket-property-value-wrapper">
                                                                        <?= Html::tag('img', '', [
                                                                            'loading' => 'lazy',
                                                                            'src' => $arProperty['VALUE']['PICTURE'],
                                                                            'alt' => $arProperty['VALUE']['TEXT'],
                                                                            'title' => $arProperty['VALUE']['TEXT']
                                                                        ]) ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="startshop-basket-basket-table-cell startshop-basket-column-price">
                                            <div class="startshop-basket-basket-price-title">
                                                <?= Loc::GetMessage('SBB_DEFAULT_COLUMN_PRICE') ?>
                                            </div>
                                            <div class="startshop-basket-basket-price-value">
                                                <?= $arItem['STARTSHOP']['BASKET']['PRICE']['PRINT_VALUE'] ?>
                                            </div>
                                        </div>
                                        <div class="startshop-basket-basket-table-cell startshop-basket-column-quantity">
                                            <div class="startshop-basket-cell">
                                                <?= Html::beginTag('div', [
                                                    'class' => [
                                                        'intec-ui' => [
                                                            '',
                                                            'control-numeric',
                                                            'view-2',
                                                            'size-3'
                                                        ]
                                                    ],
                                                    'data' => [
                                                        'id' => $arItem['ID'],
                                                        'role' => 'item.counter',
                                                        'quantity' => [
                                                            'Value' => $arItem['STARTSHOP']['BASKET']['QUANTITY'],
                                                            'Minimum' => $arItem['STARTSHOP']['QUANTITY']['RATIO'],
                                                            'Ratio' => $arItem['STARTSHOP']['QUANTITY']['RATIO'],
                                                            'Maximum' => $arItem['STARTSHOP']['QUANTITY']['VALUE'],
                                                            'Unlimited' => !$arItem['STARTSHOP']['QUANTITY']['USE'],
                                                            'ValueType' => 'Float'
                                                        ]
                                                    ]
                                                ]) ?>
                                                    <?= Html::beginTag('button', [
                                                        'class' => [
                                                            'startshop-basket-basket-quantity-button',
                                                            'intec-ui-part-decrement'
                                                        ],
                                                        'data' => [
                                                            'id' => $arItem['ID'],
                                                            'action' => 'decrease'
                                                        ]
                                                    ]) ?>
                                                        -
                                                    <?= Html::endTag('button') ?>
                                                    <?= Html::tag('input', '', [
                                                        'class' => 'intec-ui-part-input',
                                                        'type' => 'text',
                                                        'value' => $arItem['STARTSHOP']['BASKET']['QUANTITY'],
                                                        'data-role' => 'item.numeric'
                                                    ]) ?>
                                                    <?= Html::beginTag('button', [
                                                        'class' => [
                                                            'startshop-basket-basket-quantity-button',
                                                            'intec-ui-part-increment'
                                                        ],
                                                        'data' => [
                                                            'id' => $arItem['ID'],
                                                            'action' => 'increase'
                                                        ]
                                                    ]) ?>
                                                        +
                                                    <?= Html::endTag('button') ?>
                                                <?= Html::endTag('div') ?>
                                            </div>
                                        </div>
                                        <div class="startshop-basket-basket-table-cell startshop-basket-column-total">
                                            <div class="startshop-basket-cell" data-role="item.price.total">
                                                <?= CStartShopCurrency::FormatAsString($arItem['STARTSHOP']['BASKET']['PRICE']['VALUE'] * $arItem['STARTSHOP']['BASKET']['QUANTITY'], $arParams['CURRENCY']) ?>
                                            </div>
                                        </div>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'startshop-basket-basket-table-cell',
                                                'startshop-basket-column-control'
                                            ],
                                            'data' => [
                                                'role' => $arParams['SHOW_ALERT_FORM'] === 'Y' ? 'alert.form.show' : 'delete.button',
                                                'id' => $arItem['ID']
                                            ]
                                        ]) ?>
                                            <i class="startshop-button-custom startshop-button-delete basket-delete-icon"></i>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?php } ?>
                                <div class="startshop-basket-basket-table-row-total">
                                    <?php if ($arParams['USE_ITEMS_PICTURES'] == 'Y') { ?>
                                        <div class="startshop-basket-basket-table-cell"></div>
                                    <?php } ?>
                                    <div class="startshop-basket-basket-table-cell"></div>
                                    <div class="startshop-basket-basket-table-cell startshop-basket-column-offers"></div>
                                    <div class="startshop-basket-basket-table-cell"></div>
                                    <div class="startshop-basket-basket-table-cell total-title">
                                        <?= Loc::GetMessage('SBB_DEFAULT_FIELD_SUM') ?>
                                    </div>
                                    <div class="startshop-basket-basket-table-cell startshop-basket-column-total">
                                        <div class="startshop-basket-cell">
                                            <?= $arResult['SUM']['PRINT_VALUE'] ?>
                                        </div>
                                    </div>
                                    <a class="startshop-basket-basket-table-cell"></a>
                                </div>
                            </div>
                        </div>
                        <div class="startshop-basket-basket-info-result">
                            <?php if ($arParams['USE_BUTTON_ORDER'] == 'Y' || $arFastOrder['SHOW']) { ?>
                                <div class="startshop-basket-basket-buttons">
                                    <div class="startshop-basket-buttons-wrapper intec-grid intec-grid intec-grid-a-h-end intec-grid-650-wrap intec-grid-a-h-end intec-grid-a-h-650-center">
                                        <?php if ($arFastOrder['SHOW']) { ?>
                                            <?= Html::beginTag('a', [
                                                'class' => [
                                                    'intec-grid-item' => [
                                                        'auto',
                                                        '650-1'
                                                    ],
                                                    'intec-ui' => [
                                                        '',
                                                        'control-button',
                                                        'mod-transparent',
                                                        'scheme-current',
                                                        'mod-round-5',
                                                        'size-4'
                                                    ]
                                                ],
                                                'onclick' => '(function () {
                                                    template.api.components.show('.JavaScript::toObject(array(
                                                        'component' => 'intec.universe:sale.order.fast',
                                                        'template' => $arFastOrder['TEMPLATE'],
                                                        'parameters' => $arFastOrder['PARAMETERS'],
                                                        'settings' => [
                                                            'parameters' => [
                                                                'width' => null
                                                            ]
                                                        ]
                                                    )).');
                                                })()'
                                            ]) ?>
                                                <?= Loc::GetMessage('SBB_DEFAULT_FAST_ORDER') ?>
                                            <?= Html::endTag('a') ?>
                                        <?php } ?>
                                        <?php if ($arParams['USE_BUTTON_ORDER'] == 'Y') { ?>
                                            <?= Html::beginTag('a', [
                                                    'class' => [
                                                        'intec-grid-item' => [
                                                            'auto',
                                                            '650-1'
                                                        ],
                                                        'startshop-basket-basket-order-button',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-button',
                                                            'scheme-current',
                                                            'mod-round-5',
                                                            'size-4'
                                                        ]
                                                    ],
                                                    'href' => $arParams['URL_ORDER']
                                            ]) ?>
                                                <?= Loc::GetMessage('SBB_DEFAULT_BUTTON_ORDER') ?>
                                            <?= Html::endTag('a') ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="startshop-basket-basket-empty intec-no-select">
                        <div class="startshop-basket-basket-empty-image">
                        </div>
                        <div class="startshop-basket-basket-empty-title">
                            <?= Loc::GetMessage('SBB_DEFAULT_EMPTY_BASKET') ?>
                        </div>
                        <div class="startshop-basket-basket-empty-description">
                            <?= Loc::GetMessage('SBB_DEFAULT_CHOOSE') ?>
                        </div>

                        <?= Html::beginTag('a', [
                            'class' => [
                                'startshop-basket-basket-empty-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-round-5',
                                    'size-4'
                                ]
                            ],
                            'href' => $arParams['URL_CATALOG']
                        ]) ?>
                            <?= Loc::GetMessage('SBB_DEFAULT_CATALOG') ?>
                        <?= Html::endTag('a') ?>
                    </div>
                <?php } ?>
                <?php if ($arParams['SHOW_ALERT_FORM'] === 'Y') {
                    include(__DIR__.'/parts/alert.form.php');
                } ?>
            <?php if ($bIsAjax) {
                exit();
            } ?>
            </div>
        </div>
    </div>
<?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>