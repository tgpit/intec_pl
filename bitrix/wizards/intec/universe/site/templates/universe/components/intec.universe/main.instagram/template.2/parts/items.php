<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var CBitrixComponent $component
 */

?>
<?php $iCounter = 0; ?>
<?php foreach ($arResult['ITEMS'] as $arItem) {
    $iCounter++;
    $sAdaptation = null;

    if ($bAdditionally) {
        if ($iCounter <= 7 && $arVisual['ITEMS']['BIG'])
            continue;

        if ($arVisual['ITEMS']['FILL'])
            if ($iCounter > $arVisual['ITEMS']['MAX']['DESKTOP'])
                break;
    } else {
        if ($arVisual['ITEMS']['BIG'] && $iCounter <= 1)
            $sAdaptation = 'desktop-hide';

        if ($arVisual['ITEMS']['BIG'] && $iCounter >= 8)
            $sAdaptation = 'desktop-hide';

        if ($arVisual['ITEMS']['FILL']) {
            if (!$arVisual['ITEMS']['BIG'] && $iCounter > $arVisual['ITEMS']['MAX']['DESKTOP'])
                $sAdaptation = 'desktop-hide';

            if ($iCounter > $arVisual['ITEMS']['MAX']['LAPTOP'])
                $sAdaptation = $sAdaptation . ' laptop-hide';

            if ($iCounter > $arVisual['ITEMS']['MAX']['MOBILE'])
                $sAdaptation = $sAdaptation . ' mobile-hide';
        }

        if ($arVisual['ITEMS']['MORE']['SHOW'] && !$arVisual['ITEMS']['MORE']['VIEW']['MOBILE']) {
            if ($iCounter > 7)
                break;
        }
    }

    $sImage = null;
    $bVideo = $arItem['VIDEO']['IS'];
    $bVideo ? $sImage = $arItem['VIDEO']['IMAGES'] : $sImage = $arItem['IMAGES'];

    $sDescription = ArrayHelper::getValue($arItem, 'DESCRIPTION');
    $sLink = ArrayHelper::getValue($arItem, 'LINK');
    ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-item',
            $sAdaptation,
            'intec-grid' => [
                $bAdditionally || !$arVisual['ITEMS']['BIG'] ? 'item-5' : 'item-3',
                $arVisual['ITEMS']['BIG'] ? null : 'item-950-3',
                $bAdditionally ? null : 'item-750-2'
            ]
        ],
    ]) ?>
        <div class="widget-item-wrapper">
            <?= Html::beginTag('a', [
                'class' => [
                    'widget-item-image'
                ],
                'target' => $arVisual['ITEMS']['BLANK'] ? '_blank' : null,
                'href' => $sLink,
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                ],
                'style' => [
                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sImage.'\')' : null
                ]
            ]) ?>
                <?php if ($bVideo) { ?>
                    <i class="fas fa-play"></i>
                <?php } ?>
                <?php if ($arVisual['ITEMS']['DESCRIPTION']['SHOW']) { ?>
                    <div class="widget-item-fade scrollbar-inner">
                        <?php if ($arVisual['ITEMS']['DATE']['SHOW']) { ?>
                            <div class="widget-item-date">
                                <?= $arItem['DATE']['FORMATTED'] ?>
                            </div>
                        <?php } ?>
                        <div class="widget-item-description">
                            <?php if ($arVisual['ITEMS']['DESCRIPTION']['CUT']) { ?>
                                <?= TruncateText($sDescription, '200') ?>
                            <?php } else { ?>
                                <?= $sDescription ?>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            <?= Html::endTag('a') ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $bAdditionally = false ?>
