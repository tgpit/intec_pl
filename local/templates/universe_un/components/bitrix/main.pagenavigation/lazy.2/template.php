<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

?>
<div class="ns-bitrix c-main-pagenavigation c-main-pagenavigation-lazy-2">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    'pagenavigation-button',
                    'intec-cl-text',
                    'intec-cl-border',
                    'intec-cl-background-light-hover',
                    'intec-cl-border-light-hover'
                ],
                'data' => [
                    'role' => 'navigation.button',
                    'state' => 'disabled'
                ]
            ]) ?>
                <div class="pagenavigation-button-content">
                    <?= Loc::getMessage('C_MAIN_PAGENAVIGATION_LAZY_2_TEMPLATE_BUTTON_NAME') ?>
                </div>
                <div class="pagenavigation-button-loader intec-ui-picture intec-cl-svg-path-stroke">
                    <?= FileHelper::getFileData(__DIR__.'/svg/button.icon.svg') ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    </div>
</div>