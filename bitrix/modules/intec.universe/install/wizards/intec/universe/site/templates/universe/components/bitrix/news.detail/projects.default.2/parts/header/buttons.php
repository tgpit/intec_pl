<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arForms
 */

?>

<div class="news-detail-content-header-info-buttons intec-grid intec-grid-wrap">
    <?php if (!empty($arForms['ORDER'])) { ?>
        <div class="intec-grid-item-auto intec-grid-item-600-1">
            <?= Html::beginTag('div', [
                'class' => [
                    'news-detail-content-header-info-buttons-order',
                    'intec' => [
                        'ui',
                        'ui-control-button',
                        'ui-mod-round-2',
                        'ui-scheme-current'
                    ]
                ],
                'data-role' => 'order.button'
            ]) ?>
                <?= Loc::getMessage('N_PROJECTS_N_D_DEFAULT_BUTTON_ORDER') ?>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
    <?php if (!empty($arForms['ASK'])) { ?>
        <div class="intec-grid-item-auto intec-grid-item-600-1">
            <?= Html::beginTag('div', [
                'class' => [
                    'news-detail-content-header-info-buttons-ask',
                    'intec' => [
                        'ui',
                        'ui-control-button',
                        'ui-mod-round-2',
                        'ui-mod-transparent'
                    ]
                ],
                'data-role' => 'ask.button'
            ]) ?>
                <?= Loc::getMessage('N_PROJECTS_N_D_DEFAULT_BUTTON_ASK') ?>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
</div>
