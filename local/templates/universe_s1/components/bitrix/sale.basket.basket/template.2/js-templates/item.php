<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 */

$bPicture = ArrayHelper::isIn('PREVIEW_PICTURE', $arParams['COLUMNS_LIST']);
$bTotal = ArrayHelper::isIn('SUM', $arParams['COLUMNS_LIST']);
$bAction = ArrayHelper::isIn('DELETE', $arParams['COLUMNS_LIST']);

?>
<script id="basket-item-template" type="text/html">
    <?= Html::beginTag('div', [
        'id' => 'basket-item-{{ID}}',
        'class' => 'basket-item',
        'data' => [
            'entity' => 'basket-item',
            'id' => '{{ID}}',
            'available' => '{{CAN_BUY}}',
            'basket-id' => '{{PRODUCT_ID}}',
            'role' => 'item',
            'data' => '{{QUICK_VIEW_DATA}}'
        ]
    ]) ?>
		{{#SHOW_RESTORE}}
			<?php include(__DIR__.'/item/restore.php') ?>
		{{/SHOW_RESTORE}}
		{{^SHOW_RESTORE}}
            <div class="basket-item-wrapper intec-grid intec-grid-650-wrap">
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-content',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-center'
                        ],
                        'intec-grid-item'
                    ]
                ]) ?>
                    <div class="basket-item-header intec-grid-item-auto intec-grid intec-grid-a-v-center">
                        <?php if ($bPicture) { ?>
                            <div class="basket-item-image-wrap intec-grid-item-auto">
                                <?php include(__DIR__.'/item/image.php') ?>
                            </div>
                        <?php } ?>
                        <div class="basket-item-name-wrap intec-grid-item-auto intec-grid-item-shrink-1">
                            <?php include(__DIR__.'/item/name.php') ?>
                        </div>
                    </div>
                    <div class="intec-grid-item"></div>
                    <div class="basket-item-price-block intec-grid-item-auto intec-grid-item-768-1 intec-grid intec-grid-768-wrap intec-grid-a-v-center">
                        {{^MEASURE_RATIO_SINGLE}}
                        <div class="basket-item-multiplicity">
                            <?= Html::tag('span', Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_PRICE_MULTIPLICITY')) ?>
                            <?= Html::tag('span', '{{MEASURE_RATIO}}', ['class' => 'intec-cl-text']) ?>
                            <?= Html::tag('span', '{{MEASURE_TEXT}}', ['class' => 'intec-cl-text']) ?>
                        </div>
                        {{/MEASURE_RATIO_SINGLE}}
                        <?php include(__DIR__.'/item/counter.php') ?>
                        <div class="basket-item-single-price">
                            <?= Html::tag('span', '{{{PRICE_FORMATED}}}', ['id' => 'basket-item-price-{{ID}}']) ?>
                            <?= Html::tag('span', Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_PRICE_DELIMITER')) ?>
                            <?= Html::tag('span', '{{MEASURE_TEXT}}') ?>
                        </div>
                        <?php if ($bTotal) {
                            include(__DIR__.'/item/price.total.php');
                        } ?>
                        <div class="basket-item-price-separator intec-grid-item intec-grid-item-768-1"></div>
                    </div>
                    <div class="basket-item-alerts intec-grid-item-1">
                        <?php include(__DIR__.'/item/alert.unavailable.php') ?>
                        <?php include(__DIR__.'/item/alert.delayed.php') ?>
                        <?php include(__DIR__.'/item/alert.warnings.php') ?>
                        <?php include(__DIR__.'/item/properties.offers.similar.php') ?>
                    </div>
                    <?php if (!empty($arParams['PRODUCT_BLOCKS_ORDER'])) { ?>
                        <?php foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $sBlock) {
                            if ($sBlock === 'sku')
                                include(__DIR__.'/item/properties.offers.php');
                            else if ($sBlock === 'props')
                                include(__DIR__.'/item/properties.basket.php');
                            else if ($sBlock === 'columns')
                                include(__DIR__.'/item/properties.product.php');
                        } ?>
                        <?php unset($sBlock) ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <?php if ($bAction) {
                    include(__DIR__.'/item/actions.php');
                } ?>
            </div>
		{{/SHOW_RESTORE}}
	<?= Html::endTag('div') ?>
</script>
<?php unset($bPicture, $bTotal, $bAction) ?>