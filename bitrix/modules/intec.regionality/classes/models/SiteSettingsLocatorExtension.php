<?php
namespace intec\regionality\models;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveRecord;

Loc::loadMessages(__FILE__);

/**
 * Модель привязки расширения сервиса определения региона по IP адресу к сайту.
 * Class SiteSettingsLocatorExtension
 * @property string $siteId Идентификатор сайта.
 * @property string $extensionCode Код сервиса.
 * @package intec\regionality\models
 * @author apocalypsisdimon@gmail.com
 */
class SiteSettingsLocatorExtension extends ActiveRecord
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
        return 'regionality_sites_settings_locator_extensions';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'siteId' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.siteId'),
            'extensionCode' => Loc::getMessage('intec.regionality.models.siteSettings.attributes.extensionCode')
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'siteId' => [['siteId'], 'string', 'length' => 2],
            'extensionCode' => [['extensionCode'], 'string', 'max' => 255],
            'required' => [['siteId', 'extensionCode'], 'required']
        ];
    }
}