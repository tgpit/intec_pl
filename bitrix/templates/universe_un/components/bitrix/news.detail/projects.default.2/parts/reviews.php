<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 */

$sPrefix = 'REVIEWS_';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];
$arExcluded = [
    'IBLOCK_ID',
    'IBLOCK_TYPE',
    'MODE',
    'ID',
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'CACHE_TYPE',
    'CACHE_TIME',
    'CACHE_NOTES',
    'DATE_FORMAT'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    if (ArrayHelper::isIn($sKey, $arExcluded))
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $arExcluded, $sKey, $sValue);

$arProperties = ArrayHelper::merge([
    'IBLOCK_TYPE' => $arParams['REVIEWS_IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['REVIEWS_IBLOCK_ID'],
    'MODE' => 'linked',
    'ID' => $arResult['ID'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['DETAIL_LAZYLOAD_USE'],
    'DATE_FORMAT' => 'd F Y',
    'ALLOW_LINK_REVIEWS' => $arParams['REVIEWS_ALLOW_LINK']
], $arProperties);

?>

<?= Html::beginTag('div', [
    'class' => 'intec-ui-part-tab',
    'id' => $sTemplateId.'-reviews',
    'data-active' => 'false'
]) ?>
    <div class="news-detail-content-tabs-content-item">
        <?php $APPLICATION->IncludeComponent(
            'intec.universe:reviews',
            'template.3',
            $arProperties,
            $component,
            ['HIDE_ICONS' => 'Y']
        ) ?>
    </div>
<?= Html::endTag('div') ?>

<?php unset($arProperties) ?>