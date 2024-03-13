<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\component\InnerTemplate;

/**
 * @var string $componentName
 * @var string $templateName
 * @var string $siteTemplate
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 * @var array $arParts
 * @var InnerTemplate $desktopTemplate
 * @var InnerTemplate $fixedTemplate
 * @var InnerTemplate $mobileTemplate
 */
//intec.regionality:regions.select
if (Loader::includeModule('intec.regionality')) {
    $arTemplateParameters['REGIONALITY_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_HEADER_TEMP1_REGIONALITY_USE'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['REGIONALITY_USE'] == 'Y') {

        $rsTemplates = CComponentUtil::GetTemplatesList('intec.regionality:regions.select', $siteTemplate);

        foreach ($rsTemplates as $arTemplate) {
            $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);
        }

        $arTemplateParameters['REGIONALITY_TEMPLATE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_HEADER_TEMP1_REGIONALITY_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => $arTemplates,
            'DEFAULT' => 'template.1',
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arTemplates);
    }
}