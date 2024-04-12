<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var Closure $getMapCoordinates
 */

?>
<div class="contacts-shops">
    <div class="contacts-title">
        <?= Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_SHOPS') ?>
    </div>
    <div class="contacts-sections">
        <?php if ($arVisual['SCROLL']) { ?>
            <div class="scrollbar-outer" data-role="scroll">
        <?php } ?>
        <?= Html::beginTag('ul', [
            'class' => Html::cssClassFromArray([
                'intec-ui' => [
                    '' => true,
                    'control-tabs' => true,
                    'mod-block' => !$arVisual['SCROLL'],
                    'scheme-current' => true
                ]
            ], true ),
            'data-ui-control' => 'tabs'
        ]) ?>
            <?php $bActive = true ?>
            <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
                <?= Html::beginTag('li', [
                    'class' => 'intec-ui-part-tab',
                    'data' => [
                        'active' => $bActive ? 'true' : 'false'
                    ]
                ]) ?>
                    <?= Html::tag('a', $arSection['NAME'], [
                        'href' => '#contacts-'.$sTemplateId.'-section-'.$arSection['ID'],
                        'data' => [
                            'type' => 'tab'
                        ]
                    ]) ?>
                <?= Html::endTag('li') ?>
                <?php $bActive = false ?>
            <?php } ?>
        <?= Html::endTag('ul') ?>
        <?php if ($arVisual['SCROLL']) { ?>
            </div>
        <?php } ?>
        <div class="intec-ui intec-ui-control-tabs-content">
            <?php $bActive = true ?>
            <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
                <?= Html::beginTag('div', [
                    'id' => 'contacts-'.$sTemplateId.'-section-'.$arSection['ID'],
                    'class' => 'intec-ui-part-tab',
                    'data' => [
                        'active' => $bActive ? 'true' : 'false'
                    ]
                ]) ?>
                    <div class="contacts-shops-list">
                        <div class="contacts-shops-list-wrapper intec-grid intec-grid-wrap intec-grid-i-15">
                            <?php foreach ($arSection['ITEMS'] as $arItem) { ?>
                            <?php
                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $sTag = 'div';

                                if (!empty($arItem['DATA']['LINK']))
                                    $sTag = 'a';

                                $sImage = $arItem['PREVIEW_PICTURE'];

                                if (empty($sImage))
                                    $sImage = $arItem['DETAIL_PICTURE'];

                                if (!empty($sImage)) {
                                    $sImage = CFile::ResizeImageGet($sImage, [
                                        'width' => 240,
                                        'height' => 240
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                    if (!empty($sImage))
                                        $sImage = $sImage['src'];
                                }

                                if (empty($sImage))
                                    $sImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                            ?>
                                <div class="contacts-shop intec-grid-item-3 intec-grid-item-800-2 intec-grid-item-500-1">
                                    <div class="contacts-shop-wrapper intec-grid intec-grid-wrap intec-grid-i-h-8" id="<?= $sAreaId ?>">
                                        <div class="intec-grid-item-auto intec-grid-item-800-1">
                                            <div class="contacts-image">
                                                <?= Html::tag($sTag, null, [
                                                    'href' => $arItem['DATA']['LINK'],
                                                    'class' => 'contacts-image-wrapper',
                                                    'data' => [
                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                                                    ],
                                                    'style' => [
                                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sImage.'\')' : null
                                                    ]
                                                ]) ?>
                                            </div>
                                        </div>
                                       <div class="intec-grid-item intec-grid-item-800-1">
                                           <div class="contacts-information">
                                               <?php if (!empty($arItem['DATA']['ADDRESS'])) { ?>
                                                   <?= Html::tag($sTag, $arItem['DATA']['ADDRESS'], [
                                                       'href' => $arItem['DATA']['LINK'],
                                                       'class' => $sTag == 'a' ? 'contacts-address intec-cl-text-hover' : 'contacts-address'
                                                   ]) ?>
                                               <?php } ?>
                                               <?php if (!empty($arItem['DATA']['PHONE'])) { ?>
                                                   <div class="contacts-phone">
                                                    <span>
                                                        <?= Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_SHOPS_PHONE') ?>:
                                                    </span>
                                                       <span>
                                                        <a href="tel:<?= $arItem['DATA']['PHONE']['VALUE'] ?>">
                                                            <?= $arItem['DATA']['PHONE']['DISPLAY'] ?>
                                                        </a>
                                                    </span>
                                                   </div>
                                               <?php } ?>
                                               <?php if (!empty($arItem['DATA']['EMAIL'])) { ?>
                                                   <a href="mailto:<?= $arItem['DATA']['EMAIL'] ?>" class="contacts-email intec-cl-text">
                                                       <?= $arItem['DATA']['EMAIL'] ?>
                                                   </a>
                                               <?php } ?>
                                               <?php if ($arResult['MAP']['SHOW'] && !empty($arItem['DATA']['MAP'])) { ?>
                                                   <?= Html::tag('a', Loc::getMessage('C_NEWS_LIST_CONTACTS_LIST_SHOPS_SHOW_ON_MAP'), [
                                                       'class' => 'contacts-on-map',
                                                       'href' => '#'.$sTemplateId.'_map',
                                                       'data' => [
                                                           'latitude' => $arItem['DATA']['MAP']['LATITUDE'],
                                                           'longitude' => $arItem['DATA']['MAP']['LONGITUDE']
                                                       ]
                                                   ]) ?>
                                               <?php } ?>
                                           </div>
                                       </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
                <?php $bActive = false ?>
            <?php } ?>
        </div>
    </div>
</div>