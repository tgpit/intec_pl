<?php
namespace intec\core\platform\iblock;

use Bitrix\Iblock\PropertyTable;
use intec\core\base\BaseObject;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * Класс, представляющий обработчик значения свойства элемента инфоблока.
 * Class ElementPropertyValueHandler
 * @package intec\core\platform\iblock
 */
class ElementPropertyValueHandler extends BaseObject implements ElementPropertyValueHandlerInterface
{
    /**
     * Обработчик свойства типа HTML.
     * @param ElementProperty $property
     * @return string|null
     */
    protected function handleHtmlType($property, &$key)
    {
        $result = null;

        if ($property->getIsMultiple()) {
            $result = [];
            $values = $property->getValue();

            foreach ($values as $value) {
                if (!Type::isArray($value) || Type::isEmpty($value['TEXT']))
                    continue;

                if ($value['TYPE'] !== 'HTML') {
                    $result[] = Html::encode($value['TEXT']);
                } else {
                    $result[] = $value['TEXT'];
                }
            }
        } else {
            $value = $property->getValue();

            if (Type::isArray($value) && !Type::isEmpty($value['TEXT'])) {
                if ($value['TYPE'] !== 'HTML') {
                    $result = Html::encode($value['TEXT']);
                } else {
                    $result = $value['TEXT'];
                }
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function handle($property, &$key = null)
    {
        switch ($property->getType()) {
            case PropertyTable::TYPE_STRING: {
                if ($property->getUserType() === 'HTML')
                    return $this->handleHtmlType($property, $key);
            }
        }

        return $property->getValue();
    }
}
