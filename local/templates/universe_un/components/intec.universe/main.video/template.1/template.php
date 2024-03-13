<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEM']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$sId = $sTemplateId.'_'.$arResult['ITEM']['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['ITEM']['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['ITEM']['DELETE_LINK']);

$sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
if (!empty($arResult['PICTURE']['SRC'])) {
    $sPicture = $arResult['PICTURE']['SRC'];
}

$arSvg = [
    'VIDEO' => FileHelper::getFileData(__DIR__.'/svg/video.play.svg')
];

?>
<div class="widget c-video c-video-template-1" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['BUTTONS']['SEE_ALL']['SHOW']) { ?>
        <div class="widget-header intec-content">
            <div class="intec-content-wrapper">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title-container intec-grid-item">
                            <?= Html::tag('div', Html::stripTags($arBlocks['HEADER']['TEXT'], ['br']), [
                                'class' => [
                                    'widget-title',
                                    'align-'.$arBlocks['HEADER']['POSITION'],
                                    $arVisual['BUTTONS']['SEE_ALL']['SHOW'] ? 'widget-title-margin' : null
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['BUTTONS']['SEE_ALL']['SHOW']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-all-container' => true,
                                'mobile' => $arBlocks['HEADER']['SHOW'],
                                'intec-grid-item' => [
                                    'auto' => $arBlocks['HEADER']['SHOW'],
                                    '1' => !$arBlocks['HEADER']['SHOW']
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'widget-all-button',
                                    'intec-cl-text-light-hover'
                                ],
                                'href' => $arVisual['BUTTONS']['SEE_ALL']['LINK']
                            ])?>
                                <span><?= $arVisual['BUTTONS']['SEE_ALL']['TEXT'] ?></span>
                                <i class="fal fa-angle-right"></i>
                            <?= Html::endTag('a')?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description-container intec-grid-item-1">
                            <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                <?= Html::stripTags($arBlocks['DESCRIPTION']['TEXT'], ['br']) ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="widget-content">
        <?php if (!$arVisual['WIDE']) { ?>
            <div class="widget-content-wrapper intec-content intec-content-visible">
                <div class="widget-content-wrapper-2 intec-content-wrapper">
        <?php } ?>
        <div class="widget-item" id="<?= $sAreaId ?>" data-role="item">
            <?= Html::beginTag('div', [
                'class' => 'widget-item-wrapper',
                'title' => $arResult['ITEM']['NAME'],
                'style' => [
                    'height' => $arVisual['HEIGHT'] !== 'auto' ? $arVisual['HEIGHT'].'px' : null,
                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                ],
                'data' => [
                    'mode' => $arVisual['HEIGHT'] === 'auto' ? 'auto' : 'fixed',
                    'theme' => $arVisual['THEME'],
                    'rounded' => $arVisual['ROUNDED'] ? 'true' : 'false',
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ],
                'data-shadow' => $arVisual['SHADOW']['USE'] ? $arVisual['SHADOW']['MODE'] : null,
                'data-src' => !empty($arResult['LINK']['embed']) ? $arResult['LINK']['embed'] : null,
                'data-parallax-ratio' => $arVisual['PARALLAX']['USE'] ? $arVisual['PARALLAX']['RATIO'] : null
            ]) ?>
                <?php if ($arVisual['FADE']) { ?>
                    <div class="widget-item-fade"></div>
                <?php } ?>
                <div class="widget-video-button-wrapper">
                    <?= $arSvg['VIDEO'] ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
        <?php if (!$arVisual['WIDE']) { ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php if (!empty($arResult['LINK']) && !defined('EDITOR')) include(__DIR__.'/parts/script.php') ?>