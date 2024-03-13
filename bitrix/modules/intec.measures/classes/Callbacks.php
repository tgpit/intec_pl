<?php
namespace intec\measures;

use Exception;
use intec\core\base\BaseObject;
use intec\measures\models\ConversionRatio;

/**
 * Класс обработчиков событий системы.
 * Class Callbacks
 * @package intec\measures
 * @author apocalypsisdimon@gmail.com
 */
class Callbacks extends BaseObject
{
    /**
     * Обработчик. Вызывается после удаления элемента инфоблока.
     * Удаление значений коэффициентов конвертировкания для удаленного элемента инфоблока.
     * @param array $fields
     * @throws Exception
     */
    public static function iblockOnAfterIBlockElementDelete($fields)
    {
        $ratios = ConversionRatio::find()->where([
            'productId' => $fields['ID']
        ])->all();

        foreach ($ratios as $ratio)
            $ratio->delete();
    }
}