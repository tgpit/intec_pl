<?php
namespace intec\constructor\models\build\template;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveRecord;
use intec\core\db\ActiveQuery;

Loc::loadMessages(__FILE__);

/**
 * Class ContainerLayoutZoneLink
 * @property integer $containerId
 * @property integer $layoutZoneCode
 * @package intec\constructor\models\build\template
 */
class ContainerLayoutZoneLink extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'constructor_builds_templates_containers_layouts_zones_links';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['containerId'], 'integer'],
            [['layoutZoneCode'], 'string', 'max' => 255],
            [['containerId', 'layoutZoneCode'], 'required']
        ];
    }

    /**
     * Реляция. Возвращает привязанный контейнер.
     * @param boolean $result Возвращать результат.
     * @return Container|ActiveQuery|null
     */
    public function getContainer($result = false)
    {
        return $this->relation(
            'container',
            $this->hasOne(Container::className(), ['id' => 'containerId']),
            $result
        );
    }
}