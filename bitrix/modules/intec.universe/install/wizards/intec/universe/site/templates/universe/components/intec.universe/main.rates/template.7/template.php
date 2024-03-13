<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arBlocks = $arResult['BLOCKS'];
$arSvg = [
    'PRICE' => [
        'DIFFERENCE' => FileHelper::getFileData(__DIR__.'/svg/price.difference.icon.svg')
    ],
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/navigation.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/navigation.right.svg')
    ]
];

$vItems = include(__DIR__.'/parts/items.php');
$vTabs = include(__DIR__.'/parts/tabs.php');

?>
<div class="widget c-rates c-rates-template-7" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['HEADER']['TEXT'], [
                            'class' => [
                                'widget-title',
                                'align-'.$arBlocks['HEADER']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                            'class' => [
                                'widget-description',
                                'align-'.$arBlocks['DESCRIPTION']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?php

                    if ($arVisual['TABS']['USE'])
                        $vTabs($arResult['SECTIONS']);
                    else
                        $vItems($arResult['ITEMS']);

                ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>