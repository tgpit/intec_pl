<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\Core;
use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core') || !Loader::includeModule('iblock'))
    return;

/** @var InnerTemplate $oTemplate */
$oTemplate = $arResult['TEMPLATE'];
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sSiteUrl = Core::$app->request->getHostInfo().SITE_DIR;

$arVisual = $arResult['VISUAL'];
$arData = [
    'id' => $sTemplateId
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-footer',
        'c-footer-template-1',
        'vcard'
    ],
    'data' => [
        'theme' => $arResult['THEME']
    ]
]) ?>
    <div class="widget-content">
        <div style="display: none;">
            <span class="url">
                <span class="value-title" title="<?= $sSiteUrl ?>"></span>
            </span>
            <span class="fn org">
                <?= $arResult['COMPANY_NAME'] ?>
            </span>
            <img class="photo" src="<?= $sSiteUrl.'include/logotype.png' ?>" alt="<?= $arResult['COMPANY_NAME'] ?>" />
        </div>
        <?php if ($arParams['PRODUCTS_VIEWED_SHOW'] === 'Y') { ?>
            <div class="widget-part">
                <?php include(__DIR__.'/parts/products.viewed.php') ?>
            </div>
        <?php } ?>
        <div class="widget-view">
            <?php if (!empty($oTemplate)) { ?>
                <?= $oTemplate->render(
                    $arParams,
                    $arResult,
                    $arData
                ) ?>
            <?php } ?>
        </div>
    </div>
<?= Html::endTag('div') ?>