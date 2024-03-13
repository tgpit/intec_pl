<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-contact-1'
    ],
    'data' => [
        'wide' => $arResult['WIDE'] ? 'true' : 'false',
        'block-show' => $arResult['BLOCK']['SHOW'] ? 'true' : 'false',
        'block-view' => $arResult['BLOCK']['VIEW']
    ]
]) ?>
    <div class="widget-content">
        <?php if (!$arResult['WIDE']) { ?>
            <div class="widget-content-wrapper intec-content">
                <div class="widget-content-wrapper-2 intec-content-wrapper">
        <?php } ?>
        <?php if ($arResult['BLOCK']['SHOW']) { ?>
            <div class="widget-block">
                <div class="widget-block-wrapper">
                    <div class="widget-block-wrapper-2">
                        <?php if (!empty($arResult['BLOCK']['TITLE'])) { ?>
                            <div class="widget-block-title">
                                <?= $arResult['BLOCK']['TITLE'] ?>
                            </div>
                        <?php } ?>
                        <div class="widget-block-items">
                            <?php if ($arResult['ADDRESS']['SHOW']) { ?>
                                <div class="widget-block-item widget-block-item-address">
                                    <div class="widget-block-item-title">
                                        <?= Loc::getMessage('C_MAIN_WIDGET_CONTACT_1_ADDRESS') ?>:
                                    </div>
                                    <?php if (!empty($arResult['ADDRESS']['CITY'])) { ?>
                                        <div class="widget-block-item-value">
                                            <?= $arResult['ADDRESS']['CITY'] ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($arResult['ADDRESS']['STREET'])) { ?>
                                        <div class="widget-block-item-value">
                                            <?= $arResult['ADDRESS']['STREET'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['EMAIL']['SHOW']) { ?>
                                <div class="widget-block-item widget-block-item-email">
                                    <div class="widget-block-item-title">
                                        <?= Loc::getMessage('C_MAIN_WIDGET_CONTACT_1_EMAIL') ?>:
                                    </div>
                                    <?php foreach ($arResult['EMAIL']['VALUES'] as $sEmail) { ?>
                                        <div class="widget-block-item-value">
                                            <?= Html::tag('a', $sEmail, [
                                                'class' => 'intec-cl-text-hover',
                                                'href' => 'mailto:'.$sEmail
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['PHONE']['SHOW']) { ?>
                                <div class="widget-block-item widget-block-item-phone">
                                    <div class="widget-block-item-title">
                                        <?= Loc::getMessage('C_MAIN_WIDGET_CONTACT_1_PHONE') ?>:
                                    </div>
                                    <?php foreach ($arResult['PHONE']['VALUES'] as $arPhone) { ?>
                                        <div class="widget-block-item-value">
                                            <?= Html::tag('a', $arPhone['DISPLAY'], [
                                                'class' => 'intec-cl-text-hover',
                                                'href' => 'tel:'.$arPhone['VALUE']
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['FORM']['SHOW']) { ?>
                            <div class="widget-block-item widget-block-item-form">
                                <?= Html::tag('div', $arResult['FORM']['BUTTON']['TEXT'], [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'size-2',
                                            'scheme-current',
                                            'mod-round-3'
                                        ]
                                    ],
                                    'data-role' => 'contact.form'
                                ]) ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="widget-map">
            <?php $APPLICATION->IncludeComponent(
                $arResult['MAP']['VENDOR'] === 'google' ? 'bitrix:map.google.view' : 'bitrix:map.yandex.view',
                '.default',
                $arResult['MAP'],
                $component,
                ['HIDE_ICONS' => 'Y']
            ) ?>
        </div>
        <?php if (!$arResult['WIDE']) { ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if ($arResult['BLOCK']['SHOW'] && $arResult['FORM']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>