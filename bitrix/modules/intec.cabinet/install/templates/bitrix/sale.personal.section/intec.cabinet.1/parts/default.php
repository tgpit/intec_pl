<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arItem
 * @var array $arItems
 */

?>

<div class="sale-personal-section-sublinks-wrap" data-role="links">
    <div class="sale-personal-section-sublinks" data-role="items">
        <?php foreach ($arItems as $arItem) { ?>
            <div class="sale-personal-section-sublink-item" data-role="item">
                <?= Html::tag('a', Html::encode($arItem['NAME']), [
                    'class' => Html::cssClassFromArray([
                        'sale-personal-section-sublink' => true,
                        'intec-cl' => [
                            'text' => $arItem['ACTIVE'],
                            'text-light-hover' => true
                        ]
                    ], true),
                    'href' => Html::encode($arItem['PATH'])
                ]); ?>
            </div>
        <?php } ?>
    </div>
</div>