<?php
namespace intec\importexport\models\excel;

use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\base\InvalidParamException;

class ConditionHelper
{
    public static function getComputedValue ($item, $currentCode, $settings, $delimiter)
    {
        if (empty($item) || empty($currentCode) || empty($settings))
            return $item[$currentCode]; // test and fix

        try {
            $settings = Json::decode($settings, true, true);
        } catch (InvalidParamException $exception) {
            return $item[$currentCode];
        }

        $offersDelimiter = $delimiter;
        //$offersDelimiter = '|';

        $res = null;

        $offers = explode($offersDelimiter, $item[$currentCode]);

        if (count($offers) > 1) {
            foreach ($offers as &$offer) {
                foreach ($settings as $setting) {
                    $field = $setting['field'] === 'CURRENT' ? $currentCode : $setting['field'];

                    if ($setting['field'] === 'CURRENT') {
                        if (self::whenCurrent($offer, $setting)) {

                            $thenValue = StringHelper::replaceMacros($setting['thenValue'], $item);
                            $offer  = self::{$setting['then']}($offer, $thenValue, $setting['whenValue']);
                        }
                    } else {
                        if (self::when($item, $field, $setting)) {

                            $thenValue = StringHelper::replaceMacros($setting['thenValue'], $item);
                            $offer  = self::{$setting['then']}($offer, $thenValue, $setting['whenValue']);
                        }
                    }
                }
            }

            $res = implode($offers, $offersDelimiter);
        } else {
            foreach ($settings as $setting) {
                $field = $setting['field'] === 'CURRENT' ? $currentCode : $setting['field'];
                $isWhen = false;

                if (empty($res) && $res !== 0 && $res !== false)
                    $res = $item[$field];

                if ($setting['field'] === 'CURRENT') {
                    $isWhen = self::whenCurrent($res, $setting);
                } else {
                    $isWhen = self::when($item, $field, $setting);
                }

                if ($isWhen) {
                    $thenValue = StringHelper::replaceMacros($setting['thenValue'], $item);
                    $res  = self::{$setting['then']}($res, $thenValue, $setting['whenValue']);
                } /*else {
                    $res = $item[$currentCode];
                }*/
            }
        }

        return $res;
    }

    public static function getComputedImportValue ($item, $currentCode, $settings, $delimiter)
    {
        if (empty($item) || (empty($currentCode) && $currentCode !== 0) || empty($settings))
            return $item[$currentCode]; // nenf fix

        try {
            $settings = Json::decode($settings, true, true);
        } catch (InvalidParamException $exception) {
            return $item[$currentCode];
        }

        $res = null;

        $offers = explode($delimiter, $item[$currentCode]);

        if (count($offers) > 1) {
            foreach ($offers as &$offer) {
                foreach ($settings as $setting) {
                    $field = $setting['field'] === 'CURRENT' ? $currentCode : $setting['field'];

                    if ($setting['field'] === 'CURRENT') {
                        if (self::whenCurrent($offer, $setting)) {

                            $thenValue = StringHelper::replaceMacros($setting['thenValue'], $item, '#CELL_');
                            $offer  = self::{$setting['then']}($offer, $thenValue, $setting['whenValue']);
                        }
                    } else {
                        if (self::when($item, $field, $setting)) {

                            $thenValue = StringHelper::replaceMacros($setting['thenValue'], $item, '#CELL_');
                            $offer  = self::{$setting['then']}($offer, $thenValue, $setting['whenValue']);
                        }
                    }
                }
            }

            $res = implode($offers, $delimiter);
        } else {
            foreach ($settings as $setting) {
                $field = $setting['field'] === 'CURRENT' ? $currentCode : $setting['field'];
                $isWhen = false;

                if (empty($res) && $res !== 0 && $res !== false)
                    $res = $item[$field];

                if ($setting['field'] === 'CURRENT') {
                    $isWhen = self::whenCurrent($res, $setting);
                } else {
                    $isWhen = self::when($item, $field, $setting);
                }

                if ($isWhen) {
                    $thenValue = StringHelper::replaceMacros($setting['thenValue'], $item, '#CELL_');
                    $res  = self::{$setting['then']}($res, $thenValue, $setting['whenValue']);
                } /*else {
                    $res = $item[$currentCode];
                }*/
            }
        }

        return $res;
    }

    private static function when ($item, $field, $setting)
    {
        if ($setting['when'] === 'empty')
            return self::isempty($item[$field], $setting['whenValue']);
        else
            return self::{$setting['when']}($item[$field], $setting['whenValue']);
    }

    private static function whenCurrent ($offer, $setting)
    {
        if ($setting['when'] === 'empty')
            return self::isempty($offer, $setting['whenValue']);
        else
            return self::{$setting['when']}($offer, $setting['whenValue']);
    }

    public static function equal ($a, $b)
    {
        return $a == $b;
    }

    public static function nequal ($a, $b)
    {
        return $a != $b;
    }

    public static function more ($a, $b)
    {
        return $a > $b;
    }

    public static function less ($a, $b)
    {
        return $a < $b;
    }

    public static function moreq ($a, $b)
    {
        return $a >= $b;
    }

    public static function loreq ($a, $b)
    {
        return $a <= $b;
    }

    public static function between ($a, $b)
    {
        $values = explode('-', $b);
        $min = null;
        $max = null;

        foreach ($values as $value) {
            $value = Type::toFloat($value);

            if (empty($value))
                $value = 0;

            if ($value <= $min)
                $min = $value;

            if ($value >= $max)
                $max = $value;
        }

        return $a >= $min && $a <= $max;
    }

    public static function substring ($a, $b)
    {
        return StringHelper::position($b, $a) !== false;
    }

    public static function nsubstring ($a, $b)
    {
        return StringHelper::position($b, $a) === false;
    }

    public static function isempty ($a)
    {
        return empty($a) && $a !== 0 && $a !== false;
    }

    public static function nempty ($a)
    {
        return !(empty($a) && $a !== 0 && $a !== false);
    }

    public static function regularexp ($a, $b) //fix regular expire. php and js expire not equal.
    {
        return preg_match('/' . $b . '/', $a) > 0;
    }

    public static function nregularexp ($a, $b) //fix regular expire. php and js expire not equal.
    {
        return preg_match('/' . $b . '/', $a) < 1;
    }

    public static function any ()
    {
        return true;
    }


    public static function replaceto ($a, $b)
    {
        return $b;
    }

    public static function removesubs ($a, $b)
    {
        return str_replace($b, '', $a);
    }

    public static function replacesubsto ($a, $b, $substr)
    {

        if (empty($substr) && $substr !== 0 && $substr !== false)
            return $b;
        else
            return str_replace($substr, $b, $a);
    }

    public static function addtobegin ($a, $b)
    {
        return $b . $a;
    }

    public static function addtoend ($a, $b)
    {
        return $a . $b;
    }

    public static function translit ($a)
    {
        $params = [
            'max_len' => 100,
            'change_case' => 'L',
            'replace_space' => '_',
            'replace_other' => '_',
            'delete_repeat_replace' => true,
            'safe_chars' => ''
        ];

        return \CUtil::translit($a, 'ru', $params);
    }

    public static function striptags ($a)
    {
        return Html::stripTags($a);
    }

    public static function cleartags ($a, $b)
    {
        //finish later
    }

    public static function round ($a)
    {
        return round(self::converToFloat($a), 0);
    }

    public static function multiply ($a, $b)
    {
        return self::converToFloat($a) * self::converToFloat($b);
    }

    public static function divide ($a, $b)
    {
        return self::converToFloat($a) / self::converToFloat($b);
    }

    public static function add ($a, $b)
    {
        return self::converToFloat($a) + self::converToFloat($b);
    }

    public static function subtract ($a, $b)
    {
        return self::converToFloat($a) - self::converToFloat($b);
    }

    public static function removefromfile ($a, $b)
    {

    }

    public static function setbg ($a, $b)
    {
        //finish later
    }

    public static function settext ($a, $b)
    {
        //finish later
    }

    public static function addlink ($a, $b)
    {
        //finish later
    }

    public static function php ($a, $b)
    {
        //finish later
    }

    private static function converToFloat ($value)
    {
        if (!empty($value) && (Type::isFloat($value) || Type::isInteger($value)))
            return $value;

        $value = str_replace(',', '.', $value);

        $value = Type::toFloat($value);

        if (empty($value))
            $value = 0;

        return $value;
    }
}