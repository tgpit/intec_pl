<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var array $arData
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'ADDITIONAL_PRODUCTS_';
$sLength = StringHelper::length($sPrefix);

$arParameters = [];

$arExcluded = [
    'SHOW',
    'HEADER_SHOW',
    'HEADER_TEXT'
];

foreach ($arParams as $key => $sValue) {
    if (StringHelper::startsWith($key, $sPrefix)) {
        $key = StringHelper::cut($key, $sLength);

        if (!ArrayHelper::isIn($key, $arExcluded))
            $arParameters[$key] = $sValue;
    }
}

if (!empty($arParameters['TEMPLATE']))
    $arParameters['TEMPLATE'] = 'catalog.' . $arParameters['TEMPLATE'];

unset($key, $sValue);

if (empty($arParameters['FILTER_NAME']))
    $arParameters['FILTER_NAME'] = 'arrFilter';

$productsFilterName = ArrayHelper::getValue($arParameters, ['FILTER_NAME'], '');

$GLOBALS[$productsFilterName] = [
    'ID' => $arData['ADDITIONAL']['PRODUCTS']
];
?>
    <div class="news-detail-additional-item">
        <?php if ($arVisual['ADDITIONAL']['PRODUCTS']['HEADER']['SHOW']) { ?>
            <div class="news-detail-additional-header intec-template-part intec-template-part-title">
                <?= Html::stripTags($arVisual['ADDITIONAL']['PRODUCTS']['HEADER']['TEXT'], ['br']) ?>
            </div>
        <?php } ?>
        <div class="news-detail-additional-content">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:catalog.section',
                $arParameters['TEMPLATE'],
                ArrayHelper::merge($arParameters, [
                    'DATE_FORMAT' => $arParams['DATE_FORMAT'],
                    'DATE_TYPE' => $arParams['DATE_TYPE'],
                    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                    'SHOW_ALL_WO_SECTION' => 'Y',
                ]),
                $component
            ) ?>
        </div>
    </div>
<?php unset($sPrefix, $sLength, $arParameters, $arExcluded) ?>