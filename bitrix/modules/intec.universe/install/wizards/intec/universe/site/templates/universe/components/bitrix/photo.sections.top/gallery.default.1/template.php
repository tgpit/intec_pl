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

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'SECTION' => [
        'DECORATION' => FileHelper::getFileData(__DIR__.'/svg/section.decoration.svg')
    ]
];

?>
<div class="ns-bitrix c-photo-sections-top c-photo-sections-top-default-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="photo-sections-top-sections">
                <div class="intec-grid intec-grid-wrap intec-grid-i-16">
                    <?php foreach ($arResult['SECTIONS'] as $arSection) {

                        $sId = $sTemplateId.'_'.$arSection['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arSection['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

                        $isSlider = count($arSection['GALLERY']) > 1;

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-3' => $arVisual['COLUMNS'] > 3,
                                    '768-2' => true,
                                    '500-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <div class="photo-sections-top-section-container" id="<?= $sAreaId ?>">
                                <?= Html::beginTag('a', [
                                    'class' => 'photo-sections-top-section',
                                    'href' => $arSection['SECTION_PAGE_URL'],
                                    'data-role' => 'photo.item'
                                ]) ?>
                                    <?php if ($isSlider) { ?>
                                        <div class="photo-sections-top-loader photo-sections-top-section-picture" data-role="photo.item.loader">
                                            <div class="photo-sections-top-loader-name photo-sections-top-loader-part"></div>
                                            <div class="photo-sections-top-loader-navigation photo-sections-top-loader-part"></div>
                                        </div>
                                    <?php } ?>
                                    <?= Html::beginTag('div', [
                                        'class' => 'photo-sections-top-section-content',
                                        'data' => [
                                            'role' => 'photo.item.content',
                                            'loaded' => $isSlider ? 'false' : 'true'
                                        ]
                                    ]) ?>
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'photo-sections-top-section-pictures' => true,
                                                'owl-carousel' => $isSlider
                                            ], true),
                                            'data-role' => $isSlider ? 'photo.item.slider' : null
                                        ]) ?>
                                            <?php if (!empty($arSection['GALLERY'])) { ?>
                                                <?php foreach ($arSection['GALLERY'] as $arPicture) {

                                                    $sPicture = CFile::ResizeImageGet($arPicture, [
                                                        'width' => 600,
                                                        'height' => 600
                                                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                                    if (!empty($sPicture))
                                                        $sPicture = $sPicture['src'];

                                                    if (empty($sPicture))
                                                        continue;

                                                ?>
                                                    <?= Html::tag('div', null, [
                                                        'class' => [
                                                            'photo-sections-top-section-picture',
                                                            'owl-lazy'
                                                        ],
                                                        'data-original' => !$isSlider && $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                                        'data-lazyload-use' => !$isSlider && $arVisual['LAZYLOAD']['USE'] ? 'true' : null,
                                                        'data-src' => $isSlider ? $sPicture : null
                                                    ]) ?>
                                                <?php } ?>
                                                <?php unset($arPicture) ?>
                                            <?php } else {

                                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                            ?>
                                                <?= Html::tag('div', null, [
                                                    'class' => 'photo-sections-top-section-picture',
                                                    'data' => [
                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    ],
                                                    'style' => [
                                                        'background-image' => 'url(\''.(
                                                            $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                                                        ).'\')'
                                                    ]
                                                ]) ?>
                                            <?php } ?>
                                        <?= Html::endTag('div') ?>
                                        <div class="photo-sections-top-section-information">
                                            <div class="photo-sections-top-section-information-content">
                                                <?php if (empty($arSection['ITEMS'])) { ?>
                                                    <div class="photo-sections-top-section-count">
                                                        <?= Loc::getMessage('C_PHOTO_SECTIONS_TOP_GALLERY_DEFAULT_1_TEMPLATE_SECTION_EMPTY') ?>
                                                    </div>
                                                <?php } ?>
                                                <div class="photo-sections-top-section-name">
                                                    <?= $arSection['NAME'] ?>
                                                </div>
                                                <?= Html::tag('div', $arSvg['SECTION']['DECORATION'], [
                                                    'class' => [
                                                        'photo-sections-top-section-decoration',
                                                        'intec-ui-picture'
                                                    ]
                                                ]) ?>
                                                <?php if ($isSlider) { ?>
                                                    <?= Html::tag('div', null, [
                                                        'class' => [
                                                            'photo-sections-top-section-navigation',
                                                            'intec-grid'
                                                        ],
                                                        'data-role' => 'photo.item.navigation'
                                                    ]) ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('a') ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?php unset($arSection) ?>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>