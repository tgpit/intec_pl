<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 */

$this->setFrameMode(true);

$arViewParams = ArrayHelper::getValue($arResult, 'VIEW_PARAMETERS');
$bUserAccess = ArrayHelper::getValue($arResult, 'USER_HAVE_ACCESS');

$arVisual = $arResult['VISUAL'];
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="photo-section photo-section-default" id="<?= $sTemplateId ?>">
    <?php if ($bUserAccess && !empty($arResult['ITEMS'])) { ?>
        <?php if ($arParams['DISPLAY_TOP_PAGER'] && !empty($arResult['NAV_STRING'])) { ?>
            <div class="photo-section-navigation photo-section-navigation-top">
                <!-- pagination-container -->
                <?= $arResult['NAV_STRING'] ?>
                <!-- pagination-container -->
            </div>
        <?php } ?>
        <div class="photo-section-items photo-section-wrapper intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-a-h-start">
            <?php foreach($arResult["ITEMS"] as $arItem) {

                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"));

                $arImage = ArrayHelper::getValue($arItem, 'PICTURE');
                ?>
                <?php if (is_array($arImage)){ ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-grid-item' => [
                                $arViewParams['LINE_ELEMENT_COUNT'],
                                '1000-3',
                                '768-2',
                                '550-1'
                            ]
                        ],
                        'data' => [
                            'role' => 'item',
                            'src' => $arImage['SRC'],
                            'preview-src' => $arImage['SRC']
                        ],
                        'itemscope' => '',
                        'itemtype' => 'http://schema.org/ImageObject'
                    ]) ?>
                        <?= Html::beginTag('div', [
                            'class' => 'section-item',
                            'style' => 'padding-top:' . $arVisual['IMAGE']['ASPECT_RATIO'] . '%',
                            'id' => $this->GetEditAreaId($arItem['ID'])
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'section-item-wrapper'
                                ],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SRC'] : null
                                ],
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arImage['SRC'].'\')' : null
                                ]
                            ]) ?>
                                <span itemprop="name" style="display: none">
                                    <?= $arImage['ALT'] ?>
                                </span>
                                <span class="section-item-search-plus fa fa-search-plus"></span>
                            <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?php } ?>
        </div>
        <?php if ($arParams['DISPLAY_BOTTOM_PAGER'] && !empty($arResult['NAV_STRING'])) { ?>
            <div class="photo-section-navigation photo-section-navigation-bottom">
                <!-- pagination-container -->
                <?= $arResult['NAV_STRING'] ?>
                <!-- pagination-container -->
            </div>
        <?php } ?>
    <?php } ?>
    <?php if (count($arResult['ITEMS'])) { ?>
        <div class="btn-block">
            <a href="<?= $arParams['SECTION_TOP_URL'] ?>" class="list-sections intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-mod-round-3 intec-ui-size-2">
                <span class="intec-ui-part-icon">
                    <i class="far fa-angle-left"></i>
                </span>
                <span class="intec-ui-part-content">
                    <?= Loc::getMessage('CT_BPS_ELEMENT_RETURN_ALBUM') ?>
                </span>
            </a>
        </div>
    <?php } ?>
</div>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var gallery = root.find('.photo-section-wrapper');

        gallery.lightGallery({
            animateThumb: false,
            thumbnail: true,
            exThumbImage: 'data-preview-src'
        });
    }, {
        'name': '[Component] bitrix:photo (photogallery) > bitrix:photo.section (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>