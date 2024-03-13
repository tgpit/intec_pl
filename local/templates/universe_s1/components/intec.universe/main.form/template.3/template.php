<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$this->setFrameMode(true);

$arTitle = ArrayHelper::getValue($arResult, 'TITLE');
$arLazyLoad = ArrayHelper::getValue($arResult, 'LAZYLOAD');
$arDescription = ArrayHelper::getValue($arResult, 'DESCRIPTION');
$arButtonOne = ArrayHelper::getValue($arResult, 'BUTTON_ONE');
$arButtonTwo = ArrayHelper::getValue($arResult, 'BUTTON_TWO');
$arImage = ArrayHelper::getValue($arResult, 'IMAGE');
$sView = ArrayHelper::getValue($arResult, 'VIEW');
$sTemplate = ArrayHelper::getValue($arResult, 'TEMPLATE');
$sFormOneID = ArrayHelper::getValue($arParams, 'FORM1_ID');
$sFormTwoID = ArrayHelper::getValue($arParams, 'FORM2_ID');
$sFormOneName = ArrayHelper::getValue($arParams, 'FORM1_NAME');
$sFormTwoName = ArrayHelper::getValue($arParams, 'FORM2_NAME');
$sConsent = ArrayHelper::getValue($arResult, 'CONSENT');

$sDefaultButtonColor = Html::cssClassFromArray([
    'form-button',
    'intec-ui' => [
        '',
        'control-button',
        'size-3',
        'mod-transparent',
        'mod-round-half',
        'scheme-white'
    ],
    'intec-cl-text-hover'
]);

$arTitleAttributes = [
    'class' => Html::cssClassFromArray([
        'form-title' => true,
        'intec-grid-item' => true,
        'intec-grid-item-2' => $arDescription['SHOW'] ? true : false,
        'intec-grid-item-1150-1' => true,
        'intec-grid-item-900-auto' => true,
        'intec-grid-item-450-1' => true
    ], true)
];

$arDescriptionAttributes = [
    'class' => Html::cssClassFromArray([
        'form-description' => true,
        'intec-grid-item' => true,
        'intec-grid-item-2' => true,
        'intec-grid-item-1150-1' => true,
        'intec-grid-item-1000-1' => true,
        'intec-grid-item-900-auto' => true,
        'intec-grid-item-450-1' => true
    ], true)
];

$arFormOne = [];
$arFormTwo = [];

if (!empty($sFormOneID)) {
    $arFormOne = [
        'id' => $sFormOneID,
        'template' => $sTemplate,
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_1_ASK',
            'CONSENT_URL' => $sConsent
        ],
        'settings' => [
            'title' => $sFormOneName
        ]
    ];
}

if (!empty($sFormTwoID)) {
    $arFormTwo = [
        'id' => $sFormTwoID,
        'template' => $sTemplate,
        'parameters' => [
            'AJAX_OPTION_ADDITIONAL' => $sTemplateId . '_FORM_2_ASK',
            'CONSENT_URL' => $sConsent
        ],
        'settings' => [
            'title' => $sFormTwoName
        ]
    ];
}

$arButtonOneAttributes = [
    'class' => $sDefaultButtonColor,
    'data-role' => 'form.button.1'
];

$arButtonTwoAttributes = [
    'class' => $sDefaultButtonColor,
    'data-role' => 'form.button.2'
];

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-form',
        'c-form-template-3',
        'intec-ui-clearfix'
    ],
    'data-theme' => $arVisual['THEME'],
    'data-background' => $arVisual['BACKGROUND']['USE'] ? 'true' : null,
    'data-button-position' => $arVisual['BUTTON']['POSITION'],
    'style' => [
        'background-image' => $arVisual['BACKGROUND']['USE'] ? 'url('.$arVisual['BACKGROUND']['PATH'].')' : null
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="form-wrapper intec-cl-background">
                <?= Html::tag('div', '', [
                    'class' => [
                        'form-image'
                    ],
                    'data' => [
                        'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                        'original' => $arLazyLoad['USE'] ? $arImage['SRC'] : null
                    ],
                    'style' => [
                        'background-image' => !$arLazyLoad['USE']['USE'] ? 'url(\''.$arImage['SRC'].'\')' : null
                    ]
                ]) ?>
                <div class="form-content intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-10">
                    <div class="intec-grid-item intec-grid-item intec-grid-item-1050-1">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-h-900-center intec-grid-a-v-center intec-grid-i-10">
                            <?= Html::tag('div', $arTitle['TEXT'], $arTitleAttributes) ?>
                            <?php if ($arDescription['SHOW']) {?>
                                <?= Html::tag('div', $arDescription['TEXT'], $arDescriptionAttributes) ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="intec-grid-item intec-grid-item-auto intec-grid-item-900-1 intec-grid-item-shrink-1">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-900-center intec-grid-i-10">
                            <?php if (!empty($arParams['FORM1_ID'])) { ?>
                                <div class="form-button-wrap intec-grid-item intec-grid-item-auto">
                                    <?= Html::tag('div', $arButtonOne['TEXT'], $arButtonOneAttributes) ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arParams['FORM2_ID'])) { ?>
                                <div class="form-button-wrap intec-grid-item intec-grid-item-auto">
                                    <?= Html::tag('div', $arButtonTwo['TEXT'], $arButtonTwoAttributes) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>