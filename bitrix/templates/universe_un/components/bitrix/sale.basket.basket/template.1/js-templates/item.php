<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 */

$bPicture = ArrayHelper::isIn('PREVIEW_PICTURE', $arParams['COLUMNS_LIST']);
$bPriceApart = ArrayHelper::isIn('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$bTotal = ArrayHelper::isIn('SUM', $arParams['COLUMNS_LIST']);
$bAction = ArrayHelper::isIn('DELETE', $arParams['COLUMNS_LIST']) || ArrayHelper::isIn('DELAY', $arParams['COLUMNS_LIST']);

?>
<script id="basket-item-template" type="text/html">
        <?= Html::beginTag('div', [
            'id' => 'basket-item-{{ID}}',
            'class' => 'basket-item',
            'data' => [
                'entity' => 'basket-item',
                'id' => '{{ID}}',
                'role' => 'item',
                'basket-id' => '{{PRODUCT_ID}}',
                'available' => '{{CAN_BUY}}',
                'data' => '{{QUICK_VIEW_DATA}}'
            ]
        ]) ?>
		{{#SHOW_RESTORE}}
			<?php include(__DIR__.'/item/restore.php') ?>
		{{/SHOW_RESTORE}}
		{{^SHOW_RESTORE}}
            <?= Html::beginTag('div', [
                'class' => [
                    'basket-item-wrapper',
                    'intec-grid' => [
                        '',
                        'a-v-stretch',
                        '650-wrap'
                    ]
                ]
            ]) ?>
                <div class="intec-grid-item intec-grid-item-650-1">
                    <div class="basket-item-content">
                        <div class="intec-grid intec-grid-650-wrap intec-grid-a-h-between">
                            <div class="intec-grid-item-3 intec-grid-item-800-2 intec-grid-item-650-1">
                                <div class="basket-item-main">
                                    <div class="intec-grid intec-grid-425-wrap intec-grid-800-wrap intec-grid-a-h-between">
                                        <?php if ($bPicture)
                                            include(__DIR__.'/item/image.php');
                                        ?>
                                        <div class="basket-item-info-wrap intec-grid-item intec-grid-item-shrink-max intec-grid-item-400-1 intec-grid-item-650 intec-grid-item-800-1">
                                            <div class="basket-item-info">
                                                <?php include(__DIR__.'/item/name.php') ?>
                                                <?php include(__DIR__.'/item/alert.unavailable.php') ?>
                                                <?php include(__DIR__.'/item/alert.delayed.php') ?>
                                                <?php include(__DIR__.'/item/alert.warnings.php') ?>
                                                <?php if (!empty($arParams['PRODUCT_BLOCKS_ORDER'])) { ?>
                                                    <div class="basket-item-properties">
                                                        <?php foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $sBlock) {
                                                            if ($sBlock === 'sku')
                                                                include(__DIR__.'/item/properties.offers.php');
                                                            else if ($sBlock === 'props')
                                                                include(__DIR__.'/item/properties.basket.php');
                                                            else if ($sBlock === 'columns')
                                                                include(__DIR__.'/item/properties.product.php');
                                                        } ?>
                                                        <?php unset($sBlock) ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="basket-item-control-wrap intec-grid-item-auto intec-grid intec-grid-wrap" data-print="false">
                                            <div class="intec-grid-item-1 intec-grid-item-400-auto basket-item-action" data-entity="basket-item-delete" data-item-action="delete">
                                                <svg width="18" height="22" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.9341 8.07083C11.6589 8.07083 11.4359 8.29385 11.4359 8.56902V17.9848C11.4359 18.2598 11.6589 18.483 11.9341 18.483C12.2093 18.483 12.4323 18.2598 12.4323 17.9848V8.56902C12.4323 8.29385 12.2093 8.07083 11.9341 8.07083Z" fill="#404040"/>
                                                    <path d="M6.05581 8.07083C5.78064 8.07083 5.55762 8.29385 5.55762 8.56902V17.9848C5.55762 18.2598 5.78064 18.483 6.05581 18.483C6.33098 18.483 6.554 18.2598 6.554 17.9848V8.56902C6.554 8.29385 6.33098 8.07083 6.05581 8.07083Z" fill="#404040"/>
                                                    <path d="M1.77138 6.69674V18.9711C1.77138 19.6966 2.03741 20.3779 2.50212 20.8667C2.9647 21.3569 3.60845 21.6352 4.28218 21.6364H13.7081C14.382 21.6352 15.0257 21.3569 15.4881 20.8667C15.9528 20.3779 16.2189 19.6966 16.2189 18.9711V6.69674C17.1427 6.45154 17.7413 5.55908 17.6177 4.61116C17.4939 3.66344 16.6865 2.95449 15.7306 2.95429H13.1799V2.33156C13.1828 1.80788 12.9758 1.30502 12.605 0.935073C12.2343 0.565324 11.7307 0.359432 11.207 0.363713H6.78324C6.25956 0.359432 5.75592 0.565324 5.3852 0.935073C5.01448 1.30502 4.80742 1.80788 4.81034 2.33156V2.95429H2.25965C1.30375 2.95449 0.496331 3.66344 0.372562 4.61116C0.248988 5.55908 0.847593 6.45154 1.77138 6.69674ZM13.7081 20.64H4.28218C3.43039 20.64 2.76776 19.9083 2.76776 18.9711V6.74053H15.2225V18.9711C15.2225 19.9083 14.5599 20.64 13.7081 20.64ZM5.80671 2.33156C5.8034 2.07215 5.90538 1.82247 6.08947 1.63935C6.27338 1.45623 6.52364 1.35561 6.78324 1.36009H11.207C11.4666 1.35561 11.7169 1.45623 11.9008 1.63935C12.0849 1.82228 12.1868 2.07215 12.1835 2.33156V2.95429H5.80671V2.33156ZM2.25965 3.95067H15.7306C16.2259 3.95067 16.6273 4.35214 16.6273 4.84741C16.6273 5.34268 16.2259 5.74415 15.7306 5.74415H2.25965C1.76438 5.74415 1.36291 5.34268 1.36291 4.84741C1.36291 4.35214 1.76438 3.95067 2.25965 3.95067Z" fill="#404040"/>
                                                    <path d="M8.99501 8.07083C8.71984 8.07083 8.49683 8.29385 8.49683 8.56902V17.9848C8.49683 18.2598 8.71984 18.483 8.99501 18.483C9.27018 18.483 9.49319 18.2598 9.49319 17.9848V8.56902C9.49319 8.29385 9.27018 8.07083 8.99501 8.07083Z" fill="#404040"/>
                                                </svg>
                                            </div>
                                            {{^DELAYED}}
                                                <div class="intec-grid-item-1 intec-grid-item-400-auto basket-item-action basket-item-add-delayed basket-item-delayed-button" data-entity="basket-item-add-delayed" data-item-action="delay">
                                                    <svg width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.0083 1.33334C18.4425 1.33334 20.75 4.56168 20.75 7.57334C20.75 13.6725 11.1733 18.6667 11 18.6667C10.8267 18.6667 1.25 13.6725 1.25 7.57334C1.25 4.56168 3.5575 1.33334 6.99167 1.33334C8.96333 1.33334 10.2525 2.31918 11 3.18584C11.7475 2.31918 13.0367 1.33334 15.0083 1.33334Z" stroke="#404040" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            {{/DELAYED}}
                                            {{#DELAYED}}
                                                <div class="intec-grid-item-1 intec-grid-item-400-auto basket-item-action basket-item-remove-delayed" data-entity="basket-item-remove-delayed">
                                                    <svg class="intec-cl-background" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M15.0083 1.33334C18.4425 1.33334 20.75 4.56168 20.75 7.57334C20.75 13.6725 11.1733 18.6667 11 18.6667C10.8267 18.6667 1.25 13.6725 1.25 7.57334C1.25 4.56168 3.5575 1.33334 6.99167 1.33334C8.96333 1.33334 10.2525 2.31918 11 3.18584C11.7475 2.31918 13.0367 1.33334 15.0083 1.33334Z" stroke="#404040" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </div>
                                            {{/DELAYED}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-grid-item' => [
                                        '2',
                                        '1200-4',
                                        '1000-3',
                                        '800-2',
                                        '650-1'
                                    ]
                                ]
                            ]) ?>
                                <div class="basket-item-additional">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'intec-grid' => [
                                                '',
                                                'i-10',
                                                'wrap',
                                                'a-v-center',
                                                '1200-wrap',
                                                '375-wrap',
                                                'a-h-between'
                                            ]
                                        ]
                                    ]) ?>
                                        <?php if ($bPriceApart)
                                            include(__DIR__.'/item/price.apart.php');
                                        ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'basket-item-quantity-wrap',
                                                'intec-grid-item' => [
                                                    'auto',
                                                    '650-auto',
                                                    '1200-1',
                                                ]
                                            ],
                                            'data-print' => 'false'
                                        ]) ?>
                                            <div class="basket-item-quantity-wrapper">
                                                <?php include(__DIR__.'/item/counter.php') ?>
                                                <?php if (!$bPriceApart) {
                                                    include(__DIR__.'/item/price.along.php');
                                                } ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                        <?php if ($bTotal)
                                            include(__DIR__.'/item/price.total.php');
                                        ?>
                                        <?php if ($bAction)
                                            include(__DIR__.'/item/actions.php')
                                        ?>
                                    <?= Html::endTag('div') ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
		{{/SHOW_RESTORE}}
	<?= Html::endTag('div') ?>
</script>
<?php unset($bPicture, $bPriceApart, $bTotal, $bAction) ?>