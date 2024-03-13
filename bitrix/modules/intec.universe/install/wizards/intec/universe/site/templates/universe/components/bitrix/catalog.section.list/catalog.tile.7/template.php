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

$vChildrenRender = include(__DIR__.'/parts/view.'.$arVisual['CHILDREN']['VIEW'].'.php');

if ($arVisual['PICTURE']['SIZE'] === 'big') {
    $arPictureSize = [
        'width' => 124,
        'height' => 124
    ];
} else if ($arVisual['PICTURE']['SIZE'] === 'medium') {
    $arPictureSize = [
        'width' => 64,
        'height' => 64
    ];
} else {
    $arPictureSize = [
        'width' => 48,
        'height' => 48
    ];
}

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-tile-7'
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
                        '1200-2' => $arVisual['COLUMNS'] >= 3,
                        '768-1' => true
                    ]
                ], true),
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'catalog-section-list-item-wrapper' => true,
                        'intec-grid' => [
                                '' => true,
                                'a-v-center' => !$arVisual['CHILDREN']['SHOW'] || empty($arSection['SECTIONS']),
                                '400-wrap' => true
                        ]
                    ], true)
                ]) ?>
                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                        <div class="intec-grid-item-auto intec-grid-item-400-1">
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
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item-400-1">
                        <div class="catalog-section-list-item-name">
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'intec-cl-text-hover'
                                ],
                                'href' => $arSection['SECTION_PAGE_URL'],
                                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <?= $arSection['NAME'] ?>
                                <span>
                                    <?= $arSection['ELEMENT_CNT'] ?>
                                </span>
                            <?= Html::endTag('a') ?>
                        </div>
                        <?php if ($arVisual['CHILDREN']['SHOW'] && !empty($arSection['SECTIONS'])) {

                            $bExpandable = false;
                            $iCountSections = count($arSection['SECTIONS']);

                            if ($arVisual['CHILDREN']['COUNT']['USE'] && $iCountSections > $arVisual['CHILDREN']['COUNT']['VALUE'])
                                $bExpandable = true;

                        ?>
                            <div class="catalog-section-list-item-children-container">
                                <?= Html::beginTag('div', [
                                    'class' => 'catalog-section-list-item-children',
                                    'data-role' => $bExpandable ? 'children' : null,
                                    'data-expanded' => $bExpandable ? 'false' : null,
                                    'data-children-view' => $arVisual['CHILDREN']['VIEW']
                                ]) ?>
                                    <?php $vChildrenRender($arSection['SECTIONS']) ?>
                                <?= Html::endTag('div') ?>
                                <?php if ($bExpandable) { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-section-list-item-button',
                                            'intec-cl-border-hover'
                                        ],
                                        'data' => [
                                            'role' => 'button',
                                            'expanded' => 'false'
                                        ]
                                    ]) ?>
                                        <div class="catalog-section-list-item-button-decoration"></div>
                                        <div class="catalog-section-list-item-button-text" data-role="button.text">
                                            <?= Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_TEMPLATE_BUTTON_SHOW') ?>
                                        </div>
                                        <div class="catalog-section-list-item-button-count">
                                            <?= $iCountSections - $arVisual['CHILDREN']['COUNT']['VALUE'] ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arSection['DESCRIPTION'])) { ?>
                            <div class="catalog-section-list-item-description">
                                <?= Html::stripTags($arSection['DESCRIPTION']) ?>
                            </div>
                        <?php } ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
    <?php if ($arVisual['CHILDREN']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>