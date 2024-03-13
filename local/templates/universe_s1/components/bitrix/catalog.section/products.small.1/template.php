<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-1'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS'],
        'position' => $arVisual['POSITION'],
        'size' => $arVisual['SIZE'],
        'wide' => $arVisual['WIDE'] ? 'true' : 'false',
        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false',
        'slider-dots' => $arVisual['SLIDER']['DOTS'] ? 'true' : 'false',
        'slider-navigation' => $arVisual['SLIDER']['NAVIGATION'] ? 'true' : 'false'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="catalog-section-items">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'catalog-section-items-content' => true,
                        'owl-carousel' => $arVisual['SLIDER']['USE'],
                        'intec-grid' => !$arVisual['SLIDER']['USE'] ? [
                            '' => true,
                            'wrap' => true,
                            'a-h-start' => $arVisual['POSITION'] === 'left',
                            'a-h-center' => $arVisual['POSITION'] === 'center',
                            'a-h-end' => $arVisual['POSITION'] === 'right',
                            'a-v-stretch' => true,
                            'i-5' => true
                        ] : false
                    ], true),
                    'data-role' => $arVisual['SLIDER']['USE'] ? 'slider' : null
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 115,
                                'height' => 115
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'catalog-section-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                    '1200-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 3,
                                    '1024-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 2,
                                    '600-1' => !$arVisual['SLIDER']['USE']
                                ]
                            ], true)
                        ]) ?>
                            <div id="<?= $sAreaId ?>" class="catalog-section-item-wrapper">
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'intec-grid' => [
                                            '' => true,
                                            'nowrap' => true,
                                            'a-h' => [
                                                'start' => true,
                                                '550-center' => $arVisual['SLIDER']['USE'] ? true : null
                                            ],
                                            'a-v-start' => true,
                                            'i-10' => true
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid-item-auto' => true,
                                            'ntec-grid-item-550-1' => $arVisual['SLIDER']['USE'],
                                        ], true)
                                    ]) ?>
                                        <?= Html::beginTag('a', [
                                            'class' => [
                                                'catalog-section-item-image',
                                                'intec-ui-picture',
                                                'intec-image-effect'
                                            ],
                                            'href' => $arItem['DETAIL_PAGE_URL']
                                        ]) ?>
                                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                                'alt' => !empty($arItem['PICTURE']['ALT']) ? $arItem['PICTURE']['ALT'] : $arItem['NAME'],
                                                'title' => !empty($arItem['PICTURE']['TITLE']) ? $arItem['PICTURE']['TITLE'] : $arItem['NAME'],
                                                'loading' => 'lazy',
                                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : null,
                                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ]) ?>
                                        <?= Html::endTag('a') ?>
                                    <?= Html::endTag('div') ?>
                                    <div class="catalog-section-item-information intec-grid-item">
                                        <div class="catalog-section-item-name intec-cl-text-hover">
                                            <?= Html::tag('a', $arItem['NAME'], [
                                                'class' => 'intec-cl-text-hover',
                                                'href' => $arItem['DETAIL_PAGE_URL']
                                            ]) ?>
                                        </div>
                                        <?php if ($arItem['DATA']['PRICE']['SHOW'] && !empty($arItem['ITEM_PRICES'])) {

                                            $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

                                        ?>
                                            <div class="catalog-section-item-price">
                                                <div class="catalog-section-item-price-discount">
                                                    <?php if ($arItem['DATA']['OFFER']) { ?>
                                                        <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_1_PRICE_FORM', [
                                                            '#PRICE#' => $arPrice['PRINT_PRICE']
                                                        ]) ?>
                                                    <?php } else { ?>
                                                        <?= $arPrice['PRINT_PRICE'] ?>
                                                    <?php } ?>
                                                </div>
                                                <?php if ($arPrice['PERCENT'] > 0) { ?>
                                                    <div class="catalog-section-item-price-base">
                                                        <?php if ($arItem['DATA']['OFFER']) { ?>
                                                            <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_1_PRICE_FORM', [
                                                                '#PRICE#' => $arPrice['PRINT_PRICE']
                                                            ]) ?>
                                                        <?php } else { ?>
                                                            <?= $arPrice['PRINT_BASE_PRICE'] ?>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } else if ($arItem['DATA']['ACTION'] === 'request') { ?>
                                            <div class="catalog-section-item-price">
                                                <?= Html::beginTag('a', [
                                                    'class' => [
                                                        'catalog-section-item-button',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-button',
                                                            'scheme-current',
                                                            'mod-transparent',
                                                            'mod-round-2'
                                                        ]
                                                        /*'intec-cl-text',
                                                        'intec-cl-text-light-hover',*/
                                                    ],
                                                    'href' => $arItem['DETAIL_PAGE_URL']
                                                ]) ?>
                                                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_1_MESSAGE_REQUEST') ?>
                                                <?= Html::endTag('a') ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <div class="catalog-section-navigation" data-role="slider.navigation"></div>
                <?= Html::tag('div', null, [
                    'class' => [
                        'catalog-section-dots',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-center',
                            'a-h-center',
                            'i-8'
                        ]
                    ],
                    'data-role' => 'slider.dots'
                ]) ?>
            </div>
        </div>
    </div>
    <?php if ($arVisual['SLIDER']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>
