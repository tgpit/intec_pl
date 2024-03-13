<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php $vRating = function (&$arData, &$arList) use (&$arVisual, &$arSvg) {

    $isMatch = false;

?>
    <div class="intec-grid-item-auto intec-grid-item-900-1">
        <div class="widget-item-rating intec-grid intec-grid-i-h-3">
            <?php foreach ($arList as $key => $value) { ?>
                <?= Html::tag('div', $arSvg['RATING'], [
                    'class' => [
                        'widget-item-rating-item',
                        'intec-grid-item-auto',
                        'intec-ui-picture'
                    ],
                    'title' => ArrayHelper::getValue(
                        $arList,
                        $arData['RATING']['VALUE']
                    ),
                    'data-active' => !$isMatch ? 'true' : 'false'
                ]) ?>
                <?php if ($key == $arData['RATING']['VALUE'])
                    $isMatch = true;
                ?>
            <?php } ?>
            <?/*php for ($countStar = 1; $countStar <= $arVisual['RATING']['MAX']; $countStar++) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid-item-auto' => true,
                        'widget-item-rating' => [
                            '' => true,
                            'active' => ($countStar <= $arData['RATING']['VALUE'])
                        ]
                    ], true)
                ]) ?>
                <i class="intec-ui-icon intec-ui-icon-star-1"></i>
                <?= Html::endTag('div') ?>
            <?php } */?>
        </div>
    </div>
<?php } ?>