<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CBitrixComponentTemplate $this
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
        'c-news-list-certificates-tile-1'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="news-list-content" data-role="content">
                <?php if ($arParams['DISPLAY_TOP_PAGER']) { ?>
                    <div class="news-list-navigation news-list-navigation-top">
                        <?= $arResult['NAV_STRING'] ?>
                    </div>
                <?php } ?>
                <div class="news-list-items">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-v-20 intec-grid-i-h-12">
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sPicture = $arItem['PICTURE'];

                            if (empty($sPicture['RESIZE_SRC']))
                                $sPicture['RESIZE_SRC'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        ?>
                            <?= Html::beginTag('div', [
                                'id' => $sAreaId,
                                'class' => [
                                    'intec-grid' => [
                                        'item-4',
                                        'item-1024-3',
                                        'item-768-2',
                                        'item-550-1'
                                    ]
                                ]
                            ]) ?>
                                <div class="news-list-item">
                                    <?= Html::tag('div', '', [
                                        'class' => 'news-list-item-image',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture['RESIZE_SRC'] : null,
                                            'src' => !empty($sPicture['ORIGINAL_SRC']) ? $sPicture['ORIGINAL_SRC'] : '',
                                            'preview-src' => !empty($sPicture['ORIGINAL_SRC']) ? $sPicture['RESIZE_SRC'] : '',
                                            'role' => !empty($sPicture['ORIGINAL_SRC']) ? 'zoom' : ''
                                        ],
                                        'style' => [
                                            'background-image' => 'url(\''.($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture['RESIZE_SRC']).'\')'
                                        ]
                                    ]) ?>
                                    <div class="news-list-item-name intec-cl-text-hover">
                                        <?= $arItem['NAME'] ?>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($arParams['DISPLAY_BOTTOM_PAGER']) { ?>
                    <div class="news-list-navigation news-list-navigation-bottom">
                        <?= $arResult['NAV_STRING'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>
