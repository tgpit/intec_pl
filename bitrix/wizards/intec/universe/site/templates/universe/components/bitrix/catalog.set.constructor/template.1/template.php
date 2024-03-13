<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];

$arSvg = [
    'DYNAMIC' => [
        'REMOVE' => FileHelper::getFileData(__DIR__.'/svg/dynamic.remove.svg')
    ],
    'TOTAL' => [
        'ECONOMY' => FileHelper::getFileData(__DIR__.'/svg/total.economy.svg')
    ]
];

?>
<?= Html::beginTag('div', [
    'id' => 'set-'.$arResult['ELEMENT']['ID'],
    'class' => [
        'ns-bitrix',
        'c-catalog-set-constructor',
        'c-catalog-set-constructor-template-1'
    ]
]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="constructor-main">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-h-16 intec-grid-i-v-8">
                    <div class="intec-grid-item-3 intec-grid-item-1024-1">
                        <?php include(__DIR__.'/parts/main.item.php') ?>
                    </div>
                    <div class="intec-grid-item intec-grid-item-768-1">
                        <?php include(__DIR__.'/parts/main.dynamic.php') ?>
                    </div>
                    <div class="intec-grid-item-auto intec-grid-item-768-1">
                        <?php include(__DIR__.'/parts/main.total.php') ?>
                    </div>
                </div>
            </div>
            <div class="constructor-set">
                <?php include(__DIR__.'/parts/set.items.php') ?>
            </div>
        </div>
    </div>
    <?php $arJsParams = [
        'siteId' => SITE_ID,
        'iblockId' => $arParams['IBLOCK_ID'],
        'basketUrl' => $arParams['BASKET_URL'],
        'id' => '#set-'.$arResult['ELEMENT']['ID'],
        'currency' => $arResult['ELEMENT']['PRICE_CURRENCY'],
        'ratio' => $arResult['BASKET_QUANTITY'],
        'offersProperties' => $arParams['OFFERS_CART_PROPERTIES'],
        'ajaxPath' => $this->GetFolder().'/ajax.php'
    ] ?>
    <script type="text/javascript">
        BX.ready(function () {
            var root = $(<?= JavaScript::toObject($arJsParams['id']) ?>);
            var component = new SetConstructorCustom(<?=CUtil::PhpToJSObject($arJsParams, false, true, true)?>);

            BX.Currency.setCurrencyFormat(
                <?= CUtil::PhpToJSObject($arResult['ELEMENT']['PRICE_CURRENCY'], true, false) ?>,
                <?= CUtil::PhpToJSObject(CCurrencyLang::GetFormatDescription($arResult['ELEMENT']['PRICE_CURRENCY']), true, false) ?>
            );

            component.init();

            root.add = $('[data-set-action="add"]', root);
            root.remove = $('[data-set-action="remove"]', root);
            root.buy = $('[data-role="set.buy"]', root);

            root.add.on('click', function () {
                component.actionAdd(this.getAttribute('data-set-action-id'));
            });
            root.remove.on('click', function () {
                component.actionRemove(this.getAttribute('data-set-action-id'));
            });
            root.buy.on('click', function () {
                component.addToBasket();
            })
        });
    </script>
<?= Html::endTag('div') ?>