<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

?>
<div class="widget c-news c-news-template-5" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
        <div class="widget-header">
            <?php if (!$arVisual['HEADER']['WIDE']) { ?>
                <div class="intec-content">
                    <div class="intec-content-wrapper">
            <?php } ?>
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title-container intec-grid-item">
                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                'class' => [
                                    'widget-title',
                                    'align-'.$arBlocks['HEADER']['POSITION'],
                                    $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                ]
                            ]) ?>
                        </div>
                        <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
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
                                        'intec-cl-text-light-hover',
                                    ],
                                    'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
                                ])?>
                                    <i class="fal fa-angle-right"></i>
                                <?= Html::endTag('a')?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="intec-grid-item-1">
                            <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php if (!$arVisual['HEADER']['WIDE']) { ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="widget-content">
        <?= Html::beginTag('div', [
            'class' => [
                'widget-items',
                'owl-carousel',
                'intec-grid' => [
                    '',
                    'wrap',
                    'a-v-stretch'
                ]
            ],
            'data' => [
                'role' => 'slider',
                'columns' => $arVisual['COLUMNS']
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
                    $sPicture = CFile::ResizeImageGet(
                        $sPicture, [
                        'width' => 700,
                        'height' => 700
                    ],
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                    );

                    if (!empty($sPicture))
                        $sPicture = $sPicture['src'];
                }

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                $sPreviewText = strip_tags($arItem['PREVIEW_TEXT']);
                if (!empty($arVisual['PREVIEW']['LENGTH']))
                    $sPreviewText = StringHelper::truncate($sPreviewText, $arVisual['PREVIEW']['LENGTH']);

            ?>
                <div class="widget-item" id="<?= $sAreaId ?>">
                    <?= Html::tag($sTag, '', [
                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
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
                    <div class="widget-item-text">
                        <?php if ($arVisual['DATE']['SHOW']) { ?>
                            <div class="widget-item-date">
                                <?php if (!empty($arItem['DATE_ACTIVE_FROM_FORMATTED'])) { ?>
                                    <?= $arItem['DATE_ACTIVE_FROM_FORMATTED'] ?>
                                <?php } else { ?>
                                    <?= $arItem['DATE_CREATE_FORMATTED'] ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?= Html::tag($sTag, $arItem['NAME'], [
                            'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                            'class' => [
                                'widget-item-name',
                                'intec-cl-text-hover'
                            ]
                        ]) ?>
                        <?php if ($arVisual['PREVIEW']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                            <div class="widget-item-description">
                                <?= $sPreviewText ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    </div>
    <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'widget-footer' => true,
                'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
            ], true)
        ]) ?>
            <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                    'href' => $arBlocks['FOOTER']['BUTTON']['LINK'],
                    'class' => [
                        'widget-footer-button',
                        'intec-ui' => [
                            '',
                            'size-5',
                            'scheme-current',
                            'control-button',
                            'mod' => [
                                'transparent',
                                'round-half'
                            ]
                        ]
                    ]
                ]) ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
<?php include(__DIR__.'/parts/script.php') ?>