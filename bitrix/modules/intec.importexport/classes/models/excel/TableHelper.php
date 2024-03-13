<?php
namespace intec\importexport\models\excel;

use intec\core\helpers\FileHelper;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

class TableHelper
{

    public static function changeValueOnKey ($array, $change)
    {
        if (!is_array($array) || empty($array))
            return null;

        foreach ($array as $key => &$value) {
            $value[$change] = $key;
        }

        return $array;
    }

    public static function getIndexByCode ($properties)
    {
        if (empty($properties))
            return $properties;

        $propertiesByCode = [];

        foreach ($properties as $property) {
            if (!empty($property['code']))
                $propertiesByCode[$property['code']] = $property;
            else
                $propertiesByCode[] = $property;
        }

        $properties = $propertiesByCode;

        return $properties;
    }

    public static function getLetter ($number)
    {
        if (empty($number))
            return null;

        $letterList =  [
            '', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

        $result = '';
        $division = floor($number / 26);
        $number = $number - ($division * 26);

        if ($number == 0) {
            $division = $division - 1;
            $number = 26;
        }

        $result = $result . $letterList[$number];

        if ($division > 0) {
            $result = self::getLetter($division) . $result;
        }

        return $result;
    }

    public static function getNumberFromLetter ($letter)
    {
        if (empty($letter))
            return null;

        $letter = mb_strtoupper($letter);

        $letterList =  [
            '', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ];

        $count = iconv_strlen($letter) - 1;
        $result = 0;
        $pow = 0;

        while ($count >= 0) {
            $number = array_search($letter[$count], $letterList);

            if ($result > 0) {
                $result = $result + $number * pow(26, $pow);
            } else {
                $result = $result + $number;
            }

            $count--;
            $pow++;
        }

        return $result;
    }

    public static function prepareToMenu ($array, $useGroup = false, $useTitle = false)
    {
        if (empty($array))
            return $array;

        $result = [];
        $counter = 0;

        foreach ($array as $item) {
            $result[$counter]['TEXT'] = $item['name'];
            $result[$counter]['ONCLICK'] = 'window.page.setMacros(\'' . $item['code'] . '\')';

            if ($useTitle)
                $result[$counter]['TITLE'] = '#' . $item['code'] . '# (' . $item['name'] . ')';

            if ($useGroup && StringHelper::startsWith($item['code'], 'GROUP_')) {
                $result[$counter]['ONCLICK'] = 'javascript:void(0)';
                $result[$counter]['HTML'] = '<b style="font-size: 1.2em;">' . $item['name'] . '</b>';
            }

            $counter++;
        }

        return $result;
    }

    /**
     * Проверяет существование директории по пути до файла.
     * Если директории не существует, то создает ее.
     * @param $pathToFile
     * @return null
     * @throws \intec\core\base\Exception

     */
    public static function checkAndCreateDirectory ($pathToFile)
    {
        if (empty($pathToFile))
            return null;

        $pathToFile = StringHelper::explode($pathToFile, '/');
        $pathToFile = ArrayHelper::slice($pathToFile, 0, -1);

        if (count($pathToFile) <= 0)
            return null;

        $pathToFile = implode('/', $pathToFile);

        if ($pathToFile[0] !== '/')
            $pathToFile = '/' . $pathToFile;

        $pathToFile = $_SERVER['DOCUMENT_ROOT'] . $pathToFile;

        if (!FileHelper::isDirectory($pathToFile))
            FileHelper::createDirectory($pathToFile);

        return;
    }
}
