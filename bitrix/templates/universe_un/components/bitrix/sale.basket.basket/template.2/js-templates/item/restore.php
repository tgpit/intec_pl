<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

?>

<?= Html::beginTag('div', [
    'class' => [
        'basket-item-wrapper',
        'basket-item-restore',
        'intec-grid' => [
            '',
            'a-v-stretch',
            '650-wrap'
        ]
    ]
]) ?>
    <div class="basket-item-content intec-grid-item intec-grid intec-grid-wrap intec-grid-a-h-between">
        <div class="basket-item-text-wrap intec-grid-item-auto intec-grid-item-shrink-1">
            <span>
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_RESTORE_PART_1') ?>
            </span>
            <span class="basket-item-restore-name">
                {{NAME}}
            </span>
            <span>
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_RESTORE_PART_2') ?>
            </span>
        </div>
        <div class="basket-item-restore-button-wrap intec-grid-item-auto">
            <?= Html::tag('span', Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_RESTORE_ACTION'), [
                'class' => 'basket-item-restore-button intec-cl-text',
                'data-entity' => 'basket-item-restore-button'
            ]) ?>
        </div>
    </div>
    <?= Html::tag('div', FileHelper::getFileData(__DIR__.'/../../svg/action.item.delete.svg'), [
        'class' => [
            'basket-item-action',
            'basket-item-restore-close',
            'intec-grid-item-auto',
            'intec-grid-item-650-1'
        ],
        'title' => Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_ITEM_ACTION_DELETE'),
        'data-entity' => 'basket-item-close-restore-button'
    ]) ?>
<?= Html::endTag('div') ?>