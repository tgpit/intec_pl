<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

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
$arSvg = [
    'ICON' => FileHelper::getFileData(__DIR__.'/images/icon.svg'),
    'ARROW' => FileHelper::getFileData(__DIR__.'/images/arrow.svg'),
    'LOGOUT' => FileHelper::getFileData(__DIR__.'/images/logout.svg')
];

$oFrame = $this->createFrame();

?>
<div class="widget-authorization-panel" id="<?= $sTemplateId ?>">
    <?php $oFrame->begin() ?>
        <?php if ($sFormType == 'login') { ?>
            <div class="widget-panel-part" data-action="login">
                <div class="widget-authorization-personal-button intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                    <div class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-stroke">
                        <?= $arSvg['ICON'] ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <div class="widget-authorization-personal-text intec-cl-text">
                            <?= Loc::getMessage('W_HEADER_S_A_F_LOGIN') ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="widget-authorization-personal" data-expanded="false" data-role="personal">
                <div class="widget-authorization-personal-button" data-block-action="popup.open">
                    <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                        <div class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-stroke">
                            <?= $arSvg['ICON'] ?>
                        </div>
                        <div class="intec-grid-item-auto">
                            <a href="<?= $arResult['PROFILE_URL'] ?>" class="widget-authorization-personal-text intec-cl-text">
                                <?= Loc::getMessage('W_HEADER_S_A_F_PERSONAL') ?>
                            </a>
                        </div>
                        <div class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-stroke" data-role="arrow">
                            <?= $arSvg['ARROW'] ?>
                        </div>
                    </div>
                </div>
                <div class="widget-authorization-personal-menu" data-block-element="popup">
                    <?php if (!empty($arParams['MENU_PERSONAL_SECTION'])) { ?>
                        <?php include(__DIR__.'/parts/menu.php') ?>
                    <?php } else { ?>
                        <div class="widget-authorization-buttons">
                            <div class="widget-authorization-button" data-selected="false">
                                <?= Html::beginTag('a', [
                                    'href' => $arResult['LOGOUT_URL'],
                                    'class' => 'widget-authorization-button-text'
                                ]) ?>
                                    <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                                        <div class="intec-grid-item-auto intec-ui-picture">
                                            <?= $arSvg['LOGOUT'] ?>
                                        </div>
                                        <div class="intec-grid-item-auto">
                                            <?= Loc::getMessage('W_HEADER_S_A_F_LOGOUT') ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('a') ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php $oFrame->beginStub() ?>
        <div class="widget-panel-part" data-action="login">
            <div class="widget-authorization-personal-button intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-4">
                <div class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-stroke">
                    <?= $arSvg['ICON'] ?>
                </div>
                <div class="intec-grid-item-auto">
                    <div class="widget-authorization-personal-text intec-cl-text">
                        <?= Loc::getMessage('W_HEADER_S_A_F_LOGIN') ?>
                    </div>
                </div>
            </div>
        </div>
    <?php $oFrame->end() ?>
    <?php if (!defined('EDITOR')) { ?>
        <?php include(__DIR__.'/parts/script.php') ?>
    <?php } ?>
</div>