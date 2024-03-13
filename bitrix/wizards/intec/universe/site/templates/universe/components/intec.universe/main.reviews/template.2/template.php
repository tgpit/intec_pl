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

$iCountTotal = count($arResult['ITEMS']);

?>
<div class="widget c-reviews c-reviews-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content">
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
                                                    <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_2_TEMPLATE_SEND_BUTTON_DEFAULT') ?>
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
                                                    <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_2_TEMPLATE_FOOTER_TEXT_DEFAULT') ?>
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
                    'class' => [
                        'widget-items',
                        'owl-carousel'
                    ],
                    'data-role' => 'container'
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        if (!$arItem['DATA']['PREVIEW']['SHOW'])
                            continue;

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $iCount++;

                        $sPicture = null;

                        if (!empty($arItem['PREVIEW_PICTURE']))
                            $sPicture = $arItem['PREVIEW_PICTURE'];
                        else if (!empty($arItem['DETAIL_PICTURE']))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 150,
                                    'height' => 150
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            );

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="widget-item" id="<?= $sAreaId ?>">
                            <div class="widget-item-wrapper intec-grid intec-grid-768-wrap">
                                <div class="widget-item-picture-wrap intec-grid-item-auto intec-grid-item-768-1">
                                    <?= Html::tag($sTag, null, [
                                        'class' => [
                                            'widget-item-picture',
                                            'intec-image-effect'
                                        ],
                                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                        ]
                                    ]) ?>
                                    <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                        <div class="widget-item-counter">
                                            <?= $iCount.' / '.$iCountTotal ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="widget-item-text intec-grid-item intec-grid-item-768-1">
                                    <div class="widget-item-description">
                                        <?= $arItem['DATA']['PREVIEW']['VALUE'] ?>
                                    </div>
                                    <div class="widget-item-name-wrap intec-grid intec-grid-768-wrap">
                                        <div class="intec-grid-item intec-grid-item-768-1">
                                            <?= Html::tag($sTag, $arItem['NAME'], [
                                                'class' => 'widget-item-name',
                                                'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                                'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                            ]) ?>
                                            <?php if ($arItem['DATA']['POSITION']['SHOW']) { ?>
                                                <div class="widget-item-position">
                                                    <?= $arItem['DATA']['POSITION']['VALUE'] ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php if ($arVisual['LOGOTYPE']['SHOW']) {

                                            $sPicture = CFile::ResizeImageGet(
                                                $arItem['DATA']['LOGOTYPE']['PICTURE'], [
                                                    'width' => 150,
                                                    'height' => 150
                                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                            if (!empty($sPicture))
                                                $sPicture = $sPicture['src'];

                                            $sTagLogotype = $arItem['DATA']['LOGOTYPE']['URL']['USE'] ? 'a' : 'div';

                                        ?>
                                            <div class="widget-item-logotype-wrap intec-grid-item intec-grid-item-768-1">
                                                <?= Html::beginTag($sTagLogotype, [
                                                    'class' => 'widget-item-logotype',
                                                    'href' => $sTagLogotype === 'a' ? $arItem['DATA']['LOGOTYPE']['URL']['VALUE'] : null,
                                                    'target' => $sTagLogotype === 'a' && $arVisual['LOGOTYPE']['LINK']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                                    <?= Html::img($sPicture, [
                                                        'alt' => '',
                                                        'loading' => 'lazy'
                                                    ]) ?>
                                                <?= Html::endTag($sTagLogotype) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>