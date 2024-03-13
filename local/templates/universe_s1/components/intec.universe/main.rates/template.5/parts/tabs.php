<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var Closure $vItems()
 */

?>
<div class="widget-tabs-wrap">
    <div class="widget-tabs-wrap-2 intec-content intec-content-visible">
        <div class="widget-tabs-wrap-3 intec-content-wrapper">
            <?= Html::beginTag('ul', [
                'class' => [
                    'widget-tabs',
                    'intec-ui' => [
                        '',
                        'control-tabs',
                        'mod-block',
                        'mod-position-'.$arVisual['TABS']['POSITION'],
                        'scheme-current',
                        'view-1'
                    ]
                ],
                'data' => [
                    'ui-control' => 'tabs'
                ]
            ]) ?>
            <?php $iCounter = 0 ?>
            <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                <?= Html::beginTag('li', [
                    'class' => 'intec-ui-part-tab',
                    'data' => [
                        'active' => $iCounter === 0 ? 'true' : 'false'
                    ]
                ]) ?>
                    <a href="<?= '#'.$sTemplateId.'-tab-'.$iCounter ?>" data-type="tab">
                        <?= $arSection['NAME'] ?>
                    </a>
                <?= Html::endTag('li') ?>
                <?php $iCounter++ ?>
            <?php } ?>
            <?= Html::endTag('ul') ?>
        </div>
    </div>
</div>
<div class="widget-tabs-content-wrap intec-content intec-content-visible">
    <div class="widget-tabs-content-wrap-2 intec-content-wrapper">
        <div class="widget-tabs-content intec-ui intec-ui-control-tabs-content">
            <?php $iCounter = 0 ?>
            <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
                <?= Html::beginTag('div', [
                    'id' => $sTemplateId.'-tab-'.$iCounter,
                    'class' => 'intec-ui-part-tab',
                    'data' => [
                        'active' => $iCounter === 0 ? 'true' : 'false'
                    ]
                ]) ?>
                <?php if ($arVisual['SECTION']['DESCRIPTION']['SHOW'] && !empty($arSection['DESCRIPTION'])) { ?>
                    <div class="widget-section-description" data-align="<?= $arVisual['SECTION']['DESCRIPTION']['POSITION'] ?>">
                        <?= $arSection['DESCRIPTION'] ?>
                    </div>
                <?php } ?>
                <?php $vItems($arSection['ITEMS']) ?>
                <?= Html::endTag('div') ?>
                <?php $iCounter++ ?>
            <?php } ?>
        </div>
    </div>
</div>