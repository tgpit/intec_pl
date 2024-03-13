<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arSvg
 */

?>
<div class="catalog-element-delivery-calculation">
    <div class="catalog-element-delivery-calculation-content">
        <?= Html::tag('div', $arSvg['DELIVERY_CALCULATION'], [
            'class' => [
                'catalog-element-delivery-calculation-icon',
                'catalog-element-delivery-calculation-part'
            ]
        ]) ?>
        <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_DELIVERY_CALCULATION'), [
            'class' => [
                'catalog-element-delivery-calculation-name',
                'catalog-element-delivery-calculation-part',
                'intec-cl-text-hover',
                'intec-cl-border-hover'
            ],
            'data-role' => 'deliveryCalculation'
        ]) ?>
    </div>
</div>