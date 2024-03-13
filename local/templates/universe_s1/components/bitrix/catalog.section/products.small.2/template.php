<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$dData = include(__DIR__.'/parts/data.php');
$vCounter = include(__DIR__.'/parts/counter.php');
$vImage = include(__DIR__.'/parts/image.php');
$vPrice = include(__DIR__.'/parts/price.php');
$vPurchase = include(__DIR__.'/parts/purchase.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-2'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-section-items',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-h-start',
                        'a-v-stretch'
                    ]
                ]
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                ?>
                    <?= Html::beginTag('div', [
                        'id' => $sAreaId,
                        'class' => Html::cssClassFromArray([
                            'catalog-section-item' => true,
                            'intec-grid-item' => [
                                '' => true,
                                $arVisual['COLUMNS'] => true,
                                '950-3' => $arVisual['WIDE'] && $arVisual['COLUMNS'] >= 4,
                                '950-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] >= 3,
                                '720-2' => $arVisual['COLUMNS'] >= 3,
                                '450-1' => $arVisual['COLUMNS'] >= 2
                            ]
                        ], true),
                        'data' => [
                            'role' => 'item',
                            'data' => Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
                            'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                            'expanded' => 'false',
                            'action' => $arItem['ACTION'] !== 'none' ? 'true' : 'false',
                            'border' => $arVisual['BORDERS'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <div class="catalog-section-item-wrapper">
                            <div class="catalog-section-item-base">
                                <div class="catalog-section-item-picture-block">
                                    <?php $vImage($arItem) ?>
                                </div>
                                <?= Html::tag('a', $arItem['NAME'], [
                                    'href' => Html::decode($arItem['DETAIL_PAGE_URL']),
                                    'class' => [
                                        'catalog-section-item-name',
                                        'intec-cl-text-hover'
                                    ],
                                    'title' => $arItem['NAME'],
                                    'data-align' => $arVisual['NAME']['ALIGN']
                                ]) ?>
                                <?php if ($arItem['DATA']['PRICE']['SHOW']) { ?>
                                    <div class="catalog-section-item-price">
                                        <?php $vPrice(
                                            $arItem['MIN_PRICE'],
                                            $arItem['DATA']['PRICE']['RECALCULATION'],
                                            $arItem['DATA']['OFFER']
                                        ) ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if ($arItem['DATA']['ACTION'] !== 'none') { ?>
                                <div class="catalog-section-item-advanced">
                                    <div class="intec-grid intec-grid-a-v-center">
                                        <?php if ($arItem['DATA']['COUNTER']['SHOW']) { ?>
                                            <div class="catalog-section-item-counter intec-grid-item">
                                                <!--noindex-->
                                                <?php $vCounter() ?>
                                                <!--/noindex-->
                                            </div>
                                        <?php } ?>
                                        <div class="catalog-section-item-purchase intec-grid-item">
                                            <?php $vPurchase($arItem) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>
<?php unset($dData, $vCounter, $vImage, $vPrice, $vPurchase) ?>
