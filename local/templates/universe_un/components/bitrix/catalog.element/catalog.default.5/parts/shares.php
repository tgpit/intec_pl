<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 */

?>
<div class="catalog-element-shares">
    <div class="intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-4">
        <div class="intec-grid-item-auto">
            <div class="catalog-element-shares-icon intec-cl-svg">
                <?= $arSvg['SHARES'] ?>
            </div>
        </div>
        <div class="catalog-element-shares-items intec-grid-item-auto intec-grid-item-shrink-1">
            <?php if (!empty($arResult['SHARES']['HEADER'])) {

                if (empty($arResult['SHARES']['HEADER']))
                    $arResult['SHARES']['HEADER'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SHARES_HEADER_DEFAULT');

            ?>
                <div class="catalog-element-shares-items-header">
                    <?= $arResult['SHARES']['HEADER'] ?>
                </div>
            <?php } ?>
            <?php foreach ($arResult['SHARES']['ITEMS'] as $arSharesItem) { ?>
                <?= Html::tag('div', $arSharesItem['NAME'], [
                    'class' => [
                        'catalog-element-shares-name',
                        'intec' => [
                            'cl-text-hover',
                            'cl-border-hover'
                        ],
                    ],
                    'onclick' => 'template.api.components.show('.JavaScript::toObject([
                        'component' => 'intec.universe:main.widget',
                        'template' => 'catalog.shares.1',
                        'parameters' => ArrayHelper::merge($arResult['SHARES']['PARAMETERS'], [
                            'ELEMENT_ID' => $arSharesItem['ID']
                        ]),
                        'settings' => [
                            'parameters' => [
                                'width' => 580,
                                'max-height' =>  680
                            ]
                        ]
                    ]).');'
                ]) ?>
            <?php } ?>
            <?php unset($arSharesItem) ?>
        </div>
    </div>
</div>