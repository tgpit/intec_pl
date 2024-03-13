<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\template\Properties;
use intec\core\helpers\FileHelper;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var $arResult
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sStub = Properties::get('template-images-lazyload-stub');

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-tile-2'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS'],
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
                'a-v-start'
            ]
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
                    $arPicture['SOURCE'] = CFile::ResizeImageGet($arSection['PICTURE'], [
                        'width' => 200,
                        'height' => 200
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
                $arPicture['ALT'] = $arSection['PICTURE']['ALT'];
                $arPicture['TITLE'] = $arSection['PICTURE']['TITLE'];
            }
        ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-section-list-item' => true,
                    'intec-grid-item' => [
                        $arVisual['COLUMNS'] => true,
                        '700-2' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                        '900-3' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 3,
                        '950-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                        '1050-4' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 4,
                        '1200-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 3
                    ]
                ], true)
            ]) ?>
                <div id="<?= $sAreaId ?>" class="catalog-section-list-item-wrapper">
                    <div class="catalog-section-list-item-wrapper-2">
                        <?= Html::beginTag('a', [
                            'class' => 'catalog-section-list-item-image',
                            'href' => $arSection['SECTION_PAGE_URL']
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-section-list-item-image-wrapper' => true,
                                    'intec-ui-picture' => true,
                                    'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                    'intec-image-effect' => true,
                                ], true)
                            ]) ?>
                                <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                    <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                                <?php } else { ?>
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : $arPicture['SOURCE'], [
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
                        <div class="catalog-section-list-item-information">
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'catalog-section-list-item-title',
                                    'intec-cl-text-hover'
                                ],
                                'href' => $arSection['SECTION_PAGE_URL']
                            ]) ?>
                                <span class="catalog-section-list-item-title-text">
                                    <?= $arSection['NAME'] ?>
                                </span>
                                <?php if ($arVisual['ELEMENTS']['QUANTITY']) { ?>
                                    <span class="catalog-section-list-item-title-elements">
                                        (<?= $arSection['ELEMENT_CNT'] ?>)
                                    </span>
                                <?php } ?>
                            <?= Html::endTag('a') ?>
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>