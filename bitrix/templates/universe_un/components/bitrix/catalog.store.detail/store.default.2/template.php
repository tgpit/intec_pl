<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];


$arForm = $arResult['FORM'];

$arForm['PARAMETERS'] = [
    'id' => $arForm['ID'],
    'template' => $arForm['TEMPLATE'],
    'parameters' => [
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM_ORDER',
        'CONSENT_URL' => $arForm['CONSENT']
    ],
    'settings' => [
        'title' => $arForm['TITLE']
    ],
    'fields' => [
        $arForm['FIELD'] => null
    ]
];

if (empty($arVisual['BUTTON']['TEXT']))
    $arVisual['BUTTON']['TEXT'] = Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_FORM_BUTTON_DEFAULT');

?>
<div class="ns-bitrix c-catalog-store-detail c-catalog-store-detail-store-default-2" id="<?= $sTemplateId ?>">
    <div class="catalog-store-detail-wrap" itemscope itemtype="http://schema.org/Product" data-map-show="<?= $arResult['MAP']['SHOW'] ? 'true' : 'false' ?>">
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <div class="intec-grid intec-grid-wrap">
                    <div class="intec-grid-item-2 intec-grid-item-800-1">
                        <div class="catalog-store-detail-content">
                            <?php if ($arVisual['PICTURE']['SHOW'] && !empty($arResult['PICTURE'])) { ?>
                                <div class="catalog-store-detail-pictures-wrap" data-role="gallery">
                                    <div class="catalog-store-detail-gallery-pictures" data-role="gallery.pictures">
                                        <div class="catalog-store-detail-gallery-pictures-slider">
                                            <div class="">
                                                <?php
                                                    $sPicture = CFile::ResizeImageGet($arResult['PICTURE'], [
                                                        'width' => 480,
                                                        'height' => 480
                                                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
                                                ?>
                                                <div class="catalog-store-detail-gallery-pictures-slider-item">
                                                    <?= Html::beginTag('div', [
                                                        'class' => 'catalog-store-detail-gallery-pictures-slider-item-picture',
                                                        'data-role' => 'gallery.pictures.item.picture',
                                                        'data-src' => $arPicture['SRC']
                                                    ]) ?>
                                                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture['src'], [
                                                            'alt' => $arResult['NAME'],
                                                            'title' => $arResult['NAME'],
                                                            'loading' => 'lazy',
                                                            'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                            'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture['src'] : null
                                                        ]) ?>
                                                    <?= Html::endTag('div') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="catalog-store-detail-properties">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-v-8 intec-grid-i-h-20">
                                    <?php if ($arVisual['ADDRESS']['SHOW'] && !empty($arResult['ADDRESS'])) { ?>
                                        <div class="intec-grid-item-1">
                                            <div class="catalog-store-detail-property catalog-store-detail-property-address">
                                                <div class="catalog-store-detail-property-text street-address">
                                                    <?= $arResult['ADDRESS'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arResult['SCHEDULE'])) { ?>
                                        <div class="intec-grid-item-2 intec-grid-item-600-1">
                                            <div class="catalog-store-detail-property catalog-store-detail-property-schedule">
                                                <i class="far fa-clock"></i>
                                                <div class="catalog-store-detail-property-text">
                                                    <span class="catalog-store-detail-property-name">
                                                        <?= Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_SCHEDULE') ?>:
                                                    </span>
                                                    <div class="workhours">
                                                        <?= $arResult['SCHEDULE'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($arVisual['PHONE']['SHOW'] && !empty($arResult['PHONE'])) { ?>
                                        <div class="intec-grid-item-2 intec-grid-item-600-1">
                                            <div class="catalog-store-detail-property catalog-store-detail-property-phone">
                                                <i class="fas fa-phone fa-flip-horizontal"></i>
                                                <div class="catalog-store-detail-property-text">
                                                    <span class="catalog-store-detail-property-name">
                                                        <?= Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_PHONE') ?>:
                                                    </span>
                                                    <div class="catalog-store-detail-property-link">
                                                        <a class="tel" href="tel:<?= $arResult['PHONE']['VALUE'] ?>">
                                                            <?= $arResult['PHONE']['DISPLAY'] ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arResult['EMAIL'])) { ?>
                                        <div class="intec-grid-item-2 intec-grid-item-600-1">
                                            <div class="catalog-store-detail-property catalog-store-detail-property-email">
                                                <i class="fas fa-envelope"></i>
                                                <div class="catalog-store-detail-property-text">
                                                    <span class="catalog-store-detail-property-name">
                                                        <?= Loc::getMessage('C_CATALOG_STORE_DETAIL_STORE_DEFAULT_2_EMAIL') ?>:
                                                    </span>
                                                    <a class="email" href="mailto:<?= $arResult['EMAIL'] ?>">
                                                        <?= $arResult['EMAIL'] ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arResult['DESCRIPTION'])) { ?>
                                        <div class="intec-grid-item-1 catalog-store-detail-description">
                                            <div class="intec-grid-item-1 catalog-store-detail-description-wrapper">
                                                <?= $arResult['DESCRIPTION'] ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['SOCIAL_SERVICES']['SHOW']) { ?>
                                        <div class="intec-grid-item-1 catalog-store-detail-social-items">
                                            <div class="catalog-store-detail-social-items-wrapper">
                                                <div class="intec-grid intec-grid-wrap intec-grid-i-6">
                                                    <?php foreach ($arResult['SOCIAL_SERVICES'] as $arSocialService) { ?>
                                                        <?php if (!empty($arSocialService['LINK'])) { ?>
                                                            <div class="intec-grid-item-auto">
                                                                <?= Html::tag('a', $arSocialService['ICON'], [
                                                                    'class' => [
                                                                        'catalog-store-detail-social-item',
                                                                        'intec-cl-text-hover'
                                                                    ],
                                                                    'href' => $arSocialService['LINK'],
                                                                ]) ?>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if ($arForm['SHOW']) { ?>
                                        <div class="intec-grid-item-1 catalog-store-detail-form-wrap">
                                            <?= Html::tag('div', $arVisual['BUTTON']['TEXT'], [
                                                'class' => [
                                                    'intec-ui' => [
                                                        '',
                                                        'scheme-current',
                                                        'control-button',
                                                        'mod-transparent',
                                                        'mod-round-2',
                                                        'size-2'
                                                    ],
                                                    'catalog-store-detail-form'
                                                ],
                                                'onclick' => '(function() {
                                                    template.api.forms.show('.JavaScript::toObject($arForm['PARAMETERS']).');
                                                    template.metrika.reachGoal(\'forms.open\');
                                                    template.metrika.reachGoal('.JavaScript::toObject('forms.'.$arForm['ID'].'.open').');
                                                })()'
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($arResult['MAP']['SHOW']) { ?>
            <div class="catalog-store-detail-map">
                <?php if ($arResult['MAP']['VENDOR'] == 0) {
                    $APPLICATION->IncludeComponent(
                        'bitrix:map.yandex.view',
                        '.default',
                        [
                            'INIT_MAP_TYPE' => 'MAP',
                            'MAP_DATA' => serialize([
                                'yandex_lat' => $arResult['MAP']['GPS']['N'],
                                'yandex_lon' => $arResult['MAP']['GPS']['S'],
                                'yandex_scale' => 10,
                                'PLACEMARKS' => [[
                                    'LON' => $arResult['MAP']['GPS']['S'],
                                    'LAT' => $arResult['MAP']['GPS']['N'],
                                    'TEXT' => $arResult['ADDRESS']
                                ]]
                            ]),
                            'CONTROLS' => [
                                'ZOOM',
                            ],
                            'OPTIONS' => [
                                'ENABLE_SCROLL_ZOOM',
                                'ENABLE_DBLCLICK_ZOOM',
                                'ENABLE_DRAGGING',
                            ],
                            'MAP_ID' => $arParams['MAP_ID'],
                            'MAP_WIDTH' => '100%',
                            'MAP_HEIGHT' => '100%',
                            'OVERLAY' => 'Y'
                        ],
                        $component
                    );
                } else {
                    $APPLICATION->IncludeComponent(
                        'bitrix:map.google.view',
                        '.default',
                        [
                            'INIT_MAP_TYPE' => 'MAP',
                            'MAP_DATA' => serialize([
                                'google_lat' => $arResult['MAP']['GPS']['N'],
                                'google_lon' => $arResult['MAP']['GPS']['S'],
                                'google_scale' => 10,
                                'PLACEMARKS' => [[
                                    'LON' => $arResult['MAP']['GPS']['S'],
                                    'LAT' => $arResult['MAP']['GPS']['N'],
                                    'TEXT' => $arResult['ADDRESS']
                                ]]
                            ]),
                            'CONTROLS' => [
                                'ZOOM',
                            ],
                            'OPTIONS' => [
                                'ENABLE_SCROLL_ZOOM',
                                'ENABLE_DBLCLICK_ZOOM',
                                'ENABLE_DRAGGING'
                            ],
                            'MAP_ID' => $arParams['MAP_ID'],
                            'MAP_WIDTH' => '100%',
                            'MAP_HEIGHT' => '100%',
                            'OVERLAY' => 'Y'
                        ],
                        $component
                    );
                } ?>
            </div>
        <?php } ?>
    </div>
</div>