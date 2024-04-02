<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var string $sTemplateId
 * @var array $arVisual
 * @var string $sTag
 */

?>
<?php return function (&$arItem) use (&$sTemplateId, &$arVisual, &$sTag) {

    $sId = $sTemplateId.'_'.$arItem['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

    $arData = $arItem['DATA'];

    $sPicture = $arData['PICTURE'];

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 80,
            'height' => 60
        ], BX_RESIZE_IMAGE_EXACT);

        if (!empty($sPicture))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <div class="widget-item" id="<?= $sAreaId ?>">
        <div class="intec-grid intec-grid-nowrap intec-grid-i-h-6 intec-grid-a-v-start">
            <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                <div class="intec-grid-item-auto">
                    <?= Html::beginTag($sTag, [
                        'class' => [
                            'widget-item-picture',
                            'intec-image-effect'
                        ],
                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                        'data-picture-size' => $arVisual['PICTURE']['SIZE']
                    ]) ?>
                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                            'alt' => $arItem['NAME'],
                            'title' => $arItem['NAME'],
                            'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                        ]) ?>
                    <?= Html::endTag($sTag) ?>
                </div>
            <?php } ?>
            <div class="intec-grid-item">
                <div class="widget-item-name">
                    <?= Html::tag($sTag, $arItem['NAME'], [
                        'class' => Html::cssClassFromArray([
                            'intec-cl-text-hover' => $sTag === 'a'
                        ], true),
                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                        'title' => $arItem['NAME']
                    ]) ?>
                </div>
                <?php if($arVisual['PRICE']['SHOW']) { ?>
                    <div class="widget-item-price-holder">
                        <?php if ($arData['PRICE']['OLD']['SHOW']) { ?>
                            <?= Html::tag('div', $arData['PRICE']['OLD']['PRINT'], [
                                'class' => 'widget-item-price-old',
                                'title' => $arData['PRICE']['OLD']['PRINT']
                            ]) ?>
                        <?php } ?>
                        <?= Html::tag('div', $arData['PRICE']['PRINT'], [
                            'class' => 'widget-item-price',
                            'title' => $arData['PRICE']['PRINT']
                        ]) ?>
                    </div>
                 <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>