<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEM']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$sId = $sTemplateId.'_'.$arResult['ITEM']['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['ITEM']['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['ITEM']['DELETE_LINK']);

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'VIDEO' => FileHelper::getFileData(__DIR__.'/svg/video.play.svg')
];

$renderTitle = include(__DIR__.'/parts/title.php');
$renderName = include(__DIR__.'/parts/name.php');
$renderPreview = include(__DIR__.'/parts/preview.php');
$renderButton = include(__DIR__.'/parts/button.php');
$renderPicture = include(__DIR__.'/parts/picture.php');
$renderVideo = include(__DIR__.'/parts/video.php');
$renderAdvantages = include(__DIR__.'/parts/advantages.php');

?>
<div class="widget c-about-ref c-about-ref-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-primary intec-content-visible">
        <?= Html::beginTag('div', [
            'id' => $sAreaId,
            'class' => 'intec-content-wrapper',
            'data-view' => $arVisual['VIEW']
        ]) ?>
            <?php if ($arVisual['VIEW'] === 1) { ?>
                <div class="intec-grid intec-grid-a-v-start intec-grid-i-20">
                    <div class="widget-block-picture intec-grid-item-auto" data-role="video">
                        <?php $renderPicture() ?>
                        <?php $renderVideo() ?>
                    </div>
                    <div class="widget-block-text intec-grid-item-auto intec-grid-item-768-1">
                        <?php $renderTitle() ?>
                        <?php $renderName() ?>
                        <?php $renderPreview() ?>
                        <?php $renderAdvantages() ?>
                        <?php $renderButton() ?>
                    </div>
                </div>
            <?php } else if ($arVisual['VIEW'] === 2) { ?>
                <div class="intec-grid intec-grid-a-v-start intec-grid-i-20 intec-grid-1024-wrap">
                    <div class="widget-block-picture intec-grid-item-auto intec-grid-item-1024-1">
                        <?php $renderTitle() ?>
                        <?php $renderName() ?>
                        <?php $renderButton() ?>
                    </div>
                    <div class="widget-block-text intec-grid-item-auto intec-grid-item-1024-1">
                        <?php $renderPreview() ?>
                        <?php $renderAdvantages() ?>
                    </div>
                </div>
            <?php } else if ($arVisual['VIEW'] === 3) { ?>
                <div class="intec-grid intec-grid-a-v-start intec-grid-i-20">
                    <div class="widget-block-picture intec-grid-item-auto" data-role="video">
                        <?php $renderPicture() ?>
                        <?php $renderVideo() ?>
                    </div>
                    <div class="widget-block-text intec-grid-item-auto intec-grid-item-768-1">
                        <?php $renderTitle() ?>
                        <?php $renderName() ?>
                        <?php $renderPreview() ?>
                        <?php $renderButton() ?>
                    </div>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    </div>
    <?php if ($arVisual['VIDEO']['SHOW'])
        include(__DIR__.'/parts/script.php')
    ?>
</div>