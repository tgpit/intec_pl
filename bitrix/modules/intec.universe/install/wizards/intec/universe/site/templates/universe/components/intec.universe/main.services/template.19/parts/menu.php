<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function (&$arSections) { ?>
    <div class="widget-sections" data-role="services.tabs">
        <?php $bFirst = true ?>
        <?php foreach ($arSections as &$arSection) { ?>
            <?= Html::tag('div', $arSection['NAME'], [
                'class' => Html::cssClassFromArray([
                    'widget-section' => true,
                    'intec-cl-border' => $bFirst
                ], true),
                'data' => [
                    'role' => 'services.tabs.item',
                    'id' => $arSection['ID'],
                    'active' => $bFirst ? 'true' : 'false'
                ]
            ]) ?>
            <?php if ($bFirst) $bFirst = false ?>
        <?php } ?>
    </div>
<?php } ?>