<?php
namespace intec\seo\models\articles\template;

use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\articles\Template;

/**
 * Модель привязки шаблона статей к разделу.
 * Class Section
 * @property integer $templateId Шаблон.
 * @property integer $iBlockSectionId Раздел.
 * @package intec\seo\models\iblocks\metadata\template
 * @author apocalypsisdimon@gmail.com
 */
class Section extends ActiveRecord
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
        return 'seo_articles_templates_sections';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'templateId' => [['templateId'], 'integer'],
            'iBlockSectionId' => [['iBlockSectionId'], 'integer'],
            'required' => [[
                'templateId',
                'iBlockSectionId'
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