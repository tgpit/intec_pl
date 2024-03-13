<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\RegExp;
use intec\core\helpers\StringHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'CONSENT_URL' => null
], $arParams);

if (!Loader::includeModule('intec.core'))
    return;

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

    $arMatches = RegExp::matchesBy('/^(<(input|select|textarea)[^>]*?class=")([^>]*?)(".*?\\/?>)(.*)/is', $arQuestion['HTML_CODE'], false, 0);
    $sClass = 'intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-2';

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

unset($arQuestion);