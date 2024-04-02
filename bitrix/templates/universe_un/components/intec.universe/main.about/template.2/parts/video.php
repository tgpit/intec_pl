<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function () use (&$arResult, &$arVisual, &$arSvg) { ?>
    <?php if (!$arVisual['VIDEO']['SHOW']) return ?>
    <?= Html::tag('div', $arSvg['VIDEO'], [
        'class' => 'widget-video',
        'data' => [
            'play' => '',
            'src' => $arResult['VIDEO']
        ]
    ]) ?>
<?php } ?>