<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$arSvg = [
    'BUTTON' => FileHelper::getFileData(__DIR__.'/svg/button.icon.svg')
];

?>
<div class="ns-bitrix c-main-pagenavigation c-main-pagenavigation-lazy-1">
    <?= Html::beginTag('div', [
        'class' => 'pagenavigation-button',
        'data' => [
            'role' => 'navigation.button',
            'state' => 'disabled'
        ]
    ]) ?>
        <div class="pagenavigation-button-content">
            <?= Html::tag('div', $arSvg['BUTTON'], [
                'class' => [
                    'pagenavigation-button-icon',
                    'pagenavigation-button-part',
                    'intec-cl-svg-path-stroke'
                ]
            ]) ?>
            <?= Html::tag('div', Loc::getMessage('C_MAIN_PAGENAVIGATION_LAZY_1_TEMPLATE_BUTTON_SHOW'), [
                'class' => [
                    'pagenavigation-button-name',
                    'pagenavigation-button-part',
                    'intec-cl-text'
                ]
            ]) ?>
        </div>
    <?= Html::endTag('div') ?>
</div>