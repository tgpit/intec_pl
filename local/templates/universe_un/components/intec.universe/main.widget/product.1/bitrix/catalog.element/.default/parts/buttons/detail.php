<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?= Html::beginTag('a', [
    'class' => [
        'catalog-element-button-detail',
        'intec-cl-background-light-hover',
        'intec-ui' => [
            '',
            'control-button',
            'mod-block'
        ]
    ],
    'href' => $arResult['DETAIL_PAGE_URL'],
    'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
]) ?>
    <span class="catalog-element-button-detail-content intec-ui-part-content">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_DETAIL') ?>
    </span>
<?= Html::endTag('a') ?>