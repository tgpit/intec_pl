<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;
use intec\core\helpers\RegExp;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'FORM_ID' => null,
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'CONSENT_SHOW' => 'N',
    'CONSENT_URL' => null,
    'FORM_TITLE_SHOW' => 'N',
    'FORM_DESCRIPTION_SHOW' => 'N',
    'FORM_POSITION' => 'left',
    'FORM_ADDITIONAL_PICTURE_SHOW' => 'N',
    'FORM_ADDITIONAL_PICTURE_PATH' => null,
    'FORM_ADDITIONAL_PICTURE_VERTICAL' => 'center',
    'FORM_ADDITIONAL_PICTURE_SIZE' => 'contain',
    'FORM_BACKGROUND_PATH' => null,
    'FORM_BACKGROUND_PARALLAX_USE' => 'N',
    'FORM_BACKGROUND_PARALLAX_RATIO' => 10
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH,
    'TEMPLATE_PATH' => $this->GetFolder(),
    'SITE_DIR' => SITE_DIR
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'CONSENT' => [
        'SHOW' => $arParams['CONSENT_SHOW'] === 'Y' && !empty($arParams['CONSENT_URL']),
        'URL' => StringHelper::replaceMacros($arParams['CONSENT_URL'], $arMacros),
        'CHECKED' => !empty($_POST['licenses_popup'])
    ],
    'FORM' => [
        'POSITION' => $arParams['FORM_POSITION'],
        'TITLE' => [
            'SHOW' => $arResult['isFormTitle'] == 'Y' && $arParams['FORM_TITLE_SHOW'] === 'Y',
            'VALUE' => $arResult['FORM_TITLE']
        ],
        'DESCRIPTION' => [
            'SHOW' => $arResult['isFormDescription'] == 'Y' && $arParams['FORM_DESCRIPTION_SHOW'] === 'Y',
            'VALUE' => $arResult['FORM_DESCRIPTION']
        ]
    ],
    'BACKGROUND' => [
        'PATH' => StringHelper::replaceMacros($arParams['FORM_BACKGROUND_PATH'], $arMacros),
        'PARALLAX' => [
            'USE' => $arParams['FORM_BACKGROUND_PARALLAX_USE'] === 'Y',
            'RATIO' => Type::toInteger($arParams['FORM_BACKGROUND_PARALLAX_RATIO'])
        ],
    ],
    'ADDITIONAL_PICTURE' => [
        'SHOW' => $arParams['FORM_ADDITIONAL_PICTURE_SHOW'] === 'Y' && !empty($arParams['FORM_ADDITIONAL_PICTURE_PATH']) && $arParams['FORM_POSITION'] != 'center',
        'PATH' => StringHelper::replaceMacros($arParams['FORM_ADDITIONAL_PICTURE_PATH'], $arMacros),
        'VERTICAL_ALIGN' => $arParams['FORM_ADDITIONAL_PICTURE_VERTICAL'],
        'SIZE' => $arParams['FORM_ADDITIONAL_PICTURE_SIZE']
    ],
    'CAPTCHA' => [
        'USE' => $arResult['isUseCaptcha'] == 'Y'
    ]
];

if ($arVisual['BACKGROUND']['PARALLAX']['USE']) {
    if ($arVisual['BACKGROUND']['PARALLAX']['RATIO'] < 0)
        $arVisual['BACKGROUND']['PARALLAX']['RATIO'] = 0;
    else if ($arVisual['BACKGROUND']['PARALLAX']['RATIO'] > 100)
        $arVisual['BACKGROUND']['PARALLAX']['RATIO'] = 100;

    $arVisual['BACKGROUND']['PARALLAX']['RATIO'] = (100 - $arVisual ['BACKGROUND']['PARALLAX']['RATIO']) / 100;
}

foreach ($arResult['QUESTIONS'] as &$arQuestion) {
    $arQuestion['HTML_CODE'] = trim($arQuestion['HTML_CODE']);
    $sType = ArrayHelper::getValue($arQuestion, ['STRUCTURE', 0, 'FIELD_TYPE']);

    if ($sType === 'radio' || $sType === 'checkbox') {
        $arFields = explode('<br />', $arQuestion['HTML_CODE']);

        foreach ($arFields as $iIndex => $sField) {
            $arMatches = [];
            $sClass = null;

            if ($sType === 'radio') {
                $arMatches = RegExp::matchesBy('/<label>.*(<input.*?\\/?>).*<\\/label>.*<label[^>]*>(.*)?<\\/label>/is', $arQuestion['HTML_CODE'], false, 0);
                $sClass = 'intec-ui intec-ui-control-radiobox intec-ui-scheme-current';
            } else {
                $arMatches = RegExp::matchesBy('/(<input.*?\\/?>).*<label[^>]*>(.*)?<\\/label>/is', $arQuestion['HTML_CODE'], false, 0);
                $sClass = 'intec-ui intec-ui-control-checkbox intec-ui-scheme-current';
            }

            if (!empty($arMatches))
                $arFields[$iIndex] =
                    Html::beginTag('label', [
                        'class' => $sClass
                    ]).
                    $arMatches[1].
                    Html::tag('span', null, [
                        'class' => 'intec-ui-part-selector'
                    ]).
                    Html::tag('span', $arMatches[2], [
                        'class' => 'intec-ui-part-content'
                    ]).
                    Html::endTag('label');
        }

        $arQuestion['HTML_CODE'] = implode('<br />', $arFields);

        unset($arFields, $iIndex, $sField, $arMatches, $sClass);
    } else {
        $arMatches = RegExp::matchesBy('/^(<(input|select|textarea)[^>]*?class=")([^>]*?)(".*?\\/?>)(.*)/is', $arQuestion['HTML_CODE'], false, 0);
        $sClass = 'intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-3 intec-ui-size-2';
        $sRequired = ($arQuestion['REQUIRED'] === 'Y' ? $arQuestion['CAPTION']."*" : $arQuestion['CAPTION']);

        if (!empty($arMatches)) {
            $arQuestion['HTML_CODE'] = $arMatches[1].(!empty($arMatches[3]) ? $arMatches[3] . ' ' : null).$sClass."\" placeholder=\"".$arMatches[4].$arMatches[5];
        } else {
            $arMatches = RegExp::matchesBy('/^(<(input|select|textarea)[^>]*?)(\\/?>)(.*)/is', $arQuestion['HTML_CODE'], false, 0);

            if (!empty($arMatches)) {
                $arQuestion['HTML_CODE'] = $arMatches[1].' class="'.$sClass.'"'.$arMatches[3];

                if (!empty($arMatches[4]))
                    $arQuestion['HTML_CODE'] =
                        Html::beginTag('div', [
                            'class' => 'intec-grid intec-grid-nowrap intec-grid-i-h-5 intec-grid-a-v-center'
                        ]).
                        Html::tag('div', $arQuestion['HTML_CODE'], [
                            'class' => 'intec-grid-item intec-grid-item-shrink-1'
                        ]).
                        Html::tag('div', $arMatches[4], [
                            'class' => 'intec-grid-item-auto'
                        ]).
                        Html::endTag('div');
            }
        }

        unset($arMatches, $sClass, $sRequired, $arQuestion);
    }
}

unset($arQuestion, $sType);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);
