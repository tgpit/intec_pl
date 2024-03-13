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
<div class="widget c-news c-news-template-10" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
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
                    <div class="intec-grid-item-3 intec-grid-item-768-1">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-10">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="intec-grid-item-1">
                                    <div class="widget-title">
                                        <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $iCounter++;

                                if ($iCounter > 4)
                                    break;

                                $arData = $arItem['DATA'];
                            ?>
                                <div class="intec-grid-item-1">
                                    <div class="widget-item widget-item-left" id="<?= $sAreaId ?>">
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
                                        <div class="intec-grid intec-grid-wrap intec-grid-i-8">
                                            <div class="intec-grid-item-auto">
                                                <?= Html::tag('div', $arData['DATE'], [
                                                    'class' => [
                                                        'widget-item-date'
                                                    ]
                                                ])?>
                                            </div>
                                            <div class="intec-grid-item">
                                                <?php if ($arResult['TAGS']['SHOW'] && !empty($arItem['DATA']['TAGS'])) {
                                                    $tagsRender($arItem['DATA']['TAGS'], false);
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php foreach ($arResult['BIG_ITEMS'] as $arItem) {

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

                        $arData = $arItem['DATA'];
                    ?>
                        <div class="intec-grid-item-3 intec-grid-item-768-1">
                            <div class="widget-item" id="<?= $sAreaId ?>">
                                <?= Html::beginTag('div', [
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
                                <?= Html::endTag('div') ?>

                                <div class="widget-item-information">
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
                                    <div class="intec-grid intec-grid-wrap intec-grid-i-8">
                                        <div class="intec-grid-item-auto">
                                            <?= Html::tag('div', $arData['DATE'], [
                                                'class' => [
                                                    'widget-item-date'
                                                ]
                                            ])?>
                                        </div>
                                        <div class="intec-grid-item">
                                            <?php if ($arResult['TAGS']['SHOW'] && !empty($arItem['DATA']['TAGS'])) {
                                                $tagsRender($arItem['DATA']['TAGS'], false);
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <?php $iCounter = 0 ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $iCounter++;

                        if ($iCounter <= 4)
                            continue;

                        $arData = $arItem['DATA'];

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    '3' => true,
                                    '768-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
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
                                <div class="intec-grid intec-grid-wrap intec-grid-i-8">
                                    <div class="intec-grid-item-auto">
                                        <?= Html::tag('div', $arData['DATE'], [
                                            'class' => [
                                                'widget-item-date'
                                            ]
                                        ])?>
                                    </div>
                                    <div class="intec-grid-item">
                                        <?php if ($arResult['TAGS']['SHOW'] && !empty($arItem['DATA']['TAGS'])) {
                                            $tagsRender($arItem['DATA']['TAGS'], false);
                                        } ?>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php if ($arResult['TAGS']['SHOW'] && $arResult['TAGS']['MODE'] === 'active')
        include(__DIR__.'/parts/form.php');
    ?>
</div>
