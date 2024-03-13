<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-quick-view',
        'intec-ui' => [
            '',
            'control-button',
            'mod-round-2'
        ]
    ],
    'data-role' => 'product.quickView'
]) ?>
    <div class="catalog-element-quick-view-icon intec-ui-part-icon intec-ui-picture">
        <?= FileHelper::getFileData(__DIR__.'/../../svg/button.quick.view.svg') ?>
    </div>
    <div class="catalog-element-quick-view-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_QUICK_VIEW') ?>
    </div>
<?= Html::endTag('div') ?>
