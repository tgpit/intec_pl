<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

/**
 * @var Closure $vPicture()
 */
$vPicture = include(__DIR__.'/parts/picture.php');

?>
<div class="widget c-reviews c-reviews-template-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arBlocks['FOOTER']['SHOW'] || $arVisual['SEND']['USE']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['FOOTER']['SHOW'] || $arVisual['SEND']['USE']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-title',
                                'align-'.$arBlocks['HEADER']['POSITION'],
                                $arBlocks['HEADER']['POSITION'] === 'center' && $arBlocks['FOOTER']['SHOW'] ? 'widget-title-margin' : null
                            ]
                        ]) ?>
                        <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-end intec-grid-i-h-12">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="intec-grid-item">
                                    <?= $arBlocks['HEADER']['TEXT'] ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['SEND']['USE']) { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'widget-send',
                                            'intec-cl' => [
                                                'text-hover',
                                                'border-hover',
                                                'svg-path-stroke-hover'
                                            ]
                                        ],
                                        'data-role' => 'review.send'
                                    ]) ?>
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                                            <div class="widget-send-icon intec-ui-picture intec-grid-item-auto">
                                                <?= FileHelper::getFileData(__DIR__.'/svg/send.svg') ?>
                                            </div>
                                            <div class="widget-send-content intec-grid-item">
                                                <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_1_TEMPLATE_SEND_BUTTON_DEFAULT') ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::beginTag('a', [
                                        'class' => 'widget-all',
                                        'href' => $arBlocks['FOOTER']['LINK']
                                    ]) ?>
                                        <span class="widget-all-desktop intec-cl-text-hover">
                                            <?php if (empty($arBlocks['FOOTER']['TEXT'])) { ?>
                                                <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_1_TEMPLATE_FOOTER_TEXT_DEFAULT') ?>
                                            <?php } else { ?>
                                                <?= $arBlocks['FOOTER']['TEXT'] ?>
                                            <?php } ?>
                                        </span>
                                        <span class="widget-all-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                            <?= FileHelper::getFileData(__DIR__.'/svg/all.mobile.svg') ?>
                                        </span>
                                    <?= Html::endTag('a') ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                            'class' => [
                                'widget-description',
                                'align-'.(
                                    $arBlocks['FOOTER']['SHOW'] || $arVisual['SEND']['USE'] ? 'left' : $arBlocks['DESCRIPTION']['POSITION']
                                )
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'owl-carousel' => $arVisual['SLIDER']['USE'],
                        'intec-grid' => [
                            '' => !$arVisual['SLIDER']['USE'],
                            'wrap' => !$arVisual['SLIDER']['USE'],
                            'a-v-stretch' => !$arVisual['SLIDER']['USE'],
                            'i-h-15' => !$arVisual['SLIDER']['USE'],
                            'i-v-25' => !$arVisual['SLIDER']['USE']
                        ]
                    ], true),
                    'data' => [
                        'role' => 'container',
                        'grid' => $arVisual['COLUMNS'],
                        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        if (!$arItem['DATA']['PREVIEW']['SHOW'])
                            continue;

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                    '1024-1' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 2
                                ]
                            ], true)
                        ]) ?>
                            <div class="widget-item-wrapper intec-grid intec-grid-768-wrap" id="<?= $sAreaId ?>">
                                <div class="widget-item-picture-wrap intec-grid-item-auto intec-grid-item-768-1">
                                    <?php $vPicture($arItem) ?>
                                </div>
                                <div class="widget-item-text intec-grid-item intec-grid-item-768-1">
                                    <?= Html::tag($sTag, $arItem['NAME'], [
                                        'class' => 'widget-item-name',
                                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                    ]) ?>
                                    <?php if ($arVisual['POSITION']['SHOW'] && !empty($arItem['DATA']['POSITION'])) { ?>
                                        <div class="widget-item-position">
                                            <?= $arItem['DATA']['POSITION'] ?>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-description">
                                        <?= $arItem['DATA']['PREVIEW']['VALUE'] ?>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>
<?php if ($arVisual['VIDEO']['SHOW'] || $arVisual['SLIDER']['USE'])
    include(__DIR__.'/parts/script.php');
?>