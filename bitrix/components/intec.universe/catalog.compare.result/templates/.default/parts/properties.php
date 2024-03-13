<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var callable $vProperty
 */

$vProperty = include(__DIR__.'/property.php');

?>

<?php return function ($arItems, $arProperty, $position = 'left') use (&$vProperty) { ?>
    <div class="catalog-compare-result-property-values-wrapper">
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-compare-result-property-values',
                'intec-grid' => [
                    '',
                    'nowrap',
                    'a-h-start',
                    'a-v-center'
                ]
            ],
            'data' => [
                'type' => 'compare.content',
                'position' => $position
            ]
        ]) ?>
            <?php $iIndexItem = 0;

            foreach ($arItems as $arItem) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-compare-result-property-value-item',
                        'intec-grid-item' => [
                            '5',
                            '768-1'
                        ]
                    ],
                    'data' => [
                        'fixed' => 'false',
                        'index' => $iIndexItem,
                        'role' => 'slide'
                    ]
                ]) ?>
                    <?= $vProperty($arProperty, $arItem) ?>
                <?= Html::endTag('div') ?>
            <?php $iIndexItem++ ?>
            <?php } ?>
            <?php unset($arProperty, $iIndexItem) ?>
        <?= Html::endTag('div') ?>
    </div>
<?php } ?>