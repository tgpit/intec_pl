<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<div class="intec-ui intec-ui-control-numeric intec-ui-view-2 intec-ui-scheme-current intec-ui-size-4" data-role="counter">
    <?= Html::tag('a', '-', [
        'class' => 'intec-ui-part-decrement',
        'href' => 'javascript:void(0)',
        'data-type' => 'button',
        'data-action' => 'decrement'
    ]) ?>
    <?= Html::input('text', null, 0, [
        'data-type' => 'input',
        'class' => 'intec-ui-part-input'
    ]) ?>
    <div class="intec-ui-part-increment-wrapper">
        <?= Html::tag('a', '+', [
            'class' => 'intec-ui-part-increment',
            'href' => 'javascript:void(0)',
            'data-type' => 'button',
            'data-action' => 'increment'
        ]) ?>
        <div class="catalog-element-counter-max-message" data-role="max-message">
            <div class="catalog-element-counter-max-message-close" data-role="max-message-close">
                &times;
            </div>
            <?= Loc::getMessage('C_CATALOG_ELEMENT_QUICK_VIEW_1_MAX_MESSAGE') ?>
        </div>
    </div>
</div>