<?php
namespace intec\importexport\models\excel\import;


use intec\core\collections\Arrays;
use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;
use intec\importexport\models\excel\IBlockSelections;
use intec\importexport\models\excel\Price;
use Bitrix\Main\Text\Encoding;

class ImportHelper
{
    public static function saveUploadFile ($file, $old_id, $folder = null)
    {
        $uploadTmpPath = '/intec.importexport/import/' . $folder;

        $saveArray = [
            'name' => self::getFileParameter($file, 'name'),
            'size' => self::getFileParameter($file, 'size'),
            'tmp_name' => self::getFileParameter($file, 'tmp_name'),
            'type' => self::getFileParameter($file, 'type'),
            'old_file' => $old_id,
            'MODULE_ID' => 'intec.importexport'
        ];

        $type = self::getFileParameter($file, 'name');
        $type = self::typeCheckOnName($type);

        if (!$type)
            return false;

        return CFileCustom::SaveFile($saveArray, $uploadTmpPath, false, false, '', false);
    }

    public static function typeCheckOnName ($name, $rightTypes = [])
    {
        if (empty($name))
            return false;

        if (empty($rightTypes))
            $rightTypes = ['csv', 'xls', 'xlsx'];

        $name = explode('.', $name);
        $name = $name[count($name) - 1];

        foreach ($rightTypes as $rightType) {
            if ($name == $rightType) {
                return true;
                break;
            }
        }

        return false;
    }

    private static function getFileParameter ($file, $key = '')
    {
        if (empty($file))
            return null;

        return $file[$key]['parameters']['file'];
    }

    public static function deactivateNonFileElements ($iblockId, $elements = [], $deactivate = true)
    {
        if (empty($elements))
            $elements = $_SESSION['INTEC_IMPORT']['elementsIds'];

        if (!$iblockId || empty($elements) || !$deactivate)
            return false;

        $result = [];

        $fields['IBLOCK_ID'] = $iblockId;
        $fields['ACTIVE'] = 'Y';
        $fields['!ID'] = $elements;

        $deactivatedElements = IBlockSelections::getElements($iblockId, ['ID' => 'ASC'], $fields, ['ID'], 0, 0, false);

        foreach ($deactivatedElements as $deactivatedElement) {
            $newElement = new \CIBlockElement;
            $result[$deactivatedElement['ID']] = $newElement->Update($deactivatedElement['ID'], ['ACTIVE' => 'N']);
        }

        return $result;
    }

    /** @todo fix this */
    public static function actionOnNonFileElements ($iblockId, $elements = [], $deactivate = false, $zeroQuantity = false, $zeroPrice = false, $delete = false)
    {
        if (empty($elements))
            $elements = $_SESSION['INTEC_IMPORT']['elementsIds'];

        if (!$deactivate && !$zeroQuantity && !$zeroPrice && !$delete || empty($elements))
            return false;

        $result = [
            'ELEMENTS' => [],
            'DEACTIVATED' => []
        ];

        $fields['IBLOCK_ID'] = $iblockId;
        $isZeroPrice = false;
        $deactivated = [];

        if ($deactivate) {
            $deactivated = self::deactivateNonFileElements($iblockId,$elements);
        }

        if ($zeroQuantity)
            $fields['QUANTITY'] = '';

        if ($zeroPrice && !$delete) {
            $price = new Price();
            $isZeroPrice = $price->deleteByProductId($elements);
        }

        foreach ($elements as $id) {
            $isUpdate = false;
            $isDelete = false;

            if ($delete) {
                $isDelete = \CIBlockElement::Delete($id);
            } elseif ($zeroQuantity) {
                $newElement = new \CIBlockElement;
                $isUpdate = $newElement->Update($id, $fields);
            }

            $result['ELEMENTS'][$id] = [
                'delete' => $delete && $isDelete,
                'zeroQuantity' => $zeroQuantity && $isUpdate,
                'zeroPrice' => $zeroPrice && $isZeroPrice,
            ];
        }

        $result['DEACTIVATED'] = $deactivated;


        return $result;
    }

    public static function actionOnSection ($iblockId, $sections = [], $activateNonEmpty = false, $deactivateEmpty = false, $deleteEmpty = false, $nonActive = false, $getIfEmpty = false)
    {
        if (!$activateNonEmpty && !$deactivateEmpty && !$deleteEmpty || (empty($sections) && empty($iblockId)))
            return false;

        //$getIfEmpty //получать все разделы инфоблока если $sections - пустой

        $sections = self::formatSectionForAction($iblockId, $sections, $getIfEmpty, $nonActive);

        if (empty($sections))
            return false;

        $ids = [];

        foreach ($sections as $section) {
            $isEmpty = empty($section['ELEMENT_CNT']);

            if ($isEmpty) {
                if ($deleteEmpty) {
                    $ids[$section['ID']] = 'delete';
                } elseif ($deactivateEmpty) {
                    $ids[$section['ID']] = 'deactivate';
                }
            } else {
                if ($activateNonEmpty) {
                    $ids[$section['ID']] = 'activate';
                }
            }
        }

        $result = [];

        foreach ($ids as $id => $action) {
            if (empty($action))
                continue;

            $isDelete = false;
            $isDeactivate = false;
            $isActive = false;

            if ($action === 'delete') {
                $isDelete = \CIBlockSection::Delete($id);
            } else {
                $newSection = new \CIBlockSection;

                if ($action === 'deactivate') {
                    $isDeactivate = $newSection->update($id, ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'N']);
                } elseif ($action === 'activate') {
                    $isActive = $newSection->update($id, ['IBLOCK_ID' => $iblockId, 'ACTIVE' => 'Y']);
                }
            }

            $result[$id] = [
                'delete' => $action === 'delete' && $isDelete,
                'deactivate' => $action === 'deactivate' && $isDeactivate,
                'active' => $action === 'activate' && $isActive
            ];
        }

        return $result;
    }

    private static function formatSectionForAction ($iblockId, $sections, $getIfEmpty = false, $nonActive = false)
    {
        if (empty($sections)) {
            if ($getIfEmpty) {
                $filter = ['IBLOCK_ID' => $iblockId];

                if ($nonActive)
                    $filter['CNT_ACTIVE'] = 'Y';

                $sections = Arrays::fromDBResult(\CIBlockSection::GetList(
                    [],
                    $filter,
                    ['ELEMENT_SUBSECTIONS' => 'Y'],
                    ['ID', 'ELEMENT_CNT']
                ))->indexBy('ID')->asArray();
            }
        } else {
            $firstSection = ArrayHelper::getFirstValue($sections);

            if (Type::isArray($firstSection)) {
                if (!ArrayHelper::keyExists('ELEMENT_CNT', $firstSection)) {
                    $filter = ['IBLOCK_ID' => $iblockId, 'ID' => []];

                    if ($nonActive)
                        $filter['CNT_ACTIVE'] = 'Y';

                    foreach ($sections as $section) {
                        $filter['ID'][] = $section['ID'];
                    }

                    if (!empty($filter['ID'])) {
                        $sections = Arrays::fromDBResult(\CIBlockSection::GetList(
                            [],
                            $filter,
                            ['ELEMENT_SUBSECTIONS' => 'Y'],
                            ['ID', 'ELEMENT_CNT']
                        ))->indexBy('ID')->asArray();
                    }
                } else {
                    return $sections;
                }
            } else {
                $filter = ['IBLOCK_ID' => $iblockId, 'ID' => []];

                if ($nonActive)
                    $filter['CNT_ACTIVE'] = 'Y';

                foreach ($sections as $section) {
                    $filter['ID'][] = $section;
                }

                if (!empty($filter['ID'])) {
                    $sections = Arrays::fromDBResult(\CIBlockSection::GetList(
                        [],
                        $filter,
                        ['ELEMENT_SUBSECTIONS' => 'Y'],
                        ['ID', 'ELEMENT_CNT']
                    ))->indexBy('ID')->asArray();
                }
            }

            unset($firstSection);
        }

        return $sections;
    }

    /*finish this later
    array like
    return [[
        'from' => valueFrom,
        'to' => valueTo
    ],
        value,
        value
    ]*/
    public static function prepareElementsList ($list, $newId)
    {
        if (empty($newId))
            return $list;

        $result = [];
        $min = null;
        $max = null;

        foreach ($list as $key => $item) {
            if (Type::isArray($item)) {

            } else {

            }
        }

        return $result;
    }

    public static function unprepareElementsList ($list)
    {
        if (empty($list))
            return $list;

        $result = [];

        foreach ($list as $item) {
            if (Type::isArray($item)) {
                $i = $item['from'];

                for ($i; $i <= $item['to']; $i++) {
                    $result[] = $i;
                }
            } else {
                $result[] = $item;
            }
        }

        $result = array_unique($result);
        return $result;
    }

    public static function convertFilePath ($filePath, $charsetTo = 'utf-8')
    {
        if (defined('BX_UTF'))
            $currentEncoding = "utf-8";
        elseif (defined("SITE_CHARSET") && (strlen(SITE_CHARSET) > 0))
            $currentEncoding = SITE_CHARSET;
        elseif (defined("LANG_CHARSET") && (strlen(LANG_CHARSET) > 0))
            $currentEncoding = LANG_CHARSET;
        elseif (defined("BX_DEFAULT_CHARSET"))
            $currentEncoding = BX_DEFAULT_CHARSET;
        else
            $currentEncoding = "windows-1251";

        $currentEncoding = strtolower($currentEncoding);

        return Encoding::convertEncoding($filePath, $currentEncoding, $charsetTo);

    }
}