<?php
namespace intec\constructor\models\build\layout;

use intec\core\base\InvalidParamException;
use intec\core\base\Model;
use intec\core\helpers\Type;

class Zone extends Model
{
    /**
     * @var string
     */
    protected $_code;
    /**
     * @var string
     */
    protected $_name;


    public function __construct($code, $name = null, $config = [])
    {
        if (empty($code) && !Type::isNumeric($code))
            throw new InvalidParamException('Parameter "code" cannot be empty');

        if (empty($name) && !Type::isNumeric($name))
            $name = $code;

        $this->_code = $code;
        $this->_name = $name;

        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}