<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arLazyLoad = $arResult['LAZYLOAD'];

$arNavigation = !empty($arResult['NAV_RESULT']) ? [
    'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
    'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
    'NavNum' => $arResult['NAV_RESULT']->NavNum
] : [
    'NavPageCount' => 1,
    'NavPageNomer' => 1,
    'NavNum' => $this->randString()
];

$arVisual['NAVIGATION']['LAZY']['BUTTON'] =
    $arVisual['NAVIGATION']['LAZY']['BUTTON'] &&
    $arNavigation['NavPageNomer'] < $arNavigation['NavPageCount'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-list',
        'c-news-list-images-tile-1'
    ],
]) ?>
    <?php if (!empty($arResult['ITEMS'])) { ?>
        <?php if ($arVisual['NAVIGATION']['SHOW']['TOP']) { ?>
            <div data-pagination-num="<?= $arResult['NAVIGATION']['NUMBER'] ?>">
                <!-- pagination-container -->
                <?= $arResult['NAV_STRING'] ?>
                <!-- pagination-container -->
            </div>
        <?php } ?>
        <?php include(__DIR__.'/parts/tiles.php'); ?>
        <?php if ($arVisual['NAVIGATION']['SHOW']['BOTTOM']) { ?>
            <div data-pagination-num="<?= $arResult['NAVIGATION']['NUMBER'] ?>">
                <!-- pagination-container -->
                <?= $arResult['NAV_STRING'] ?>
                <!-- pagination-container -->
            </div>
        <?php } ?>
    <?php } ?>
<?= Html::endTag('div') ?>

<?php include(__DIR__.'/parts/script.php'); ?>


