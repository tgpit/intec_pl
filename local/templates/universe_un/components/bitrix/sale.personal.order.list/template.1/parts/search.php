<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var boolean $bSearchApply
 * @var array $arGet
 * @var array $arSvg
 */

?>

<div class="sale-personal-order-list-search<?= $bSearchApply ? ' intec-cl-border' : null ?>">
    <form action="" method="get" class="sale-personal-order-list-search-form" data-role="search">
        <?= ArrayHelper::keyExists('by', $arGet) ? '<input type="hidden" name="by" value="'.$arGet['by'].'">' : null ?>
        <?= ArrayHelper::keyExists('order', $arGet) ? '<input type="hidden" name="order" value="'.$arGet['order'].'">' : null ?>
        <input type="hidden" name="filter_history" value="Y">
        <input type="hidden" name="show_canceled" value="Y">
        <input type="hidden" name="show_all" value="Y">
        <?php if (isset($arGet['SECTION'])) { ?>
            <input type="hidden" name="SECTION" value="<?= $arGet['SECTION'] ?>">
        <?php } ?>
        <div class="intec-grid intec-grid-i-h-5 intec-grid-nowrap intec-grid-a-v-center">
            <div class="intec-grid-item">
                <?= Html::input('text', 'filter_id', $bSearchApply ? $arGet['filter_id'] : null, [
                    'class' => 'sale-personal-order-list-search-form-input',
                    'placeholder' => Loc::getMessage('C_SALE_PERSONAL_ORDER_LIST_TEMPLATE_1_TEMPLATE_SEARCH'),
                    'data' => [
                        'role' => 'filter.id'
                    ]
                ]) ?>
            </div>
            <div class="intec-grid-item-auto">
                <?= Html::tag('button', $arSvg['SEARCH'], [
                    'class' => 'sale-personal-order-list-search-form-button',
                    'type' => 'submit'
                ]) ?>
            </div>
            <?php if ($bSearchApply) { ?>
                <div class="intec-grid-item-auto">
                    <?= Html::tag('div', $arSvg['SEARCH_RESET'], [
                        'class' => 'sale-personal-order-list-search-form-button',
                        'data' => [
                            'role' => 'reset'
                        ]
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    </form>
</div>
