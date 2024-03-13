<?php
namespace intec\importexport\models\excel\import;

use Bitrix\Main\Localization\Loc;
use intec\core\db\ActiveRecord;

Loc::loadMessages(__FILE__);

/**
 * Модель шаблона наименований элементов.
 * Class Condition
 * @property integer $id Идентификатор.
 * @property string $code Код.
 * @property integer $active Активность.
 * @property string $name Наименование.
 * @property string $params Основные параметры.
 * @property string $tableParams Параметры таблицы.
 * @property string $createDate Дата создания.
 * @property string $editDate Дата редактирования.
 * @property integer $iBlockId Инфоблок.
 * @property integer $sort Сортировка.
 * @package intec\seo\models\iblocks\elements\names
 * @author apocalypsisdimon@gmail.com
 */
class Template extends ActiveRecord
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
        return 'intec_importexport_excel_import_profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //'activeDefault' => [['active'], 'default', 'value' => 1],
            'name' => [['name'], 'string', 'max' => 255],
            'params' => [['params'], 'string'],
            'tableParams' => [['tableParams'], 'string'],
            'createDate' => [['createDate'], 'string'],
            'editDate' => [['editDate'], 'string'],
            'required' => [['name'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Loc::getMessage('intec.importexport.models.excel.import.template.attributes.id'),
            'name' => Loc::getMessage('intec.importexport.models.excel.import.template.attributes.name'),
            'fileType' => Loc::getMessage('intec.importexport.models.excel.import.template.attributes.file.type'),
            'createDate' => Loc::getMessage('intec.importexport.models.excel.import.template.attributes.create.date'),
            'editDate' => Loc::getMessage('intec.importexport.models.excel.import.template.attributes.edit.date')
        ];
    }

}