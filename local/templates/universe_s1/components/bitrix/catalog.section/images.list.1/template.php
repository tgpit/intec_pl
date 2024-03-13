<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<div class="ns-bitrix c-catalog-section c-catalog-section-images-list-1" id="<?= $sTemplateId ?>">
    <div class="catalog-section-items">
        <?php foreach ($arResult['ITEMS'] as $arItem) {

            $sId = $sTemplateId.'_'.$arItem['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

            $arPicture = [];

            if (!empty($arItem['PREVIEW_PICTURE']))
                $arPicture = $arItem['PREVIEW_PICTURE'];
            else if (!empty($arItem['DETAIL_PICTURE']))
                $arPicture = $arItem['DETAIL_PICTURE'];

            if (!empty($arPicture)) {
                $resizePicture = CFile::ResizeImageGet($arPicture, [
                    'width' => 56,
                    'height' => 56
                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                if (!empty($resizePicture))
                    $resizePicture = $resizePicture['src'];
            } else
                $resizePicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

        ?>
            <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="catalog-section-item" id="<?= $sAreaId ?>">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-section-item-picture',
                                    'intec-ui-picture',
                                    'intec-image-effect'
                                ]
                            ]) ?>
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $resizePicture, [
                                    'alt' => $arPicture['ALT'],
                                    'title' => $arPicture['TITLE'],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $resizePicture : null
                                    ]
                                ]) ?>
                            <?= Html::endTag('div') ?>
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item-shrink-1">
                        <div class="catalog-section-item-name intec-cl-text-hover">
                            <?= $arItem['NAME'] ?>
                        </div>
                        <?php if ($arVisual['PRICE']['SHOW'] && !empty($arItem['MIN_PRICE'])) { ?>
                            <div class="catalog-section-item-price">
                                <div class="catalog-section-item-price-current">
                                    <?php if (!empty($arItem['OFFERS'])) { ?>
                                        <span>
                                            <?= Loc::getMessage('C_CATALOG_SECTION_IMAGES_LIST_1_TEMPLATE_PRICE_FROM') ?>
                                        </span>
                                    <?php } ?>
                                    <span>
                                        <?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>
                                    </span>
                                    <?php if (!$arVisual['MEASURE']['SHOW'] && !empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                                        <span>/</span>
                                        <span>
                                            <?= $arItem['CATALOG_MEASURE_NAME'] ?>
                                        </span>
                                    <?php } ?>
                                </div>
                                <?php if ($arVisual['PRICE']['DISCOUNT']['SHOW'] && $arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0) { ?>
                                    <div class="catalog-section-item-price-discount">
                                        <?= $arItem['MIN_PRICE']['PRINT_VALUE'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
</div>