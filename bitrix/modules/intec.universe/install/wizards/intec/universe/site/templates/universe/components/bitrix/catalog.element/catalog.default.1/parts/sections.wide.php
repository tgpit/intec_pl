<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

$arSectionsNoPrint = ['ARTICLES', 'DOCUMENTS', 'VIDEO', 'REVIEWS'];

?>
<div class="catalog-element-sections catalog-element-sections-wide">
    <?php foreach ($arResult['SECTIONS'] as $sCode => $arSection) { ?>
        <?= Html::beginTag('div', [
            'id' => $sTemplateId.'-'.$arSection['CODE'],
            'class' => 'catalog-element-section',
            'data' => [
                'print' => ArrayHelper::isIn($sCode, $arSectionsNoPrint) ? 'false' : 'true'
            ]
        ]) ?>
            <div class="catalog-element-section-name intec-ui-markup-header">
                <?= $arSection['NAME'] ?>
            </div>
            <div class="catalog-element-section-content">
                <?php include(__DIR__.'/sections/'.$arSection['CODE'].'.php') ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
<?php unset($sCode, $arSection) ?>