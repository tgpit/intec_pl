<?php
namespace intec\seo\models\filter;

use DateTime;
use DateTimeZone;
use intec\core\db\ActiveRecord;

/**
 * Модель визита пользователя.
 * Class Url
 * @property integer $id Идентификатор.
 * @property string $sessionId Идентификатор сессии пользователя.
 * @property string $referrerUrl Адрес, откуда пришел пользователь.
 * @property string $pageUrl Адрес страницы, куда пришел пользователь.
 * @property string $pageCount Количество посещенных страниц пользователем.
 * @property string $dateCreate Дата первого захода.
 * @property string $dateVisit Дата последнего захода.
 * @package intec\seo\models\filter
 * @author apocalypsisdimon@gmail.com
 */
class Visit extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    protected static $cache = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'seo_filter_visits';
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $date = new DateTime('now', new DateTimeZone('UTC'));

            if (empty($this->dateCreate))
                $this->dateCreate = $date->format('Y-m-d H:i:s');

            if (empty($this->dateVisit))
                $this->dateVisit = $date->format('Y-m-d H:i:s');

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'sessionId' => [['sessionId'], 'string', 'max' => 255],
            'referrerUrl' => [['referrerUrl'], 'string'],
            'pageUrl' => [['pageUrl'], 'string'],
            'pageCount' => [['pageCount'], 'integer'],
            'dateCreate' => [['dateCreate'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            'dateVisit' => [['dateVisit'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            'unique' => [['sessionId'], 'unique', 'targetAttribute' => ['sessionId']],
            'required' => [[
                'sessionId',
                'referrerUrl',
                'pageUrl',
                'pageCount'
            ], 'required']
        ];
    }
}