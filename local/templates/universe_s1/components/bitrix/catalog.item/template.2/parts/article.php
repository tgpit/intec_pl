<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arItem
 */

?>
<?= Html::beginTag('div', [
    'class' => 'catalog-item-article-container',
    'data' => [
        'role' => 'article',
        'show' => $arItem['ARTICLE']['SHOW'] ? 'true' : 'false'
    ]
]) ?>
    <span class="catalog-item-article-name">
        <?= Loc::getMessage('C_CATALOG_ITEM_TEMPLATE_2_TEMPLATE_ARTICLE') ?>
    </span>
    <span class="catalog-item-article-value" data-role="article.value">
        <?= $arItem['ARTICLE']['VALUE'] ?>
    </span>
<?= Html::endTag('div') ?>