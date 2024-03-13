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
<div class="widget-authorization-panel" id="<?= $sTemplateId ?>">
    <?php $oFrame->begin() ?>
        <?php if ($sFormType == 'login') { ?>
            <div class="widget-panel-button" data-action="login">
                <div class="widget-panel-button-wrapper intec-grid intec-grid-a-v-center intec-cl-text-hover">
                    <div class="widget-panel-button-icon intec-grid-item-auto glyph-icon-user_2"></div>
                </div>
            </div>
            <?php include(__DIR__.'/parts/script.php') ?>
        <?php } else { ?>
            <a rel="nofollow" href="<?= $arResult['PROFILE_URL'] ?>" class="widget-panel-button">
                <div class="widget-panel-button-wrapper intec-grid intec-grid-a-v-center intec-cl-text-hover">
                    <div class="widget-panel-button-icon intec-grid-item-auto glyph-icon-user_2"></div>
                    <div class="widget-panel-button-text intec-grid-item-auto">
                        <?= $arResult['USER_LOGIN'] ?>
                    </div>
                </div>
            </a>
            <a rel="nofollow" href="<?= $arResult['LOGOUT_URL'] ?>" class="widget-panel-button">
                <div class="widget-panel-button-wrapper intec-grid intec-grid-a-v-center intec-cl-text-hover">
                    <div class="widget-panel-button-icon intec-grid-item-auto glyph-icon-logout_2"></div>
                </div>
            </a>
        <?php } ?>
    <?php $oFrame->beginStub() ?>
        <div class="widget-panel-button" data-action="login">
            <div class="widget-panel-button-wrapper intec-grid intec-grid-a-v-center intec-cl-text-hover">
                <div class="widget-panel-button-icon intec-grid-item-auto glyph-icon-login_2"></div>
            </div>
        </div>
    <?php $oFrame->end() ?>
</div>