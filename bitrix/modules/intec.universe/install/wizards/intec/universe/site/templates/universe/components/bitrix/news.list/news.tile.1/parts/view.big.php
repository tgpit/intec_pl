<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var Closure $tagsRender($arItemTags)
 * @var string $sAreaId
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, &$tagsRender, &$sAreaId) {

    $sPicture = $arItem['PREVIEW_PICTURE'];

    if (empty($sPicture))
        $sPicture = $arItem['DETAIL_PICTURE'];

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 1250,
            'height' => 1250
        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

        if (!empty($sPicture['src']))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'news-list-item' => true,
            'intec-grid-item-auto' => true
        ], true),
        'data-view' => 'big'
    ]) ?>
        <div class="news-list-item-wrapper intec-grid intec-grid-a-v-end" id="<?= $sAreaId ?>">
            <?= Html::tag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a', '', [
                'class' => 'news-list-item-image',
                'href' => !$arItem['DATA']['HIDE_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null,
                'target' => $arVisual['LINK']['BLANK'] && !$arItem['DATA']['HIDE_LINK'] ? '_blank' : null,
                'title' => !empty($arItem['PREVIEW_PICTURE']) ? $arItem['PREVIEW_PICTURE']['TITLE'] : $arItem['NAME'],
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ],
                'style' => [
                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                ]
            ]) ?>
            <div class="news-list-item-text intec-grid-item">
                <div class="news-list-item-name">
                    <?= $arItem['NAME'] ?>
                </div>
                <?php ?>
                <?php if ($arResult['TAGS']['SHOW'] && !empty($arItem['DATA']['TAGS'])) {
                    $tagsRender($arItem['DATA']['TAGS']);
                } ?>
                <?php if ($arVisual['DATE']['SHOW'] && !empty($arItem['DATA']['DATE'])) { ?>
                    <div class="news-list-item-date">
                        <?= $arItem['DATA']['DATE'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
    <?php $arVisual['VIEW'] = 'default' ?>
<?php } ?>