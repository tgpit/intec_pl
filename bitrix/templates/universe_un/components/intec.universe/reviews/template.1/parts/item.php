<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var string $sTemplateId
 */

?>
<?php return function (&$arItem, $bUser = false) use (&$arVisual, &$sTemplateId) { ?>
    <?php if (!empty($arItem)) {

        $sId = $sTemplateId.'_'.$arItem['ID'];
        $sAreaId = $this->GetEditAreaId($sId);
        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

        $arData = $arItem['DATA'];

    ?>
        <div class="reviews-item" data-role="">
            <div class="reviews-item-content" id="<?= $sAreaId ?>">
                <?php if ($bUser) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'reviews-item-label',
                        'data-status' => $arItem['ACTIVE'] === 'Y' ? 'accepted' : 'on-hold'
                    ]) ?>
                        <?php if ($arItem['ACTIVE'] === 'Y') { ?>
                            <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_ITEM_USER_MODERATED') ?>
                        <?php } else { ?>
                            <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_ITEM_USER_MODERATING') ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <div class="intec-grid intec-grid-i-h-12 intec-grid-i-v-8 intec-grid-600-wrap">
                    <?php if ($arVisual['ITEMS']['PICTURE']['SHOW']) {

                        if (empty($arData['PICTURE']['VALUE']))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        else {
                            $sPicture = CFile::ResizeImageGet($arData['PICTURE']['VALUE'], [
                                'width' => 100,
                                'height' => 100
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                    ?>
                        <div class="intec-grid-item-auto intec-grid-item-600-1">
                            <?= Html::tag('div', null, [
                                'class' => [
                                    'reviews-item-avatar',
                                    'intec-image-effect'
                                ],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ],
                                'style' => [
                                    'background-image' => $arVisual['LAZYLOAD']['USE'] ? null : 'url(\''.$sPicture.'\')'
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item intec-grid-item-shrink-1 intec-grid-item-600-1">
                        <?php if ($arData['DATE']['SHOW']) { ?>
                            <div class="reviews-item-date">
                                <?= $arData['DATE']['VALUE'] ?>
                            </div>
                        <?php } ?>
                        <div class="reviews-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                        <?php if ($arData['DISPLAY']['SHOW']) { ?>
                            <div class="reviews-item-properties">
                                <?php foreach ($arData['DISPLAY']['VALUES'] as $arProperty) { ?>
                                    <div class="reviews-item-property">
                                        <div class="reviews-item-property-name">
                                            <?= $arProperty['NAME'] ?>
                                        </div>
                                        <div class="reviews-item-property-value">
                                            <?= $arProperty['VALUE'] ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } else if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                            <div class="reviews-item-properties">
                                <div class="reviews-item-property">
                                    <div class="reviews-item-property-value">
                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="reviews-item">
            <div class="reviews-item-content" data-empty="true">
                <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_ITEMS_EMPTY') ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>