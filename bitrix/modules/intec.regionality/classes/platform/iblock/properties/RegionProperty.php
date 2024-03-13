<?php
namespace intec\regionality\platform\iblock\properties;

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\base\BaseObject;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\regionality\models\Region;

Loc::loadMessages(__FILE__);

/**
 * Предстваляет собой объявление списочного свойства инфоблока с регионами.
 * Class IBlockRegion
 * @package intec\regionality\platform\iblock\properties
 * @author apocalypsisdimon@gmail.com
 */
class RegionProperty extends BaseObject
{
    /**
     * Тип свойства системы.
     */
    const PROPERTY_TYPE = 'R';
    /**
     * Пользовательский тип свойства.
     */
    const USER_TYPE = 'RegionalityRegion';

    /**
     * Кешированные регионы.
     * @var ActiveRecords
     */
    protected static $_regions;

    /**
     * Возвращает объявление для системы.
     * @return array
     */
    public static function getDefinition()
    {
        return [
            'PROPERTY_TYPE' => static::PROPERTY_TYPE,
            'USER_TYPE' => static::USER_TYPE,
            'DESCRIPTION' => Loc::getMessage('intec.regionality.properties.platform.iblock.properties.regionProperty.name'),
            'GetAdminListViewHTML' => [static::className(), 'getAdminView'],
            'GetPropertyFieldHtml' => [static::className(), 'getEditView'],
            'GetPropertyFieldHtmlMulty' => [static::className(), 'getEditViewMultiple'],
            'GetPublicEditHTML' => [static::className(), 'getEditView'],
            'GetPublicEditHTMLMulty' => [static::className(), 'getEditViewMultiple'],
            'GetPublicViewHTML' => [static::className(), 'getPublicView']
        ];
    }

    /**
     * Возвращает административный вид свойства.
     * @param array $arProperty
     * @param array $arValue
     * @param null $arControl
     * @return null|string
     */
    public static function getAdminView($arProperty, $arValue, $arControl)
    {
        /**
         * @var array $arUrlTemplates
         */

        include(Core::getAlias('@intec/regionality/module/admin/url.php'));

        $oRegions = static::getRegions();
        $arResult = null;

        /** @var Region $oRegion */
        $oRegion = $oRegions->get($arValue['VALUE']);

        if (!empty($oRegion))
            $arResult = Html::a('['.$oRegion->id.'] '.Html::encode($oRegion->name), StringHelper::replaceMacros(
                $arUrlTemplates['regions.edit'], [
                    'region' => $oRegion->id
                ]
            ), [
                'target' => '_blank'
            ]);

        return $arResult;
    }

    /**
     * Возвращает вид для редактирования.
     * @param array $arProperty
     * @param string $sValue
     * @param array $arControl
     * @return string
     */
    public static function getEditView($arProperty, $sValue, $arControl)
    {
        $oRegions = static::getRegions();
        $arOptions = [];

        if ($arProperty['IS_REQUIRED'] !== 'Y' || $arProperty['MULTIPLE'] === 'Y')
            $arOptions[''] = Loc::getMessage('IBLOCK_PROP_ELEMENT_LIST_NO_VALUE');

        $arOptions = ArrayHelper::merge(
            $arOptions,
            $oRegions->asArray(function ($iId, $oRegion) {
                /** @var Region $oRegion */

                return [
                    'key' => $oRegion->id,
                    'value' => '['.$oRegion->id.'] '.$oRegion->name
                ];
            })
        );

        return Html::dropDownList(
            $arControl['VALUE'],
            $sValue,
            $arOptions
        );
    }

    /**
     * Возвращает вид для множественного редактирования.
     * @param array $arProperty
     * @param array $arValues
     * @param array $arControl
     * @return string
     */
    public static function getEditViewMultiple($arProperty, $arValues, $arControl)
    {
        $oRegions = static::getRegions();
        $arOptions = [];

        if ($arProperty['IS_REQUIRED'] !== 'Y')
            $arOptions[''] = Loc::getMessage('IBLOCK_PROP_ELEMENT_LIST_NO_VALUE');

        $arOptions = ArrayHelper::merge(
            $arOptions,
            $oRegions->asArray(function ($iId, $oRegion) {
                /** @var Region $oRegion */

                return [
                    'key' => $oRegion->id,
                    'value' => '['.$oRegion->id.'] '.$oRegion->name
                ];
            })
        );

        foreach ($arValues as $iId => $arValue)
            $arValues[$iId] = $arValue['VALUE'];

        return Html::hiddenInput($arControl['VALUE'].'[]').Html::dropDownList(
            $arControl['VALUE'].'[]',
            $arValues,
            $arOptions,
            [
                'multiple' => 'multiple'
            ]
        );
    }

    /**
     * Возвращает публичный вид свойства.
     * @param array $arProperty
     * @param array $arValue
     * @param null $arControl
     * @return null|string
     */
    public static function getPublicView($arProperty, $arValue, $arControl)
    {
        $oRegions = static::getRegions();
        $arResult = null;

        /** @var Region $oRegion */
        $oRegion = $oRegions->get($arValue['VALUE']);

        if (!empty($oRegion))
            $arResult = Html::encode($oRegion->name);

        return $arResult;
    }

    /**
     * Возвращает список регионов.
     * @return ActiveRecords
     */
    protected static function getRegions()
    {
        if (static::$_regions === null) {
            static::$_regions = Region::find()
                ->where(['active' => 1])
                ->all();

            static::$_regions->indexBy('id');
        }

        return static::$_regions;
    }
}