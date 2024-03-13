<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var bool $bDesktop
 */

?>
<div class="basket-items-panel" data-print="false">
    <?= Html::beginTag('div', [
        'class' => [
            'intec-grid' => [
                '',
                'wrap',
                'a-h-end',
                'a-h-650-center',
                'a-v-center',
                'i-h-16',
                'i-v-8'
            ]
        ]
    ]) ?>
        <?php if ($arParams['SHOW_FILTER'] === 'Y') {
            include(__DIR__.'/panel.filter.php');
        } ?>
        <div class="intec-grid-item-auto">
            <?php include(__DIR__.'/panel.buttons.php') ?>
        </div>
    <?= Html::endTag('div') ?>
</div>
