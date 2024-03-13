<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arParams['ELEMENT_ID']) && empty($arParameters['SECTION_ID']))
    return;

$arParameters = ArrayHelper::merge($arParams, [
    'SECTION_URL' => $arResult['URL']['LIST'].$arResult['URL']['SECTION'],
    'DETAIL_URL' => $arResult['URL']['LIST'].$arResult['URL']['DETAIL'],
    'SHOW_DEACTIVATED' => 'N',
    'USE_PRICE_COUNT' => 'N',
    'PRICE_VAT_SHOW_VALUE' => 'N',
    'SET_TITLE' => 'N',
    'SET_CANONICAL_URL' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'USE_MAIN_ELEMENT_SECTION' => 'N',
    'STRICT_SECTION_CHECK' => 'N',
    'ADD_SECTIONS_CHAIN' => 'N',
    'ADD_ELEMENT_CHAIN' => 'N',
    'SHOW_SKU_DESCRIPTION' => 'N',
    'COMPATIBLE_MODE' => 'Y',
    'DISABLE_INIT_JS_IN_COMPONENT' => 'N',
    'SET_VIEWED_IN_COMPONENT' => 'N'
]);

$arBlocks = [
    'HEADER' => [
        'SHOW' => $arParams['HEADER_SHOW'] === 'Y',
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['HEADER_POSITION']),
        'TEXT' => !empty($arParams['HEADER_TEXT']) ? $arParams['HEADER_TEXT'] : Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_HEADER_TEXT_DEFAULT')
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y' && !empty($arParams['DESCRIPTION_TEXT']),
        'POSITION' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['DESCRIPTION_POSITION']),
        'TEXT' => !empty($arParams['DESCRIPTION_TEXT']) ? $arParams['DESCRIPTION_TEXT'] : null
    ]
];

?>
<div class="widget c-widget c-widget-product-1">
<div class="intec-content intec-content-visible">
    <div class="intec-content-wrapper">
        <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
            <div class="widget-header">
                <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                    <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                        <?= $arBlocks['HEADER']['TEXT'] ?>
                    </div>
                <?php } ?>
                <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                        <?= $arBlocks['DESCRIPTION']['TEXT'] ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="widget-content">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.element',
                '.default',
                $arParameters,
                $component
            ) ?>
        </div>
    </div>
</div>
</div>
<?php unset($arParameters) ?>