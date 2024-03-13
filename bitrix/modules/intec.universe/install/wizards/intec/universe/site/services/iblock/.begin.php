<? include(__DIR__.'/../.begin.php') ?>
<?

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\base\Collection;
use intec\core\collections\Arrays;
use intec\core\db\Query;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\io\Path;

/**
 * @var Collection $data
 * @var CWizardBase $wizard
 * @var CWizardStep $this
 */

if (!Loader::includeModule('iblock'))
    return;

$import = function ($code, $type, $file, $fields = [], $permissions = []) use ($wizard) {
    $import = $wizard->GetVar('systemImportIBlocks') === 'Y';

    $path = WIZARD_SERVICE_RELATIVE_PATH.'/data/'.LANGUAGE_ID.'/'.$file.'.xml';
    $site = WIZARD_SITE_ID;

    $permissions = ArrayHelper::merge([
        1 => 'X',
        2 => 'R'
    ], $permissions);

    $arGroupOwners = CGroup::GetList(
        $by = '',
        $order = '',
        ['STRING_ID' => 'site_owners_group']
    )->Fetch();

    if (!empty($arGroupOwners) && empty($permissions[$arGroupOwners['ID']]))
        $permissions[$arGroupOwners['ID']] = 'W';

    $iBlock = CIBlock::GetList(array(), array(
        'XML_ID' => $code,
        'TYPE' => $type
    ))->GetNext();

    if ($import && empty($iBlock)) {
        $iBlockId = WizardServices::ImportIBlockFromXML(
            $path,
            $code,
            $type,
            $site,
            $permissions
        );

        if (empty($iBlockId))
            return null;

        $fields = ArrayHelper::merge([
            'IBLOCK_SECTION' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'ACTIVE' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => 'Y'
            ],
            'ACTIVE_FROM' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => '=today'
            ],
            'ACTIVE_TO' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'SORT' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'NAME' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => ''
            ],
            'PREVIEW_PICTURE' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => [
                    'FROM_DETAIL' => 'N',
                    'SCALE' => 'N',
                    'WIDTH' => '',
                    'HEIGHT' => '',
                    'IGNORE_ERRORS' => 'N',
                    'METHOD' => 'resample',
                    'COMPRESSION' => 95,
                    'DELETE_WITH_DETAIL' => 'N',
                    'UPDATE_WITH_DETAIL' => 'N'
                ]
            ],
            'PREVIEW_TEXT_TYPE' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => 'text'
            ],
            'PREVIEW_TEXT' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'DETAIL_PICTURE' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => [
                    'SCALE' => 'N',
                    'WIDTH' => '',
                    'HEIGHT' => '',
                    'IGNORE_ERRORS' => 'N',
                    'METHOD' => 'resample',
                    'COMPRESSION' => 95
                ]
            ],
            'DETAIL_TEXT_TYPE' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => 'text'
            ],
            'DETAIL_TEXT' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'XML_ID' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'CODE' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => [
                    'UNIQUE' => 'Y',
                    'TRANSLITERATION' => 'Y',
                    'TRANS_LEN' => 100,
                    'TRANS_CASE' => 'L',
                    'TRANS_SPACE' => '_',
                    'TRANS_OTHER' => '_',
                    'TRANS_EAT' => 'Y',
                    'USE_GOOGLE' => 'Y'
                ]
            ],
            'TAGS' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'SECTION_NAME' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => ''
            ],
            'SECTION_PICTURE' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => [
                    'FROM_DETAIL' => 'N',
                    'SCALE' => 'N',
                    'WIDTH' => '',
                    'HEIGHT' => '',
                    'IGNORE_ERRORS' => 'N',
                    'METHOD' => 'resample',
                    'COMPRESSION' => 95,
                    'DELETE_WITH_DETAIL' => 'N',
                    'UPDATE_WITH_DETAIL' => 'N'
                ]
            ],
            'SECTION_DESCRIPTION_TYPE' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => 'text'
            ],
            'SECTION_DESCRIPTION' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'SECTION_DETAIL_PICTURE' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => [
                    'SCALE' => 'N',
                    'WIDTH' => '',
                    'HEIGHT' => '',
                    'IGNORE_ERRORS' => 'N',
                    'METHOD' => 'resample',
                    'COMPRESSION' => 95
                ]
            ],
            'SECTION_XML_ID' => [
                'IS_REQUIRED' => 'N',
                'DEFAULT_VALUE' => ''
            ],
            'SECTION_CODE' => [
                'IS_REQUIRED' => 'Y',
                'DEFAULT_VALUE' => [
                    'UNIQUE' => 'Y',
                    'TRANSLITERATION' => 'Y',
                    'TRANS_LEN' => 100,
                    'TRANS_CASE' => 'L',
                    'TRANS_SPACE' => '_',
                    'TRANS_OTHER' => '_',
                    'TRANS_EAT' => 'Y',
                    'USE_GOOGLE' => 'N'
                ]
            ]
        ], $fields);

        (new CIBlock())->Update($iBlockId, [
            'ACTIVE' => 'Y',
            'CODE' => $code,
            'XML_ID' => $code,
            'FIELDS' => $fields
        ]);

        $iBlock = CIBlock::GetByID($iBlockId)->GetNext();

        if (!empty($iBlock)) {
            $arFiles = [];
            $arProperties = [];
            $rsProperties = CUserTypeEntity::GetList([], [
                'ENTITY_ID' => 'IBLOCK_'.$iBlock['ID'].'_SECTION',
                'USER_TYPE_ID' => 'file'
            ]);

            while ($arProperty = $rsProperties->Fetch())
                $arProperties[] = $arProperty;

            if (!empty($arProperties)) {
                $rsSections = CIBlockSection::GetList([], [
                    'IBLOCK_ID' => $iBlock['ID']
                ], false, [
                    'ID',
                    'IBLOCK_ID',
                    'UF_*'
                ]);

                while ($arSection = $rsSections->Fetch()) {
                    foreach ($arProperties as $arProperty) {
                        $mValue = $arSection[$arProperty['FIELD_NAME']];

                        if (!empty($mValue)) {
                            if (Type::isArray($mValue)) {
                                foreach ($mValue as $iValue)
                                    $arFiles[] = Type::toInteger($iValue);
                            } else {
                                $arFiles[] = Type::toInteger($mValue);
                            }
                        }
                    }
                }

                unset($rsSections, $arSection);
            }

            $arProperties = [];
            $rsProperties = CIBlockProperty::GetList([], [
                'IBLOCK_ID' => $iBlock['ID'],
                'PROPERTY_TYPE' => 'F'
            ]);

            while ($arProperty = $rsProperties->Fetch())
                $arProperties[] = $arProperty;

            $rsElements = CIBlockElement::GetList([], [
                'IBLOCK_ID' => $iBlock['ID']
            ]);

            while ($rsElement = $rsElements->GetNextElement()) {
                $arElement = $rsElement->GetFields();

                if (!empty($arElement['PREVIEW_PICTURE']))
                    $arFiles[] = $arElement['PREVIEW_PICTURE'];

                if (!empty($arElement['DETAIL_PICTURE']))
                    $arFiles[] = $arElement['DETAIL_PICTURE'];

                $arElement['PROPERTIES'] = $rsElement->GetProperties();

                foreach ($arProperties as $arProperty) {
                    $sCode = $arProperty['ID'];

                    if (!empty($arProperty['CODE']) || Type::isNumeric($arProperty['CODE']))
                        $sCode = $arProperty['CODE'];

                    $mValue = $arElement['PROPERTIES'][$sCode]['VALUE'];

                    if (!empty($mValue)) {
                        if (Type::isArray($mValue)) {
                            foreach ($mValue as $iValue)
                                $arFiles[] = Type::toInteger($iValue);
                        } else {
                            $arFiles[] = Type::toInteger($mValue);
                        }
                    }
                }
            }

            unset($arElement, $rsElement, $rsElements, $sCode, $mValue, $iValue);

            $arFiles = array_filter($arFiles);

            if (!empty($arFiles)) {
                $oDatabase = Core::$app->db;
                $arRows = (new Query())->select(['ID', 'FILE_NAME'])->from('b_file')->where(['in', 'id', $arFiles])->all($oDatabase);

                foreach ($arRows as $arRow) {
                    $sExtension = Path::getExtensionFrom($arRow['FILE_NAME']);

                    if ($sExtension === 'svg') {
                        $oDatabase->createCommand()->update('b_file', ['CONTENT_TYPE' => 'image/svg+xml'], [
                            'ID' => $arRow['ID']
                        ])->execute();
                    }
                }

                unset($oDatabase, $arRows, $arRow, $sExtension);
            }

            unset($arProperties, $rsProperties, $arFiles);
        }
    }

    if (!empty($iBlock))
        return $iBlock;

    return null;
};

$linkPropertySections = function ($iIBlockId, $sPropertyCode, $iIBlockToId, $arMap) {
    $arIBlock = CIBlock::GetList([], ['ID' => $iIBlockId])->Fetch();
    $arIBlockTo = CIBlock::GetList([], ['ID' => $iIBlockToId])->Fetch();

    if (empty($arIBlock) || empty($arIBlockTo))
        return;

    $arProperty = CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $arIBlock['ID'],
        'CODE' => $sPropertyCode
    ])->Fetch();

    if (empty($arProperty))
        return;

    (new CIBlockProperty())->Update($arProperty['ID'], [
        'LINK_IBLOCK_TYPE_ID' => $arIBlockTo['TYPE'],
        'LINK_IBLOCK_ID' => $arIBlockTo['ID']
    ]);

    $arElements = Collection::from([]);
    $arMappingSections = Collection::from([]);

    if (empty($arMap))
        return;

    foreach ($arMap as $sElementCode => $mMappingSections) {
        if (!$arElements->has($sElementCode))
            $arElements->add($sElementCode);

        if (Type::isArray($mMappingSections)) {
            foreach ($mMappingSections as $sMappingSectionCode) {
                if (!$arMappingSections->has($sMappingSectionCode))
                    $arMappingSections->add($sMappingSectionCode);
            }
        } else if (!$arMappingSections->has($mMappingSections)) {
            $arMappingSections->add($mMappingSections);
        }
    }

    unset($sMappingSectionCode);
    unset($mMappingSections);
    unset($sElementCode);

    if ($arElements->isEmpty() || $arMappingSections->isEmpty())
        return;

    $arElements = Arrays::fromDBResult(CIBlockElement::GetList([], [
        'IBLOCK_ID' => $arIBlock['ID'],
        'CODE' => $arElements->asArray()
    ]))->indexBy('CODE');

    $arMappingSections = Arrays::fromDBResult(CIBlockSection::GetList([], [
        'IBLOCK_ID' => $arIBlockTo['ID'],
        'CODE' => $arMappingSections->asArray()
    ]))->indexBy('CODE');

    foreach ($arMap as $sElementCode => $mMappingSections) {
        $arElement = $arElements->get($sElementCode);
        $mValues = [];

        if (empty($arElement))
            continue;

        if (Type::isArray($mMappingSections)) {
            foreach ($mMappingSections as $sMappingSectionCode) {
                $arMappingSection = $arMappingSections->get($sMappingSectionCode);

                if (empty($arMappingSection))
                    continue;

                $mValues[] = $arMappingSection['ID'];
            }
        } else {
            $arMappingSection = $arMappingSections->get($mMappingSections);

            if (!empty($arMappingSection))
                $mValues = $arMappingSection['ID'];
        }

        CIBlockElement::SetPropertyValuesEx($arElement['ID'], $arIBlock['ID'], [
            $arProperty['ID'] => $mValues
        ]);
    }
};

$linkPropertyElements = function ($iIBlockId, $sPropertyCode, $iIBlockToId, $arMap) {
    $arIBlock = CIBlock::GetList([], ['ID' => $iIBlockId])->Fetch();
    $arIBlockTo = CIBlock::GetList([], ['ID' => $iIBlockToId])->Fetch();

    if (empty($arIBlock) || empty($arIBlockTo))
        return;

    $arProperty = CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $arIBlock['ID'],
        'CODE' => $sPropertyCode
    ])->Fetch();

    if (empty($arProperty))
        return;

    (new CIBlockProperty())->Update($arProperty['ID'], [
        'LINK_IBLOCK_TYPE_ID' => $arIBlockTo['TYPE'],
        'LINK_IBLOCK_ID' => $arIBlockTo['ID']
    ]);

    $arElements = Collection::from([]);
    $arMappingElements = Collection::from([]);

    if (empty($arMap))
        return;

    foreach ($arMap as $sElementCode => $mMappingElements) {
        if (!$arElements->has($sElementCode))
            $arElements->add($sElementCode);

        if (Type::isArray($mMappingElements)) {
            foreach ($mMappingElements as $sMappingElementCode) {
                if (!$arMappingElements->has($sMappingElementCode))
                    $arMappingElements->add($sMappingElementCode);
            }
        } else if (!$arMappingElements->has($mMappingElements)) {
            $arMappingElements->add($mMappingElements);
        }
    }

    unset($sMappingElementCode);
    unset($mMappingElements);
    unset($sElementCode);

    if ($arElements->isEmpty() || $arMappingElements->isEmpty())
        return;

    $arElements = Arrays::fromDBResult(CIBlockElement::GetList([], [
        'IBLOCK_ID' => $arIBlock['ID'],
        'CODE' => $arElements->asArray()
    ]))->indexBy('CODE');

    $arMappingElements = Arrays::fromDBResult(CIBlockElement::GetList([], [
        'IBLOCK_ID' => $arIBlockTo['ID'],
        'CODE' => $arMappingElements->asArray()
    ]))->indexBy('CODE');

    foreach ($arMap as $sElementCode => $mMappingElements) {
        $arElement = $arElements->get($sElementCode);
        $mValues = [];

        if (empty($arElement))
            continue;

        if (Type::isArray($mMappingElements)) {
            foreach ($mMappingElements as $sMappingElementCode) {
                $arMappingElement = $arMappingElements->get($sMappingElementCode);

                if (empty($arMappingElement))
                    continue;

                $mValues[] = $arMappingElement['ID'];
            }
        } else {
            $arMappingElement = $arMappingElements->get($mMappingElements);

            if (!empty($arMappingElement))
                $mValues = $arMappingElement['ID'];
        }

        CIBlockElement::SetPropertyValuesEx($arElement['ID'], $arIBlock['ID'], [
            $arProperty['ID'] => $mValues
        ]);
    }
};

$linkPropertyElementsMultipleSource = function ($iIBlockId, $sPropertyCode, $arMap) {
    $arIBlock = CIBlock::GetList([], ['ID' => $iIBlockId])->Fetch();

    if (empty($arIBlock))
        return;

    $arProperty = CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $arIBlock['ID'],
        'CODE' => $sPropertyCode
    ])->Fetch();

    if (empty($arProperty))
        return;

    foreach ($arMap as $sElementCode => $arMappingElements) {
        $arValues = [];
        $arElement = CIBlockElement::GetList([], [
            'IBLOCK_ID' => $arIBlock['ID'],
            'CODE' => $sElementCode
        ])->Fetch();

        if (empty($arElement))
            continue;

        foreach ($arMappingElements as $arMappingElement) {
            if (count($arMappingElement) !== 2)
                continue;

            $arMappingElement = CIBlockElement::GetList([], [
                'IBLOCK_ID' => $arMappingElement[0],
                'CODE' => $arMappingElement[1]
            ])->Fetch();

            if (!empty($arMappingElement))
                $arValues[] = $arMappingElement['ID'];
        }

        CIBlockElement::SetPropertyValuesEx($arElement['ID'], $arIBlock['ID'], [
            $arProperty['ID'] => $arValues
        ]);
    }
};