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

$vChildrenRender = include(__DIR__.'/parts/children.php');

$arPictureSize = [
    'width' => 104,
    'height' => 104
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-tile-8'
    ],
    'data-picture-size' => $arVisual['PICTURE']['SIZE']
]) ?>
    <div class="catalog-section-list-items intec-grid intec-grid-wrap intec-grid-a-v-sretch">
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
                'id' => $sAreaId,
                'class' => Html::cssClassFromArray([
                    'catalog-section-list-item' => true,
                    'intec-grid-item' => [
                        $arVisual['COLUMNS'] => true,
                        '1200-3' => $arVisual['COLUMNS'] >= 4,
                        '768-2' => $arVisual['COLUMNS'] >= 3,
                        '500-1' => true
                    ]
                ], true),
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-section-list-item-wrapper'
                ]) ?>
                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                        <?= Html::beginTag('a', [
                            'class' => Html::cssClassFromArray([
                                'catalog-section-list-item-picture-wrap' => true,
                                'catalog-section-list-item-picture' => true,
                                'intec-ui-picture' => true,
                                'intec-image-effect' => true,
                                'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false
                            ], true),
                            'href' => $arSection['SECTION_PAGE_URL'],
                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                        ]) ?>
                        <?php if ($arPicture['TYPE'] === 'svg') { ?>
                            <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                        <?php } else { ?>
                            <?= Html::img($arPicture['SOURCE'], [
                                'class' => 'intec-image-effect',
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
                    <?php } ?>
                    <div class="catalog-section-list-item-content">
                        <div class="catalog-section-list-item-content-wrapper intec-grid intec-grid-a-v-center">
                            <div class="intec-grid-item-auto"></div>
                            <div class="intec-grid-item">
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
                                <?php if ($arVisual['CHILDREN']['SHOW'] && !empty($arSection['SECTIONS'])) {

                                    $bExpandable = false;
                                    $iCountSections = count($arSection['SECTIONS']);

                                    if ($arVisual['CHILDREN']['COUNT']['USE'] && $iCountSections > $arVisual['CHILDREN']['COUNT']['VALUE'])
                                        $bExpandable = true;

                                    ?>
                                    <div class="catalog-section-list-item-children">
                                        <?php $vChildrenRender($arSection['SECTIONS']) ?>
                                    </div>
                                    <?php if ($bExpandable) { ?>
                                        <div class="catalog-section-list-item-button-wrap">
                                            <?= Html::beginTag('a', [
                                                'class' => [
                                                    'catalog-section-list-item-button',
                                                    'intec-cl-border-hover'
                                                ],
                                                'href' => $arSection['SECTION_PAGE_URL']
                                            ]) ?>
                                            <div class="catalog-section-list-item-button-text" data-role="button.text">
                                                <?= Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_8_TEMPLATE_BUTTON_SHOW') ?>
                                            </div>
                                            <div class="catalog-section-list-item-button-count">
                                                <?= $iCountSections - $arVisual['CHILDREN']['COUNT']['VALUE'] ?>
                                            </div>
                                            <?= Html::endTag('a') ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>