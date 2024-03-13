<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */
?>
<?php if (!empty($arResult['SECTIONS']['DESCRIPTION'])) { ?>

    <?php if ($arVisual['WIDE']) { ?>
            </div>
        </div>
    <?php } ?>
    <div class="catalog-element-sections catalog-element-sections-wide">
        <div class="<?= Html::cssClassFromArray([
            'catalog-element-section' => true,
            'catalog-element-section-dark' => $arVisual['WIDE'],
            'intec-content-wrap' => $arVisual['WIDE']
        ], true) ?>">
            <div class="catalog-element-section-wrapper">
                <div class="catalog-element-section-name intec-ui-markup-header">
                    <?= $arResult['SECTIONS']['DESCRIPTION']['NAME'] ?>
                </div>
                <div class="catalog-element-section-content">
                    <?php if ($arVisual['WIDE']) { ?>
                        <div class="intec-content">
                            <div class="intec-content-wrapper">
                    <?php } ?>
                    <?php include(__DIR__.'/sections/description.php') ?>
                    <?php if ($arVisual['WIDE']) { ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($arVisual['WIDE']) { ?>
        <div class="catalog-element-wrapper intec-content intec-content-visible">
            <div class="catalog-element-wrapper-2 intec-content-wrapper">
    <?php } ?>

<?php } ?>
<div class="catalog-element-sections catalog-element-sections-wide" data-role="sections">
    <?php foreach($arResult['SECTIONS'] as $sCode => $arSection)  {

        if ($sCode == 'DESCRIPTION')
            continue;

        if ($sCode == 'STORES' && !$arVisual['STORES']['SHOW'])
            continue;
        ?>

        <?php if (!empty($arSection)) { ?>
            <?= Html::beginTag('div', [
                    'id' => $sTemplateId.'-'.$arSection["CODE"],
                    'class' => 'catalog-element-section',
                    'data' => [
                        'role' => 'section',
                        'code' => $arSection["CODE"],
                        'print' => !ArrayHelper::getValue($arSection, 'PRINT') ? 'false' : ''
                    ]
            ]);?>
                <div class="catalog-element-section-name intec-ui-markup-header">
                    <div class="catalog-element-section-name-wrapper" data-role="section.toggle">
                        <span>
                            <?= $arSection['NAME'] ?>
                        </span>
                        <div class="catalog-element-section-name-decoration"></div>
                    </div>
                </div>
                <div class="catalog-element-section-content" data-role="section.content">
                    <?php include(__DIR__.'/sections/'.$arSection["CODE"].'.php') ?>
                </div>
            <?= Html::endTag('div'); ?>
        <?php } ?>
    <?php }?>
</div>