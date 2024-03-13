<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 */

$sPrefix = 'REVIEWS_';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];
$arExcluded = [
    'SHOW',
    'NAME',
    'IBLOCK_ID',
    'IBLOCK_TYPE',
    'MODE',
    'PROPERTY_ID',
    'ID',
    'SETTINGS_USE',
    'LAZYLOAD_USE',
    'CAPTCHA_USE',
    'CACHE_TYPE',
    'CACHE_TIME',
    'CACHE_NOTES'
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
    'PROPERTY_ID' => $arParams['REVIEWS_PROPERTY_ELEMENT_ID'],
    'ID' => $arResult['ID'],
    'CAPTCHA_USE' => $arParams['REVIEWS_USE_CAPTCHA'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
], $arProperties);

if ($arProperties['MODE'] === 'linked' && empty($arProperties['ID']))
    $arProperties['ID'] = $arResult['ID'];

?>
<div id="<?= $sTemplateId.'-reviews-content' ?>" data-print="false">
    <div data-role="reviews.loader">
        <div class="catalog-element-loader-reviews-button catalog-element-loader"></div>
        <div class="catalog-element-loader-reviews-items catalog-element-loader"></div>
    </div>
</div>
<script type="text/javascript">
    template.load(function (data) {
        this.api.components.get(<?= JavaScript::toObject([
            'component' => 'intec.universe:reviews',
            'template' => 'template.1',
            'parameters' => $arProperties
        ]) ?>).then(function (response) {
            data.nodes.find('[data-role="reviews.loader"]').remove();
            data.nodes.append(response);
        });
    }, {
        'name': '[Component] bitrix:catalog.element (catalog.default.2) > Reviews',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId.'-reviews-content') ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
<?php unset($arProperties) ?>