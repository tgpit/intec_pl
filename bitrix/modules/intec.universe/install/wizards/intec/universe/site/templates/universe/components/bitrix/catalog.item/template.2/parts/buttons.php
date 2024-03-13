<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arItem
 * @var bool $bOffers
 * @var CAllMain $APPLICATION
 * @var CBitrixBasketComponent $component
 */

?>
<?= Html::beginTag('div', [
    'class' => 'catalog-item-button-container',
    'data-entity' => 'buttons-block'
]) ?>
    <?php if (!$bOffers) { ?>
        <?php if ($arItem['CAN_BUY']) { ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-item-button',
                    'catalog-item-button-add',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'control-basket-button',
                        'scheme-current',
                        'mod-block'
                    ]
                ],
                'data' => [
                    'data-role' => 'item.button',
                    'basket-id' => $arItem['ID'],
                    'basket-action' => 'add',
                    'basket-state' => 'none'
                ]
            ]) ?>
                <span class="catalog-item-button-icon intec-ui-part-icon intec-ui-picture">
                    <?= FileHelper::getFileData(__DIR__.'/../svg/basket.button.add.svg') ?>
                </span>
                <span class="catalog-item-button-content intec-ui-part-content">
                    <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_2_TEMPLATE_BASKET_ADD') ?>
                </span>
                <span class="intec-ui-part-effect intec-ui-part-effect-bounce">
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-item-button',
                    'catalog-item-button-default',
                    'catalog-item-button-added',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'scheme-current',
                        'mod-block'
                    ]
                ],
                'data' => [
                    'basket-id' => $arItem['ID'],
                    'basket-state' => 'none'
                ]
            ]) ?>
                <span class="catalog-item-button-content intec-ui-part-content">
                    <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_2_TEMPLATE_BASKET_ADDED') ?>
                </span>
            <?= Html::endTag('div') ?>
        <?php } else { ?>
            <?php if ($arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && $arItem['CATALOG_SUBSCRIBE'] === 'Y') { ?>
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.product.subscribe',
                    '.default', [
                        'PRODUCT_ID' => $arItem['ID'],
                        'BUTTON_ID' => $arItem['ID'].'_catalog_item_subscribe',
                        'BUTTON_CLASS' => Html::cssClassFromArray([
                            'catalog-item-button',
                            'catalog-item-button-content',
                        ])
                    ],
                    $component,
                    ['HIDE_ICONS' => 'Y']
                ) ?>
            <?php } else { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-item-button',
                        'catalog-item-button-unavailable',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'state-disabled',
                            'mod-block'
                        ]
                    ]
                ]) ?>
                    <span class="catalog-item-button-content intec-ui-part-content">
                        <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_2_TEMPLATE_BASKET_UNAVAILABLE') ?>
                    </span>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?php } ?>
    <?php } else { ?>
        <?= Html::beginTag('a', [
            'class' => [
                'catalog-item-button',
                'intec-ui' => [
                    '',
                    'control-button',
                    'scheme-current',
                    'mod-block'
                ]
            ],
            'href' => $arItem['DETAIL_PAGE_URL']
        ]) ?>
            <span class="catalog-item-button-content intec-ui-part-content">
                <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_2_TEMPLATE_BASKET_DETAIL') ?>
            </span>
        <?= Html::endTag('a') ?>
    <?php } ?>
<?= Html::endTag('div') ?>
