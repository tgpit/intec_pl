<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

?>
<div class="basket-items-panel-buttons">
    <?= Html::beginTag('div', [
        'class' => [
            'basket-items-panel-button',
            'basket-items-panel-button-delete',
            'intec-ui' => [
                '',
                'control-button',
                'size-2',
                'mod-round-4'
            ]
        ],
        'data-role' => 'clear'
    ]) ?>
        <span class="basket-items-panel-button-icon intec-ui-part-icon">
            <?= FileHelper::getFileData(__DIR__.'/../svg/panel.button.delete.svg') ?>
        </span>
        <span class="basket-items-panel-button-content intec-ui-part-content">
            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_PANEL_BUTTONS_BUTTON_DELETE') ?>
        </span>
    <?= Html::endTag('div') ?>
</div>
