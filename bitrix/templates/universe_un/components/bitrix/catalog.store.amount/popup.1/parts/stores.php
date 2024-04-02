<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 * @var bool $bOffers
 */

?>
<?php $vStores = function (&$arStore) use (&$arResult, &$arVisual) { ?>
    <div class="catalog-store-amount-item" data-store-id="<?= $arStore['ID'] ?>">
        <?= Html::tag('div', $arStore['TITLE'], [
            'class' => 'catalog-store-amount-name'
        ]) ?>
        <div class="intec-grid intec-grid-a-v-center intec-grid-i-4">
            <div class="intec-grid-item-auto">
                <?= Html::tag('div', null, [
                    'class' => 'catalog-store-amount-indicator',
                    'data' => [
                        'role' => 'store.state',
                        'store-state' => $arStore['AMOUNT_STATUS']
                    ]
                ]) ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="catalog-store-amount-quantity" data-store-state="<?= $arStore['AMOUNT_STATUS'] ?>">
                    <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                        'data-role' => 'store.quantity'
                    ]) ?>
                    <?php if (!$arVisual['MIN_AMOUNT']['USE']) { ?>
                        <?= Html::tag('span', !$arResult['IS_SKU'] ? ArrayHelper::getFirstValue($arResult['MEASURES']) : null, [
                            'data-role' => 'store.measure'
                        ]) ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php
    $vStores($arStore);
    unset($vStores);
?>