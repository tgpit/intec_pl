<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$bHeaderColumn = $arVisual['HEADER']['POSITION'] === 'left';

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-services',
        'c-services-template-20'
    ],
    'data-borders' => $arVisual['BORDERS']['USE'] ? 'true' : 'false',
    'data-svg-file-use' => $arVisual['SVG']['USE'] ? 'true' : 'false'
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="intec-grid intec-grid-wrap intec-grid-i-25">
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arBlocks['BUTTON']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid-item-3' => $bHeaderColumn,
                        'intec-grid-item-1' => !$bHeaderColumn,
                        'intec-grid-item-1000-1' => true
                    ], true)
                ]) ?>
                    <?php if ($bHeaderColumn) { ?>
                        <div class="widget-header">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                                <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                    <div class="widget-title-container intec-grid-item">
                                        <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                            'class' => [
                                                'widget-title',
                                                'align-'.$arBlocks['HEADER']['POSITION'],
                                                $arBlocks['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                            ]
                                        ]) ?>
                                    </div>
                                    <?php if ($arBlocks['BUTTON']['SHOW']) { ?>
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
                                                'href' => $arBlocks['BUTTON']['LINK']
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
                            <?php if ($arBlocks['BUTTON']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                        'class' => [
                                            'widget-header-button-wrap',
                                            $arBlocks['HEADER']['SHOW'] ? 'mobile' : null,
                                            'intec-grid-item-auto',
                                            'align-' . $arBlocks['BUTTON']['POSITION']
                                        ]
                                ]) ?>
                                    <?= Html::tag('a', $arBlocks['BUTTON']['TEXT'], [
                                        'href' => $arBlocks['BUTTON']['LINK'],
                                        'class' => [
                                            'widget-header-button',
                                            'intec-ui' => [
                                                '',
                                                'size-3',
                                                'scheme-current',
                                                'control-button',
                                                'mod' => [
                                                    'transparent',
                                                    'round-2'
                                                ]
                                            ]
                                        ]
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="widget-header">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-10">
                                <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                    <div class="widget-title-container position-top intec-grid-item">
                                        <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                            'class' => [
                                                'widget-title',
                                                'align-'.$arBlocks['HEADER']['POSITION'],
                                                $arBlocks['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                            ]
                                        ]) ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arBlocks['BUTTON']['SHOW']) { ?>
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
                                            'href' => $arBlocks['BUTTON']['LINK']
                                        ])?>
                                            <span><?= $arBlocks['BUTTON']['TEXT'] ?></span>
                                            <i class="fal fa-angle-right"></i>
                                        <?= Html::endTag('a')?>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                                <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                    <div class="intec-grid-item-1">
                                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <?php } ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid-item' => $bHeaderColumn,
                        'intec-grid-item-1' => !$bHeaderColumn
                    ], true)
                ]) ?>
                    <div class="widget-content">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-items',
                                'intec-grid' => [
                                    '',
                                    'wrap',
                                    'a-v-stretch',
                                    'a-h-start'
                                ]
                            ],
                            'data' => [
                                'grid' => $arVisual['COLUMNS']
                            ]
                        ]) ?>
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $arPicture = [
                                'TYPE' => 'picture',
                                'SOURCE' => null,
                                'ALT' => null,
                                'TITLE' => null
                            ];

                            if (!empty($arItem['PREVIEW_PICTURE'])) {
                                if ($arItem['PREVIEW_PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                                    $arPicture['TYPE'] = 'svg';
                                    $arPicture['SOURCE'] = $arItem['PREVIEW_PICTURE']['SRC'];
                                } else {
                                    $arPicture['SOURCE'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], $arPictureSize, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                    if (!empty($arPicture['SOURCE'])) {
                                        $arPicture['SOURCE'] = $arPicture['SOURCE']['src'];
                                    } else {
                                        $arPicture['SOURCE'] = null;
                                    }
                                }
                            }

                            if (empty($arPicture['SOURCE'])) {
                                $arPicture['TYPE'] = 'picture';
                                $arPicture['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                            } else {
                                $arPicture['ALT'] = $arItem['PICTURE']['ALT'];
                                $arPicture['TITLE'] = $arItem['PICTURE']['TITLE'];
                            }

                            $sTag = $arVisual['LINK']['USE'] && !empty($arItem['DETAIL_PAGE_URL']) ? 'a' : 'div';

                            ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item' => true,
                                    'intec-grid-item' => [
                                        $arVisual['COLUMNS'] => true,
                                        '1000-3' => $arVisual['COLUMNS'] >= 4,
                                        '768-2' => $arVisual['COLUMNS'] >= 3,
                                        '600-1' => true
                                    ]
                                ], true)
                            ]) ?>
                            <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                                <div class="widget-item-picture-wrap">
                                    <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                        <?= Html::tag($sTag, FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                            'class' => [
                                                Html::cssClassFromArray([
                                                    'widget-item-picture' => true,
                                                    'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                                    'intec-image-effect' => true,
                                                ], true)
                                            ],
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null
                                        ]) ?>
                                    <?php } else { ?>
                                        <?= Html::tag($sTag, null, [
                                            'class' => [
                                                'widget-item-picture',
                                                'intec-image-effect'
                                            ],
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arPicture['SOURCE'].'\')' : null
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                </div>
                                <?= Html::tag($sTag, $arItem['NAME'], [
                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                    'class' => [
                                        'widget-item-name',
                                        'intec-cl-text-hover',
                                        'align-'.$arVisual['NAME']['POSITION']
                                    ]
                                ]) ?>
                            </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <?= Html::endTag('div') ?>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>