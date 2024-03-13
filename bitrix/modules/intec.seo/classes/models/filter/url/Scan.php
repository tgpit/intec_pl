<?php
namespace intec\seo\models\filter\url;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveQuery;
use intec\core\db\ActiveRecord;
use intec\seo\models\filter\Url;

Loc::loadMessages(__FILE__);

/**
 * Модель результата сканирования ссылки фильтра.
 * Class Url
 * @property integer $id Идентификатор.
 * @property integer $urlId Ссылка фильтра.
 * @property string $date Дата.
 * @property integer $status Статус.
 * @property string $metaTitle Заголовок meta.
 * @property string $metaKeywords Ключевые слова meta.
 * @property string $metaDescription Описание meta.
 * @property string $metaPageTitle Заголовок страницы.
 * @package intec\seo\models\filter\url
 * @author apocalypsisdimon@gmail.com
 */
class Scan extends ActiveRecord
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
        return 'seo_filter_url_scans';
    }

    /**
     * Возвращает запрос на поиск по последней дате сканирования.
     * @return ActiveQuery
     */
    public static function findLatest()
    {
        $result = Scan::find();
        $result->join['result'] = ['INNER JOIN', [
            'result' => Scan::find()->select([
                'urlId', 'MAX(`date`) AS `date`'
            ])->groupBy(['urlId'])
        ], Scan::tableName().'.`urlId` = `result`.`urlId` AND '.Scan::tableName().'.`date` = `result`.`date`'];

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'urlId' => [['urlId'], 'integer'],
            'date' => [['date'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            'status' => [['status'], 'integer'],
            'metaTitle' => [['metaTitle'], 'string'],
            'metaKeywords' => [['metaKeywords'], 'string'],
            'metaDescription' => [['metaDescription'], 'string'],
            'metaPageTitle' => [['metaDescription'], 'string'],
            'required' => [[
                'urlId',
                'date'
            ], 'required']
        ];
    }

    /**
     * Реляция. Возвращает связанную ссылку фильтра.
     * @param boolean $result Возвращать результат.
     * @return Url|ActiveQuery|null
     */
    public function getUrl($result = false)
    {
        return $this->relation(
            'url',
            $this->hasOne(Url::className(), ['id' => 'urlId']),
            $result
        );
    }
}