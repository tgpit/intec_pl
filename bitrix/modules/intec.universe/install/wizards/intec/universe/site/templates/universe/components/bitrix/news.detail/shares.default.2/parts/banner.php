<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>

<?= Html::beginTag('div', [
    'class' => 'news-detail-banner',
    'data' => [
        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
        'original' => $arVisual['LAZYLOAD']['USE'] ? $arResult['BLOCKS']['BANNER']['PICTURE'] : null,
        'theme' => $arVisual['BANNER']['THEME']
    ],
    'style' => [
        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arResult['BLOCKS']['BANNER']['PICTURE'].'\')' : null
    ],
    'title' => $arResult['NAME']
]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="news-detail-banner-discount">
                <div class="news-detail-banner-discount-overhead">
                    <?= $arResult['BLOCKS']['BANNER']['SUBTITLE'] ?>
                </div>
                <div class="news-detail-banner-discount-title">
                    <?= $arResult['BLOCKS']['BANNER']['TITLE'] ?>
                </div>
                <?php if ($arVisual['TIMER']['SHOW']) { ?>
                    <div class="news-detail-banner-discount-timer" data-role="timer"></div>
                <?php } ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>