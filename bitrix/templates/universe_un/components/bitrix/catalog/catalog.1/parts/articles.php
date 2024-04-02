<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'ADDITIONAL_ARTICLES_';
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
    $arParameters['TEMPLATE'] = 'news.' . $arParameters['TEMPLATE'];

unset($key, $sValue);

$GLOBALS['arrFilterArticles'] = [
    'ID' => $arResult['ADDITIONAL']['ARTICLES']['VALUE']
];
?>
    <div class="catalog-additional-item">
        <?php if ($arResult['ADDITIONAL']['ARTICLES']['HEADER']['SHOW']) { ?>
            <div class="catalog-additional-header intec-template-part intec-template-part-title">
                <?= Html::stripTags($arResult['ADDITIONAL']['ARTICLES']['HEADER']['TEXT'], ['br']) ?>
            </div>
        <?php } ?>
        <div class="catalog-additional-content">
            <?php $APPLICATION->IncludeComponent(
                'bitrix:news.list',
                $arParameters['TEMPLATE'],
                ArrayHelper::merge($arParameters, [
                    'FILTER_NAME' => 'arrFilterArticles',
                    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                    "SET_TITLE" => "N",
                    "SET_BROWSER_TITLE" => "N",
                    "SET_META_KEYWORDS" => "N",
                    "SET_META_DESCRIPTION" => "N",
                    "SET_LAST_MODIFIED" => "N",
                    "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
                    "ADD_SECTIONS_CHAIN" => "N",
                ]),
                $component
            ) ?>
        </div>
    </div>
<?php unset($sPrefix, $sLength, $arParameters, $arExcluded) ?>