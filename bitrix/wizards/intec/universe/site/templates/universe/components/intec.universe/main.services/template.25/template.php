<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
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
$vFrontPicture = include(__DIR__ . '/parts/picture.front.php');
$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

?>
<div class="widget c-services c-services-template-25" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
        <div class="widget-header">
            <div class="intec-content">
                <div class="intec-content-wrapper">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['HEADER']['TEXT'], [
                            'class' => [
                                'widget-title',
                                'align-' . $arBlocks['HEADER']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
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
        <div class="widget-content-wrapper">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <?= Html::beginTag('div', [
                        'class' => 'widget-content-items',
                        'data' => [
                            'role' => 'content',
                            'jointly' => $arVisual['JOINTLY'] ? 'true' : 'false',
                            'picture' => $arVisual['PICTURE'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-items' => true,
                                'owl-carousel' => count($arResult['ITEMS']) > 1
                            ], true),
                            'data' => [
                                'role' => 'container'
                            ]
                        ]) ?>
                            <?php $iIndexItem = 1 ?>
                            <?php $sTotalAmount = count($arResult['ITEMS']) < 10 ? '0'.count($arResult['ITEMS']) : count($arResult['ITEMS']) ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) {
                                $sId = $sTemplateId . '_' . $arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);
                                $arData = $arItem['DATA'];

                                if ($arData['PICTURE']['FRONT']['SHOW']) {
                                    $arData['PICTURE']['FRONT']['VALUE'] = CFile::ResizeImageGet($arData['PICTURE']['FRONT']['VALUE'], [
                                        'width' => 700,
                                        'height' => 500
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                    if (!empty($arData['PICTURE']['FRONT']['VALUE']))
                                        $arData['PICTURE']['FRONT']['VALUE'] = $arData['PICTURE']['FRONT']['VALUE']['src'];
                                }

                                if ($arData['PICTURE']['BACK']['SHOW']) {
                                    $arData['PICTURE']['BACK']['VALUE'] = CFile::ResizeImageGet($arData['PICTURE']['BACK']['VALUE'], [
                                        'width' => 1650,
                                        'height' => 500
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                    if (!empty($arData['PICTURE']['BACK']['VALUE']))
                                        $arData['PICTURE']['BACK']['VALUE'] = $arData['PICTURE']['BACK']['VALUE']['src'];
                                }
                                ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item',
                                    'id' => $sAreaId,
                                    'data' => [
                                        'scheme' => $arData['SCHEME'],
                                        'picture' => $arData['PICTURE']['BACK']['SHOW'] ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <?php if ($arData['PICTURE']['BACK']['SHOW']) { ?>
                                        <?= Html::beginTag($arVisual['JOINTLY'] ? 'div' : $sTag, [
                                            'class' => Html::cssClassFromArray([
                                                'widget-item-picture' => true,
                                                'intec-image-effect' => !$arVisual['JOINTLY']
                                            ], true),
                                            'style' => 'background-image: url("' . ($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arData['PICTURE']['BACK']['VALUE']) . '")',
                                            'href' => $sTag === 'a' && !$arVisual['JOINTLY'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'target' => $sTag === 'a' && !$arVisual['JOINTLY'] && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                            'data' => [
                                                'role' => 'item.picture',
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arData['PICTURE']['BACK']['VALUE'] : null,
                                            ]
                                        ]) ?>
                                            <?= $arData['PICTURE']['FRONT']['SHOW'] ? $vFrontPicture($arData['PICTURE']['FRONT']['VALUE']) : '' ?>
                                        <?= Html::endTag($arVisual['JOINTLY'] ? 'div' : $sTag) ?>
                                    <?php } ?>
                                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch">
                                        <div class="intec-grid-item intec-grid-item-768-1">
                                            <div class="widget-item-content">
                                                <div class="widget-item-indexes">
                                                    <span class="widget-item-index-current">
                                                        <?= $iIndexItem < 10 ? '0' . $iIndexItem : $iIndexItem ?>
                                                    </span>
                                                    <span class="widget-item-index-total">
                                                        <?= Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_25_TEMPLATE_TOTAL_AMOUNT', [
                                                            '#TOTAL#' => $sTotalAmount
                                                        ]) ?>
                                                    </span>
                                                </div>
                                                <?= Html::tag($sTag, $arItem['NAME'], [
                                                    'class' => Html::cssClassFromArray([
                                                        'widget-item-name' => true,
                                                        'intec-cl-text-hover' => $sTag === 'a'
                                                    ], true),
                                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                                    'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                                <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                                    <div class="widget-item-description">
                                                        <?= strip_tags($arItem['PREVIEW_TEXT']) ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($arVisual['LINK']['USE'] && !empty($arItem['DETAIL_PAGE_URL'])) { ?>
                                                    <?= Html::tag('a', Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_25_TEMPLATE_BOTTOM_DETAIL'), [
                                                        'class' => [
                                                            'widget-item-button',
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'mod-round-2'
                                                            ]
                                                        ],
                                                        'href' => $arItem['DETAIL_PAGE_URL']
                                                    ]) ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if ($arVisual['JOINTLY'] && $arData['PICTURE']['FRONT']['SHOW']) { ?>
                                            <div class="intec-grid-item-2 intec-grid-item-768-1">
                                                <?php $vFrontPicture($arData['PICTURE']['FRONT']['VALUE']) ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                                <? $iIndexItem++ ?>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                        <?php if ($arVisual['SLIDER']['NAV']['SHOW']) { ?>
                            <div class="widget-nav" data-role="container.nav"></div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__ . '/parts/script.php') ?>
</div>