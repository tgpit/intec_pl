<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var string $sPicture
 * @var array $arVisual
 */

?>
<?php return function ($sPicture) use (&$arVisual) { ?>
    <?= Html::tag('div', null, [
        'class' => 'widget-item-picture-front',
        'style' => [
            'background-image' => 'url("' . ($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture) . '")'
        ],
        'data' => [
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
        ]
    ]) ?>
<?php } ?>
