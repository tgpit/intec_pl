<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'OVERLAY' => [
        'ICON' => FileHelper::getFileData(__DIR__.'/svg/overlay.icon.svg')
    ],
    'BACK' => [
        'ICON' => FileHelper::getFileData(__DIR__.'/svg/back.icon.svg')
    ]
];

?>
<div class="ns-bitrix c-photo-section c-photo-section-default-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="photo-section-items">
                <div class="intec-grid intec-grid-wrap intec-grid-i-16" data-role="items">
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sPicture = null;

                        if (!empty($arItem['PREVIEW_PICTURE']))
                            $arPicture = $arItem['PREVIEW_PICTURE'];
                        else if (!empty($arItem['DETAIL_PICTURE']))
                            $arPicture = $arItem['DETAIL_PICTURE'];
                        else
                            $arPicture = null;

                        if (!empty($arPicture)) {
                            $sPicture = CFile::ResizeImageGet($arPicture, [
                                'width' => 600,
                                'height' => 600
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            continue;

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-3' => $arVisual['COLUMNS'] >= 4,
                                    '768-2' => true,
                                    '500-1' => true
                                ],
                            ], true)
                        ]) ?>
                            <div class="photo-section-item" id="<?= $sAreaId ?>">
                                <?= Html::beginTag('div', [
                                    'class' => 'photo-section-item-content',
                                    'title' => $arItem['NAME'],
                                    'data' => [
                                        'role' => 'item.content',
                                        'src' => $arPicture['SRC'],
                                        'thumb' => $sPicture
                                    ]
                                ]) ?>
                                    <?= Html::tag('div', null, [
                                        'class' => 'photo-section-item-picture',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $sPicture
                                        ],
                                        'style' => [
                                            'background-image' => 'url(\''.(
                                                $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                                            ).'\')'
                                        ]
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                                <div class="photo-section-item-overlay intec-ui-align">
                                    <?= Html::tag('div', $arSvg['OVERLAY']['ICON'], [
                                        'class' => [
                                            'photo-section-item-overlay-icon',
                                            'intec-ui-picture'
                                        ]
                                    ]) ?>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
            <?php if (!empty($arResult['LIST_PAGE_URL'])) { ?>
                <div class="photo-section-footer">
                    <div class="photo-section-back">
                        <div class="intec-grid intec-grid-i-h-4 intec-grid-a-v-center">
                            <div class="intec-grid-item-auto">
                                <?= Html::tag('div', $arSvg['BACK']['ICON'], [
                                    'class' => [
                                        'photo-section-back-icon',
                                        'intec-ui-picture'
                                    ]
                                ]) ?>
                            </div>
                            <div class="intec-grid-item">
                                <?= Html::tag('a', Loc::getMessage('C_PHOTO_SECTION_GALLERY_DEFAULT_1_TEMPLATE_BACK_DEFAULT'), [
                                    'class' => [
                                        'photo-section-back-content',
                                        'intec-cl-text-hover'
                                    ],
                                    'href' => $arResult['LIST_PAGE_URL']
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
