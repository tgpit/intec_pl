<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="widget c-form c-form-template-1" id="<?= $sTemplateId ?>">
    <div class="widget-form-buttons-wrap">
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'widget-form-button' => true,
                'intec-cl-background' => [
                    '' => true,
                    'light-hover' => true
                ],
                'intec-ui' => [
                    '' => true,
                    'control-button' => true,
                    'mod-round-2' => true,
                    'scheme-current' => true,
                ]
            ], true),
            'data-role' => 'form.button'
       ]) ?>
    <?php if (!empty($arResult['BUTTON']['TEXT'])) { ?>
    <?= $arResult['BUTTON']['TEXT'] ?>
    <?php } else { ?>
    <?= Loc::getMessage('C_FORM_TEMPLATE_1_TEMPLATE_BUTTON_TEXT_DEFAULT') ?>
    <?php } ?>
    <?= Html::endTag('div') ?>
       </div>
    <?php if ($arResult['BUTTON']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>
