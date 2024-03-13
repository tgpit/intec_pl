<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 */

$arSizes = JavaScript::toObject([
    'component' => 'intec.universe:main.widget',
    'template' => 'catalog.sizes.1',
    'parameters' => [
        'PATH' => $arResult['SIZES']['PATH']
    ],
    'settings' => [
        'title' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SIZES_NAME'),
        'parameters' => [
            'width' => null
        ]
    ]
]);

?>
<div class="catalog-element-sizes">
    <div class="intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-4">
        <div class="intec-grid-item-auto">
            <div class="catalog-element-sizes-icon">
                <?= $arSvg['SIZES'] ?>
            </div>
        </div>
        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
            <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SIZES_NAME'), [
                'class' => [
                    'catalog-element-sizes-name',
                    'intec-cl-text-hover',
                    'intec-cl-border-hover'
                ],
                'onclick' => 'template.api.components.show('.$arSizes.');'
            ]) ?>
        </div>
    </div>
</div>
<?php unset($arSizes) ?>