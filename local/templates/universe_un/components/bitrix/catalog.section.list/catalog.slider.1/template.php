<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

if ($arVisual['PICTURE']['SIZE'] === 'medium') {
    $arPictureSize = [
        'width' => 80,
        'height' => 80
    ];
} else {
    $arPictureSize = [
        'width' => 64,
        'height' => 64
    ];
}
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-slider-1'
    ],
    'data' => [
        'picture-size' => $arVisual['PICTURE']['SIZE'],
        'effect' => $arVisual['EFFECT'],
        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false',
        'slider-dots' => $arVisual['SLIDER']['DOTS'] ? 'true' : 'false',
        'slider-navigation' => $arVisual['SLIDER']['NAVIGATION'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS']
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-section-list-items' => true,
                    'intec-grid' => [
                        '' => !$arVisual['SLIDER']['USE'],
                        'wrap' => !$arVisual['SLIDER']['USE'],
                        'a-v-start' => !$arVisual['SLIDER']['USE'],
                        'a-h-'.$arVisual['ALIGNMENT'] => !$arVisual['SLIDER']['USE'],
                        'i-15' => !$arVisual['SLIDER']['USE']
                    ],
                    'owl-carousel' => $arVisual['SLIDER']['USE']
                ], true),
                'data' => [
                    'role' => $arVisual['SLIDER']['USE'] ? 'slider' : null
                ]
            ]) ?>
                <?php foreach ($arResult['SECTIONS'] as $arSection) {

                    $sId = $sTemplateId.'_'.$arSection['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arSection['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

                    $arPicture = [
                        'TYPE' => 'picture',
                        'SOURCE' => null,
                        'ALT' => null,
                        'TITLE' => null
                    ];

                    if (!empty($arSection['PICTURE'])) {
                        if ($arSection['PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                            $arPicture['TYPE'] = 'svg';
                            $arPicture['SOURCE'] = $arSection['PICTURE']['SRC'];
                        } else {
                            $arPicture['SOURCE'] = CFile::ResizeImageGet($arSection['PICTURE'], $arPictureSize, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

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
                        $arPicture['ALT'] = $arSection['PICTURE']['ALT'];
                        $arPicture['TITLE'] = $arSection['PICTURE']['TITLE'];
                    }
                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'catalog-section-list-item' => true,
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                '1024-4' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 5,
                                '768-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 4,
                                '600-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 3,
                                '375-1' => !$arVisual['SLIDER']['USE']
                            ]
                        ], true),
                        'data-role' => 'item',
                        'data-expanded' => 'false'
                    ]) ?>
                        <?= Html::beginTag('div', [
                            'id' => $sAreaId,
                            'class' => Html::cssClassFromArray([
                                'catalog-section-list-item-wrapper' => true,
                            ], true)
                        ]) ?>
                                <?= Html::beginTag('a', [
                                    'class' => Html::cssClassFromArray([
                                        'catalog-section-list-item-picture-wrap' => true,
                                        'catalog-section-list-item-picture' => true,
                                        'intec-image-effect' => true,
                                        'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                        'intec-ui-picture' => true
                                    ], true),
                                    'href' => $arSection['SECTION_PAGE_URL'],
                                    'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                ]) ?>
                                <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                    <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                                <?php } else { ?>
                                    <?= Html::img($arPicture['SOURCE'], [
                                        'alt' => $arPicture['ALT'],
                                        'title' => $arPicture['TITLE'],
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <?= Html::endTag('a') ?>
                                <div class="catalog-section-list-item-name-wrap">
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'catalog-section-list-item-name',
                                            'intec-cl-text-hover'
                                        ],
                                        'href' => $arSection['SECTION_PAGE_URL'],
                                        'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                    ]) ?>
                                        <?= $arSection['NAME'] ?>
                                    <?= Html::endTag('a') ?>
                                </div>
                        <?= Html::endTag('div') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php'); ?>
<?= Html::endTag('div') ?>