<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function () use (&$arResult, &$arVisual) { ?>
    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
        <?= Html::tag('div', null, [
            'class' => 'widget-picture',
            'data' => [
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true': 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $arResult['PICTURE']['SRC'] : null
            ],
            'style' => [
                'background-size' => $arVisual['PICTURE']['SIZE'],
                'background-position' => $arVisual['PICTURE']['POSITION']['VERTICAL'].' '.$arVisual['PICTURE']['POSITION']['HORIZONTAL'],
                'background-image' => 'url(\''.(
                    $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arResult['PICTURE']['SRC']
                    ).'\')'
            ]
        ]) ?>
    <?php } ?>
<?php } ?>