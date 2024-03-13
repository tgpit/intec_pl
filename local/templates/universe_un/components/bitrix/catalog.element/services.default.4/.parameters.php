<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Json;
use intec\core\helpers\Type;
use intec\core\helpers\StringHelper;

CBitrixComponent::includeComponentClass('bitrix:catalog.element');

/**
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();
$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
    'SORT' => 'ASC'
], [
    'ACTIVE' => 'Y'
]))->indexBy('ID');

$arIBlock = null;

if (!empty($arCurrentValues['IBLOCK_ID']))
    $arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arBlocks = include(__DIR__.'/common/blocks.php');

foreach ($arBlocks as $sBlock => &$arBlock) {
    $arBlock['ACTIVE'] = false;
    $arBlock['SORT'] = -1;

    if (!isset($arBlock['SORTABLE']))
        $arBlock['SORTABLE'] = true;
}

unset($arBlock);

if (isset($arCurrentValues['BLOCKS']) && Type::isArray($arCurrentValues['BLOCKS'])) {
    foreach ($arCurrentValues['BLOCKS'] as $sBlock)
        if (isset($arBlocks[$sBlock]))
            $arBlocks[$sBlock]['ACTIVE'] = true;

    unset($sBlock);
}

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arBlocks = Arrays::from($arBlocks);
$arTemplateParameters['BLOCKS'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS'),
    'TYPE' => 'LIST',
    'VALUES' => $arBlocks->asArray(function ($sCode, $arBlock) {
        return [
            'key' => $sCode,
            'value' => $arBlock['NAME']
        ];
    }),
    'MULTIPLE' => 'Y',
    'SIZE' => 16,
    'REFRESH' => 'Y'
];

$arBlocksSortable = $arBlocks->asArray(function ($sCode, $arBlock) {
    if (!$arBlock['ACTIVE'] || !$arBlock['SORTABLE'])
        return ['skip' => true];

    return [
        'key' => $sCode,
        'value' => $arBlock['NAME']
    ];
});

if (!empty($arBlocksSortable)) {
    $arTemplateParameters['BLOCKS_ORDER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ORDER'),
        'TYPE' => 'CUSTOM',
        'JS_FILE' => CatalogElementComponent::getSettingsScript('/bitrix/components/bitrix/catalog.element', 'dragdrop_order'),
        'JS_EVENT' => 'initDraggableOrderControl',
        'JS_DATA' => Json::encode($arBlocksSortable, 320, true),
        'DEFAULT' => 'description.1,result.1,including.1'
    ];
}

unset($arBlocksSortable);

if (!empty($arIBlock)) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arIBlock['ID']
    ]));

    $hProperties = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        return [
            'key' => $arProperty['CODE'],
            'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
        ];
    };

    $hPropertiesFile = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'F' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesString = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['MULTIPLE'] !== 'Y')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesMultipleString = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['MULTIPLE'] === 'Y')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesNumber = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'N' && $arProperty['MULTIPLE'] !== 'Y')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesElements = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['MULTIPLE'] === 'Y' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesBoolean = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'L' && $arProperty['LIST_TYPE'] === 'C' && $arProperty['MULTIPLE'] !== 'Y' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesList = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'L' && $arProperty['LIST_TYPE'] === 'L' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['PROPERTY_PRICE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_PROPERTY_PRICE'),
        'TYPE' => 'LIST',
        'VALUES' => ArrayHelper::merge($arProperties->asArray($hPropertiesNumber), $arProperties->asArray($hPropertiesString)),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PRICE'])) {

        $arTemplateParameters['PRICE_OLD_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_PRICE_OLD_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
        if($arCurrentValues['PRICE_OLD_SHOW']){
            $arTemplateParameters['PROPERTY_PRICE_OLD'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_PROPERTY_PRICE_OLD'),
                'TYPE' => 'LIST',
                'VALUES' => ArrayHelper::merge($arProperties->asArray($hPropertiesNumber), $arProperties->asArray($hPropertiesString)),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
        }

    }

    $arTemplateParameters['PROPERTY_CURRENCY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_PROPERTY_CURRENCY'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesList),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['CURRENCY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_CURRENCY'),
        'TYPE' => 'STRING'
    ];

    $arTemplateParameters['PROPERTY_PRICE_FORMAT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_PROPERTY_PRICE_FORMAT'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesString),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PRICE_FORMAT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_PRICE_FORMAT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '#VALUE# #CURRENCY#'
    ];

    $arTemplateParameters['DESCRIPTION_HEADER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_DESCRIPTION_HEADER'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_DESCRIPTION_HEADER_DEFAULT')
    ];

    $arTemplateParameters['DESCRIPTION_PROPERTY_HEADER'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_DESCRIPTION_PROPERTY_HEADER'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesString),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    if ($arBlocks['banner']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_BANNER_WIDE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_WIDE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BANNER_TITLE_H1'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_TITLE_H1'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BANNER_SPLIT'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_SPLIT'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['BLOCKS_BANNER_SPLIT'] !== 'Y') {
            $arTemplateParameters['BLOCKS_BANNER_ADDITIONAL_SHOW'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ADDITIONAL_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['BLOCKS_BANNER_ADDITIONAL_SHOW'] === 'Y') {
                $arTemplateParameters['BLOCKS_BANNER_ADDITIONAL'] = [
                    'PARENT' => 'BASE',
                    'TYPE' => 'LIST',
                    'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ADDITIONAL'),
                    'VALUES' => $arProperties->asArray($hPropertiesMultipleString),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
            }

            $arTemplateParameters['BLOCKS_BANNER_OVERHEAD_SHOW'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_OVERHEAD_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['BLOCKS_BANNER_OVERHEAD_SHOW'] === 'Y') {
                $arTemplateParameters['BLOCKS_BANNER_OVERHEAD'] = [
                    'PARENT' => 'BASE',
                    'TYPE' => 'LIST',
                    'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_OVERHEAD'),
                    'VALUES' => $arProperties->asArray($hPropertiesString),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
            }
        }

        $arTemplateParameters['BLOCKS_BANNER_TEXT_SHOW'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_TEXT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BANNER_PROPERTY_ORDER_BUTTON_SHOW'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_PROPERTY_ORDER_BUTTON_SHOW'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesBoolean),
            'REFRESH' => 'Y',
            'ADDITIONAL_VALUES' => 'Y'
        ];

        if (!empty($arCurrentValues['BLOCKS_BANNER_PROPERTY_ORDER_BUTTON_SHOW'])) {
            if (Loader::includeModule('form')) {
                $rsForms = CForm::GetList(
                    $by = 'sort',
                    $order = 'asc',
                    [],
                    $filtered = false
                );

                while ($arForm = $rsForms->Fetch())
                    $arForms[$arForm['ID']] = '['.$arForm['ID'].'] '.$arForm['NAME'];

                if (!empty($arCurrentValues['BLOCKS_BANNER_ORDER_FORM_ID'])) {
                    $arFields = [];
                    $rsFields = CFormField::GetList(
                        $arCurrentValues['BLOCKS_BANNER_ORDER_FORM_ID'],
                        'N',
                        $by = null,
                        $asc = null,
                        [
                            'ACTIVE' => 'Y'
                        ],
                        $filtered = false
                    );

                    while ($arField = $rsFields->GetNext()) {
                        $rsAnswers = CFormAnswer::GetList(
                            $arField['ID'],
                            $sort = '',
                            $order = '',
                            [],
                            $filtered = false
                        );

                        while ($arAnswer = $rsAnswers->GetNext()) {
                            $sType = $arAnswer['FIELD_TYPE'];

                            if (empty($sType))
                                continue;

                            $sId = 'form_' . $sType . '_' . $arAnswer['ID'];
                            $arFields[$sId] = '[' . $arAnswer['ID'] . '] ' . $arField['TITLE'];
                        }
                    }

                    unset($arField);
                }

                $rsTemplates = CComponentUtil::GetTemplatesList('bitrix:form.result.new', $siteTemplate);

            } else if (Loader::includeModule('intec.startshop')) {
                $rsForms = CStartShopForm::GetList();

                while ($arForm = $rsForms->Fetch())
                    $arForms[$arForm['ID']] = '['.$arForm['ID'].'] '.(!empty($arForm['LANG'][LANGUAGE_ID]['NAME']) ? $arForm['LANG'][LANGUAGE_ID]['NAME'] : $arForm['CODE']);

                if (!empty($arCurrentValues['BLOCKS_BANNER_ORDER_FORM_ID']))
                    $arFields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
                        'FORM' => $arCurrentValues['BLOCKS_BANNER_ORDER_FORM_ID']
                    ]))->asArray(function ($iIndex, $arField) {
                        return [
                            'key' => $arField['ID'],
                            'value' => '['.$arField['ID'].'] '.$arField['LANG'][LANGUAGE_ID]['NAME']
                        ];
                    });

                $rsTemplates = CComponentUtil::GetTemplatesList('intec:startshop.forms.result.new', $siteTemplate);
            } else {
                return;
            }

            foreach ($rsTemplates as $arTemplate) {
                $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);
            }

            $arTemplateParameters['BLOCKS_BANNER_ORDER_BUTTON_TEXT'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ORDER_BUTTON_TEXT'),
                'TYPE' => 'STRING',
                'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ORDER_BUTTON_TEXT_DEFAULT')
            ];

            $arTemplateParameters['BLOCKS_BANNER_ORDER_FORM_ID'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ORDER_FORM_ID'),
                'TYPE' => 'LIST',
                'VALUES' => $arForms,
                'REFRESH' => 'Y'
            ];

            $arTemplateParameters['BLOCKS_BANNER_ORDER_FORM_TEMPLATE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ORDER_FORM_TEMPLATE'),
                'TYPE' => 'LIST',
                'VALUES' => $arTemplates,
                'DEFAULT' => '.default'
            ];

            $arTemplateParameters['BLOCKS_BANNER_ORDER_FORM_SERVICE'] = [
                'PARENT' => 'BASE',
                'TYPE' => 'LIST',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ORDER_FORM_SERVICE'),
                'VALUES' => $arFields,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            unset($arFields);

            $arTemplateParameters['BLOCKS_BANNER_ORDER_FORM_CONSENT'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BANNER_ORDER_FORM_CONSENT'),
                'TYPE' => 'STRING'
            ];
        }
    }

    if ($arBlocks['icons.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_ICONS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_ICONS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ICONS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_ICONS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ICONS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_ICONS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_ICONS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ICONS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ICONS_1_SVG_USE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_SVG_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['BLOCKS_ICONS_1_SVG_USE'] === 'Y') {
            $arPropertiesIcons = Arrays::fromDBResult(CIBlockProperty::GetList([], [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $arCurrentValues['BLOCKS_ICONS_1_IBLOCK_ID']
            ]))->indexBy('ID');

            $hPropertiesFileIcons = function ($sKey, $arProperty) {
                if ($arProperty['PROPERTY_TYPE'] === 'F')
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                    ];

                return ['skip' => true];
            };

            $arPropertiesFileIcons = $arPropertiesIcons->asArray($hPropertiesFileIcons);

            $arTemplateParameters['BLOCKS_ICONS_1_SVG_PROPERTY'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ICONS_1_SVG_PROPERTY'),
                'TYPE' => 'LIST',
                'VALUES' => $arPropertiesFileIcons,
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }
    }

    if ($arBlocks['description.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_DESCRIPTION_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_DESCRIPTION_1_THEME'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_1_THEME'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesList),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['description.items.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_DESCRIPTION_ITEMS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arBlockIBlock);

        $arTemplateParameters['BLOCKS_DESCRIPTION_ITEMS_1_TEMPLATE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DESCRIPTION_ITEMS_1_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'template.37' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_1'),
                'template.38' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_2')
            ],
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => 'template.37'
        ];
    }

    if ($arBlocks['stages.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_STAGES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_STAGES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_STAGES_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_STAGES_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_STAGES_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['properties.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_PROPERTIES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROPERTIES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROPERTIES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_PROPERTIES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROPERTIES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROPERTIES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROPERTIES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];
    }

    if ($arBlocks['rates.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_RATES_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_RATES_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_RATES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_RATES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_RATES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_RATES_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_RATES_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_RATES_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_RATES_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_RATES_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_RATES_1_TEMPLATE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'template.5' => '1',
                'template.6' => '2'
            ],
            'DEFAULT' => 'template.5',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['BLOCKS_RATES_1_TEMPLATE'])) {
            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.rates',
                $arCurrentValues['BLOCKS_RATES_1_TEMPLATE'],
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_RATES_1_',
                function ($sKey, &$arParameter) {
                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1') . ' ' . $arParameter['NAME'];

                    if (ArrayHelper::isIn($sKey, [
                        'PROPERTY_LIST',
                        'SORT_BY',
                        'ORDER_BY'
                    ])) return true;

                    return false;
                },
                Component::PARAMETERS_MODE_COMPONENT
            ));

            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.rates',
                $arCurrentValues['BLOCKS_RATES_1_TEMPLATE'],
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_RATES_1_',
                function ($sKey, &$arParameter) {
                    if (ArrayHelper::isIn($sKey, [
                        'SETTINGS_USE',
                        'LAZYLOAD_USE'
                    ])) return false;

                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_RATES_1') . ' ' . $arParameter['NAME'];

                    return true;
                },
                Component::PARAMETERS_MODE_TEMPLATE
            ));
        }
    }

    if ($arBlocks['services.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_SERVICES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_SERVICES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_SERVICES_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_SERVICES_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_1_TEMPLATE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'template.22' => '1',
                'template.23' => '2'
            ],
            'DEFAULT' => 'template.22',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['BLOCKS_SERVICES_1_TEMPLATE'])) {
            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.services',
                $arCurrentValues['BLOCKS_SERVICES_1_TEMPLATE'],
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_SERVICES_1_',
                function ($sKey, &$arParameter) {
                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1') . ' ' . $arParameter['NAME'];

                    if (ArrayHelper::isIn($sKey, [
                        'SORT_BY',
                        'SORT_ORDER'
                    ])) return true;

                    return false;
                },
                Component::PARAMETERS_MODE_COMPONENT
            ));

            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.services',
                $arCurrentValues['BLOCKS_SERVICES_1_TEMPLATE'],
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_SERVICES_1_',
                function ($sKey, &$arParameter) {
                    if (ArrayHelper::isIn($sKey, [
                        'SETTINGS_USE',
                        'LAZYLOAD_USE'
                    ])) return false;

                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_1') . ' ' . $arParameter['NAME'];

                    return true;
                },
                Component::PARAMETERS_MODE_TEMPLATE
            ));
        }
    }

    if ($arBlocks['stages.2']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_STAGES_2_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_VIEW'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_VIEW'),
            'TYPE' => 'LIST',
            'VALUES' => [
                '1' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_VIEW_1'),
                '2' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_VIEW_2')
            ],
            'DEFAULT' => '1'
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_BACKGROUND_USE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_BACKGROUND_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_STAGES_2_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_STAGES_2_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAGES_2_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAGES_2_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['documents.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_DOCUMENTS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DOCUMENTS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DOCUMENTS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_DOCUMENTS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DOCUMENTS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_DOCUMENTS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DOCUMENTS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_DOCUMENTS_1_PROPERTY_FILES'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_DOCUMENTS_1_PROPERTY_FILES'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesFile),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['form.1']['ACTIVE']) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.widget',
            'form.6',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_FORM_1_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FORM_1').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'PROPERTY_HEADER',
                    'PROPERTY_DESCRIPTION',
                    'SORT_BY',
                    'ORDER_BY'
                ])) return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ));

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.widget',
            'form.4',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_FORM_1_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FORM_1').' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }

    if ($arBlocks['certificates.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_CERTIFICATES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_CERTIFICATES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_CERTIFICATES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_CERTIFICATES_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_CERTIFICATES_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_CERTIFICATES_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_CERTIFICATES_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_CERTIFICATES_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_CERTIFICATES_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['staff.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_STAFF_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_DESCRIPTION'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_PROPERTY_DESCRIPTION'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_PROPERTY_DESCRIPTION'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_STAFF_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_STAFF_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '[' . $arIBlock['ID'] . '] ' . $arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_STAFF_1_VIEW'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_VIEW'),
            'TYPE' => 'LIST',
            'VALUES' => [
                1 => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_1'),
                2 => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_2'),
                3 => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_3')
            ],
            'DEFAULT' => '1',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['BLOCKS_STAFF_1_VIEW'] === '1') {
            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.staff',
                'template.5',
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_STAFF_1_',
                function ($sKey, &$arParameter) {
                    if (ArrayHelper::isIn($sKey, [
                        'SETTINGS_USE',
                        'LAZYLOAD_USE'
                    ])) return false;

                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1').' '.$arParameter['NAME'];

                    return true;
                },
                Component::PARAMETERS_MODE_TEMPLATE
            ));
        } else {
            $arBlockIBlock = null;

            if (!empty($arCurrentValues['BLOCKS_STAFF_1_IBLOCK_ID']))
                $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_STAFF_1_IBLOCK_ID']);

            if (!empty($arBlockIBlock)) {
                $arBlockProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
                    'SORT' => 'ASC'
                ], [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $arBlockIBlock['ID']
                ]));

                $arTemplateParameters['BLOCKS_STAFF_1_PROPERTY_POSITION'] = [
                    'PARENT' => 'DATA_SOURCE',
                    'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_STAFF_1_PROPERTY_POSITION'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arBlockProperties->asArray($hPropertiesString),
                    'ADDITIONAL_VALUES' => 'Y'
                ];

                unset($arBlockProperties);
            }
        }
    }

    if ($arBlocks['faq.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_FAQ_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_FAQ_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_FAQ_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_FAQ_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_FAQ_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_FAQ_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_FAQ_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_FAQ_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FAQ_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['advantages.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_ADVANTAGES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_ADVANTAGES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ADVANTAGES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_ADVANTAGES_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ADVANTAGES_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_ADVANTAGES_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_ADVANTAGES_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_ADVANTAGES_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_ADVANTAGES_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['videos.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_VIDEOS_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_VIDEOS_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_VIDEOS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_DESCRIPTION'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_DESCRIPTION_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_DESCRIPTION_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_PROPERTY_DESCRIPTION'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_PROPERTY_DESCRIPTION'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_VIDEOS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_VIDEOS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arBlockIBlock)) {
            $arBlockProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
                'SORT' => 'ASC'
            ], [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $arBlockIBlock['ID']
            ]));

            $arTemplateParameters['BLOCKS_VIDEOS_1_PROPERTY_LINK'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_PROPERTY_LINK'),
                'TYPE' => 'LIST',
                'VALUES' => $arBlockProperties->asArray($hPropertiesString),
                'ADDITIONAL_VALUES' => 'Y'
            ];

            unset($arBlockProperties);
        }

        $arTemplateParameters['BLOCKS_VIDEOS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_VIDEOS_1_TEMPLATE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIDEOS_1_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'template.4' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_1'),
                'template.5' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_2'),
                'template.3' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_3')
            ],
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => 'template.3'
        ];

        unset($arBlockIBlock);
    }

    if ($arBlocks['gallery.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_GALLERY_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_GALLERY_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_GALLERY_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_GALLERY_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_GALLERY_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_GALLERY_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_GALLERY_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_GALLERY_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_GALLERY_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_GALLERY_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arBlockIBlock);

        $arTemplateParameters['BLOCKS_GALLERY_1_TEMPLATE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_GALLERY_1_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'template.5' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_1'),
                'template.6' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_2'),
                'template.7' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_3')
            ],
            'ADDITIONAL_VALUES' => 'Y',
            'DEFAULT' => 'template.7'
        ];
    }

    if ($arBlocks['projects.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_PROJECTS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_PROJECTS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_PROJECTS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    if ($arBlocks['reviews.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_REVIEWS_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_REVIEWS_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_REVIEWS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_REVIEWS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_REVIEWS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_REVIEWS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_REVIEWS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_REVIEWS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_REVIEWS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arBlockIBlock)) {
            $arBlockProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
                'SORT' => 'ASC'
            ], [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $arBlockIBlock['ID']
            ]));

            $arTemplateParameters['BLOCKS_REVIEWS_1_PROPERTY_RATING'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_PROPERTY_RATING'),
                'TYPE' => 'LIST',
                'VALUES' => $arBlockProperties->asArray($hPropertiesList),
                'ADDITIONAL_VALUES' => 'Y'
            ];

            unset($arBlockProperties);
        }

        $arTemplateParameters['BLOCKS_REVIEWS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_REVIEWS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arBlockIBlock);
    }

    if ($arBlocks['products.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_PRODUCTS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PRODUCTS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PRODUCTS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_PRODUCTS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PRODUCTS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PRODUCTS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PRODUCTS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_PRODUCTS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PRODUCTS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'bitrix:catalog.section',
            'products.small.1',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_PRODUCTS_1_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PRODUCTS_1').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'IBLOCK_TYPE',
                    'IBLOCK_ID',
                    'PRICE_CODE'

                ])) return true;

                return false;
            },
            Component::PARAMETERS_MODE_BOTH
        ));
    }

    if ($arBlocks['services.2']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_SERVICES_2_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_SERVICES_2_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_2_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_SERVICES_2_PRICE_OLD_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_PRICE_OLD_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
        if($arCurrentValues['BLOCKS_SERVICES_2_PRICE_OLD_SHOW']){
            $arTemplateParameters['BLOCKS_SERVICES_2_PRICE_OLD'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_PROPERTY_PRICE_OLD'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesString),
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }

        $arTemplateParameters['BLOCKS_SERVICES_2_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:iblock.elements',
            'tiles.landing.3',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_SERVICES_2_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SERVICES_2').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'SORT_BY',
                    'SORT_ORDER'
                ])) return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ));
    }

    if ($arBlocks['news.1']['ACTIVE']) {
        $arTemplateParameters['BLOCKS_NEWS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_DESCRIPTION'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_PROPERTY_DESCRIPTION'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_PROPERTY_DESCRIPTION'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_NEWS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_NEWS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_NEWS_1_VIEW'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1_VIEW'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'template.9' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_1'),
                'template.10' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_2'),
                'template.11' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_VIEW_3')
            ],
            'ADDITIONAL_VALUES' => 'template.9',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.news',
            $arCurrentValues['BLOCKS_NEWS_1_VIEW'],
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_NEWS_1_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1') . ' ' . $arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'DATE_FORMAT',
                ])) return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ));

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.news',
            $arCurrentValues['BLOCKS_NEWS_1_VIEW'],
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_NEWS_1_',
            function ($sKey, &$arParameter) {
                if (ArrayHelper::isIn($sKey, [
                    'SETTINGS_USE',
                    'LAZYLOAD_USE'
                ])) return false;

                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_NEWS_1').' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }

    if ($arBlocks['brands.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_BRANDS_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_BRANDS_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_BRANDS_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_DESCRIPTION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_DESCRIPTION'),
            'TYPE' => 'STRING',
            'DEFAULT' => ''
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_PROPERTY_DESCRIPTION'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_PROPERTY_DESCRIPTION'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_BRANDS_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_BRANDS_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_BRANDS_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_BRANDS_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arBlockIBlock);
    }

    if ($arBlocks['projects.2']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_PROJECTS_2_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_PROJECTS_2_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_PROJECTS_2_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_2_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_2_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_2_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_PROJECTS_2_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_PROJECTS_2_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_2_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_PROJECTS_2_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.projects',
            'template.4',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_PROJECTS_2_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2') . ' ' . $arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'PROPERTY_LIST',
                    'SORT_BY',
                    'ORDER_BY'
                ])) return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ));

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.projects',
            'template.4',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_PROJECTS_2_',
            function ($sKey, &$arParameter) {
                if (ArrayHelper::isIn($sKey, [
                    'SETTINGS_USE',
                    'LAZYLOAD_USE'
                ])) return false;

                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_PROJECTS_2') . ' ' . $arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }

    if ($arBlocks['shares.1']['ACTIVE']) {
        $arBlockIBlock = null;

        if (!empty($arCurrentValues['BLOCKS_SHARES_1_IBLOCK_ID']))
            $arBlockIBlock = $arIBlocks->get($arCurrentValues['BLOCKS_SHARES_1_IBLOCK_ID']);

        $arTemplateParameters['BLOCKS_SHARES_1_HEADER'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_HEADER_DEFAULT')
        ];

        $arTemplateParameters['BLOCKS_SHARES_1_HEADER_POSITION'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_HEADER_POSITION'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_HEADER_POSITION_CENTER')
            ],
            'DEFAULT' => 'left'
        ];

        $arTemplateParameters['BLOCKS_SHARES_1_IBLOCK_TYPE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_IBLOCK_TYPE'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocksTypes,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SHARES_1_IBLOCK_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) use (&$arCurrentValues) {
                if (!empty($arCurrentValues['BLOCKS_SHARES_1_IBLOCK_TYPE']))
                    if ($arIBlock['IBLOCK_TYPE_ID'] != $arCurrentValues['BLOCKS_SHARES_1_IBLOCK_TYPE'])
                        return ['skip' => true];

                return [
                    'key' => $arIBlock['ID'],
                    'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
                ];
            }),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SHARES_1_PROPERTY_HEADER'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_PROPERTY_HEADER'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesString),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['BLOCKS_SHARES_1_PROPERTY_ELEMENTS'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_PROPERTY_ELEMENTS'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $sTemplate = 'template.';

        $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
            'intec.universe:main.shares',
            $siteTemplate
        ))->asArray(function ($iIndex, $arTemplate) use (&$sTemplate) {
            if (!StringHelper::startsWith($arTemplate['NAME'], $sTemplate))
                return ['skip' => true];

            $sName = StringHelper::cut(
                $arTemplate['NAME'],
                StringHelper::length($sTemplate)
            );

            return [
                'key' => $sName,
                'value' => $sName
            ];
        });

        $sTemplate = ArrayHelper::getValue($arCurrentValues, 'BLOCKS_SHARES_1_TEMPLATE');
        $sTemplate = ArrayHelper::fromRange($arTemplates, $sTemplate, false, false);

        if (!empty($sTemplate))
            $sTemplate = 'catalog.'.$sTemplate;

        $arTemplateParameters['BLOCKS_SHARES_1_TEMPLATE'] = [
            'PARENT' => 'LIST_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1_TEMPLATE'),
            'TYPE' => 'LIST',
            'VALUES' => $arTemplates,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['BLOCKS_SHARES_1_TEMPLATE'])) {
            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.shares',
                $sTemplate,
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_SHARES_1_',
                function ($sKey, &$arParameter) {
                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1') . ' ' . $arParameter['NAME'];

                    return true;
                },
                Component::PARAMETERS_MODE_COMPONENT
            ));

            $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
                'intec.universe:main.shares',
                $sTemplate,
                $siteTemplate,
                $arCurrentValues,
                'BLOCKS_SHARES_1_',
                function ($sKey, &$arParameter) {
                    if (ArrayHelper::isIn($sKey, [
                        'SETTINGS_USE',
                        'LAZYLOAD_USE'
                    ])) return false;

                    $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_SHARES_1') . ' ' . $arParameter['NAME'];

                    return true;
                },
                Component::PARAMETERS_MODE_TEMPLATE
            ));
        }
    }

    if ($arBlocks['form.2']['ACTIVE']) {
        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.widget',
            'form.6',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_FORM_2_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FORM_2').' '.$arParameter['NAME'];

                if (ArrayHelper::isIn($sKey, [
                    'PROPERTY_HEADER',
                    'PROPERTY_DESCRIPTION',
                    'SORT_BY',
                    'ORDER_BY'
                ])) return true;

                return false;
            },
            Component::PARAMETERS_MODE_COMPONENT
        ));

        $arTemplateParameters = ArrayHelper::merge($arTemplateParameters, Component::getParameters(
            'intec.universe:main.widget',
            'form.4',
            $siteTemplate,
            $arCurrentValues,
            'BLOCKS_FORM_2_',
            function ($sKey, &$arParameter) {
                $arParameter['NAME'] = Loc::getMessage('C_CATALOG_SERVICES_DEFAULT_4_BLOCKS_FORM_2').' '.$arParameter['NAME'];

                return true;
            },
            Component::PARAMETERS_MODE_TEMPLATE
        ));
    }
}