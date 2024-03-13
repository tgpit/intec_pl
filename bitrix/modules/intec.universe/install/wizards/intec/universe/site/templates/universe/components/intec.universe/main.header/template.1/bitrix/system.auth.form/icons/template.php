<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

if (!CModule::IncludeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sFormType = $arResult['FORM_TYPE'];

$oFrame = $this->createFrame();

?>
<div class="widget-authorization-icons" id="<?= $sTemplateId ?>">
    <?php $oFrame->begin() ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-authorization-items',
                'intec-grid' => [
                    '',
                    'nowrap',
                    'i-h-15',
                    'a-v-center'
                ]
            ]
        ]) ?>
            <?php if ($sFormType == 'login') { ?>
                <div class="widget-authorization-item-wrap intec-grid-item-auto">
                    <div class="widget-authorization-item intec-cl-text-hover" data-action="login">
                        <i class="glyph-icon-login_2"></i>
                    </div>
                </div>
            <?php } else { ?>
                <div class="widget-authorization-item-wrap intec-grid-item-auto">
                    <a href="<?= $arResult['PROFILE_URL'] ?>" class="widget-authorization-item intec-cl-text-hover">
                        <i class="glyph-icon-user_2"></i>
                    </a>
                </div>
                <div class="widget-authorization-item-wrap intec-grid-item-auto">
                    <a href="<?= $arResult['LOGOUT_URL'] ?>" class="widget-authorization-item intec-cl-text-hover">
                        <i class="glyph-icon-logout_2"></i>
                    </a>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
        <?php include(__DIR__.'/parts/script.php') ?>
    <?php $oFrame->beginStub() ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-authorization-items',
                'intec-grid' => [
                    '',
                    'nowrap',
                    'i-h-5',
                    'a-v-center'
                ]
            ]
        ]) ?>
            <div class="widget-authorization-item-wrap intec-grid-item-auto">
                <div class="widget-authorization-item intec-cl-text-hover" data-action="login">
                    <i class="glyph-icon-login_2"></i>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php $oFrame->end() ?>
</div>