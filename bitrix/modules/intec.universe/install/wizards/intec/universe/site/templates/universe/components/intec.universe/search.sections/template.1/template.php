<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-intec-universe',
        'c-search-sections',
        'c-search-sections-template-1'
    ]
]) ?>
    <div class="search-sections-content">
        <div class="search-sections-items">
            <div class="search-sections-title">
                <?= Loc::getMessage('C_SEARCH_SECTIONS_TEMPLATE_1_TITLE') ?>
            </div>
            <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                <?= Html::beginTag('a', [
                    'class' => Html::cssClassFromArray([
                        'search-sections-item' => true,
                        'intec-cl-text-hover' => true,
                        'intec-cl-text' => $arSection['ACTIVE'] === 'Y'
                    ], true),
                    'href' => $arSection['URL'],
                    'data-active' => $arSection['CURRENT'] === 'Y' ? 'true' : 'false'
                ]) ?>
                    <div class="intec-grid intec-grid-a-h-between">
                        <span class="intec-grid-item-auto search-sections-item-name">
                            <?= $arSection['NAME'] ?>
                        </span>
                            <span class="intec-grid-item-auto search-sections-item-count">
                            <?= $arSection['ELEMENTS_COUNT'] ?>
                        </span>
                    </div>
                <?= Html::endTag('a') ?>
            <?php } ?>
        </div>
    </div>
<?= Html::endTag('div') ?>