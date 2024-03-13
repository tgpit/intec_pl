<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

?>
<div class="widget c-collections c-collections-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arBlocks['LINK_ALL']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['LINK_ALL']['SHOW']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-title',
                                'align-'.(
                                    $arBlocks['LINK_ALL']['SHOW'] ? 'left' : $arBlocks['HEADER']['POSITION']
                                )
                            ]
                        ]) ?>
                            <div class="intec-grid intec-grid-a-h-end intec-grid-a-v-center intec-grid-i-h-8">
                                <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                    <div class="intec-grid-item">
                                        <?= $arBlocks['HEADER']['TEXT'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arBlocks['LINK_ALL']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::beginTag('a', [
                                            'class' => 'widget-all',
                                            'href' => $arBlocks['LINK_ALL']['BUTTON']['URL']
                                        ]) ?>
                                            <span class="widget-all-desktop intec-cl-text-hover">
                                                <?php if (!empty($arBlocks['LINK_ALL']['BUTTON']['TEXT'])) { ?>
                                                    <?= $arBlocks['LINK_ALL']['BUTTON']['TEXT'] ?>
                                                <?php } else { ?>
                                                    <?= Loc::getMessage('C_MAIN_COLLECTIONS_TEMPLATE_2_TEMPLATE_LINK_ALL_TEXT_DEFAULT') ?>
                                                <?php } ?>
                                            </span>
                                            <span class="widget-all-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                                <?= FileHelper::getFileData(__DIR__.'/svg/list.arrow.svg') ?>
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
                                    $arBlocks['LINK_ALL']['SHOW'] ? 'left' : $arBlocks['DESCRIPTION']['POSITION']
                                )
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch intec-grid-i-16" data-role="items">
                    <!--items-->
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sDescription = null;

                        if (!empty($arItem['DETAIL_TEXT']))
                            $sDescription = $arItem['DETAIL_TEXT'];
                        else if (!empty($arItem['PREVIEW_TEXT']))
                            $sDescription = $arItem['PREVIEW_TEXT'];

                        $sPicture = null;

                        if (!empty($arItem['PREVIEW_PICTURE']))
                            $sPicture = $arItem['PREVIEW_PICTURE'];
                        else if (!empty($arItem['DETAIL_PICTURE']))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 400,
                                'height' => 400
                            ], BX_RESIZE_IMAGE_EXACT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="intec-grid-item-2 intec-grid-item-1024-1">
                            <div class="widget-item" id="<?= $sAreaId ?>">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'intec-grid' => [
                                            '',
                                            'i-h-12',
                                            'i-v-8',
                                            'wrap',
                                            'a-v-center'
                                        ]
                                    ]
                                ])?>
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', '', [
                                            'class' => [
                                                'widget-item-picture',
                                                'intec-image-effect'
                                            ],
                                            'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                            ]
                                        ]) ?>
                                    </div>
                                    <div class="intec-grid-item">
                                        <div class="widget-item-text">
                                            <div class="widget-item-name">
                                                <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'span', $arItem['NAME'], [
                                                    'class' => Html::cssClassFromArray([
                                                        'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                                    ], true),
                                                    'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                    'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                            </div>
                                            <?php if (!empty($sDescription)) { ?>
                                                <div class="widget-item-description">
                                                    <?= $sDescription ?>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arVisual['LINK']['USE']) { ?>
                                                <div class="widget-item-link">
                                                    <?= Html::tag('a', Loc::getMessage('C_MAIN_COLLECTIONS_TEMPLATE_2_TEMPLATE_LINK_DETAIL'), [
                                                        'class' => 'intec-cl-text',
                                                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                        'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                    ]) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            </div>
                        </div>
                    <?php } ?>
                    <!--items-->
                </div>
            </div>
            <?php if ($arResult['NAVIGATION']['USE']) { ?>
                <div class="widget-pagination">
                    <?= $arResult['NAVIGATION']['PRINT'] ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if (!defined('EDITOR') && $arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax')
        include(__DIR__.'/parts/script.php');
    ?>
</div>