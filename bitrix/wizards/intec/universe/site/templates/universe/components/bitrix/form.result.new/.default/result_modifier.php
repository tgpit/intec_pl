<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\RegExp;
use intec\core\helpers\StringHelper;
use intec\template\Properties;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'CONSENT_URL' => null
], $arParams);

if (!Loader::includeModule('intec.core'))
    return;

$arSvg = [
    'FILE_ICON' => FileHelper::getFileData(__DIR__.'/svg/file.icon.svg'),
];

$oRequest = Core::$app->request;
$arResult['CONSENT'] = [
    'SHOW' => !defined('EDITOR') ? Properties::get('base-consent') : null,
    'URL' => $arParams['CONSENT_URL'],
    'CHECKED' => !empty($oRequest->post('licenses_popup'))
];

if (!empty($arResult['CONSENT']['URL'])) {
    $arResult['CONSENT']['URL'] = StringHelper::replaceMacros($arResult['CONSENT']['URL'], [
        'SITE_DIR' => SITE_DIR
    ]);
} else {
    $arResult['CONSENT']['SHOW'] = false;
}

if ($arResult['isFormErrors'])
    $arResult['isFormErrors'] = $oRequest->post('web_form_sent') === 'Y';

foreach ($arResult['QUESTIONS'] as &$arQuestion) {
    $arQuestion['HTML_CODE'] = trim($arQuestion['HTML_CODE']);
    $arQuestion['TYPE'] = ArrayHelper::getValue($arQuestion, ['STRUCTURE', 0, 'FIELD_TYPE']);

    if ($arQuestion['TYPE'] === 'radio' || $arQuestion['TYPE'] === 'checkbox') {
        $arFields = explode('<br />', $arQuestion['HTML_CODE']);

        foreach ($arFields as $iIndex => $sField) {
            $arMatches = [];
            $sClass = null;

            if ($arQuestion['TYPE'] === 'radio') {
                $arMatches = RegExp::matchesBy('/<label>.*(<input.*?\\/?>).*<\\/label>.*<label[^>]*>(.*)?<\\/label>/is', $sField, false, 0);
                $sClass = 'intec-ui intec-ui-control-radiobox intec-ui-scheme-current';
            } else {
                $arMatches = RegExp::matchesBy('/(<input.*?\\/?>).*<label[^>]*>(.*)?<\\/label>/is', $sField, false, 0);
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
    } elseif ($arQuestion['TYPE'] === 'file') {
        $arMatches = RegExp::matchesBy('/^(<(input)[^>]*?)(\\/?>)(.*)/is', $arQuestion['HTML_CODE'], false, 0);

        if (!empty($arMatches)) {
            $arQuestion['HTML_CODE']  =
                Html::beginTag('div', [
                    'class' => [
                        'form-result-new-field-file',
                        'intec-ui' => [
                            '',
                            'control-input',
                            'mod-block',
                            'mod-round-3',
                            'size-4'
                        ]
                    ]
                ]).
                    Html::beginTag('div', [
                        'class' => 'intec-grid'
                    ]).
                        Html::tag('div', $arSvg['FILE_ICON'], [
                            'class' => 'file-icon intec-grid-item-auto'
                        ]).
                        Html::tag('div', $arQuestion['CAPTION'], [
                            'class' => [
                                'file-caption',
                                'intec-grid-item'
                            ],
                            'data' => [
                                'role' => 'caption'
                            ]
                        ]).
                        $arQuestion['HTML_CODE'].
                    Html::endTag('div').
                Html::endTag('div');
        }
    } else {
        $arMatches = RegExp::matchesBy('/^(<(input|select|textarea)[^>]*?class=")([^>]*?)(".*?\\/?>)(.*)/is', $arQuestion['HTML_CODE'], false, 0);
        $sClass = 'intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-3 intec-ui-size-4';

        if (!empty($arMatches)) {
            $arQuestion['HTML_CODE'] = $arMatches[1].(!empty($arMatches[3]) ? $arMatches[3] . ' ' : null).$sClass.$arMatches[4].$arMatches[5];
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
    }
}

unset($sType);
unset($arQuestion);