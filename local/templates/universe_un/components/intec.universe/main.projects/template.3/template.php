<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var Closure $vItems()
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];
$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';
$arSvg = [
    'SEE_ALL_MOBILE' => FileHelper::getFileData(__DIR__ . '/svg/header.list.mobile.svg')
];

include(__DIR__.'/parts/items.php');

?>
<div class="widget c-projects c-projects-template-3" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['BUTTON_ALL']['SHOW']) { ?>
        <div class="widget-header" data-link-all="<?= $arVisual['BUTTON_ALL']['SHOW'] ? 'true' : 'false' ?>">
            <div class="intec-content">
                <div class="intec-content-wrapper">
                    <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?> intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                'class' => 'intec-grid-item'
                            ]) ?>
                        <?php } ?>
                        <?php if ($arVisual['BUTTON_ALL']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('a', [
                                    'class' => 'widget-list',
                                    'href' => $arVisual['BUTTON_ALL']['LINK']
                                ])?>
                                <span class="widget-list-desktop intec-cl-text-light-hover"><?= $arVisual['BUTTON_ALL']['TEXT'] ?></span>
                                <span class="widget-list-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                <?= $arSvg['SEE_ALL_MOBILE'] ?>
                            </span>
                                <?= Html::endTag('a')?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                            'class' => [
                                'widget-description',
                                'align-' . $arBlocks['DESCRIPTION']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="widget-content">
        <?php if (!$arVisual['WIDE']) { ?>
            <div class="intec-content">
                <div class="intec-content-wrapper">
        <?php } ?>
                    <?php if ($arVisual['TABS']['USE']) {
                        include(__DIR__.'/parts/tabs.php');
                    } else {
                        $vItems($arResult['ITEMS']);
                    } ?>
        <?php if (!$arVisual['WIDE']) { ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>