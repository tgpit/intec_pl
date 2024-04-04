<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<div class="intec-grid-item-1">
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-form',
        'c-form-template-1'
    ]
]) ?>
                    <div class="widget-form-action intec-grid-item-auto">
                        <div class="widget-form-button-wrap">
                            <?= Html::tag('div', $arVisual['BUTTON']['TEXT'], [
                                'class' => [
                                    'catalog-element-buy-fast',
									'intec-cl-text',
                                    'intec-cl-border'
                                ],
                                'data-role' => 'form.button'
                            ]) ?>
                        </div>
                    </div>
    <?php if ($arVisual['BUTTON']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>
</div>