<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$arNavigation = !empty($arResult['NAV_RESULT']) ? [
    'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
    'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
    'NavNum' => $arResult['NAV_RESULT']->NavNum
] : [
    'NavPageCount' => 1,
    'NavPageNomer' => 1,
    'NavNum' => $this->randString()
];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sTemplateContainer = $sTemplateId.'-'.$arNavigation['NavNum'];

$arVisual = $arResult['VISUAL'];

$arVisual['NAVIGATION']['LAZY']['BUTTON'] =
    $arVisual['NAVIGATION']['LAZY']['BUTTON'] &&
    $arNavigation['NavPageNomer'] < $arNavigation['NavPageCount'];

?>
<div class="ns-bitrix c-catalog-section c-catalog-section-services-list-6" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
                <div class="catalog-section-navigation-top" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
            <!-- items-container -->
            <div class="catalog-section-content intec-grid intec-grid-wrap" data-entity="<?= $sTemplateContainer ?>">
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $arData = $arItem['DATA'];

                    $arPictureSize = [
                        'width' => 400,
                        'height' => 'auto'
                    ];

                    $arPicture = [
                        'TYPE' => 'picture',
                        'SOURCE' => null,
                        'ALT' => null,
                        'TITLE' => null
                    ];

                    if (!empty($arItem['PREVIEW_PICTURE'])) {
                        if ($arItem['PREVIEW_PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                            $arPicture['TYPE'] = 'svg';
                            $arPicture['SOURCE'] = $arItem['PREVIEW_PICTURE']['SRC'];
                        } else {
                            $arPicture['SOURCE'] = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], $arPictureSize, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

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
                    <div class="intec-grid-item-1" data-entity="items-row">
                        <div class="catalog-section-item" id="<?= $sAreaId ?>">
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'intec-grid' => [
                                        '' => true,
                                        'nowrap' => true,
                                        'i-h-12' => !$arVisual['SVG']['USE'],
                                        'i-h-24' => $arVisual['SVG']['USE'],
                                        'i-v-8' => true,
                                        '500-wrap' => true,
                                    ]
                                ], true)
                            ]) ?>
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <?= Html::beginTag('a', [
                                            'class' => [
                                                Html::cssClassFromArray([
                                                    'catalog-section-item-picture' => true,
                                                    'intec-ui-picture' => $arPicture['TYPE'] === 'svg' ? false : true,
                                                    'intec-image-effect' => true
                                                ], true)
                                            ],
                                            'href' => $arItem['DETAIL_PAGE_URL'],
                                            'data-svg-use' => $arVisual['SVG']['USE'] ? 'true' : 'false'
                                        ]) ?>
                                            <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                                <?= Html::tag('div', FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                                    'class' => [
                                                        Html::cssClassFromArray([
                                                            'catalog-section-item-picture-wrapper' => true,
                                                            'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                                            'intec-ui-picture' => true,
                                                            'intec-image-effect' => true,
                                                        ], true)
                                                    ]
                                                ]) ?>
                                            <?php } else { ?>
                                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPicture['SOURCE'], [
                                                    'alt' => $arItem['NAME'],
                                                    'title' => $arItem['NAME'],
                                                    'loading' => 'lazy',
                                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                                ]) ?>
                                            <?php } ?>
                                        <?= Html::endTag('a') ?>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item intec-grid-item-500-1">
                                    <div class="catalog-section-item-name">
                                        <?= Html::tag('a', $arItem['NAME'], [
                                            'class' => 'intec-cl-text-hover',
                                            'href' => $arItem['DETAIL_PAGE_URL']
                                        ]) ?>
                                    </div>
                                    <div class="catalog-section-item-description">
                                        <?= Html::stripTags($arItem['PREVIEW_TEXT']) ?>
                                    </div>
                                    <?php if ($arVisual['PROPERTIES']['SHOW'] && !empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                        <?php $iCount = 0 ?>
                                        <div class="catalog-section-item-properties">
                                            <?php foreach ($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
                                                if ($arVisual['PROPERTIES']['COUNT'] > 0 && $iCount == $arVisual['PROPERTIES']['COUNT'])
                                                    break;

                                                if (empty($arProperty['DISPLAY_VALUE']))
                                                    continue;

                                                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                                                    $arProperty['DISPLAY_VALUE'] = implode(', ', $arProperty['DISPLAY_VALUE']);

                                            ?>
                                                <div class="catalog-section-item-properties-item">
                                                    <?= Html::tag('div', $arProperty['NAME'].':', [
                                                        'class' => [
                                                            'catalog-section-item-properties-item-name',
                                                            'catalog-section-item-properties-item-part'
                                                        ]
                                                    ]) ?>
                                                    <?= Html::tag('div', $arProperty['DISPLAY_VALUE'], [
                                                        'class' => [
                                                            'catalog-section-item-properties-item-value',
                                                            'catalog-section-item-properties-item-part'
                                                        ]
                                                    ]) ?>
                                                </div>
                                                <?php $iCount++ ?>
                                            <?php } ?>
                                        </div>
                                        <?php unset($iCount); ?>
                                    <?php } ?>
                                    <?php if ($arData['PRICE']['CURRENT']['SHOW']) { ?>
                                        <div class="catalog-section-item-price">
                                            <div class="catalog-section-item-price-container">
                                                <div class="catalog-section-item-price-item catalog-section-item-price-current">
                                                    <?= $arData['PRICE']['CURRENT']['VALUE'] ?>
                                                </div>
                                                <?php if ($arData['PRICE']['OLD']['SHOW']) { ?>
                                                    <div class="catalog-section-item-price-item catalog-section-item-price-old">
                                                        <?= $arData['PRICE']['OLD']['VALUE'] ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- items-container -->
            <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
                <!-- noindex -->
                    <div class="catalog-section-more" data-use="show-more-<?= $arNavigation['NavNum'] ?>">
                        <div class="catalog-section-more-button">
                            <div class="catalog-section-more-icon intec-cl-svg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M16.5059 9.00153L15.0044 10.5015L13.5037 9.00153"  stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4.75562 4.758C5.84237 3.672 7.34312 3 9.00137 3C12.3171 3 15.0051 5.6865 15.0051 9.0015C15.0051 9.4575 14.9496 9.9 14.8536 10.3268" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M1.4939 8.99847L2.9954 7.49847L4.49615 8.99847"  stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M13.2441 13.242C12.1574 14.328 10.6566 15 8.99838 15C5.68263 15 2.99463 12.3135 2.99463 8.99853C2.99463 8.54253 3.05013 8.10003 3.14613 7.67328" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="catalog-section-more-text intec-cl-text">
                                <?= Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_TEMPLATE_LAZY_LOAD_TEXT') ?>
                            </div>
                        </div>
                    </div>
                <!-- /noindex -->
            <?php } ?>
            <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
                <div class="catalog-section-navigation-bottom" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
                    <!-- pagination-container -->
                    <?= $arResult['NAV_STRING'] ?>
                    <!-- pagination-container -->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>