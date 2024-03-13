<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\template\Properties;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sStub = Properties::get('template-images-lazyload-stub');

$arVisual = $arResult['VISUAL'];

$arPictureSizes = [
    'small' => [
        'width' => 60,
        'height' => 60
    ],
    'medium' => [
        'width' => 90,
        'height' => 90
    ],
    'large' => [
        'width' => 120,
        'height' => 120
    ]
];

$arPictureSize = $arPictureSizes[$arVisual['PICTURE']['SIZE']];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-tile-1'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS'],
        'picture-show' => $arVisual['PICTURE']['SHOW'] ? 'true' : 'false',
        'picture-size' => $arVisual['PICTURE']['SIZE'],
        'children-show' => $arVisual['CHILDREN']['SHOW'] ? 'true' : 'false',
        'description-show' => $arVisual['DESCRIPTION']['SHOW'] ? 'true' : 'false',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false'
    ]
]) ?>
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-section-list-items',
            'intec-grid' => [
                '',
                'wrap',
                'a-h-start',
                'a-v-stretch'
            ]
        ]
    ]) ?>
        <?php foreach ($arResult['SECTIONS'] as $arSection) {

            $sId = $sTemplateId.'_'.$arSection['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arSection['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

            if ($arVisual['DESCRIPTION']['SHOW'])
                $arSection['DESCRIPTION'] = Html::stripTags($arSection['DESCRIPTION']);

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
                        $arVisual['COLUMNS'] => true,
                        '800-1' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 1,
                        '1000-1' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 1,
                        '1150-2' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                        '1200-3' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 3,
                    ]
                ], true)
            ]) ?>
                <div id="<?= $sAreaId ?>" class="catalog-section-list-item-wrapper">
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'catalog-section-item-header' => true,
                            'intec-grid' => [
                                '' => true,
                                'nowrap' => true,
                                'i-h-12' => true,
                                'i-v-10' => true,
                                'a-h-center' => true,
                                'a-v-start' => !empty($arSection['SECTIONS']),
                                'a-v-center' => empty($arSection['SECTIONS']),
                            ]
                        ], true)
                    ]) ?>
                        <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'catalog-section-list-item-image',
                                    'intec-grid-item-auto'
                                ],
                                'href' => $arSection['SECTION_PAGE_URL']
                            ]) ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'catalog-section-list-item-image-wrapper' => true,
                                        'intec-ui-picture' => true,
                                        'intec-image-effect' => true,
                                        'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false
                                    ], true)
                                ]) ?>
                                    <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                        <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                                    <?php } else { ?>
                                        <?= Html::img(!$arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : $sStub, [
                                            'alt' => $arPicture['ALT'],
                                            'title' => $arPicture['TITLE'],
                                            'loading' => 'lazy',
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            <?= Html::endTag('a') ?>
                        <?php } ?>
                        <div class="catalog-section-list-item-information intec-grid-item">
                            <?= Html::tag('a', $arSection['NAME'], [
                                'class' => [
                                    'catalog-section-list-item-title',
                                    'intec-cl-text-hover'
                                ],
                                'href' => $arSection['SECTION_PAGE_URL']
                            ]) ?>
                            <?php if ($arVisual['CHILDREN']['SHOW'] && !empty($arSection['SECTIONS'])) { ?>
                                <div class="catalog-section-list-item-children">
                                    <?php $iChildCount = 0 ?>
                                    <?php foreach ($arSection['SECTIONS'] as $arChild) {

                                        $iChildCount++;

                                        if ($arVisual['CHILDREN']['COUNT'] !== false)
                                            if ($iChildCount > $arVisual['CHILDREN']['COUNT'])
                                                break;

                                    ?>
                                        <?= Html::beginTag('a', [
                                            'class' => [
                                                'catalog-section-list-item-child',
                                                'intec-cl-text-hover'
                                            ],
                                            'href' => $arChild['SECTION_PAGE_URL']
                                        ]) ?>
                                            <span class="catalog-section-list-item-child-name">
                                                <?= $arChild['NAME'] ?>
                                            </span>
                                            <?php if ($arVisual['ELEMENTS']['QUANTITY']) { ?>
                                                <span class="catalog-section-list-item-child-elements">
                                                    <?= $arChild['ELEMENT_CNT'] ?>
                                                </span>
                                            <?php } ?>
                                        <?= Html::endTag('a') ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                    <?php if (!empty($arVisual['DESCRIPTION']['SHOW']) && !empty($arSection['DESCRIPTION'])) { ?>
                        <div class="catalog-section-list-item-description">
                            <?= $arSection['DESCRIPTION'] ?>
                        </div>
                    <?php } ?>
                </div>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>