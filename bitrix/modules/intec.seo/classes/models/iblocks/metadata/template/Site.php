<?php
namespace intec\seo\models\iblocks\metadata\template;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\iblocks\metadata\Template;

Loc::loadMessages(__FILE__);

/**
 * Модель привязки шаблона метаданных к сайту.
 * Class Site
 * @property integer $templateId Шаблон.
 * @property string $siteId Сайт.
 * @package intec\seo\models\iblocks\metadata\template
 * @author apocalypsisdimon@gmail.com
 */
class Site extends ActiveRecord
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
        return 'seo_iblocks_metadata_templates_sites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'templateId' => [['templateId'], 'integer'],
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'required' => [[
                'templateId',
                'siteId'
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