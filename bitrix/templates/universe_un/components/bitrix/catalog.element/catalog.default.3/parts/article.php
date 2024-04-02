<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<?= Html::beginTag('div', [
    'class' => 'catalog-element-article',
    'data' => [
        'role' => 'article',
        'show' => !empty($arResult['ARTICLE']) ? 'true' : 'false'
    ]
]) ?>
    <span class="catalog-element-article-name">
        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_ARTICLE').':' ?>
    </span>
    <span class="catalog-element-article-value" data-role="article.value">
        <?= $arResult['ARTICLE'] ?>
    </span>
<?= Html::endTag('div') ?>