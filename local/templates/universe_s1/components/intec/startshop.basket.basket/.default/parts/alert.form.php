<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var bool $bOffer
 */

?>

<div class="startshop-basket-basket-button-delete-alert-layout" data-role="alert.basket.form">
    <div class="startshop-basket-basket-button-delete-alert">
        <?php include(__DIR__.'/../images/alert.form.close.icon.svg') ?>
        <div class="startshop-basket-basket-button-delete-alert-title" data-role="alert.form.title"></div>
        <div class="startshop-basket-basket-button-delete-alert-text" data-role="alert.form.text"></div>
        <div class="startshop-basket-basket-button-delete-alert-buttons intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item-auto">
                <?= Html::beginTag('div', [
                    'class' => [
                        'startshop-basket-basket-button-delete-alert-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-4'
                        ]
                    ],
                    'data' => [
                        'role' => 'alert.button.yes',
                        'basket-item-id' => ''
                    ]
                ]) ?>
                    <?= Loc::getMessage('SBB_DEFAULT_ALERT_BUTTON_YES') ?>
                <?= Html::endTag('div') ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="startshop-basket-basket-button-delete-alert-button intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-mod-round-4 intec-ui-scheme-current" data-role="alert.button.no">
                    <?= Loc::getMessage('SBB_DEFAULT_ALERT_BUTTON_NO') ?>
                </div>
            </div>
        </div>
    </div>
</div>