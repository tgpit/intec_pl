<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];
$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';
$arSvg = [
    'SEE_ALL_MOBILE' => FileHelper::getFileData(__DIR__.'/svg/header.list.mobile.svg')
];

?>
<div class="widget c-sections c-sections-template-3" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['BUTTON_ALL']['SHOW']) { ?>
        <div class="widget-header" data-link-all="<?= $arVisual['BUTTON_ALL']['SHOW'] ? 'true' : 'false' ?>">
            <div class="intec-content intec-content-visual">
                <div class="intec-content-wrapper">
                    <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?> intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                'class' => 'intec-grid-item'
                            ]) ?>
                        <?php } ?>
                        <?php if ($arVisual['BUTTON_ALL']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('a', [
                                    'class' => 'widget-list',
                                    'href' => $arVisual['BUTTON_ALL']['LINK']
                                ])?>
                                    <span class="widget-list-desktop intec-cl-text-light-hover"><?= $arVisual['BUTTON_ALL']['TEXT'] ?></span>
                                    <span class="widget-list-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                        <?= $arSvg['SEE_ALL_MOBILE'] ?>
                                    </span>
                                <?= Html::endTag('a')?>
                            </div>
                        <?php } ?>
                    </div>
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
        <?php if (!$arVisual['WIDE']) { ?>
            <div class="intec-content intec-content-visual">
                <div class="intec-content-wrapper">
        <?php } ?>
        <?= Html::beginTag('div', [
            'class' => 'widget-items',
            'data' => [
                'role' => 'items',
                'status' => 'loading'
            ]
        ]) ?>
            <div class="intec-grid intec-grid-wrap intec-grid-i-16">
                <?php foreach ($arResult['SECTIONS'] as $arItem) {
                    $sId = $sTemplateId . '_' . $arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $bDescription = $arVisual['TEXT']['SHOW'] && !empty($arItem['DESCRIPTION']);
                    $arPicture = [
                        'TYPE' => 'picture',
                        'SOURCE' => null,
                        'ALT' => null,
                        'TITLE' => null
                    ];

                    if (!empty($arItem['PICTURE'])) {
                        if ($arItem['PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                            $arPicture['TYPE'] = 'svg';
                            $arPicture['SOURCE'] = $arItem['PICTURE']['SRC'];
                        } else {
                            $arPicture['SOURCE'] = CFile::ResizeImageGet($arItem['PICTURE'], [
                                'width' => 950,
                                'height' => 950
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

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

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'] => true,
                                '1200-3' => $arVisual['COLUMNS'] >= 4,
                                '1000-2' => $arVisual['COLUMNS'] >= 3,
                                '750-1' => true
                            ]
                        ], true)
                    ]) ?>
                        <?= Html::beginTag('div', [
                            'class' => 'widget-item',
                            'id' => $sAreaId,
                            'data' => [
                                'role' => 'item',
                                'description' => $bDescription ? 'true' : 'false',
                                'svg' => $arPicture['TYPE'] === 'svg' ? 'true' : 'false'
                            ]
                        ]) ?>
                            <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-item-picture' => true,
                                        'svg' => true,
                                        'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                    ], true),
                                    'href' => $arItem['SECTION_PAGE_URL']
                                ]) ?>
                                    <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                                <?= Html::endTag('div')?>
                            <?php } else { ?>
                                <?= Html::tag('div', '', [
                                    'class' => 'widget-item-picture',
                                    'title' => !empty($arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_TITLE']) ? $arItem['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_TITLE'] : $arItem['NAME'],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                    ],
                                    'style' => [
                                        'background-image' => 'url(\'' . ($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPicture['SOURCE']) . '\')'
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?= Html::beginTag($sTag, [
                                'class' => [
                                    'widget-item-content',
                                ],
                                'href' => $arVisual['LINK']['USE'] ? $arItem['SECTION_PAGE_URL'] : null
                            ])?>
                                <div class="widget-item-content-container" data-role="content">
                                    <div class="widget-item-text scrollbar-inner" data-role="container">
                                        <div class="widget-item-header" data-role="item.header">
                                            <div class="widget-item-name">
                                                <?= $arItem['NAME'] ?>
                                            </div>
                                            <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                                <div class="widget-item-count-elements">
                                                    <?= $arItem['ELEMENT_CNT_DISPLAY'] ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php if ($bDescription) { ?>
                                            <div class="widget-item-description" data-role="description">
                                                <?= $arItem['DESCRIPTION'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
        <?php if (!$arVisual['WIDE']) { ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
