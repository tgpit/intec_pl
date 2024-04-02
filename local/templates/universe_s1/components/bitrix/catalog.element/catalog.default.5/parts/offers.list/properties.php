<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$arUsedProperty = [];

?>
<?php return function (&$arOffer) use (&$arResult, &$arVisual) { ?>
    <div class="catalog-element-offers-list-item-properties">
        <?php $iCounter = 0 ?>
        <?php foreach ($arResult['SKU_PROPS'] as $arProperty) {

            $sPropertyValue = ArrayHelper::getValue($arProperty, [
                'values',
                $arOffer['TREE']['PROP_'.$arProperty['id']],
                'name'
            ]);

            if (empty($sPropertyValue))
                continue;

            $arUsedProperty[] = $arProperty['id'];
            $iCounter++;

            if ($iCounter > $arVisual['OFFERS']['PROPERTIES']['COUNT'] && $arVisual['OFFERS']['PROPERTIES']['COUNT'] > 0)
                break;
        ?>
            <div class="catalog-element-offers-list-item-properties-item">
                <?= Html::tag('div', $arProperty['name'], [
                    'class' => [
                        'catalog-element-offers-list-item-properties-item-name',
                        'catalog-element-offers-list-item-properties-item-part'
                    ]
                ]) ?>
                <?= Html::tag('div', $sPropertyValue, [
                    'class' => [
                        'catalog-element-offers-list-item-properties-item-value',
                        'catalog-element-offers-list-item-properties-item-part'
                    ]
                ]) ?>
            </div>
        <?php } ?>
        <?php if ($arVisual['OFFERS']['PROPERTIES']['SHOW'] && !empty($arResult['FIELDS']['OFFERS'])) { ?>
            <?php foreach ($arResult['FIELDS']['OFFERS'][$arOffer['ID']] as $arProperty) {

                if (!empty($arUsedProperty) && ArrayHelper::isIN($arProperty['ID'], $arUsedProperty))
                    continue;

                $iCounter++;

                if ($iCounter > $arVisual['OFFERS']['PROPERTIES']['COUNT'] && $arVisual['OFFERS']['PROPERTIES']['COUNT'] > 0)
                    break;

                ?>
                <div class="catalog-element-offers-list-item-properties-item">
                    <?= Html::tag('div', $arProperty['NAME'], [
                        'class' => [
                            'catalog-element-offers-list-item-properties-item-name',
                            'catalog-element-offers-list-item-properties-item-part'
                        ]
                    ]) ?>
                    <?= Html::tag('div', $arProperty['VALUE'], [
                        'class' => [
                            'catalog-element-offers-list-item-properties-item-value',
                            'catalog-element-offers-list-item-properties-item-part'
                        ]
                    ]) ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php unset($iCounter) ?>
    </div>
<?php } ?>