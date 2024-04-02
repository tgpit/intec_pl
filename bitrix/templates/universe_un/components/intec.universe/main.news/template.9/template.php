<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
$arBlocks = $arResult['BLOCKS'];

/**
 * @var Closure $tagsRender($arItem)
 */
$tagsRender = include(__DIR__.'/parts/tags.php');

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

$bFirstItemBig = $arVisual['LIST_VIEW'] === 'big' && $arVisual['COLUMNS'] !== '2';
$iCounter = 0;
?>
<div class="widget c-news c-news-template-9" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => 'true',
                        'intec-grid' => [
                            '' => true,
                            'wrap' => true,
                            'i-8' => true,
                        ]
                    ], true),
                    'data-role' => 'items'
                ])?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        $sPictureResized = CFile::ResizeImageGet($sPicture, [
                            'width' => 1400,
                            'height' => 700
                        ], BX_RESIZE_IMAGE_EXACT);

                        if (!empty($sPictureResized)) {
                            $sPictureResized = $sPictureResized['src'];
                        } else {
                            $sPictureResized = $sPicture['SRC'];
                        }

                        $sPicture = $sPicture['SRC'];

                        $iCounter++;

                        $iColumns = $arVisual['COLUMNS'];

                        if ($bFirstItemBig && $iCounter < 4) {
                            $iColumns = '4';
                        }
                    ?>
                        <?php if ($bFirstItemBig && $iCounter === 1) { ?>
                            <div class="widget-item intec-grid-item-2 intec-grid-item-1024-3 intec-grid-item-900-2 intec-grid-item-600-1">
                                <?= Html::beginTag($sTag, [
                                    'class' => 'widget-item-wrapper',
                                    'id' => $sAreaId,
                                    'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                    'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'widget-item-picture-big',
                                            'intec-image-effect'
                                        ],
                                        'title' => Html::decode(Html::stripTags($arItem['NAME'])),
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPictureResized.'\')' : null
                                        ]
                                    ]) ?>
                                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPictureResized, [
                                            'alt' => $arItem['NAME'],
                                            'title' => $arItem['NAME'],
                                            'loading' => 'lazy',
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                            ]
                                        ]) ?>
                                        <div class="widget-item-picture-big-wrapper">
                                            <div class="widget-item-information">
                                                <?php if ($arResult['TAGS']['SHOW'] && !empty($arItem['DATA']['TAGS'])) {
                                                    $tagsRender($arItem['DATA']['TAGS'], true);
                                                } ?>
                                                <?= Html::tag('div', $arItem['NAME'], [
                                                    'class' => 'widget-item-name'
                                                ]) ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?=Html::endTag($sTag);?>
                            </div>
                        <?php } else { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item' => true,
                                    'intec-grid-item' => [
                                        $iColumns => true,
                                        '1024-3' => $iColumns <= 4,
                                        '900-2' => true,
                                        '600-1' => true
                                    ]
                                ], true)
                            ]) ?>
                                <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                                    <?= Html::beginTag($sTag, [
                                        'class' => [
                                            'widget-item-picture',
                                            'intec-image-effect'
                                        ],
                                        'title' => Html::decode(Html::stripTags($arItem['NAME'])),
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPictureResized.'\')' : null
                                        ],
                                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                    ]) ?>
                                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPictureResized, [
                                            'alt' => $arItem['NAME'],
                                            'title' => $arItem['NAME'],
                                            'loading' => 'lazy',
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureResized : null
                                            ]
                                        ]) ?>
                                    <?= Html::endTag('a') ?>
                                    <div class="widget-item-information">
                                        <?php if ($arResult['TAGS']['SHOW'] && !empty($arItem['DATA']['TAGS'])) {
                                            $tagsRender($arItem['DATA']['TAGS'], false);
                                        } ?>
                                        <div class="widget-item-name-wrap">
                                            <?= Html::tag($sTag, $arItem['NAME'], [
                                                'class' => Html::cssClassFromArray([
                                                    'widget-item-name' => true,
                                                    'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                                ], true),
                                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                            ]) ?>
                                        </div>
                                        <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                            <div class="widget-item-description">
                                                <?= $arItem['PREVIEW_TEXT'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php if ($arResult['TAGS']['SHOW'] && $arResult['TAGS']['MODE'] === 'active')
        include(__DIR__.'/parts/form.php');
    ?>
</div>
