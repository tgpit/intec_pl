<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arOriginal = $arResult['ORIGINAL_PARAMETERS'];

$bSlider = $arVisual['SLIDER']['USE'] && count($arResult['ITEMS']) > $arVisual['COLUMNS'];

$vItem = include(__DIR__.'/parts/item.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-6'
    ],
    'data' => [
        'grid' => $arVisual['COLUMNS'],
        'slider' => $bSlider ? 'true' : 'false'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="catalog-section-content">
                <?php if ($bSlider) { ?>
                    <div class="catalog-section-items owl-carousel" data-role="slider">
                        <?php foreach ($arResult['ITEMS'] as $arItem)
                            $vItem($arItem);
                        ?>
                    </div>
                    <?= Html::tag('div', null, [
                        'class' => 'catalog-section-navigation',
                        'data' => [
                            'role' => 'navigation',
                            'view' => $arVisual['SLIDER']['NAV']['VIEW']
                        ]
                    ]) ?>
                <?php } else { ?>
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch catalog-section-items">
                        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-section-item-container' => true,
                                    'intec-grid-item' => [
                                        $arVisual['COLUMNS'] => true,
                                        '1024-3' => $arVisual['COLUMNS'] >= 4,
                                        '768-2' => true,
                                        '500-1' => true
                                    ]
                                ], true)
                            ]) ?>
                                <?php $vItem($arItem) ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if ($bSlider)
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>
