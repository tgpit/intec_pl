<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

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
<div class="widget c-staff c-staff-template-3" id="<?= $sTemplateId ?>" data-collapsed="true" data-hiding="false">
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
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
                    'class' => [
                        'widget-items',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-stretch',
                            'a-h-start',
                            'i-12'
                        ]
                    ],
                    'data' => [
                        'collapsed' => 'true',
                        'role' => 'items'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 350,
                                'height' => 350
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1000-1' => true
                                ]
                            ], true),
                            'data' => [
                                'role' => 'item',
                                'hidden' => 'false'
                            ]
                        ]) ?>
                            <div class="widget-item-wrapper intec-cl-border-hover" id="<?= $sAreaId ?>">
                                <div class="intec-grid intec-grid-nowrap intec-grid-a-h-center intec-grid-a-v-start intec-grid-500-wrap">
                                    <div class="intec-grid-item-auto">
                                        <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', '', [
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
                                            ],
                                            'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                        ]) ?>
                                    </div>
                                    <div class="intec-grid-item intec-grid-item-500-1" style="overflow: hidden">
                                        <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', $arItem['NAME'], [
                                            'class' => [
                                                'widget-item-name',
                                                $arVisual['LINK']['USE'] ? 'intec-cl-text-hover' : null
                                            ],
                                            'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                        ]) ?>
                                        <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                            <div class="widget-item-description">
                                                <?= $arItem['PREVIEW_TEXT'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arVisual['HIDING']['USE']) { ?>
                <div class="widget-buttons">
                    <div class="<?= Html::cssClassFromArray([
                        'widget-button',
                        'widget-button-show',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'mod-transparent',
                            'mod-round-half',
                            'scheme-current',
                            'size-5'
                        ]
                    ]) ?>" data-role="button">
                        <?= Loc::getMessage('C_MAIN_STAFF_TEMPLATE_3_BUTTONS_SHOW') ?>
                    </div>
                    <div class="<?= Html::cssClassFromArray([
                        'widget-button',
                        'widget-button-hide',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'mod-transparent',
                            'mod-round-half',
                            'scheme-current',
                            'size-5'
                        ]
                    ]) ?>" data-role="button">
                        <?= Loc::getMessage('C_MAIN_STAFF_TEMPLATE_3_BUTTONS_HIDE') ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($arVisual['HIDING']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>