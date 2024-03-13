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

$iCounter = 0;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-services-list-5'
    ],
    'data' => [
        'wide' => $arParams['WIDE'] === 'Y' ? 'true' : 'false'
    ]
]) ?>
    <div class="catalog-section-list-items">
        <?php foreach ($arResult['SECTIONS'] as $arSection) {

            $sId = $sTemplateId.'_'.$arSection['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arSection['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

            $iCounter++;
            $arPicture = $arSection['PICTURE'];

            if (!empty($arPicture)) {
                $arPicture = CFile::ResizeImageGet($arPicture, [
                    'width' => 500,
                    'height' => 500
                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                if (!empty($arPicture))
                    $arPicture = [
                        'ALT' => $arSection['PICTURE']['ALT'],
                        'SRC' => $arPicture['src'],
                        'TITLE' => $arSection['PICTURE']['TITLE']
                    ];
            }

            if (empty($arPicture)) {
                $arPicture = [
                    'ALT' => null,
                    'SRC' => SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                    'TITLE' => null
                ];
            }
        ?>
            <div class="catalog-section-list-item-wrap">
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
                                'i-h-24' => true,
                                'o-horizontal-reverse' => $iCounter % 2 == 0
                            ]
                        ], true)
                    ]) ?>
                        <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-section-list-item-picture-wrap' => true,
                                    'intec-grid-item' => [
                                        'auto' => true,
                                        '500-1' => true,
                                        '720-auto' => $arParams['WIDE'] === 'N' && !$arVisual['SVG']['USE'],
                                        '900-1' => $arParams['WIDE'] === 'N' && !$arVisual['SVG']['USE']
                                    ]
                                ], true)
                            ]) ?>
                                <?= Html::tag('a', null, [
                                    'href' => $arSection['SECTION_PAGE_URL'],
                                    'class' => [
                                        'catalog-section-list-item-picture',
                                        'intec-image-effect'
                                    ],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SRC'] : null
                                    ],
                                    'style' => [
                                        'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$arPicture['SRC'].'\')',
                                        'border-radius' => $arVisual['ROUNDING']['USE'] ? ($arVisual['ROUNDING']['VALUE'] / 2).'%' : null
                                    ]
                                ]) ?>
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
                                <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                                    <div class="catalog-section-list-item-description">
                                        <?= Html::stripTags($arSection['DESCRIPTION']) ?>
                                    </div>
                                <?php } ?>
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
                                            <span data-role="button.text.show"><?= Loc::getMessage('C_CATALOG_SECTION_LIST_SERVICES_LIST_5_BUTTON_TEXT_SHOW') ?></span>
                                            <span data-role="button.text.hide"><?= Loc::getMessage('C_CATALOG_SECTION_LIST_SERVICES_LIST_5_BUTTON_TEXT_HIDE') ?></span>
                                            <i class="far fa-angle-down"></i>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            </div>
        <?php } ?>
    </div>
    <?php if ($arVisual['CHILDREN']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>