<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function () use (&$arResult, &$arVisual) { ?>
    <?php if (empty($arResult['ADVANTAGES'])) return ?>
    <div class="widget-advantages">
        <div class="intec-grid intec-grid-wrap intec-grid-i-12">
            <?php foreach ($arResult['ADVANTAGES'] as $arAdvantage) {

                if ($arVisual['ADVANTAGES']['SVG'] && !empty($arAdvantage['SVG'])) {
                    $sPicture = $arAdvantage['SVG']['SRC'];
                } else {
                    $sPicture = $arAdvantage['PICTURE'];

                    if (!empty($sPicture)) {
                        $sPicture = CFile::ResizeImageGet($sPicture, [
                            'width' => 96,
                            'height' => 96
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                    }
                }

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid-item' => [
                            $arVisual['ADVANTAGES']['COLUMNS'] => true,
                            '1024-2' => $arVisual['ADVANTAGES']['COLUMNS'] >= 3,
                            '600-1' => true
                        ]
                    ], true)
                ]) ?>
                    <div class="intec-grid intec-grid-i-8">
                        <div class="intec-grid-item-auto">
                            <?php if ($arVisual['ADVANTAGES']['SVG'] && !empty($arAdvantage['SVG'])) { ?>
                                <?= Html::tag('div', FileHelper::getFileData('@root/'.$sPicture), [
                                    'class' => [
                                        Html::cssClassFromArray([
                                            'catalog-section-list-item-picture' => true,
                                            'intec-cl-svg' => true,
                                            'intec-image-effect' => true,
                                        ], true)
                                    ]
                                ]) ?>
                            <?php } else { ?>
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-advantages-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => 'url(\''.(
                                            $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                                        ).'\')'
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                        <div class="intec-grid-item">
                            <div class="widget-advantages-item-name">
                                <?= $arAdvantage['NAME'] ?>
                            </div>
                            <div class="widget-advantages-item-preview">
                                <?= $arAdvantage['PREVIEW'] ?>
                            </div>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
<?php } ?>