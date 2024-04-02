<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)die();

use intec\Core;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponent $this
 * @var CMain $APPLICATION
 */

if (!empty($arResult['DETAIL_PICTURE']))
    $sPicture = $arResult['DETAIL_PICTURE']['SRC'];

if (empty($sPicture) && !empty($arResult['PREVIEW_PICTURE']))
    $sPicture = $arResult['PREVIEW_PICTURE']['SRC'];

if (!empty($sPicture))
    $APPLICATION->SetPageProperty(
        'og:image',
        Core::$app->request->getHostInfo().$sPicture
    );

?>
<?php if ($arResult['VISUAL']['ANCHORS']['USE']) { ?>
    <?php ob_start() ?>
    <?= Html::beginTag('div', [
        'id' => 'news-news-1-detail-anchors',
        'class' => [
            'ns-bitrix',
            'c-news',
            'c-news-news-1',
            'news-detail-anchors'
        ],
        'data-position' => $arResult['VISUAL']['ANCHORS']['POSITION'] === 'fixed' ? 'fixed' : 'default'
    ]) ?>
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <div class="news-detail-anchors-content">
                    <div class="news-detail-anchors-items">
                        <div class="owl-carousel" data-role="news.anchors.slider">
                            <?php $iCount = 0 ?>
                            <?php foreach ($arResult['DATA']['ANCHORS']['ITEMS'] as $arAnchor) {

                                $iCount++;

                            ?>
                                <div class="news-detail-anchor">
                                    <?= Html::beginTag('a', [
                                        'class' => 'intec-cl-text-hover intec-cl-active',
                                        'href' => '#'.$arAnchor['ID'],
                                        'data-role' => 'news.anchors.item'
                                    ]) ?>
                                        <?php if ($arResult['VISUAL']['ANCHORS']['NUMBER']) { ?>
                                            <?= $iCount.'. '.$arAnchor['PRINT'] ?>
                                        <?php } else { ?>
                                            <?= $arAnchor['PRINT'] ?>
                                        <?php } ?>
                                    <?= Html::endTag('a') ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="news-detail-anchors-navigation" data-role="news.anchors.navigation"></div>
                </div>
            </div>
        </div>
    <?= Html::endTag('div') ?>
    <?php $view = ob_get_contents() ?>
    <?php ob_end_clean() ?>
<?php } ?>
<?php if ($arResult['VISUAL']['ANCHORS']['POSITION'] === 'fixed') {
        $APPLICATION->AddViewContent('template-header-fixed-after', $view);
        echo $APPLICATION->ShowViewContent('template-header-fixed-after');
    } else {
        $APPLICATION->AddViewContent('template-header-desktop-after', $view);
        echo $APPLICATION->ShowViewContent('template-header-desktop-after');
    }
?>
