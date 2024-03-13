<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

$arVisual = $arResult['VISUAL'];

?>
<div class="ns-bitrix c-news-list c-news-list-collections-list-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['NAVIGATION']['SHOW']['TOP']) { ?>
                <div class="news-list-navigation news-list-navigation-top">
                    <?= $arResult['NAV_STRING'] ?>
                </div>
            <?php } ?>
            <div class="news-list-items">
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
                            'width' => 645,
                            'height' => 420
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    if (!empty($sPicture))
                        $sPicture = $sPicture['src'];
                    else
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                ?>
                    <div class="news-list-item" id="<?= $sAreaId ?>">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid' => [
                                    '',
                                    'i-h-12',
                                    'a-v-center',
                                    'wrap'
                                ]
                            ],
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-grid' => [
                                        'item-auto',
                                        'item-768-1'
                                    ]
                                ]
                            ]) ?>
                                <?= Html::tag('a', '', [
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
                            <?= Html::endTag('div') ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'news-list-item-text',
                                    'intec-grid' => [
                                        'item',
                                        'item-768-1',
                                    ]
                                ]
                            ]) ?>
                                <div class="news-list-item-name">
                                    <?= Html::tag($arItem['DATA']['LINK']['SHOW'] ? 'a' : 'div', $arItem['NAME'], [
                                        'class' => Html::cssClassFromArray([
                                            'intec-cl-text-light-hover' => $arItem['DATA']['LINK']['SHOW']
                                        ], true),
                                        'href' => $arItem['DATA']['LINK']['SHOW'] ? $arItem['DATA']['LINK']['VALUE'] : null
                                    ]) ?>
                                </div>
                                <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                    <div class="news-list-item-description">
                                        <?= Html::stripTags($arItem['PREVIEW_TEXT']) ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['BUTTONS']['MORE']['SHOW'] && $arItem['DATA']['LINK']['SHOW']) { ?>
                                    <a class="news-list-item-show-more-button" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                                        <?= Loc::getMessage('C_NEWS_LIST_COLLECTIONS_LIST_1_TEMPLATE_SHOW_MORE') ?>
                                    </a>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($arVisual['NAVIGATION']['SHOW']['BOTTOM']) { ?>
                <div class="news-list-navigation news-list-navigation-bottom">
                    <?= $arResult['NAV_STRING'] ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>