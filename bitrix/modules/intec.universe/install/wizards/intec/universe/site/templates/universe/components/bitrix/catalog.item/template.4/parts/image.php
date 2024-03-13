<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arItem
 */

$sPicture = null;

if (!empty($arItem['PREVIEW_PICTURE']['SRC']))
    $sPicture = $arItem['PREVIEW_PICTURE']['SRC'];
else if (!empty($arItem['PREVIEW_PICTURE_SECOND']['SRC']))
    $sPicture = $arItem['PREVIEW_PICTURE_SECOND']['SRC'];

if (empty($sPicture))
    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <div class="catalog-item-picture-container">
        <?= Html::beginTag('a', [
            'class' => [
                'catalog-item-picture',
                'intec-ui-picture',
                'intec-image-effect'
            ],
            'href' => $arItem['DETAIL_PAGE_URL']
        ]) ?>
            <?= Html::img($sPicture, [
                'alt' => !empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'] : $arItem['NAME'],
                'title' => !empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE']) ? $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'] : $arItem['NAME'],
                'loading' => 'lazy'
            ]) ?>
        <?= Html::endTag('a') ?>
    </div>
<?php unset($sPicture, $sPictureTitle) ?>