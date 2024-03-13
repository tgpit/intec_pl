<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$sId = $sTemplateId.'_'.$arItem['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['DELETE_LINK']);

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-product-1',
    ],
    'data' => [
        'action' => $arResult['ACTIONS']['ACTION']
    ]
]) ?>
    <div class="catalog-element-content intec-grid intec-grid-a-v-stretch intec-grid-768-wrap" id="<?= $sAreaId ?>">
        <div class="catalog-element-gallery-container intec-ui-align intec-grid-item-2 intec-grid-item-768-1">
            <?php include(__DIR__.'/parts/gallery.php') ?>
        </div>
        <div class="intec-grid-item-2 intec-grid-item-768-1">
            <div class="catalog-element-main intec-grid intec-grid-wrap intec-grid-o-vertical intec-grid-a-h-between">
                <div class="catalog-element-main-content intec-grid-item-auto">
                    <?php if ($arVisual['TIMER']['SHOW']) { ?>
                        <?= Html::tag('div', null, [
                            'class' => 'catalog-element-part',
                            'data' => [
                                'role' => 'product.timer',
                                'indent' => 'two'
                            ],
                            'style' => 'display: none'
                        ]) ?>
                    <?php } ?>
                    <div class="catalog-element-part" data-indent="one" data-overflow="hidden">
                        <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'span', $arResult['NAME'], [
                            'class' => Html::cssClassFromArray([
                                'catalog-element-name' => true,
                                'intec-cl-text-hover' => $arVisual['LINK']['USE']
                            ], true),
                            'href' => $arVisual['LINK']['USE'] ? $arResult['DETAIL_PAGE_URL'] : null,
                            'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                        ]) ?>
                    </div>
                    <?php if ($arResult['ACTIONS']['DELAY']['USE'] || $arResult['ACTIONS']['COMPARE']['USE'] || $arVisual['VOTE']['USE']) { ?>
                        <div class="catalog-element-part" data-indent="one" data-overflow="hidden">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-16 intec-grid-i-v-8">
                                <?php if ($arResult['ACTIONS']['DELAY']['USE'] || $arResult['ACTIONS']['COMPARE']['USE']) { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-4">
                                            <?php if ($arResult['ACTIONS']['DELAY']['USE']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-500-1">
                                                    <?php include(__DIR__.'/parts/buttons/delay.php') ?>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arResult['ACTIONS']['COMPARE']['USE']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-500-1">
                                                    <?php include(__DIR__.'/parts/buttons/compare.php') ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['VOTE']['USE']) { ?>
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <?php include(__DIR__.'/parts/vote.php') ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['QUANTITY']['SHOW'] || $arResult['DATA']['ARTICLE']['SHOW']) { ?>
                        <div class="catalog-element-part" data-indent="one-half" data-overflow="hidden">
                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                                <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                                    <div class=" intec-grid-item-auto">
                                        <?php include(__DIR__.'/parts/quantity.php') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['DATA']['ARTICLE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?php include(__DIR__.'/parts/article.php') ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="catalog-element-part" data-indent="one-half">
                        <?php include(__DIR__.'/parts/price.php') ?>
                    </div>
                    <?php if ($arResult['ACTIONS']['ACTION'] !== 'none') { ?>
                        <div class="catalog-element-part catalog-element-part-mobile-bottom" data-indent="one-half" data-overflow="hidden">
                            <?php if ($arResult['ACTIONS']['ACTION'] === 'buy') { ?>
                                <div class=" intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-4 intec-grid-item-500-i-0">
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <div class="intec-grid intec-grid-a-v-center">
                                            <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-500-2">
                                                    <?php include(__DIR__.'/parts/buttons/counter.php') ?>
                                                </div>
                                            <?php } ?>
                                            <div class="intec-grid-item-auto intec-grid-item-500">
                                                <?php include(__DIR__ . '/parts/buttons/buy.php') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($arVisual['ORDER_FAST']['USE']) { ?>
                                        <div class="intec-grid-item-auto intec-grid-item-500-1">
                                            <?php include(__DIR__.'/parts/buttons/order.fast.php') ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else if ($arResult['ACTIONS']['ACTION'] === 'order') { ?>
                                <div class="intec-grid intec-grid-wrap -intec-grid-a-v-center intec-grid-i-4">
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <div class=" intec-grid intec-grid-wrap intec-grid-a-v-center">
                                            <div class="intec-grid-item-auto intec-grid-item-500">
                                                <?php include(__DIR__ . '/parts/buttons/order.php') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else if ($arResult['ACTIONS']['ACTION'] === 'subscribe') { ?>
                                <div class="intec-grid intec-grid-wrap -intec-grid-a-v-center intec-grid-i-4">
                                    <div class="intec-grid-item-auto intec-grid-item-500-1">
                                        <div class=" intec-grid intec-grid-wrap intec-grid-a-v-center">
                                            <div class="intec-grid-item-auto intec-grid-item-500">
                                                <?php include(__DIR__ . '/parts/buttons/subscribe.php') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if ($arVisual['LINK']['USE']) { ?>
                    <div class="catalog-element-main-detail intec-grid-item-auto">
                        <?php include(__DIR__.'/parts/buttons/detail.php') ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>