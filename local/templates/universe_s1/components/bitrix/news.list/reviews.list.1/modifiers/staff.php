<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arCollection
 */

$arStaff = new ElementsQuery();

$arStaff->setIBlockType($arParams['STAFF_IBLOCK_TYPE'])
    ->setIBlockId($arParams['STAFF_IBLOCK_ID'])
    ->setIBlockElementsId($arCollection['STAFF'])
    ->setWithProperties(false)
    ->setFilter([
        'ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R'
    ])
    ->setSelect([
        'ID',
        'IBLOCK_ID',
        'NAME',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'PROPERTY_'.strtoupper($arParams['STAFF_PROPERTY_POSITION'])
    ]);

$arStaff = $arStaff->execute()->asArray();
$arStaffResult = [];

foreach ($arStaff as $staff) {
    $result = [
        'NAME' => null,
        'PICTURE' => null,
        'POSITION' => null
    ];

    if (!empty($staff['NAME']))
        $result['NAME'] = $staff['NAME'];

    if (!empty($staff['PREVIEW_PICTURE'])) {
        $arCollection['FILES'][] = $staff['PREVIEW_PICTURE'];
        $result['PICTURE'] = $staff['PREVIEW_PICTURE'];
    } else if (!empty($staff['DETAIL_PICTURE'])) {
        $arCollection['FILES'][] = $staff['DETAIL_PICTURE'];
        $result['PICTURE'] = $staff['DETAIL_PICTURE'];
    }

    if (!empty($staff['PROPERTY_'.strtoupper($arParams['STAFF_PROPERTY_POSITION']).'_VALUE'])) {
        $position = $staff['PROPERTY_'.strtoupper($arParams['STAFF_PROPERTY_POSITION']).'_VALUE'];

        if (Type::isArray($position))
            $position = ArrayHelper::getFirstValue($position);

        $result['POSITION'] = $position;
    }

    $arStaffResult[$staff['ID']] = $result;

    unset($result, $position);
}

if (!empty($arStaffResult))
    $arCollection['STAFF'] = $arStaffResult;

unset($arStaff, $arStaffResult, $staff);