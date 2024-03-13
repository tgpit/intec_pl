<?php
namespace intec\measures\interaction\iblock;

use CCatalog;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CCatalogProduct;
use intec\Core;
use intec\core\base\BaseObject;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\measures\Module;
use intec\measures\models\ConversionRatio;

class Element extends BaseObject
{
    protected static function validateConversionTab($iBlockId, $productId)
    {
        global $APPLICATION;

        if (!Loader::includeModule('catalog'))
            return false;

        if ($APPLICATION->GetCurPage(false) !== '/bitrix/admin/iblock_element_edit.php')
            return false;

        if (empty($iBlockId) || empty($productId))
            return false;

        $catalog = CCatalog::GetList([], [
            'IBLOCK_ID' => $iBlockId
        ])->Fetch();

        if (empty($catalog))
            return false;

        return true;
    }

    public static function showConversionTab(&$form)
    {
        $request = Core::$app->request;
        $iBlockId = $request->get('IBLOCK_ID');
        $productId = $request->get('ID');

        if (!static::validateConversionTab($iBlockId, $productId))
            return;

        $arProduct = CCatalogProduct::GetByID($productId);

        $measures = Module::getMeasures();
        $ratios = ConversionRatio::find()->where([
            'productId' => $productId
        ])->all()->indexBy('measureId');

        $content =
            '<table class="adm-list-table">'.
                '<thead>'.
                    '<tr class="adm-list-table-header">'.
                        '<td class="adm-list-table-cell" style="width: 1px">'.
                            '<div class="adm-list-table-cell-inner"></div>'.
                        '</td>'.
                        '<td class="adm-list-table-cell">'.
                            '<div class="adm-list-table-cell-inner">'.Loc::getMessage('intec.measures.interaction.iblock.element.tabs.conversion.table.field.measure').'</div>'.
                        '</td>'.
                        '<td class="adm-list-table-cell">'.
                            '<div class="adm-list-table-cell-inner">'.Loc::getMessage('intec.measures.interaction.iblock.element.tabs.conversion.table.field.ratio').'</div>'.
                        '</td>'.
                    '</tr>'.
                '</thead>'.
                '<tbody>';

        foreach ($measures as $measure) {
            if ($arProduct['MEASURE'] == $measure['ID'])
                continue;

            $name = !empty($measure['MEASURE_TITLE']) ? $measure['MEASURE_TITLE'] : $measure['SYMBOL_LETTER_INTL'];
            $name .= ' ('.(!empty($measure['SYMBOL']) ? $measure['SYMBOL'] : $measure['SYMBOL_INTL']).')';

            $ratio = $ratios->get($measure['ID']);

            if (empty($ratio)) {
                $ratio = new ConversionRatio();
                $ratio->loadDefaultValues();
                $ratio->active = 0;
            }

            $content .=
                '<tr class="adm-list-table-row">'.
                    '<td class="adm-list-table-cell" style="width: 1px; padding-right: 10px">'.
                        Html::hiddenInput($ratio->formName().'['.$measure['ID'].'][active]', 0).
                        Html::checkbox($ratio->formName().'['.$measure['ID'].'][active]', $ratio->active, [
                            'value' => '1'
                        ]).
                    '</td>'.
                    '<td class="adm-list-table-cell">'.
                        Html::encode($name).
                    '</td>'.
                    '<td class="adm-list-table-cell">'.
                        Html::textInput($ratio->formName().'['.$measure['ID'].'][value]', $ratio->value).
                    '</td>'.
                '</tr>';
        }

        $content .= '</tbody></table>';
        $content = '<tr><td colspan="2">'.$content.'</td></tr>';

        $form->tabs[] = [
            'DIV' => 'measures_conversion',
            'TAB' => Loc::getMessage('intec.measures.interaction.iblock.element.tabs.conversion.name'),
            'TITLE' => Loc::getMessage('intec.measures.interaction.iblock.element.tabs.conversion.name'),
            'CONTENT' => $content
        ];
    }

    public static function handleConversionTab(&$fields)
    {
        if (!static::validateConversionTab($fields['IBLOCK_ID'], $fields['ID']))
            return;

        $request = Core::$app->request;

        if ($request->getIsPost()) {
            $post = $request->post();
            $formName = (new ConversionRatio())->formName();
            $measures = Module::getMeasures();
            $ratios = ConversionRatio::find()->where([
                'productId' => $fields['ID']
            ])->all()->indexBy('measureId');

            foreach ($measures as $measure) {
                $data = ArrayHelper::getValue($post, [$formName, $measure['ID']]);

                if (!Type::isArray($data) || empty($data))
                    continue;

                $ratio = $ratios->get($measure['ID']);

                if (empty($ratio)) {
                    $ratio = new ConversionRatio();
                    $ratio->loadDefaultValues();
                    $ratio->productId = $fields['ID'];
                    $ratio->measureId = $measure['ID'];
                    $ratio->active = 0;
                }

                if (isset($data['active']))
                    $ratio->active = $data['active'];

                if (isset($data['value']))
                    $ratio->value = $data['value'];

                $ratio->save();
            }
        }
    }
}