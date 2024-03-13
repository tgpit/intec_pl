<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 */

$bFirstStories = true;

?>
<div class="widget-stories-popup">
    <div class="widget-popup-main-slider" data-role="main.slider" data-status="load">
        <?php foreach ($arResult['SECTIONS'] as $arSection) {
            $bActive = $arSection['ID'] == $arVisual['POPUP']['SELECTED'];
            $iCounter = count($arSection['ITEMS']);

            $sPicture = $arSection['PICTURE']['SRC'];

            if (empty($sPicture))
                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
            ?>
            <div class="widget-popup-sub-slider" data-status="<?= $bActive ? 'active': 'nonactive' ?>" data-role="sub.slider">
                <?php foreach ($arSection['ITEMS'] as $arItem) { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-popup-slider-item',
                            'intec-grid' => [
                                '',
                                'o-vertical',
                                'a-h-end'
                            ]
                        ],
                        'data' => [
                            'role' => 'sub.slider.item',
                            'status' => $bFirstStories ? 'enabled': 'disabled'
                        ],
                        'style' => 'background-image:url(' . $arItem['DATA']['PICTURE']['SRC'] . ')'
                    ])?>
                        <?php if ($arItem['DATA']['BUTTON']['SHOW']) { ?>
                            <a class="widget-popup-slider-item-button intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current" href="<?= $arItem['DATA']['BUTTON']['LINK'] ?>">
                                <?= $arItem['DATA']['BUTTON']['TEXT'] ?>
                            </a>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php $bFirstStories = false ?>
                <?php } ?>
                <div class="widget-popup-sub-slider-header">
                    <div class="widget-popup-header-load-bars intec-grid intec-grid-i-h-2" data-role="load.bars" data-pause="false">
                        <?php for ($i = 0; $i < $iCounter; $i++) { ?>
                            <div class="widget-popup-header-load-bar-wrapper intec-grid-item">
                                <div class="widget-popup-header-load-bar" data-role="load.bar" data-status="nonactive">
                                    <div class="widget-popup-header-load-bar-inner" data-role="load.bar.inner" style="animation-duration: <?= $arVisual['POPUP']['TIME']?>s"></div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="widget-popup-title-wrapper intec-grid intec-grid-a-v-center">
                        <div class="widget-popup-header-picture" style="background-image: url('<?= $sPicture ?>')"></div>
                        <div class="widget-popup-header-title">
                            <?= $arSection['NAME'] ?>
                        </div>
                    </div>
                </div>
                <div class="widget-popup-sub-slider-control intec-grid intec-grid-a-h-between">
                    <div class="widget-popup-sub-slider-control-field" data-role="sub.slider.control" data-action="prev"></div>
                    <div class="widget-popup-sub-slider-control-field" data-role="sub.slider.control" data-action="next"></div>
                </div>
            </div>
        <?php $bFirstStories = true ?>
        <?php } ?>
    </div>
</div>