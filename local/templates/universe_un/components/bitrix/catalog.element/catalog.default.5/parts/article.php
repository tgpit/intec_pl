<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arFields
 */

?>
<?= Html::beginTag('div', [
    'class' => 'catalog-element-article-container',
    'data' => [
        'role' => 'article',
        'show' => $arFields['ARTICLE']['SHOW'] ? 'true' : 'false'
    ]
]) ?>
    <div class="catalog-element-article">
        <span class="catalog-element-article-name">
            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_ARTICLE_NAME') ?>
        </span>
        <span class="catalog-element-article-value" data-role="article.value">
            <?= $arFields['ARTICLE']['VALUE'] ?>
        </span>
    </div>
<?= Html::endTag('div') ?>