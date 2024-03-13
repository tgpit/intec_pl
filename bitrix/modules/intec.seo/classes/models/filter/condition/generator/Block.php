<?php
namespace intec\seo\models\filter\condition\generator;

use intec\core\base\Model;
use intec\core\helpers\Type;
use intec\seo\models\filter\condition\generator\block\Property;
use intec\seo\models\filter\condition\generator\block\Properties;

/**
 * Класс, представляющий модель блока условий.
 * Class Block
 * @package intec\seo\models\filter\condition\generator
 * @author apocalypsisdimon@gmail.com
 */
class Block extends Model
{
    /**
     * @var Properties
     */
    protected $_properties;

    /**
     * Устанавливает свойства блока.
     * @param Properties|array $value
     */
    public function setProperties($value)
    {
        $this->_properties = Properties::from($value);
    }

    /**
     * Возвращает свойства блока.
     * @return Properties
     */
    public function getProperties()
    {
        return Properties::from($this->_properties);
    }

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        if (isset($config['properties'])) {
            $this->_properties = Properties::create($config['properties']);
        } else {
            $this->_properties = new Properties();
        }

        unset($config['properties']);
        parent::__construct($config);
    }

    /**
     * Возвращает блок в виде массива.
     * @return array
     */
    public function export()
    {
        return [
            'properties' => $this->_properties->export()
        ];
    }
}