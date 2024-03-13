<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 */

?>
<div class="intec-grid-item-1" data-print="false">
    <div class="basket-item-desktop-actions">
        <div class="intec-grid intec-grid-i-10 intec-grid-a-h-end">
            <?php if (ArrayHelper::isIn('DELAY', $arParams['COLUMNS_LIST'])) { ?>
                {{^DELAYED}}
                    <div class="intec-grid-item-auto">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'basket-item-desktop-action',
                                'intec-ui' => [
                                    '',
                                    'control-button'
                                ],
                                'intec-cl' => [
                                    'border-hover',
                                    'text-hover',
                                    'svg-path-stroke-hover'
                                ]
                            ],
                            'data' => [
                                'entity' => 'basket-item-add-delayed',
                                'type' => 'default'
                            ]
                        ]) ?>
                            <span class="intec-ui-part-icon intec-ui-picture">
                                <?= FileHelper::getFileData(__DIR__.'/../../svg/item.desktop.action.delay.svg') ?>
                            </span>
                            <span class="basket-item-desktop-action-text intec-ui-part-content">
                                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_DELAY') ?>
                            </span>
                        <?= Html::endTag('div') ?>
                    </div>
                {{/DELAYED}}
            <?php } ?>
            <?php if (ArrayHelper::isIn('DELETE', $arParams['COLUMNS_LIST'])) { ?>
                <div class="intec-grid-item-auto">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'basket-item-desktop-action',
                            'intec-ui' => [
                                '',
                                'control-button'
                            ]
                        ],
                        'data' => [
                            'type' => 'alert',
                            'entity' => 'basket-item-delete',
                            'role' => 'delete.button'
                        ]
                    ]) ?>
                        <span class="intec-ui-part-icon intec-ui-picture">
                            <?= FileHelper::getFileData(__DIR__.'/../../svg/item.desktop.action.delete.svg') ?>
                        </span>
                        <span class="basket-item-desktop-action-text intec-ui-part-content">
                            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_DELETE') ?>
                        </span>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
