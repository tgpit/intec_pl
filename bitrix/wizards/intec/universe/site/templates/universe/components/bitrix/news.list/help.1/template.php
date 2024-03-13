<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-list',
        'c-news-list-help-1'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="news-list-content">
                <?php if ($arVisual['BANNER']['SHOW']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'news-list-banner',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['BANNER']['PICTURE'] : null,
                            'theme' => $arVisual['BANNER']['THEME']
                        ],
                        'style' => [
                            'background-image' => 'url(\''.($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arVisual['BANNER']['PICTURE']).'\')'
                        ]
                    ]) ?>
                        <?php if ($arVisual['BANNER']['TITLE']['SHOW']) { ?>
                            <div class="news-list-banner-title">
                                <?= $arVisual['BANNER']['TITLE']['VALUE'] ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['BANNER']['SUBTITLE']['SHOW']) { ?>
                            <div class="news-list-banner-subtitle">
                                <?= $arVisual['BANNER']['SUBTITLE']['VALUE'] ?>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php if ($arVisual['IBLOCK_DESCRIPTION']['SHOW']) { ?>
                    <div class="news-list-iblock-description">
                        <?= $arResult['DESCRIPTION'] ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
                    <div class="news-list-navigation news-list-navigation-top">
                        <?= $arResult['NAV_STRING'] ?>
                    </div>
                <?php } ?>
                <div class="news-list-items">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-17">
                        <?php foreach($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sImage = $arItem['PREVIEW_PICTURE'];

                            if (empty($sImage))
                                $sImage = $arItem['DETAIL_PICTURE'];

                            if (!empty($sImage)) {
                                $sImage = CFile::ResizeImageGet($sImage, [
                                    'width' => 400,
                                    'height' => 320
                                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                if (!empty($sImage))
                                    $sImage = $sImage['src'];
                            } else {
                                $sImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                            }
                        ?>
                            <?= Html::beginTag('div', [
                                'id' => $sAreaId,
                                'class' => [
                                    'intec-grid' => [
                                        'item-4',
                                        'item-1200-3',
                                        'item-768-2',
                                        'item-550-1'
                                    ]
                                ]
                            ]) ?>
                                <?= Html::beginTag($arItem['DATA']['LINK']['SHOW'] ? 'a' : 'div', [
                                    'class' => 'news-list-item',
                                    'href' => $arItem['DATA']['LINK']['SHOW'] ? $arItem['DATA']['LINK']['VALUE'] : null
                                ]) ?>
                                    <?= Html::tag('div', '', [
                                        'class' => [
                                            'news-list-item-picture',
                                            'intec-image-effect'
                                        ],
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                                        ],
                                        'style' => [
                                            'background-image' => 'url(\''.($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sImage).'\')'
                                        ]
                                    ]) ?>
                                    <?= Html::tag('div', $arItem['NAME'], [
                                        'class' => [
                                            'news-list-item-name',
                                            'intec-cl-text-hover'
                                        ]
                                    ]) ?>
                                <?= Html::endTag($arItem['DATA']['LINK']['SHOW'] ? 'a' : 'div') ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
                    <div class="news-list-navigation news-list-navigation-bottom">
                        <?= $arResult['NAV_STRING'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>