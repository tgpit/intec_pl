<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var $arVisual
 * @var string $sTag
 */

?>
<?php $vItems = function (&$arItems) use (&$arVisual, &$sTag, &$sTemplateId) { ?>
    <?php if (!empty($arItems)) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-items',
                'intec-grid',
                'intec-grid-wrap',
                'intec-grid-i-17'
            ],
            'data' => [
                'grid' => $arVisual['COLUMNS']
            ]
        ]) ?>
            <?php foreach ($arItems as $arItem) {

                $sId = $sTemplateId.'_'.$arItem['ID'];
                $sAreaId = $this->GetEditAreaId($sId);
                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                $sPicture = $arItem['PREVIEW_PICTURE'];

                if (empty($sPicture))
                    $sPicture = $arItem['DETAIL_PICTURE'];

                if (!empty($sPicture)) {
                    $sPicture = CFile::ResizeImageGet($sPicture, [
                        'width' => 700,
                        'height' => 700
                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                    if (!empty($sPicture))
                        $sPicture = $sPicture['src'];
                }

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-item' => true,
                        'intec-grid-item' => [
                            $arVisual['COLUMNS'] => true,
                            '1024-3' => $arVisual['COLUMNS'] > 3,
                            '768-2' => true,
                            '500-1' => true,
                        ]
                    ], true)
                ]) ?>
                    <?= Html::beginTag($sTag, [
                        'id' => $sAreaId,
                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                        'class' => [
                            'widget-item-wrapper',
                            'intec-cl-text-light-hover'
                        ]
                    ]) ?>
                        <?= Html::tag('div', '', [
                            'class' => 'widget-item-picture',
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                            ],
                            'style' => [
                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                            ]
                        ]) ?>
                        <div class="widget-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                    <?= Html::endTag($sTag) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php }; ?>