<?php
namespace intec\measures\helpers;

use CCatalogMeasure;
use CCatalogProduct;
use intec\core\base\BaseObject;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\measures\models\ConversionRatio;

/**
 * Class ProductsHelper
 * @package intec\measures\helpers
 */
class ProductsHelper extends BaseObject
{
    /**
     * @param $id
     */
    public static function getMeasures ($id)
    {
        $result = [];
        $defaultMeasure = '';

        $measures = Arrays::fromDBResult(CCatalogMeasure::getList())
            ->indexBy('ID');

        foreach ($measures as $measure)
            if ($measure['IS_DEFAULT'] === 'Y') {
                $defaultMeasure = $measure['ID'];

                continue;
            }

        $ratios = ConversionRatio::find()->where(['productId' => $id, 'active' => true])->all();

        if (Type::isArray($id)) {
            $arProducts = Arrays::fromDBResult(CCatalogProduct::GetList([], ['ID' => $id]))
                ->indexBy('ID');

            foreach ($arProducts as $arProduct) {
                $iMeasureID = $arProduct['MEASURE'];
                $iProductID = $arProduct['ID'];

                if (empty($iMeasureID))
                    $iMeasureID = $defaultMeasure;

                $name = !empty($measures[$iMeasureID]['MEASURE_TITLE']) ? $measures[$iMeasureID]['MEASURE_TITLE'] : $measures[$iMeasureID]['SYMBOL_LETTER_INTL'];
                $symbol = !empty($measures[$iMeasureID]['SYMBOL']) ? $measures[$iMeasureID]['SYMBOL'] : $measures[$iMeasureID]['SYMBOL_INTL'];

                $result[$iProductID][$iMeasureID] = [
                    'id' => Type::toInteger($iMeasureID),
                    'code' => $measures[$iMeasureID]['CODE'],
                    'name' => $name,
                    'symbol' => $symbol,
                    'base' => true,
                    'multiplier' => 1
                ];
            }

            unset ($iMeasureID, $iProductID);

            foreach ($ratios as $ratio) {
                $iMeasureID = $ratio->measureId;
                $iProductID = $ratio->productId;

                if (!ArrayHelper::keyExists($iProductID, $result))
                    $result[$iProductID] = [];

                $name = !empty($measures[$iMeasureID]['MEASURE_TITLE']) ? $measures[$iMeasureID]['MEASURE_TITLE'] : $measures[$iMeasureID]['SYMBOL_LETTER_INTL'];
                $symbol = !empty($measures[$iMeasureID]['SYMBOL']) ? $measures[$iMeasureID]['SYMBOL'] : $measures[$iMeasureID]['SYMBOL_INTL'];

                $result[$iProductID][$iMeasureID] = [
                    'id' => $iMeasureID,
                    'code' => $measures[$iMeasureID]['CODE'],
                    'name' => $name,
                    'symbol' => $symbol,
                    'base' => $arProducts[$iProductID]['MEASURE'] == $iMeasureID,
                    'multiplier' => $ratio->value
                ];
            }
        } else {
            $arProduct = CCatalogProduct::GetByID($id);
            $iMeasureID = $arProduct['MEASURE'];

            if (empty($iMeasureID))
                $iMeasureID = $defaultMeasure;

            $name = !empty($measures[$iMeasureID]['MEASURE_TITLE']) ? $measures[$iMeasureID]['MEASURE_TITLE'] : $measures[$iMeasureID]['SYMBOL_LETTER_INTL'];
            $symbol = !empty($measures[$iMeasureID]['SYMBOL']) ? $measures[$iMeasureID]['SYMBOL'] : $measures[$iMeasureID]['SYMBOL_INTL'];

            $result[$iMeasureID] = [
                'id' => Type::toInteger($iMeasureID),
                'code' => $measures[$iMeasureID]['CODE'],
                'name' => $name,
                'symbol' => $symbol,
                'base' => true,
                'multiplier' => 1
            ];

            foreach ($ratios as $ratio) {
                $iMeasureID = $ratio->measureId;

                $name = !empty($measures[$iMeasureID]['MEASURE_TITLE']) ? $measures[$iMeasureID]['MEASURE_TITLE'] : $measures[$iMeasureID]['SYMBOL_LETTER_INTL'];
                $symbol = !empty($measures[$iMeasureID]['SYMBOL']) ? $measures[$iMeasureID]['SYMBOL'] : $measures[$iMeasureID]['SYMBOL_INTL'];

                $result[$iMeasureID] = [
                    'id' => $iMeasureID,
                    'code' => $measures[$iMeasureID]['CODE'],
                    'name' => $name,
                    'symbol' => $symbol,
                    'base' => $arProduct['MEASURE'] == $iMeasureID,
                    'multiplier' => $ratio->value
                ];

                if ($result[$iMeasureID]['base']) {
                    $result[$iMeasureID]['multiplier'] = 1;
                }
            }
        }

        return $result;
    }
}