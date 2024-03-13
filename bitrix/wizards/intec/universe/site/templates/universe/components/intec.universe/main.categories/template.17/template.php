<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !==true) die();

use intec\core\bitrix\Component;
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
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-categories',
        'c-categories-template-17',
        'intec-content-wrap'
    ],
    'data' => [
        'columns' => $arVisual['COLUMNS']
    ]
]) ?>
    <div class="widget-wrapper">
        <div class="widget-wrapper-2">
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
                <div class="widget-items intec-grid intec-grid-wrap">
                    <?php foreach ($arResult['ITEMS'] as $arItem) {
                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $iCounter++;

                        $arData = $arItem['DATA'];
                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture))
                            $sPicture = $sPicture['SRC'];

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';

                        if ($arVisual['LINK']['USE'] && !empty($arItem['DETAIL_PAGE_URL'])) {
                            $sTag = 'a';
                        } else {
                            $sTag = 'div';
                        }
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '768-1' => $arVisual['COLUMNS'] == 2
                                ]
                            ], true),
                            'data' => [
                                'theme' => $arItem['DATA']['THEME']
                            ]
                        ]) ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'class' => [
                                    'widget-item-block'
                                ],
                                'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <?= Html::tag('div', '', [
                                    'class' => [
                                        'widget-item-picture',
                                        'intec-image-effect'
                                    ],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-item-content-wrap' => true,
                                        'intec-content' => true,
                                        'intec-grid' => true,
                                        'intec-grid-a-v-center' => true,
                                        'intec-grid-a-h-between' =>  $arVisual['COLUMNS'] == 1
                                    ], true)
                                ]) ?>
                                    <?php if ($arVisual['COLUMNS'] == 2) { ?>
                                        <div class="widget-item-content">
                                    <?php } ?>
                                        <?php if($arVisual['COLUMNS'] == 1) { ?>
                                            <div class="widget-item-left-text">
                                        <?php } ?>
                                            <?php if(!empty($arItem['DATA']['STICKER'])) { ?>
                                                <div class="widget-item-sticker">
                                                    <?= $arItem['DATA']['STICKER'] ?>
                                                </div>
                                            <?php } ?>
                                            <div class="widget-item-name">
                                                <?= $arItem['NAME'] ?>
                                            </div>
                                        <?php if($arVisual['COLUMNS'] == 1) { ?>
                                            </div>
                                            <div class="widget-item-right-text">
                                        <?php } ?>
                                            <? if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['COLUMNS'] == 1) { ?>
                                                <div class="widget-item-preview-text">
                                                    <?= $arItem['PREVIEW_TEXT'] ?>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arVisual['PRICE']['SHOW'] && !empty($arItem['DATA']['PRICE']['VALUE'])) { ?>
                                                <?= Html::beginTag('div', [
                                                    'class' => Html::cssClassFromArray([
                                                        'widget-item-price' => true,
                                                        'intec-cl-background intec-cl-background-light-hover' => empty($arItem['DATA']['PRICE']['BACKGROUND']['COLOR']) || substr($arItem['DATA']['PRICE']['BACKGROUND']['COLOR'], 0,1) !== '#' || strlen($arItem['DATA']['PRICE']['BACKGROUND']['COLOR']) < 4 || strlen($arItem['DATA']['PRICE']['BACKGROUND']['COLOR']) > 7
                                                    ], true),
                                                    'style' => [
                                                        'background' => $arItem['DATA']['PRICE']['BACKGROUND']['COLOR']
                                                    ]
                                                ]) ?>
                                                    <?= $arItem['DATA']['PRICE']['VALUE'] ?>
                                                <?= Html::endTag('div') ?>
                                            <?php } ?>
                                        <?php if ($arVisual['COLUMNS'] == 1) { ?>
                                            </div>
                                        <?php } ?>
                                    <?php if ($arVisual['COLUMNS'] == 2) { ?>
                                        </div>
                                    <?php } ?>
                                <?= Html::endtag('div') ?>
                            <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
