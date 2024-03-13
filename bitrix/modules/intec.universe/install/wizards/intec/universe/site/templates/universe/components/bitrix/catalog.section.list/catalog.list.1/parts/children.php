<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arSections) use (&$arVisual) { ?>
    <?php if (empty($arSections) || !Type::isArray($arSections)) return ?>
    <?php $iCount = 0 ?>
    <?php foreach ($arSections as $arSection) { ?>
        <?php $iCount++ ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-list-item-child',
                'intec-grid-item-3',
                'intec-grid-item-768-2',
                'intec-grid-item-500-1'
            ]
        ]) ?>
            <?= Html::tag('a', $arSection['NAME'], [
                'class' => [
                    'catalog-section-list-item-child-name',
                    'intec-cl-text'
                ],
                'href' => $arSection['SECTION_PAGE_URL'],
                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
            ]) ?>
            <?php if ($arVisual['CHILDREN']['ELEMENTS']) { ?>
                <div class="catalog-section-list-item-child-count intec-cl-text">
                    <?= $arSection['ELEMENT_CNT'] ?>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>