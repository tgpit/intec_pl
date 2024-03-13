<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/gallery.navigation.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/gallery.navigation.right.svg')
    ]
];

$arContact = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['PROPERTY_MAP'], 'VALUE']);
$arData = [];

if (!empty($arContact)) {
    $arCoordinates = StringHelper::explode($arContact);
    if (!empty($arCoordinates) && count($arCoordinates) == 2) {
        $arCoordinates[0] = Type::toFloat($arCoordinates[0]);
        $arCoordinates[1] = Type::toFloat($arCoordinates[1]);
    }

    if (!empty($arCoordinates)) {
        if ($arParams['MAP_VENDOR'] == 'google') {
            $arData['google_lat'] = $arCoordinates[0];
            $arData['google_lon'] = $arCoordinates[1];
            $arData['google_scale'] = 16;
        } else if ($arParams['MAP_VENDOR'] == 'yandex') {
            $arData['yandex_lat'] = $arCoordinates[0];
            $arData['yandex_lon'] = $arCoordinates[1];
            $arData['yandex_scale'] = 16;
        }

        $arData['PLACEMARKS'] = [];

        $arPlaceMark = [];

        $arPlaceMark['LAT'] = $arCoordinates[0];
        $arPlaceMark['LON'] = $arCoordinates[1];
        $arPlaceMark['TEXT'] = $arResult['NAME'];

        $arData['PLACEMARKS'][] = $arPlaceMark;
    }
}

$bMapShow = ArrayHelper::getValue($arParams, 'MAP_SHOW') == 'Y';

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
    $arVisual['BUTTON']['TEXT'] = Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_FORM_BUTTON_DEFAULT');
?>
<div class="ns-bitrix c-news-detail c-news-detail-store-default-2" id="<?= $sTemplateId ?>">
    <div class="news-detail-wrap vcard" data-map-show="<?= $bMapShow ? 'true' : 'false' ?>">
        <div class="fn org" style="display:none;"><?= $arResult['NAME'] ?></div>
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <div class="intec-grid">
                    <div class="intec-grid-item-2 intec-grid-item-800-1">
                        <div class="news-detail-content">
                            <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                <div class="news-detail-pictures-wrap" data-role="gallery">
                                    <div class="news-detail-gallery-pictures" data-role="gallery.pictures">
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'news-detail-gallery-pictures-slider' => true,
                                                'owl-carousel' => count($arResult['GALLERY']) > 1
                                            ], true),
                                            'data-role' => count($arResult['GALLERY']) > 1 ? 'gallery.pictures.slider' : null
                                        ]) ?>
                                            <?php foreach ($arResult['GALLERY'] as $arPicture) {

                                                $sPicture = CFile::ResizeImageGet($arPicture, [
                                                    'width' => 480,
                                                    'height' => 480
                                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                            ?>
                                                <div class="news-detail-gallery-pictures-slider-item" data-role="gallery.pictures.item">
                                                    <?= Html::beginTag('div', [
                                                        'class' => 'news-detail-gallery-pictures-slider-item-picture',
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
                                            <?php } ?>
                                            <?php unset($arPicture, $sPicture) ?>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="news-detail-properties">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-v-8 intec-grid-i-h-20">
                                <?php if ($arVisual['ADDRESS']['SHOW'] && !empty($arResult['ADDRESS'])) { ?>
                                    <div class="intec-grid-item-1">
                                        <div class="news-detail-property news-detail-property-address">
                                            <div class="news-detail-property-text street-address">
                                                <?= $arResult['ADDRESS'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arResult['SCHEDULE'])) { ?>
                                    <div class="intec-grid-item-2 intec-grid-item-600-1">
                                        <div class="news-detail-property news-detail-property-schedule">
                                            <i class="far fa-clock"></i>
                                            <div class="news-detail-property-text">
                                                <span class="news-detail-property-name">
                                                    <?= Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_SCHEDULE') ?>:
                                                </span>
                                                <div class="workhours">
                                                    <?= implode(', ', $arResult['SCHEDULE']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($arVisual['PHONES']['SHOW'] && !empty($arResult['PHONES'])) { ?>
                                    <div class="intec-grid-item-2 intec-grid-item-600-1">
                                        <div class="news-detail-property news-detail-property-phone">
                                            <i class="fas fa-phone fa-flip-horizontal"></i>
                                            <div class="news-detail-property-text">
                                                <span class="news-detail-property-name">
                                                    <?= Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_PHONE') ?>:
                                                </span>
                                                <?php foreach ($arResult['PHONES'] as $arPhone) { ?>
                                                    <div class="news-detail-property-link">
                                                        <a class="tel" href="tel:<?= $arPhone['VALUE'] ?>">
                                                            <?= $arPhone['DISPLAY'] ?>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arResult['EMAIL'])) { ?>
                                    <div class="intec-grid-item-2 intec-grid-item-600-1">
                                        <div class="news-detail-property news-detail-property-email">
                                            <i class="fas fa-envelope"></i>
                                            <div class="news-detail-property-text">
                                                <span class="news-detail-property-name">
                                                    <?= Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_EMAIL') ?>:
                                                </span>
                                                <a class="email" href="mailto:<?= $arResult['EMAIL'] ?>">
                                                    <?= $arResult['EMAIL'] ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arResult['DESCRIPTION'])) { ?>
                                    <div class="intec-grid-item-1 news-detail-description">
                                        <div class="intec-grid-item-1 news-detail-description-wrapper">
                                            <?= $arResult['DESCRIPTION'] ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['SOCIAL_SERVICES']['SHOW']) { ?>
                                    <div class="intec-grid-item-1 news-detail-social-items">
                                        <div class="news-detail-social-items-wrapper">
                                            <div class="intec-grid intec-grid-wrap intec-grid-i-6">
                                                <?php foreach ($arResult['SOCIAL_SERVICES'] as $arSocialService) { ?>
                                                    <?php if (!empty($arSocialService['LINK'])) { ?>
                                                        <div class="intec-grid-item-auto">
                                                            <?= Html::tag('a', $arSocialService['ICON'], [
                                                                'class' => [
                                                                    'news-detail-social-item',
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
                                    <div class="intec-grid-item-1 news-detail-form-wrap">
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
                                            'news-detail-form'
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
                            <div class="news-detail-property news-detail-links">
                                <a class="news-detail-back intec-cl-text intec-cl-text-light-hover" href="javascript:history.back();">
                                    <span class="news-detail-back-icon far fa-angle-left"></span>
                                    <span class="news-detail-back-text">
                                        <?= Loc::getMessage('C_NEWS_DETAIL_STORE_DEFAULT_2_BACK_URL') ?>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($bMapShow) { ?>
            <div class="news-detail-map">
                <?php if ($arParams['MAP_VENDOR'] == 'yandex') {
                    $APPLICATION->IncludeComponent(
                        'bitrix:map.yandex.view',
                        '.default',
                        [
                            'INIT_MAP_TYPE' => 'MAP',
                            'MAP_DATA' => serialize($arData),
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
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    );
                } else if ($arParams['MAP_VENDOR'] == 'google') {
                    $APPLICATION->IncludeComponent(
                        'bitrix:map.google.view',
                        '.default',
                        [
                            'INIT_MAP_TYPE' => 'MAP',
                            'MAP_DATA' => serialize($arData),
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
                        $component,
                        ['HIDE_ICONS' => 'Y']
                    );
                } ?>
            </div>
        <?php } ?>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php') ?>