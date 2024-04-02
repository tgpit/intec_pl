<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

?>
<div class="catalog-element-delivery-calculation-button-wrap" data-print="false">
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-element-delivery-calculation-button',
            'intec-ui' => [
                '',
                'control-button',
                'scheme-current',
                'mod-link'
            ]
        ],
        'data-role' => 'deliveryCalculation'
    ]) ?>
        <div class="intec-ui-part-icon">
            <i>
                <?= FileHelper::getFileData(__DIR__.'/../svg/delivery.icon.svg')?>
            </i>
        </div>
        <div class="catalog-element-delivery-calculation-text intec-ui-part-content intec-cl-text-hover">
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PURCHASE_DELIVERY_CALCULATION') ?>
        </div>
    <?= Html::endTag('div') ?>
</div>