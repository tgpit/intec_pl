<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

$arVisual = $arResult['VISUAL'];

?>
<div class="ns-bitrix c-news-list c-news-list-collections-tile-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['SHOW']['TOP']) { ?>
                <div class="news-list-navigation news-list-navigation-top">
                    <?= $arResult['NAV_STRING'] ?>
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'news-list-items',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'i-10'
                    ]
                ]
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);
                    $sPicture = null;

                    if (!empty($arItem['PREVIEW_PICTURE']))
                        $sPicture = $arItem['PREVIEW_PICTURE'];
                    else if (!empty($arItem['DETAIL_PICTURE']))
                        $sPicture = $arItem['DETAIL_PICTURE'];

                    if (!empty($sPicture))
                        $sPicture = CFile::ResizeImageGet($sPicture, [
                            'width' => 490,
                            'height' => 430
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    if (!empty($sPicture))
                        $sPicture = $sPicture['src'];
                    else
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                ?>
                    <?= Html::beginTag('div', [
                        'id' => $sAreaId,
                        'class' => Html::cssClassFromArray([
                            'news-list-item' => true,
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'] => true,
                                '500-1' => $arVisual['COLUMNS'] <= 4,
                                '720-2' => $arVisual['COLUMNS'] > 2,
                                '1000-3' => $arVisual['COLUMNS'] > 3,
                            ]
                        ],  true)
                    ]) ?>
                        <?= Html::beginTag($arItem['DATA']['LINK']['SHOW'] ? 'a' : 'div', [
                            'class' => 'news-list-item-wrapper',
                            'href' => $arItem['DATA']['LINK']['SHOW'] ? $arItem['DATA']['LINK']['VALUE'] : null
                        ]) ?>
                            <?= Html::tag('div', '', [
                                'class' => 'news-list-item-picture',
                                'href' => $arItem['DETAIL_PAGE_URL'],
                                'title' => $arItem['NAME'],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ],
                                'style' => [
                                    'background-image' => 'url(\''.(
                                        $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                                    ).'\')'
                                ]
                            ]) ?>
                            <div class="news-list-item-name">
                                <?= $arItem['NAME'] ?>
                            </div>
                        <?= Html::endTag($arItem['DATA']['LINK']['SHOW'] ? 'a' : 'div') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
            <?php if ($arVisual['NAVIGATION']['SHOW']['BOTTOM']) { ?>
                <div class="news-list-navigation news-list-navigation-bottom">
                    <?= $arResult['NAV_STRING'] ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>