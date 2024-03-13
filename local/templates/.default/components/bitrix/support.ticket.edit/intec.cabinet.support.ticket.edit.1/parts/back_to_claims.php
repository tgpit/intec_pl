<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="intec-ui-m-t-15 intec-ui-m-b-15">
    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start">
        <div class="intec-grid-item-auto">
            <?= Html::beginTag('a', [
                'class' => [
                    'support-ticket-edit-return',
                    'intec-grid' => [
                        '',
                        'nowrap',
                        'a-v-center',
                        'i-h-4'
                    ],
                    'intec-cl-text' => [
                        '',
                        'light-hover'
                    ]
                ],
                'href' => $arResult['REAL_FILE_PATH']
            ]) ?>
            <?= Html::tag('span', $arSvg['RETURN'], [
                'class' => [
                    'intec-grid-item-auto',
                    'intec-ui-picture',
                    'intec-cl-svg-path-stroke'
                ]
            ]) ?>
            <?= Html::tag('span', Loc::getMessage('C_SUPPORT_TICKET_EDIT_TEMPLATE_1_TEMPLATE_RETURN'), [
                'class' => 'intec-grid-item-auto'
            ]) ?>
            <?= Html::endTag('a') ?>
        </div>
    </div>
</div>
