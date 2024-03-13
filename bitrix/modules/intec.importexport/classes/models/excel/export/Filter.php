<?php
namespace intec\importexport\models\excel\export;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\helpers\Json;
use intec\core\collections\Arrays;
use intec\core\base\InvalidParamException;
use intec\importexport\models\excel\IBlockHelper;

class Filter
{
    public static function getList ($iBlockId)
    {
        $iBlockId = Type::toInteger($iBlockId);

        if (empty($iBlockId))
            return [];

        $filterProperties = IBlockHelper::getMainProperties(false);
        $filterProperties = array_merge($filterProperties, IBlockHelper::getBaseProperties($iBlockId, null, false));
        $filterProperties = array_merge($filterProperties, IBlockHelper::getCatalogProperties($iBlockId, false));
        $filterProperties = array_merge($filterProperties, IBlockHelper::getOffersMainProperties(false));
        $filterProperties = array_merge($filterProperties, IBlockHelper::getOffersBaseProperties($iBlockId, null, false));

        if (empty($filterProperties))
            return [];

        $filterList = [
            'name' => [],
            'code' => []
        ];

        foreach ($filterProperties as $key => $filterProperty) {
            $filterList[$filterProperty['code']] = $filterProperty['name'];
        }

        return $filterList;
    }

    public static function getFilterListNames ($iBlockId = null)
    {
        $names = [];
        $isBase = Loader::includeModule('catalog');
        $prices = [];

        if ($isBase)
            $prices = Arrays::fromDBResult(\CCatalogGroup::GetList(["SORT" => "ASC"]))->asArray() ;

        $names[] = Loc::getMessage('intec.filter.field.id');
        $names[] = Loc::getMessage('intec.filter.field.section');
        $names[] = Loc::getMessage('intec.filter.field.modified.user.id');
        $names[] = Loc::getMessage('intec.filter.field.create.user.id');
        $names[] = Loc::getMessage('intec.filter.field.active.date.begin');
        $names[] = Loc::getMessage('intec.filter.field.active.date.end');
        $names[] = Loc::getMessage('intec.filter.field.active');
        $names[] = Loc::getMessage('intec.filter.field.sort');
        $names[] = Loc::getMessage('intec.filter.field.name');
        $names[] = Loc::getMessage('intec.filter.field.preview.text');
        $names[] = Loc::getMessage('intec.filter.field.detail.text');
        $names[] = Loc::getMessage('intec.filter.field.code');
        $names[] = Loc::getMessage('intec.filter.field.external');
        $names[] = Loc::getMessage('intec.filter.field.preview.picture');
        $names[] = Loc::getMessage('intec.filter.field.detail.picture');
        $names[] = Loc::getMessage('intec.filter.field.tags');

        if ($isBase) {
            $names[] = Loc::getMessage('intec.filter.field.product.type');
            $names[] = Loc::getMessage('intec.filter.field.bundle');
            $names[] = Loc::getMessage('intec.filter.field.available');
            $names[] = Loc::getMessage('intec.filter.field.quantity');
            $names[] = Loc::getMessage('intec.filter.field.purchase.price');

            if (!empty($prices)) {
                foreach ($prices as $price) {
                    $names[] = Loc::getMessage('intec.filter.field.purchase.price') . ' "' . $price['NAME_LANG'] . '"';
                }
            }
            $names[] = Loc::getMessage('intec.filter.field.weight');
            $names[] = Loc::getMessage('intec.filter.field.length');
            $names[] = Loc::getMessage('intec.filter.field.width');
            $names[] = Loc::getMessage('intec.filter.field.height');
            $names[] = Loc::getMessage('intec.filter.field.vat');
        }

        if (!empty($iBlockId)) {
            $properties = [];
            $properties = Arrays::fromDBResult(
                \CIBlockProperty::GetList(
                    ['SORT' => 'ASC', 'NAME' => 'ASC'],
                    ['IBLOCK_ID' => $iBlockId, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N',])
            )->asArray();

            foreach ($properties as $property) {
                if (($property['PROPERTY_TYPE'] === 'N' || $property['PROPERTY_TYPE'] === 'S' ||
                        $property['PROPERTY_TYPE'] === 'L' || $property['PROPERTY_TYPE'] === 'F' || $property['PROPERTY_TYPE'] === 'E') &&
                    $property['FILTRABLE'] === 'Y') {
                    $names[] = $property['NAME'];
                }
            }
        }


        return $names;
    }

    public static function getLogicOperation(&$val, $op)
    {
        switch ($op) {
            case 'eq':
                return '=';
                break;
            case 'neq':
                return '!=';
                break;
            case 'gt':
                return '>';
                break;
            case 'geq':
                return '>=';
                break;
            case 'lt':
                return '<';
                break;
            case 'leq':
                return '<=';
                break;
            case 'from_to':
                $val = array_map('trim', explode('-', $val));
                return '><';
                break;
            case 'empty':
                $val = false;
                return '';
                break;
            case 'not_empty':
                $val = false;
                return '!';
                break;
            case 'contain':
                return '%';
                break;
            case 'not_contain':
                return '!%';
                break;
            case 'logical':
                return '?';
                break;
        }
    }

    public static function ReplaceCatalogPrice($value)
    {
        return preg_replace("/^catalog_price_(\d+)$/", "$1", $value);
    }

    public static function getFilter ($formData, $iBlockId)
    {
        $isBase = Loader::includeModule('catalog');

        $filter = [];

        if (empty($formData))
            return [];

        if (!empty($iBlockId))
            $filter['IBLOCK_ID'] = $iBlockId;

        if (!empty($formData['id_start']))
            $filter['>=ID'] = $formData['id_start'];

        if (!empty($formData['id_end']))
            $filter['<=ID'] = $formData['id_end'];

        if (!empty($formData['section'])) {
            if (!IBlockHelper::hasValue('-1', $formData['section'])) {
                $filter['SECTION_ID'] = $formData['section'];
            }

            if (!empty($filter['SECTION_ID'])) {
                if (IBlockHelper::hasValue('0', $filter['SECTION_ID']) && $formData['include_subsections'] === 'Y') {
                    unset($filter['SECTION_ID']);
                } elseif ($formData['include_subsections'] === 'Y') {
                    $filter["INCLUDE_SUBSECTIONS"] = "Y";
                }
            }
        }

        if (!empty($formData['modified_user_id']))
            $filter['MODIFIED_USER_ID'] = $formData['modified_user_id'];

        if (!empty($formData['create_user_id']))
            $filter['CREATED_USER_ID'] = $formData['create_user_id'];

        /* need check */
        if($formData['active_date_from_mod']=='empty') {
            $filter["DATE_ACTIVE_FROM"] = false;
        } elseif ($formData['active_date_from_mod']=='not_empty') {
            $filter["!DATE_ACTIVE_FROM"] = false;
        } else {
            if(!empty($formData['active_date_from']))
                $filter[">=DATE_ACTIVE_FROM"] = $formData['active_date_from'];

            if(!empty($formData['active_date_from_to']))
                $filter["<=DATE_ACTIVE_FROM"] = $formData['active_date_from_to'];
        }
        /* need check */
        if($formData['active_date_to_mod']=='empty') {
            $filter["DATE_ACTIVE_FROM"] = false;
        } elseif ($formData['active_date_to_mod']=='not_empty') {
            $filter["!DATE_ACTIVE_FROM"] = false;
        } else {
            if(!empty($formData['active_date_to']))
                $filter[">=DATE_ACTIVE_FROM"] = $formData['active_date_to'];

            if(!empty($formData['active_date_to_from']))
                $filter["<=DATE_ACTIVE_FROM"] = $formData['active_date_to_from'];
        }

        if(!empty($formData['active']))
            $filter['ACTIVE'] = $formData['active'];

        if(!empty($formData['sort'])) {
            $log = self::getLogicOperation($formData['sort'], $formData['sort_comp']);
            $filter[$log.'SORT'] = $formData['sort'];
        }

        if(!empty($formData['name']))
            $filter['?NAME'] = $formData['name'];

        if (!empty($formData['preview_text_mod'])) {
            if($formData['preview_text_mod'] == 'empty')
                $filter['PREVIEW_TEXT'] = false;
            elseif($formData['preview_text_mod'] == 'not_empty')
                $filter['!PREVIEW_TEXT'] = false;
        } elseif (!empty($formData['preview_text'])) {
            $filter['?PREVIEW_TEXT'] = $formData['preview_text'];
        }

        if (!empty($formData['detail_text_mod'])) {
            if($formData['detail_text_mod'] == 'empty')
                $filter['DETAIL_TEXT'] = false;
            elseif($formData['detail_text_mod'] == 'not_empty')
                $filter['!DETAIL_TEXT'] = false;
        } elseif (!empty($formData['detail_text'])) {
            $filter['?DETAIL_TEXT'] = $formData['detail_text'];
        }

        if(!empty($formData['code']))
            $filter['?CODE'] = $formData['code'];

        if(!empty($formData['external_id']))
            $filter['EXTERNAL_ID'] = $formData['external_id'];

        if($formData['preview_picture'] == 'Y')
            $filter['!PREVIEW_PICTURE'] =  false;
        elseif($formData['preview_picture'] == 'N')
            $filter['PREVIEW_PICTURE'] =  false;

        if($formData['detail_picture'] == 'Y')
            $filter['!DETAIL_PICTURE'] =  false;
        elseif($formData['detail_picture'] == 'N')
            $filter['DETAIL_PICTURE'] =  false;

        if(!empty($formData['tags']))
            $filter['?TAGS'] = $formData['tags'];


        if ($isBase) {
            if(!empty($formData['catalog_type'])) {
                $catalogTypes = $formData['catalog_type'];

                if(Type::isArray($catalogTypes))
                    $catalogTypes = array_diff($catalogTypes, ['']);

                if(!empty($catalogTypes))
                    $filter['CATALOG_TYPE'] = $catalogTypes;
            }

            if(!empty($formData['catalog_bundle']))
                $filter['CATALOG_BUNDLE'] = $formData['catalog_bundle'];

            if(!empty($formData['catalog_available']))
                $filter['CATALOG_AVAILABLE'] = $formData['catalog_available'];

            /** finish later for offers */
            if (!empty($formData['catalog_quantity'])) {
                $log = self::getLogicOperation($formData['catalog_quantity'], $formData['catalog_quantity_comp']);
                $filter[$log.'CATALOG_QUANTITY'] = $formData['catalog_quantity'];
            }

            if (!empty($formData['catalog_purchasing_price'])) {
                $log = self::getLogicOperation($formData['catalog_purchasing_price'], $formData['catalog_purchasing_price_comp']);
                $filter[$log.'CATALOG_PURCHASING_PRICE'] = $formData['catalog_purchasing_price'];
            }


            $arPriceKeys = preg_grep('/^catalog_price_\d+$/', array_keys($formData));

            if(!empty($arPriceKeys)) {
                $arPriceKeys = array_unique(array_map([__CLASS__, 'ReplaceCatalogPrice'], $arPriceKeys));

                foreach($arPriceKeys as $priceKey) {
                    if(!empty($formData['catalog_price_'.$priceKey]) || $formData['catalog_price_'.$priceKey.'_comp'] == 'empty') {
                        $log = self::getLogicOperation($formData['catalog_price_'.$priceKey], $formData['catalog_price_'.$priceKey.'_comp']);
                        $filter[$log.'CATALOG_PRICE_'.$priceKey] = $formData['catalog_price_'.$priceKey];
                    }
                }
            }

            if (!empty($formData['catalog_weight']) || strpos($formData['catalog_weight_comp'], 'empty') !== false) {
                $log = self::getLogicOperation($formData['catalog_weight'], $formData['catalog_weight_comp']);
                $filter[$log.'WEIGHT'] = $formData['catalog_weight'];
            }

            if (!empty($formData['catalog_length']) || strpos($formData['catalog_length_comp'], 'empty') !== false) {
                $log = self::getLogicOperation($formData['catalog_length'], $formData['catalog_length_comp']);
                $filter[$log.'LENGTH'] = $formData['catalog_length'];
            }

            if (!empty($formData['catalog_width']) || strpos($formData['catalog_width_comp'], 'empty') !== false) {
                $log = self::getLogicOperation($formData['catalog_width'], $formData['catalog_width_comp']);
                $filter[$log.'WIDTH'] = $formData['catalog_width'];
            }

            if (!empty($formData['catalog_height']) || strpos($formData['catalog_height_comp'], 'empty') !== false) {
                $log = self::getLogicOperation($formData['catalog_height'], $formData['catalog_height_comp']);
                $filter[$log.'HEIGHT'] = $formData['catalog_height'];
            }
            /** finish later for offers */
        }

        $properties = [];
        $properties = Arrays::fromDBResult(
            \CIBlockProperty::GetList(
                ['SORT' => 'ASC', 'NAME' => 'ASC'],
                ['IBLOCK_ID' => $iBlockId, 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N',])
        )->asArray();

        foreach ($properties as $property) {
            if (($property['PROPERTY_TYPE'] === 'N' || $property['PROPERTY_TYPE'] === 'S' ||
                    $property['PROPERTY_TYPE'] === 'L' || $property['PROPERTY_TYPE'] === 'F' || $property['PROPERTY_TYPE'] === 'E') &&
                $property['FILTRABLE'] === 'Y') {

                if(Type::isArray($formData["property_".$property['ID']]) && isset($formData["property_".$property['ID']]['TYPE']))
                    $formData["property_".$property['ID']] = '';


                $value = $formData["property_".$property['ID']];
                $valueComp = $formData["property_".$property['ID']."_comp"];

                if(Type::isArray($value))
                    $value = array_diff(array_map('trim', $value), array(''));

                if((Type::isArray($value) && count($value)>0) || (!Type::isArray($value) && strlen($value)) ||
                    strpos($valueComp, 'empty')!==false) {

                    if(Type::isArray($value)) {
                        foreach($value as $k=>$v)
                        {
                            if($v === "NOT_REF") $value[$k] = false;
                        }
                    } elseif ($value === "NOT_REF") {
                        $value = false;
                    }

                    if($property["PROPERTY_TYPE"]=='E' && $property["USER_TYPE"]=='') {
                        $value = trim($value);
                        if(preg_match('/[,;\s\|]/', $value))
                        {
                            $filter[] = [
                                'LOGIC'=>'OR',
                                ["PROPERTY_".$property['ID'] => array_diff(array_map('trim', preg_split('/[,;\s\|]/', $value)), [''])],
                                ["PROPERTY_".$property['ID'].".NAME" => array_diff(array_map('trim', preg_split('/[,;\|]/', $value)), [''])]
                            ];
                        }
                        else
                        {
                            $filter[] = [
                                'LOGIC'=>'OR',
                                ["PROPERTY_".$property['ID'] => $value],
                                ["PROPERTY_".$property['ID'].".NAME" => $value]
                            ];
                        }
                    } elseif($property["PROPERTY_TYPE"]=='N' && $property["USER_TYPE"]=='') {
                        $value = trim($value);
                        $log = self::getLogicOperation($value, $formData["property_".$property['ID']."_comp"]);
                        $filter[$log.'PROPERTY_'.$property['ID']] = $value;
                    } elseif($property["PROPERTY_TYPE"]=='F') {
                        if($formData['property_'.$property['ID']]=='Y') $filter['!PROPERTY_'.$property['ID']] =  false;
                        elseif($formData['property_'.$property['ID']]=='N') $filter['PROPERTY_'.$property['ID']] =  false;
                    } else {
                        $log = self::getLogicOperation($value, $formData["property_".$property['ID']."_comp"]);
                        $filter[$log."PROPERTY_".$property['ID']] = $value;
                    }
                }
            }
        }

        return $filter;
    }

    public static function showFilter ($templateId)
    {
        Loader::includeModule('iblock');
        $isBase = Loader::includeModule('catalog');

        $template = Template::findOne($templateId);

        try {
            $parameters = Json::decode($template->getAttribute('params'));
        } catch (InvalidParamException $exception) {
            $parameters = [];
        }

        try {
            $tableParameters = Json::decode($template->getAttribute('tableParams'));
        } catch (InvalidParamException $exception) {
            $tableParameters = [];
        }

        $fields = [];

        if (!empty($tableParameters['filter']))
            $fields = $tableParameters['filter'];


        $filterList = self::getList($parameters['iblock']);

        $sections = Arrays::fromDBResult(\CIBlockSection::GetTreeList(["IBLOCK_ID" => $parameters['iblock']], ["ID", "NAME", "DEPTH_LEVEL"]))->asArray();

        if ($isBase) {
            $productTypeList = \CCatalogAdminTools::getIblockProductTypeList($parameters['iblock'], true);
            $prices = Arrays::fromDBResult(\CCatalogGroup::GetList(["SORT" => "ASC"]))->asArray() ;
        }

        $properties = [];
        $properties = Arrays::fromDBResult(
                \CIBlockProperty::GetList(
                        ['SORT' => 'ASC', 'NAME' => 'ASC'],
                        ['IBLOCK_ID' => $parameters['iblock'], 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N',])
        )->asArray();



        if (!empty($filterList)) {
            ?>

            <tr>
                <td><?= $filterList['ID'] ?>:</td>
                <td nowrap>
                    <input type="text" name="data[tableParameters][filter][id_start]" size="10" value="<?= htmlspecialcharsex($fields['id_start'])?>">
                    ...
                    <input type="text" name="data[tableParameters][filter][id_end]" size="10" value="<?= htmlspecialcharsex($fields['id_end'])?>">
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.section') ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][section][]" multiple size="5">
                        <option value="-1"><?= Loc::getMessage('intec.filter.value.section.any') ?></option>
                        <option value="0"<?if ((Type::isArray($fields['section']) && in_array("0", $fields['section'])) || $fields['section']=="0")echo" selected"?>>
                            <?echo Loc::getMessage('intec.filter.value.section.cor')?>
                        </option>
                        <?php foreach ($sections as $section) { ?>
                            <option value="<?= $section['ID']?>"
                                <?if((Type::isArray($fields['section']) && in_array($section['ID'], $fields['section'])) || $section['ID']==$fields['section'])echo " selected"?>>
                                <?= str_repeat("&nbsp;.&nbsp;", $section['DEPTH_LEVEL']) . ' ' . $section['NAME'] ?>
                            </option>
                        <?php } ?>
                    </select><br>
                    <?= Loc::getMessage("intec.filter.field.include.subsection")?> <input type="checkbox" name="data[tableParameters][filter][include_subsections]" value="Y" <?= $fields['include_subsections'] === "Y" ? "checked" : null?>>
                </td>
            </tr>
            <?/*
            <tr>
                <td>
                    EDIT DATE
                </td>
                <td>

                </td>
            </tr>
            */?>
            <tr>
                <td><?= Loc::getMessage('intec.filter.field.modified.user.id') ?> </td>
                <td>
                    <?= FindUserID(
                        "data[tableParameters][filter][modified_user_id]",
                        $fields['modified_user_id'],
                        "",
                        "post_form",
                        "5",
                        "",
                        " ... ",
                        "",
                        ""
                    );?>
                </td>
            </tr>
            <?/*
            <tr>
                <td>
                    CREATE DATE
                </td>
                <td>

                </td>
            </tr>
            */?>
            <tr>
                <td><?= Loc::getMessage('intec.filter.field.create.user.id') ?> </td>
                <td>
                    <?= FindUserID(
                        "data[tableParameters][filter][create_user_id]",
                        $fields['create_user_id'],
                        "",
                        "post_form",
                        "5",
                        "",
                        " ... ",
                        "",
                        ""
                    );?>
                </td>
            </tr>

            <?/*fix later ( hide calendar block)*/?>
            <tr>
                <td><?= Loc::getMessage('intec.filter.field.active.date.begin') ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][active_date_from_mod]">
                        <option value="">
                            <?= Loc::getMessage('intec.filter.from.to')?>
                        </option>
                        <option value="empty"<?=  $fields['active_date_from_mod'] == 'empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.empty')?>
                        </option>
                        <option value="not_empty"<?= $fields['active_date_from_mod'] == 'not_empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.not.empty')?>
                        </option>
                    </select>
                    <?= CalendarPeriod("data[tableParameters][filter][active_date_from]", htmlspecialcharsex($fields['active_date_from']), "data[tableParameters][filter][active_date_from_to]", htmlspecialcharsex($fields['active_date_from_to']), "dataload")?>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.active.date.end') ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][active_date_to_mod]">
                        <option value="">
                            <?= Loc::getMessage('intec.filter.from.to')?>
                        </option>
                        <option value="empty"<?=  $fields['active_date_to_mod'] == 'empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.empty')?>
                        </option>
                        <option value="not_empty"<?= $fields['active_date_to_mod'] == 'not_empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.not.empty')?>
                        </option>
                    </select>
                    <?= CalendarPeriod("data[tableParameters][filter][active_date_to]", htmlspecialcharsex($fields['active_date_to']), "data[tableParameters][filter][active_date_to_from]", htmlspecialcharsex($fields['active_date_to_from']), "dataload")?>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.active') ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][active]">
                        <option value="">
                            <?=htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?>
                        </option>
                        <option value="Y"<?= $fields['active'] == "Y" ? " selected" : null ?>>
                            <?=htmlspecialcharsex(Loc::getMessage("intec.filter.yes"))?>
                        </option>
                        <option value="N"<?= $fields['active'] == "N" ? " selected" : null ?>>
                            <?=htmlspecialcharsex(Loc::getMessage("intec.filter.no"))?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage("intec.filter.field.sort") ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][sort_comp]">
                        <option value="eq" <?= $fields['sort_comp'] == 'eq' ? 'selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.eq') ?>
                        </option>
                        <option value="gt" <?= $fields['sort_comp'] == 'gt' ? 'selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.gt') ?>
                        </option>
                        <option value="geq" <?= $fields['sort_comp'] == 'geq' ? 'selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.geq') ?>
                        </option>
                        <option value="lt" <?= $fields['sort_comp'] == 'lt' ? 'selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.lt') ?>
                        </option>
                        <option value="leq" <?= $fields['sort_comp'] == 'leq' ? 'selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.leq') ?>
                        </option>
                    </select>
                    <input type="text" name="data[tableParameters][filter][sort]" value="<?= htmlspecialcharsex($fields['sort']) ?>" size="10">
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.name') ?>:</td>
                <td>
                    <input type="text" name="data[tableParameters][filter][name]" value="<?echo htmlspecialcharsex($fields['name'])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.preview.text') ?></td>
                <td>
                    <select name="data[tableParameters][filter][preview_text_mod]">
                        <option value="">
                            <?= Loc::getMessage('intec.filter.value') ?>
                        </option>
                        <option value="empty"<?= $fields['preview_text_mod'] == 'empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.empty') ?>
                        </option>
                        <option value="not_empty" <?= $fields['preview_text_mod'] == 'not_empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.not.empty') ?>
                        </option>
                    </select>
                    <input type="text" name="data[tableParameters][filter][preview_text]" value="<?echo htmlspecialcharsex($fields['preview_text'])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.detail.text') ?></td>
                <td>
                    <select name="data[tableParameters][filter][detail_text_mod]">
                        <option value="">
                            <?= Loc::getMessage('intec.filter.value') ?>
                        </option>
                        <option value="empty" <?= $fields['detail_text_mod'] == 'empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.empty') ?>
                        </option>
                        <option value="not_empty" <?= $fields['detail_text_mod'] == 'not_empty' ? ' selected' : null ?>>
                            <?= Loc::getMessage('intec.filter.not.empty') ?>
                        </option>
                    </select>
                    <input type="text" name="data[tableParameters][filter][detail_text]" value="<?= htmlspecialcharsex($fields['detail_text']) ?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.code') ?>:</td>
                <td>
                    <input type="text" name="data[tableParameters][filter][code]" value="<?= htmlspecialcharsex($fields['code']) ?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
                </td>
            </tr>
            <tr>
                <td><?= Loc::getMessage('intec.filter.field.external') ?>:</td>
                <td>
                    <input type="text" name="data[tableParameters][filter][external_id]" value="<?= htmlspecialcharsex($fields['external_id']) ?>" size="30">
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.preview.picture') ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][preview_picture]">
                        <option value="">
                            <?= htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?>
                        </option>
                        <option value="Y"<?= $fields['preview_picture'] == "Y" ? " selected" : null ?>>
                            <?= htmlspecialcharsex(Loc::getMessage('intec.filter.not.empty'))?>
                        </option>
                        <option value="N"<?= $fields['preview_picture'] == "N" ? " selected" : null ?>>
                            <?= htmlspecialcharsex(Loc::getMessage('intec.filter.empty'))?>
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><?= Loc::getMessage('intec.filter.field.detail.picture') ?>:</td>
                <td>
                    <select name="data[tableParameters][filter][detail_picture]">
                        <option value="">
                            <?= htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?></option>
                        <option value="Y"<?= $fields['detail_picture'] == "Y" ? " selected" : null ?>>
                            <?= htmlspecialcharsex(Loc::getMessage('intec.filter.not.empty'))?>
                        </option>
                        <option value="N"<?= $fields['detail_picture'] == "N" ? " selected" : null ?>>
                            <?= htmlspecialcharsex(Loc::getMessage('intec.filter.empty'))?>
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <td><?= Loc::getMessage('intec.filter.field.tags') ?>:</td>
                <td>
                    <input type="text" name="data[tableParameters][filter][tags]" value="<?echo htmlspecialcharsex($fields['tags'])?>" size="30">
                </td>
            </tr>

            <?php if ($isBase) { ?>
                <?php if (Type::isArray($productTypeList)) { ?>
                    <tr>
                        <td><?= Loc::getMessage('intec.filter.field.product.type') ?>:</td>
                        <td>
                            <select name="data[tableParameters][filter][catalog_type][]" multiple>
                                <option value="">
                                    <?= htmlspecialcharsex(Loc::getMessage('intec.filter.any')) ?>
                                </option>
                                <?php $catalogTypes = (!empty($arFields['catalog_type']) ? $arFields['catalog_type'] : array()); ?>
                                <?php foreach ($productTypeList as $productType => $productTypeName) { ?>
                                    <option value="<?= $productType ?>"<?= (in_array($productType, $catalogTypes) ? ' selected' : '') ?>>
                                        <?= htmlspecialcharsex($productTypeName); ?>
                                    </option>
                                <?php } ?>
                                <?php unset($productType, $productTypeName, $catalogTypes); ?>
                            </select>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.bundle') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_bundle]">
                            <option value="">
                                <?=htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?>
                            </option>
                            <option value="Y"<?= $fields['catalog_bundle'] == "Y" ? " selected" : null ?>>
                                <?=htmlspecialcharsex(Loc::getMessage("intec.filter.yes"))?>
                            </option>
                            <option value="N"<?= $fields['catalog_bundle'] == "N" ? " selected" : null ?>>
                                <?=htmlspecialcharsex(Loc::getMessage("intec.filter.no"))?>
                            </option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.available') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_available]">
                            <option value="">
                                <?=htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?>
                            </option>
                            <option value="Y"<?= $fields['catalog_available'] == "Y" ? " selected" : null ?>>
                                <?=htmlspecialcharsex(Loc::getMessage("intec.filter.yes"))?>
                            </option>
                            <option value="N"<?= $fields['catalog_available'] == "N" ? " selected" : null ?>>
                                <?=htmlspecialcharsex(Loc::getMessage("intec.filter.no"))?>
                            </option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.quantity') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_quantity_comp]">
                            <option value="eq" <?= $fields['catalog_quantity_comp'] == 'eq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.eq') ?>
                            </option>
                            <option value="gt" <?= $fields['catalog_quantity_comp'] == 'gt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.gt') ?>
                            </option>
                            <option value="geq" <?= $fields['catalog_quantity_comp'] == 'geq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.geq') ?>
                            </option>
                            <option value="lt" <?= $fields['catalog_quantity_comp'] == 'lt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.lt') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_quantity_comp'] == 'leq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.leq') ?>
                            </option>
                        </select>
                        <input type="text" name="data[tableParameters][filter][catalog_quantity]" value="<?= htmlspecialcharsex($fields['catalog_quantity']) ?>" size="10">
                    </td>
                </tr>

                <?php /* stors */ ?>

                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.purchase.price') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_purchasing_price_comp]">
                            <option value="eq" <?= $fields['catalog_purchasing_price_comp'] == 'eq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.eq') ?>
                            </option>
                            <option value="gt" <?= $fields['catalog_purchasing_price_comp'] == 'gt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.gt') ?>
                            </option>
                            <option value="geq" <?= $fields['catalog_purchasing_price_comp'] == 'geq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.geq') ?>
                            </option>
                            <option value="lt" <?= $fields['catalog_purchasing_price_comp'] == 'lt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.lt') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_purchasing_price_comp'] == 'leq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.leq') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_purchasing_price_comp'] == 'from_to' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.from.to') ?>
                            </option>
                        </select>
                        <input type="text" name="data[tableParameters][filter][catalog_purchasing_price]" value="<?= htmlspecialcharsex($fields['catalog_purchasing_price']) ?>" size="10">
                    </td>
                </tr>

                <?php if (!empty($prices)) { ?>
                    <?php foreach ($prices as $price) { ?>
                        <tr>
                            <td><?= Loc::getMessage('intec.filter.field.purchase.price') . ' "' . $price['NAME_LANG'] . '"' ?>:</td>
                            <td>
                                <select name="data[tableParameters][filter][catalog_price_<?= $price['ID'] ?>_comp]">
                                    <option value="eq" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'eq' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.eq') ?>
                                    </option>
                                    <option value="gt" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'empty' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.empty') ?>
                                    </option>
                                    <option value="gt" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'gt' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.gt') ?>
                                    </option>
                                    <option value="geq" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'geq' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.geq') ?>
                                    </option>
                                    <option value="lt" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'lt' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.lt') ?>
                                    </option>
                                    <option value="leq" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'leq' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.leq') ?>
                                    </option>
                                    <option value="leq" <?= $fields['catalog_price_' . $price['ID'] . '_comp'] == 'from_to' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.from.to') ?>
                                    </option>
                                </select>
                                <input type="text" name="data[tableParameters][filter][catalog_price_<?= $price['ID'] ?>]" value="<?= htmlspecialcharsex($fields['catalog_price_' . $price['ID']]) ?>" size="10">
                            </td>
                        </tr>
                    <?php } ?>
                <?php } ?>


                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.weight') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_weight_comp]">
                            <option value="eq" <?= $fields['catalog_weight_comp'] == 'eq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.eq') ?>
                            </option>
                            <option value="gt" <?= $fields['catalog_weight_comp'] == 'gt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.gt') ?>
                            </option>
                            <option value="geq" <?= $fields['catalog_weight_comp'] == 'geq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.geq') ?>
                            </option>
                            <option value="lt" <?= $fields['catalog_weight_comp'] == 'lt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.lt') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_weight_comp'] == 'leq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.leq') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_weight_comp'] == 'from_to' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.from.to') ?>
                            </option>
                        </select>
                        <input type="text" name="data[tableParameters][filter][catalog_weight]" value="<?= htmlspecialcharsex($fields['catalog_weight']) ?>" size="10">
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.length') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_length_comp]">
                            <option value="eq" <?= $fields['catalog_length_comp'] == 'eq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.eq') ?>
                            </option>
                            <option value="gt" <?= $fields['catalog_length_comp'] == 'gt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.gt') ?>
                            </option>
                            <option value="geq" <?= $fields['catalog_length_comp'] == 'geq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.geq') ?>
                            </option>
                            <option value="lt" <?= $fields['catalog_length_comp'] == 'lt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.lt') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_length_comp'] == 'leq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.leq') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_length_comp'] == 'from_to' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.from.to') ?>
                            </option>
                        </select>
                        <input type="text" name="data[tableParameters][filter][catalog_length]" value="<?= htmlspecialcharsex($fields['catalog_length']) ?>" size="10">
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.width') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_width_comp]">
                            <option value="eq" <?= $fields['catalog_width_comp'] == 'eq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.eq') ?>
                            </option>
                            <option value="gt" <?= $fields['catalog_width_comp'] == 'gt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.gt') ?>
                            </option>
                            <option value="geq" <?= $fields['catalog_width_comp'] == 'geq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.geq') ?>
                            </option>
                            <option value="lt" <?= $fields['catalog_width_comp'] == 'lt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.lt') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_width_comp'] == 'leq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.leq') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_width_comp'] == 'from_to' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.from.to') ?>
                            </option>
                        </select>
                        <input type="text" name="data[tableParameters][filter][catalog_width]" value="<?= htmlspecialcharsex($fields['catalog_width']) ?>" size="10">
                    </td>
                </tr>
                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.height') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_height_comp]">
                            <option value="eq" <?= $fields['catalog_height_comp'] == 'eq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.eq') ?>
                            </option>
                            <option value="gt" <?= $fields['catalog_height_comp'] == 'gt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.gt') ?>
                            </option>
                            <option value="geq" <?= $fields['catalog_height_comp'] == 'geq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.geq') ?>
                            </option>
                            <option value="lt" <?= $fields['catalog_height_comp'] == 'lt' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.lt') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_height_comp'] == 'leq' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.leq') ?>
                            </option>
                            <option value="leq" <?= $fields['catalog_height_comp'] == 'from_to' ? 'selected' : null ?>>
                                <?= Loc::getMessage('intec.filter.from.to') ?>
                            </option>
                        </select>
                        <input type="text" name="data[tableParameters][filter][catalog_height]" value="<?= htmlspecialcharsex($fields['catalog_height']) ?>" size="10">
                    </td>
                </tr>

                <tr>
                    <td><?= Loc::getMessage('intec.filter.field.vat') ?>:</td>
                    <td>
                        <select name="data[tableParameters][filter][catalog_vat]">
                            <option value="">
                                <?=htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?>
                            </option>
                            <option value="Y"<?= $fields['catalog_vat'] == "Y" ? " selected" : null ?>>
                                <?=htmlspecialcharsex(Loc::getMessage("intec.filter.yes"))?>
                            </option>
                            <option value="N"<?= $fields['catalog_vat'] == "N" ? " selected" : null ?>>
                                <?=htmlspecialcharsex(Loc::getMessage("intec.filter.no"))?>
                            </option>
                        </select>
                    </td>
                </tr>
            <?php } ?>


            <?php foreach ($properties as $property) { ?>
                <?php if (($property['PROPERTY_TYPE'] === 'N' || $property['PROPERTY_TYPE'] === 'S' ||
                        $property['PROPERTY_TYPE'] === 'L' || $property['PROPERTY_TYPE'] === 'F' || $property['PROPERTY_TYPE'] === 'E') &&
                        $property['FILTRABLE'] === 'Y') { ?>
                    <tr>
                        <td><?= $property["NAME"] ?>:</td>
                        <td>
                            <?php if ($property['PROPERTY_TYPE'] == 'S') { ?>
                                <?php
                                    if(Type::isArray($fields["property_".$property['ID']]) && isset($fields["property_".$property['ID']]['TYPE']))
                                        $fields["property_".$property['ID']] = '';
                                ?>
                                <select name="data[tableParameters][filter][property_<?=$property['ID']?>_comp]">
                                    <option value="eq" <?= $fields['property_'.$property['ID'].'_comp']=='eq' ? 'selected' : null ?>>
                                        <?=Loc::getMessage('intec.filter.eq')?>
                                    </option>
                                    <option value="neq" <?= $fields['property_'.$property['ID'].'_comp']=='neq' ? 'selected' : null ?>>
                                        <?=Loc::getMessage('intec.filter.neq')?>
                                    </option>
                                    <option value="empty" <?= $fields['property_'.$property['ID'].'_comp']=='empty' ? 'selected' : null ?>>
                                        <?=Loc::getMessage('intec.filter.empty')?>
                                    </option>
                                    <option value="not_empty" <?= $fields['property_'.$property['ID'].'_comp']=='not_empty' ? 'selected' : null ?>>
                                        <?=Loc::getMessage('intec.filter.not.empty')?>
                                    </option>
                                    <option value="contain" <?= $fields['property_'.$property['ID'].'_comp']=='contain' ? 'selected' : null ?>>
                                        <?=Loc::getMessage('intec.filter.contain')?>
                                    </option>
                                    <option value="not_contain" <?= $fields['property_'.$property['ID'].'_comp']=='not_contain' ? 'selected' : null?>>
                                        <?=Loc::getMessage('intec.filter.not.contain')?>
                                    </option>
                                    <option value="logical" <?= $fields['property_'.$property['ID'].'_comp']=='logical' ? 'selected' : null ?>>
                                        <?=Loc::getMessage('intec.filter.logic')?>
                                    </option>
                                </select>
                                <input type="text" name="data[tableParameters][filter][property_<?= $property['ID'] ?>]" value="<?= htmlspecialcharsex(Type::isArray($fields["property_".$property['ID']]) ? '' : $fields["property_".$property['ID']])?>" size="30">&nbsp;<?=ShowFilterLogicHelp()?>
                            <?php } elseif ($property['PROPERTY_TYPE']=='N') { ?>
                                <select name="data[tableParameters][filter][property_<?= $property['ID'] ?>_comp]">
                                    <option value="eq" <?= $fields['property_'.$property['ID'].'_comp'] == 'eq' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.eq') ?>
                                    </option>
                                    <option value="gt" <?= $fields['property_'.$property['ID'].'_comp'] == 'gt' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.gt') ?>
                                    </option>
                                    <option value="geq" <?= $fields['property_'.$property['ID'].'_comp'] == 'geq' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.geq') ?>
                                    </option>
                                    <option value="lt" <?= $fields['property_'.$property['ID'].'_comp'] == 'lt' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.lt') ?>
                                    </option>
                                    <option value="leq" <?= $fields['property_'.$property['ID'].'_comp'] == 'leq' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.leq') ?>
                                    </option>
                                    <option value="leq" <?= $fields['property_'.$property['ID'].'_comp'] == 'from_to' ? 'selected' : null ?>>
                                        <?= Loc::getMessage('intec.filter.from.to') ?>
                                    </option>
                                </select>
                                <input type="text" name="data[tableParameters][filter][property_<?= $property['ID'] ?>]" value="<?= htmlspecialcharsex(Type::isArray($fields["property_".$property['ID']]) ? '' : $fields["property_".$property['ID']])?>" size="10">
                            <?php } elseif ($property['PROPERTY_TYPE']=='E') { ?>
                                <input type="text" name="data[tableParameters][filter][property_<?=$property['ID']?>]" value="<?echo htmlspecialcharsex(Type::isArray($fields["property_".$property['ID']]) ? '' : $fields["property_".$property['ID']])?>" size="30">
                            <?php } elseif ($property['PROPERTY_TYPE']=='L') { ?>
                                <?php
                                    $propVal = $fields['property_' . $property['ID']];

                                    if(!Type::isArray($propVal))
                                        $propVal = [$propVal];
                                ?>
                                <select name="data[tableParameters][filter][property_<?=$property['ID']?>][]" multiple size="5">
                                    <option value="">
                                        <?= Loc::getMessage('intec.filter.any')?>
                                    </option>
                                    <option value="NOT_REF"<?if(in_array("NOT_REF", $propVal))echo " selected"?>>
                                        <?= Loc::getMessage('intec.filter.not.set')?>
                                    </option>
                                    <?php $enums = Arrays::fromDBResult(\CIBlockPropertyEnum::GetList(['SORT' => 'ASC', 'NAME' => 'ASC'], ['PROPERTY_ID' => $property['ID']]))->asArray(); ?>
                                    <?php foreach ($enums as $enum) { ?>
                                        <option value="<?= $enum['ID'] ?>" <?= in_array($enum['ID'], $propVal) ? " selected" : null ?>>
                                            <?=$enum["VALUE"]?>
                                        </option>
                                    <?php } ?>
                                </select>
                            <?php } elseif ($property['PROPERTY_TYPE']=='F') { ?>
                                <select name="data[tableParameters][filter][property_<?= $property['ID'] ?>]">
                                    <option value="">
                                        <?=htmlspecialcharsex(Loc::getMessage('intec.filter.any'))?>
                                    </option>
                                    <option value="Y"<?= $fields['catalog_vat'] == "Y" ? " selected" : null ?>>
                                        <?=htmlspecialcharsex(Loc::getMessage("intec.filter.not.empty"))?>
                                    </option>
                                    <option value="N"<?= $fields['catalog_vat'] == "N" ? " selected" : null ?>>
                                        <?=htmlspecialcharsex(Loc::getMessage("intec.filter.empty"))?>
                                    </option>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>

            <?php
        }
    }
}