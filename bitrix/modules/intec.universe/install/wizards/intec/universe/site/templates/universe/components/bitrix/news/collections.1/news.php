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
$arDescription = [
    'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'inside',
        'outside'
    ], $arParams['DESCRIPTION_POSITION']),
    'VALUE' => !empty($arIBlock['DESCRIPTION']) ? $arIBlock['DESCRIPTION'] : null
];

if (empty($arDescription['VALUE']))
    $arDescription['SHOW'] = false;

include(__DIR__.'/parts/list.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news',
        'c-news-collections-1',
        'p-news'
    ]
]) ?>
    <div class="news-wrapper intec-content intec-content-visible">
        <div class="news-wrapper-2 intec-content-wrapper">
            <?php if ($arDescription['SHOW'] ) { ?>
                <?= Html::tag('div', $arDescription['VALUE'], [
                    'class' => [
                        'news-description',
                        'news-description-'.$arDescription['POSITION'],
                        'intec-ui-markup-text'
                    ]
                ]) ?>
            <?php } ?>
            <?php if ($arList['SHOW']) { ?>
                <? $APPLICATION->IncludeComponent (
                    'bitrix:news.list',
                    $arList['TEMPLATE'],
                    $arList['PARAMETERS'],
                    $component
                ) ?>
            <?php } ?>
        </div>
    </div>
<?= Html::endTag('div') ?>

