<?php
namespace intec\seo\models\autofill\template;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\autofill\Template;

/**
 * Модель привязки элементов к шаблону автозаполнения.
 * Class Section
 * @property integer $templateId Шаблон.
 * @property integer $iBlockElementId Раздел.
 * @package intec\seo\models\iblocks\metadata\template
 * @author apocalypsisdimon@gmail.com
 */
class Element extends ActiveRecord
{
    /**
     * @var array $cache
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_autofill_templates_elements';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'templateId' => [['templateId'], 'integer'],
            'iBlockElementId' => [['iBlockElementId'], 'integer'],
            'required' => [[
                'templateId',
                'iBlockElementId'
            ], 'required']
        ];
    }

    /**
     * Реляция. Возвращает привязанный шаблон метаданных.
     * @param boolean $result Возвращать результат.
     * @return Template|ActiveQuery|null
     */
    public function getTemplate($result = false)
    {
        return $this->relation(
            'template',
            $this->hasOne(Template::className(), ['id' => 'templateId']),
            $result
        );
    }
}