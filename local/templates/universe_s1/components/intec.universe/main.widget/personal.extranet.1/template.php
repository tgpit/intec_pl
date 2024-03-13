<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!CModule::IncludeModule('intec.core'))
    return;

$arVisual = $arResult['VISUAL'];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-personal-extranet-1'
    ]
]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['SHOW_IFRAME']) { ?>
                <div class="widget-block-iframe">
                    <?= Html::tag('iframe', Loc::getMessage('C_PERSONAL_EXTRANET_TEMPLATE_1_TEMPLATE_OLD_BROWSER_MESSAGE'), [
                        'src' => $arParams['PATH_TO_CRM']
                    ]) ?>
                </div>
            <?php } else { ?>
                <?= Loc::getMessage('C_PERSONAL_EXTRANET_TEMPLATE_1_TEMPLATE_EMPTY_PATH_ERROR') ?>
            <?php } ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
