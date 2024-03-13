<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arIBlock = $arResult['IBLOCK'];

include(__DIR__.'/parts/detail.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news',
        'c-news-collections-1',
        'p-detail'
    ]
]) ?>
    <?= Html::beginTag('div', [
        'class' => 'news-content',
        'data' => [
            'role' => 'content'
        ]
    ]) ?>
        <?php if ($arDetail['SHOW']) { ?>
            <?php $APPLICATION->IncludeComponent(
                'bitrix:news.detail',
                $arDetail['TEMPLATE'],
                $arDetail['PARAMETERS'],
                $component
            ); ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>