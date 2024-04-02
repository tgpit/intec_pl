<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

?>
<div class="catalog-element-delivery-calculation-button-wrap">
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
        'data-role' => 'deliveryCalculation',
        'data-print' => 'false'
    ]) ?>
        <div class="intec-ui-part-icon">
            <i class="button-icon">
                <?= FileHelper::getFileData(__DIR__.'/../svg/delivery.icon.svg') ?>
            </i>
        </div>
        <div class="catalog-element-delivery-calculation-text intec-cl-text-hover intec-ui-part-content">
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_DELIVERY_CALCULATION') ?>
        </div>
    <?= Html::endTag('div') ?>
</div>