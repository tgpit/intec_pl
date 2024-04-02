<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arBlock
 */

$GLOBALS['arServicesSharesFilter'] = [
    'ID' => $arBlock['IBLOCK']['ELEMENTS']
];

?>
<div class="catalog-element-shares widget">
    <div class="catalog-element-shares-wrapper intec-content intec-content-visible">
        <div class="catalog-element-shares-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arBlock['HEADER']['VALUE'])) { ?>
                <div class="catalog-element-shares-header widget-header">
                    <?= Html::tag('div', $arBlock['HEADER']['VALUE'], [
                        'class' => [
                            'widget-title',
                            'align-'.$arBlock['HEADER']['POSITION']
                        ]
                    ]) ?>
                </div>
            <?php } ?>
            <div class="catalog-element-shares-content widget-content">
                <?php $APPLICATION->IncludeComponent(
                    'intec.universe:main.shares',
                    $arBlock['TEMPLATE'],
                    ArrayHelper::merge($arBlock['PARAMETERS'], [
                        'SETTINGS_USE' => 'N',
                        'LAZYLOAD_USE' => $arResult['LAZYLOAD']['USE'] ? 'Y' : 'N'
                    ]),
                    $component
                ) ?>
            </div>
        </div>
    </div>
</div>