<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (!$arResult['NavShowAlways'])
    if ($arResult['NavRecordCount'] == 0 || ($arResult['NavPageCount'] == 1 && !$arResult['NavShowAll']))
        return;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-system-pagenavigation',
        'c-system-pagenavigation-lazy-1'
    ],
    'data-mode' => 'upload'
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="system-pagenavigation-button" data-role="pagination.button">
                <div class="system-pagenavigation-button-content">
                    <?= Html::tag('div', FileHelper::getFileData(__DIR__.'/svg/icon.svg'), [
                        'class' => [
                            'system-pagenavigation-button-icon',
                            'system-pagenavigation-button-part',
                            'intec-cl-svg-path-stroke'
                        ]
                    ]) ?>
                    <?= Html::tag('div', Loc::getMessage('C_SYSTEM_PAGENAVIGATION_LAZY_1_TEMPLATE_BUTTON_NAME'), [
                        'class' => [
                            'system-pagenavigation-button-name',
                            'system-pagenavigation-button-part',
                            'intec-cl-text'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>