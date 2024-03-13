<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\net\Url;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!empty($arResult['ORDER_PROPS'])) {
    foreach ($arResult['ORDER_PROPS'] as &$arGroupProps) {
        if (!empty($arGroupProps['PROPS'])) {
            foreach ($arGroupProps['PROPS'] as &$arProp) {
                $arResult['ORDER_PROPS_VALUES']['ORDER_PROP_'.$arProp['ID']] = $arProp['MULTIPLE'] === 'Y' ? unserialize($arProp['~DEFAULT_VALUE']) : $arProp['~DEFAULT_VALUE'];
            }

            unset($arProp);
        }
    }

    unset($arGroupProps);
}
