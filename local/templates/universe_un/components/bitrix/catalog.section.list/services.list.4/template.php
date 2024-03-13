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
    'width' => 300,
    'height' => 300
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-services-list-4'
    ],
    'data' => [
        'wide' => $arParams['WIDE'] === 'Y' ? 'true' : 'false',
        'svg-use' => $arVisual['SVG']['USE'] ? 'true' : null
    ]
]) ?>
    <div class="catalog-section-list-items">
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
                'class' => [
                    'catalog-section-list-item'
                ],
                'data-role' => 'item',
                'data-expanded' => 'false'
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'catalog-section-list-item-wrapper' => true,
                        'intec-grid' => [
                            '' => true,
                            '500-wrap' => true,
                            '900-wrap' => $arParams['WIDE'] === 'N' && !$arVisual['SVG']['USE'],
                            '720-nowrap' => $arParams['WIDE'] === 'N' && !$arVisual['SVG']['USE'],
                            'a-v-center' => !$arVisual['CHILDREN']['SHOW'] || empty($arSection['SECTIONS']),
                        ]
                    ], true)
                ]) ?>
                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'catalog-section-list-item-picture-wrap-1' => true,
                                'intec-grid-item' => [
                                    'auto' => true,
                                    '500-1' => true,
                                    '720-auto' => $arParams['WIDE'] === 'N' && !$arVisual['SVG']['USE'],
                                    '900-1' => $arParams['WIDE'] === 'N' && !$arVisual['SVG']['USE']
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag('a', [
                                'class' => 'catalog-section-list-item-picture-wrap',
                                'href' => $arSection['SECTION_PAGE_URL'],
                                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                            ]) ?>
                                <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                    <?= Html::tag('div', FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                        'class' => [
                                            Html::cssClassFromArray([
                                                'catalog-section-list-item-picture' => true,
                                                'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                                'intec-image-effect' => true,
                                            ], true)
                                        ]
                                    ]) ?>
                                <?php } else { ?>
                                    <?= Html::tag('div', null, [
                                        'class' => [
                                            'catalog-section-list-item-picture',
                                            'intec-image-effect'
                                        ],
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arPicture['SOURCE'].'\')' : null
                                        ]
                                    ]) ?>
                                <?php } ?>
                            <?= Html::endTag('a') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item">
                        <div class="catalog-section-list-item-section">
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
                            <div class="catalog-section-list-item-description">
                                <?= Html::stripTags($arSection['DESCRIPTION']) ?>
                            </div>
                        </div>
                        <?php if ($arVisual['CHILDREN']['SHOW'] && !empty($arSection['SECTIONS'])) { ?>
                            <div class="catalog-section-list-item-children-wrap">
                                <?= Html::beginTag('div', [
                                    'class' => 'catalog-section-list-item-children',
                                    'data-role' => 'children'
                                ]) ?>
                                    <div class="catalog-section-list-item-children-wrapper">
                                        <div class="intec-grid intec-grid-i-6 intec-grid-wrap">
                                            <?php $vChildrenRender($arSection['SECTIONS']) ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-section-list-item-button'
                                    ],
                                    'data' => [
                                        'role' => 'button',
                                        'expanded' => 'false'
                                    ]
                                ]) ?>
                                    <div class="catalog-section-list-item-button-text intec-cl-text" data-role="button.text">
                                        <span data-role="button.text.show"><?= Loc::getMessage('C_CATALOG_SECTION_LIST_SERVICES_LIST_4_BUTTON_TEXT_SHOW') ?></span>
                                        <span data-role="button.text.hide"><?= Loc::getMessage('C_CATALOG_SECTION_LIST_SERVICES_LIST_4_BUTTON_TEXT_HIDE') ?></span>
                                        <i class="far fa-angle-down"></i>
                                    </div>
                                <?= Html::endTag('div') ?>
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