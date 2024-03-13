<?php
namespace intec\seo\models\filter\condition\generator\block;

use intec\core\base\Model;

/**
 * Класс, представляющий модель свойства блока условий.
 * Class Block
 * @property integer $id
 * @package intec\seo\models\filter\condition\generator\block
 * @author apocalypsisdimon@gmail.com
 */
class Property extends Model
{
    /**
     * Идентификатор свойства.
     * @var integer
     */
    public $id;

    /**
     * Возвращает свойство блока в виде массива.
     * @return array
     */
    public function export()
    {
        return [
            'id' => $this->id
        ];
    }
}