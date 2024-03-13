<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arNavigation = $arResult['NAVIGATION'];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

?>
<div class="widget c-shares c-shares-template-3" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arResult['HEADER_BLOCK']['SHOW'] || $arResult['DESCRIPTION_BLOCK']['SHOW'] || $arResult['FOOTER_BLOCK']['SHOW']) { ?>
                <div class="widget-header" data-link-all="<?= $arResult['FOOTER_BLOCK']['SHOW'] ? 'true' : 'false' ?>">
                    <div class="intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                        <div class="intec-grid-item">
                            <?php if ($arResult['HEADER_BLOCK']['SHOW']) { ?>
                                <?= Html::tag('div', Html::encode($arResult['HEADER_BLOCK']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arResult['HEADER_BLOCK']['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?php if ($arResult['DESCRIPTION_BLOCK']['SHOW']) { ?>
                                <?= Html::tag('div', Html::encode($arResult['DESCRIPTION_BLOCK']['TEXT']), [
                                    'class' => [
                                        'widget-description',
                                        'align-'.$arResult['DESCRIPTION_BLOCK']['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                        <?php if ($arResult['FOOTER_BLOCK']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'widget-all-button',
                                        'intec-cl-text-light-hover'
                                    ],
                                    'href' => $arResult['FOOTER_BLOCK']['URL']
                                ]) ?>
                                    <span>
                                        <?php if (!empty($arResult['FOOTER_BLOCK']['TEXT'])) { ?>
                                            <?= $arResult['FOOTER_BLOCK']['TEXT'] ?>
                                        <?php } else { ?>
                                            <?= Loc::getMessage('C_SHARES_TEMP3_TEMPLATE_FOOTER_BLOCK_DEFAULT') ?>
                                        <?php } ?>
                                    </span>
                                    <i class="fal fa-angle-right"></i>
                                <?= Html::endTag('a')?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-start',
                            'a-h-start',
                            'i-7'
                        ]
                    ],
                    'data-role' => 'items'
                ]) ?>
                    <!--items-->
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = null;

                        if (!empty($arItem['PREVIEW_PICTURE']))
                            $arPicture = $arItem['PREVIEW_PICTURE'];
                        else if (!empty($arItem['DETAIL_PICTURE']))
                            $arPicture = $arItem['DETAIL_PICTURE'];
                        else
                            $arPicture = null;

                        if (!empty($arPicture)) {
                            $arPicture = CFile::ResizeImageGet($arPicture, [
                                'width' => 900,
                                'height' => 900
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($arPicture))
                                $sPicture = $arPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'],
                                    '1000-2',
                                    '600-1'
                                ]
                            ]
                        ]) ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'class' => 'widget-item',
                                'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ])?>
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                <div class="widget-item-fade"></div>
                                <div class="widget-item-content">
                                    <div class="widget-item-name intec-cl-background">
                                        <?= $arItem['NAME'] ?>
                                    </div>
                                    <?php if ($arItem['DATA']['PREVIEW']['SHOW']) { ?>
                                        <div class="widget-item-description">
                                            <span>
                                                <?= Html::stripTags($arItem['DATA']['PREVIEW']['VALUE']) ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                             <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <!--items-->
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arNavigation['USE']) { ?>
                <div class="widget-pagination" data-role="navigation">
                    <!--navigation-->
                    <?= $arNavigation['PRINT'] ?>
                    <!--navigation-->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($arNavigation['USE'] && $arNavigation['MODE'] === 'ajax' && !defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
</div>