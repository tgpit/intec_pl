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
<?php return function(&$arItem, $bUser = false) use (&$sTemplateId, &$arVisual, &$arItemSvg, &$sAreaId, &$arResult) { ?>
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
                            <?= Loc::getMessage('C_REVIEWS_TEMPLATE_3_ITEM_USER_MODERATED') ?>
                        <?php } else { ?>
                            <?= Loc::getMessage('C_REVIEWS_TEMPLATE_3_ITEM_USER_MODERATING') ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <div class="intec-grid intec-grid-wrap intec-grid-i-8">
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
                        <?= Html::beginTag($arResult['ALLOW_LINK_REVIEWS'] ? 'a' : 'div', [
                            'class' => [
                                'reviews-item-picture-wrapper',
                                'intec-grid' => [
                                    'item-auto',
                                    'item-600-1'
                                ]
                            ],
                            'href' => $arResult['ALLOW_LINK_REVIEWS'] ? $arItem['DETAIL_PAGE_URL'] : null
                        ]) ?>
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
                        <?= Html::endTag($arResult['ALLOW_LINK_REVIEWS'] ? 'a' : 'div') ?>
                    <?php } ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-grid' => [
                                'item',
                                'item-600-1',
                                'item-shrink-1'
                            ]
                        ]
                    ]) ?>
                        <?= Html::beginTag($arResult['ALLOW_LINK_REVIEWS'] ? 'a' : 'div', [
                            'class' => [
                                'reviews-item-name',
                            ],
                            'href' => $arResult['ALLOW_LINK_REVIEWS'] ? $arItem['DETAIL_PAGE_URL'] : null
                        ]) ?>
                            <?= $arItem['NAME'] ?>
                        <?= Html::endTag($arResult['ALLOW_LINK_REVIEWS'] ? 'a' : 'div') ?>
                          <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                            <div class="reviews-item-text">
                                <?= $arItem['PREVIEW_TEXT'] ?>
                            </div>
                        <?php } else if (!empty($arItem['DETAIL_TEXT'])) { ?>
                            <div class="reviews-item-text">
                                <?= $arItem['DETAIL_TEXT'] ?>
                            </div>
                        <?php } ?>
                        <?php if ($arData['DATE']['SHOW']) { ?>
                            <div class="reviews-item-date">
                                <?= $arData['DATE']['VALUE'] ?>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="reviews-item">
            <div class="reviews-item-content reviews-item-empty">
                <?= Loc::getMessage('C_REVIEWS_TEMPLATE_3_ITEMS_EMPTY') ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>