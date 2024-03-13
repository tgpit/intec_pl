<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<div class="ns-bitrix c-news-list c-news-list-tile-4" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="news-list-header">
                <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center">
                    <div class="intec-grid-item">
                        <?php if ($arVisual['TITLE']['SHOW']) { ?>
                            <div class="news-list-title">
                                <?= $arVisual['TITLE']['VALUE'] ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?= Html::tag('div', null, [
                            'class' => 'news-list-navigation',
                            'data' => [
                                'role' => 'navigation'
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="news-list-content">
                <?= Html::beginTag('div', [
                    'class' => [
                        'news-list-items',
                        'owl-carousel'
                    ],
                    'data-role' => 'slider'
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $bDate = $arVisual['DATE']['SHOW'] && !empty($arItem['DATA']['DATE']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 600,
                                'height' => 600
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sPicture['src']))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => 'news-list-item',
                            'id' => $sAreaId
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-grid' => [
                                        '',
                                        'nowrap',
                                        'a-h-start',
                                        'a-v-center',
                                        'i-6'
                                    ]
                                ]
                            ]) ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'news-list-item-image',
                                            'intec-ui-picture',
                                            'intec-image-effect'
                                        ],
                                        'href' => $arItem['DETAIL_PAGE_URL']
                                    ]) ?>
                                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                            'alt' => $arItem['NAME'],
                                            'title' => $arItem['NAME'],
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                        ]) ?>
                                    <?= Html::endTag('a') ?>
                                </div>
                                <div class="intec-grid-item">
                                    <?= Html::tag('a', $arItem['NAME'], [
                                        'class' => [
                                            'news-list-item-name',
                                            'intec-cl-text-hover'
                                        ],
                                        'href' => $arItem['DETAIL_PAGE_URL']
                                    ]) ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
