<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var string $sTemplateId
 * @var array $arResult
 * @var array $arVisual
 * @var array $arItemSvg
 * @var string $sAreaId
 */

global $USER;

?>
<?php return function(&$arItem, $bUser = false) use (&$sTemplateId, &$arVisual, &$arItemSvg, &$sAreaId) { ?>
    <?php if (!empty($arItem)) {

        $arData = $arItem['DATA'];

    ?>
        <div class="reviews-item" data-role="reviews.content.item">
            <div class="reviews-item-content" id="<?= $sAreaId ?>">
                <?php if ($bUser) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'reviews-item-marker',
                        'data-status' => $arItem['ACTIVE'] === 'Y' ? 'accepted' : 'on-hold'
                    ]) ?>
                        <?php if ($arItem['ACTIVE'] === 'Y') { ?>
                            <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_ITEM_USER_MODERATED') ?>
                        <?php } else { ?>
                            <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_ITEM_USER_MODERATING') ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                    <?php if ($arVisual['ITEMS']['PICTURE']['SHOW']) {

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture,  [
                                'width' => 64,
                                'height' => 64
                            ], BX_RESIZE_IMAGE_EXACT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        } else {
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        }

                    ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::tag('div', null, [
                                'class' => [
                                    'reviews-item-picture',
                                    'intec-image-effect'
                                ],
                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                'data-view' => $arVisual['ITEMS']['PICTURE']['VIEW'],
                                'style' => [
                                    'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$sPicture.'\')'
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item-shrink-1">
                        <?php if ($arData['DATE']['SHOW']) { ?>
                            <div class="reviews-item-date">
                                <?= $arData['DATE']['VALUE'] ?>
                            </div>
                        <?php } ?>
                        <div class="reviews-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                    </div>
                    <?php if ($arData['RATING']['USE']) {

                        $iCounter = 0;

                    ?>
                        <div class="intec-grid-item-auto">
                            <?= Html::beginTag('div', [
                                'class' => 'reviews-item-grade',
                                'title' => $arData['RATING']['LIST'][$arData['RATING']['VALUE']],
                                'data' => [
                                    'role' => 'reviews.content.item.grade',
                                    'value' => $arData['RATING']['VALUE']
                                ]
                            ])  ?>
                                <?php foreach ($arData['RATING']['LIST'] as $key => $sValue) { ?>
                                    <?= Html::tag('div', $arItemSvg['RATING'], [
                                        'class' => 'reviews-item-grade-item',
                                        'data' => [
                                            'role' => 'reviews.content.item.grade.item',
                                            'value' => $key,
                                            'index' => $iCounter++,
                                            'active' => 'false'
                                        ]
                                    ]) ?>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if (!empty($arData['DISPLAY']['VALUES'])) { ?>
                    <div class="reviews-item-properties">
                        <?php foreach ($arData['DISPLAY']['VALUES'] as $arValue) {

                            if (empty($arValue['VALUE']))
                                continue;

                        ?>
                            <div class="reviews-item-properties-item">
                                <div class="reviews-item-properties-item-name">
                                    <?= $arValue['NAME'] ?>
                                </div>
                                <div class="reviews-item-properties-item-value">
                                    <?= $arValue['VALUE'] ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                    <div class="reviews-item-properties">
                        <div class="reviews-item-properties-item">
                            <div class="reviews-item-properties-item-value">
                                <?= $arItem['PREVIEW_TEXT'] ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <div class="reviews-item">
            <div class="reviews-item-content reviews-item-empty">
                <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_ITEMS_EMPTY') ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>