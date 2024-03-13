<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="intec-ui-m-t-15 intec-ui-m-b-15">
    <?= Html::beginTag('div', [
        'class' => [
            'intec-grid' => [
                '',
                'wrap',
                'a-h-end'
            ]
        ]
    ]) ?>
        <div class="intec-grid-item-auto">
            <?= Html::beginTag('a', [
                'class' => [
                    'sale-personal-order-detail-return',
                    'intec-grid' => [
                        '',
                        'nowrap',
                        'a-v-center',
                        'i-h-4'
                    ],
                    'intec-cl-text' => [
                        '',
                        'light-hover'
                    ]
                ],
                'href' => Html::encode($arResult['URL_TO_LIST'])
            ]) ?>
                <span class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-stroke">
                    <?= $arSvg['RETURN'] ?>
                </span>
                <span class="intec-grid-item-auto">
                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BUTTONS_RETURN') ?>
                </span>
            <?= Html::endTag('a') ?>
        </div>
    <?= Html::endTag('div') ?>
</div>
