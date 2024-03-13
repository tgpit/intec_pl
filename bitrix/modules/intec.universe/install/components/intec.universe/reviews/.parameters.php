<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$sSite = $_REQUEST['site'];

if (empty($sSite) && !empty($_REQUEST['src_site']))
    $sSite = $_REQUEST['src_site'];

$arIBlockTypes = CIBlockParameters::GetIBlockTypes();
$arIBlockListSelect = [
    'SORT' => [
        'SORT' => 'ASC'
    ],
    'FILTER' => [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite
    ]
];
$arIBlockList = Arrays::fromDBResult(CIBlock::GetList(
    $arIBlockListSelect['SORT'],
    $arIBlockListSelect['FILTER']
));

if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $arIBlockList = $arIBlockList->asArray(function ($key, $value) use (&$arCurrentValues) {
        if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['IBLOCK_TYPE'])
            return [
                'key' => $value['ID'],
                'value' => '['.$value['ID'].'] '.$value['NAME']
            ];

        return ['skip' => true];
    });
} else {
    $arIBlockList = $arIBlockList->asArray(function ($key, $value) {
        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['NAME']
        ];
    });
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arPropertiesSelect = [
        'SORT' => [
            'SORT' => 'ASC'
        ],
        'FILTER' => [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]
    ];
    
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
        $arPropertiesSelect['SORT'],
        $arPropertiesSelect['FILTER']
    ));

    $hPropertyLinkedSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'E' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyText = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'S' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyListSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] == 'L' && $arValue['LIST_TYPE'] == 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyLinkedSingle = $arProperties->asArray($hPropertyLinkedSingle);
    $arPropertyText = $arProperties->asArray($hPropertyText);
    $arPropertyListSingle = $arProperties->asArray($hPropertyListSingle);
}

$arParameters = [];

$arParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlockTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['IBLOCK_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlockList,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arParameters['ELEMENTS_COUNT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_ELEMENTS_COUNT'),
    'TYPE' => 'STRING'
];
$arParameters['MODE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_MODE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'default' => Loc::getMessage('C_REVIEWS_COMPONENT_MODE_DEFAULT'),
        'linked' => Loc::getMessage('C_REVIEWS_COMPONENT_MODE_LINKED')
    ],
    'DEFAULT' => 'default',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arParameters['FORM_USE'] = [
        'PARENT' => 'FORM',
        'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['FORM_USE'] === 'Y') {
        $arParameters['CAPTCHA_USE'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_CAPTCHA_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arParameters['FORM_ACCESS'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ACCESS'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'registered' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ACCESS_REGISTERED'),
                'all' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ACCESS_ALL')
            ],
            'DEFAULT' => 'registered'
        ];

        if ($arCurrentValues['FORM_ACCESS'] === 'registered') {
            $arParameters['FORM_ACCESS_AUTHORIZATION'] = [
                'PARENT' => 'FORM',
                'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ACCESS_AUTHORIZATION'),
                'TYPE' => 'STRING',
                'DEFAULT' => '#SITE_DIR#personal/profile/'
            ];
        }

        $arFormFields = ArrayHelper::merge($arPropertyText, $arPropertyListSingle);

        $arParameters['PROPERTY_FORM_FIELDS'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_PROPERTY_FORM_FIELDS'),
            'TYPE' => 'LIST',
            'VALUES' => $arFormFields,
            'ADDITIONAL_VALUES' => 'Y',
            'MULTIPLE' => 'Y',
            'REFRESH' => 'Y'
        ];
        $arFormFieldsSelected = [];

        if (Type::isArray($arCurrentValues['PROPERTY_FORM_FIELDS']))
            $arFormFieldsSelected = array_filter($arCurrentValues['PROPERTY_FORM_FIELDS']);

        $arFormFieldsRequired = [];

        if (!empty($arFormFieldsSelected)) {
            foreach ($arFormFieldsSelected as $property) {
                if (ArrayHelper::keyExists($property, $arFormFields))
                    $arFormFieldsRequired[$property] = $arFormFields[$property];
            }

            unset($property);
        }

        $arParameters['PROPERTY_FORM_REQUIRED'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_PROPERTY_FORM_REQUIRED'),
            'TYPE' => 'LIST',
            'VALUES' => $arFormFieldsRequired,
            'ADDITIONAL_VALUES' => 'Y',
            'MULTIPLE' => 'Y'
        ];

        unset($arFormFields, $arFormFieldsSelected, $arFormFieldsRequired);

        $arParameters['FORM_ADD_MODE'] = [
            'PARENT' => 'FORM',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ADD_MODE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'disabled' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ADD_MODE_DISABLED'),
                'active' => Loc::getMessage('C_REVIEWS_COMPONENT_FORM_ADD_MODE_ACTIVE')
            ],
            'DEFAULT' => 'disabled'
        ];
    }

    if ($arCurrentValues['MODE'] === 'linked') {
        $arParameters['PROPERTY_ID'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_PROPERTY_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyLinkedSingle,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['PROPERTY_ID'])) {
            $arParameters['ID'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_ID'),
                'TYPE' => 'STRING'
            ];
        }
    }

    if ($arCurrentValues['FORM_USE'] === 'Y') {
        $arParameters['ITEMS_HIDE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_ITEMS_HIDE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
    }
}

$arParameters['NAVIGATION_USE'] = [
    'PARENT' => 'NAVIGATION',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_NAVIGATION_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['NAVIGATION_USE'] === 'Y') {
    $arParameters['NAVIGATION_ID'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_NAVIGATION_ID'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'reviews'
    ];
    $arParameters['NAVIGATION_MODE'] = [
        'PARENT' => 'NAVIGATION',
        'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_NAVIGATION_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'standard' => Loc::getMessage('C_REVIEWS_COMPONENT_NAVIGATION_MODE_STANDARD'),
            'ajax' => Loc::getMessage('C_REVIEWS_COMPONENT_NAVIGATION_MODE_AJAX')
        ],
        'DEFAULT' => 'ajax',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['NAVIGATION_MODE'] === 'standard') {
        $arParameters['NAVIGATION_ALL'] = [
            'PARENT' => 'NAVIGATION',
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_NAVIGATION_ALL'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }

    include(__DIR__.'/parameters/navigation.template.php');
}

$arParameters['SORT_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_SORT_BY'),
    'TYPE' => 'LIST',
    'VALUES' => CIBlockParameters::GetElementSortFields(),
    'DEFAULT' => 'SORT',
    'ADDITIONAL_VALUES' => 'Y'
];
$arParameters['ORDER_BY'] = [
    'PARENT' => 'SORT',
    'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_ORDER_BY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'ASC' => Loc::getMessage('C_REVIEWS_COMPONENT_ORDER_BY_ASC'),
        'DESC' => Loc::getMessage('C_REVIEWS_COMPONENT_ORDER_BY_DESC')
    ],
    'DEFAULT' => 'ASC'
];
$arParameters['CACHE_TIME'] = [];

$arComponentParameters = [
    'GROUPS' => [
        'FORM' => [
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_GROUPS_FORM'),
            'SORT' => 210
        ],
        'NAVIGATION' => [
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_GROUPS_NAVIGATION'),
            'SORT' => 410
        ],
        'SORT' => [
            'NAME' => Loc::getMessage('C_REVIEWS_COMPONENT_GROUPS_SORT'),
            'SORT' => 800
        ]
    ],
    'PARAMETERS' => $arParameters
];