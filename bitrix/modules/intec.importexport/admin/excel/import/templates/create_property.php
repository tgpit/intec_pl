<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use intec\Core;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\base\InvalidParamException;
use intec\importexport\models\excel\import\Template;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.importexport'))
    return;

$isBase = Loader::includeModule('catalog');

Loc::loadMessages(__FILE__);

$arJsConfig = array(
    'script_export' => array(
        'js' => '/bitrix/js/intec.importexport/script_export.js'
    )
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

Core::$app->web->js->loadExtensions(['vue', 'jquery', 'jquery_extensions', 'intec_core']);
CJSCore::Init(array('script_export'));

$request = Core::$app->request;
$columnId = $request->post('columnId');
$action = $request->post('action');
$templateId = $request->post('templateId');
$value = $request->post('value');
$data = $request->post('data');
/*$valid = false;
$isSave = false;*/

if($action == 'save') {

    $update = true; //update if property exist
    $checkCode = true; //
    $result = false;
    $error = null;
    /*$valid = true;
    $isSave = true;*/

    if (!empty($templateId)) {
        $template = Template::findOne($templateId);
        if (!$template->getIsNewRecord()) {
            try {
                $parameters = Json::decode($template->getAttribute('params'));
            } catch (InvalidParamException $exception) {
                $parameters = [];
            }
        }
    }

    $filter = [
        'IBLOCK_ID' => $parameters['iblock'],
        'NAME' => $data['name']
    ];

    if ($checkCode && !empty($data['code'])) {
        $filter['CODE'] = $data['code'];
        unset($filter['NAME']);
    }

    $propertyId = Arrays::fromDBResult(CIBlockProperty::GetList([],$filter))->asArray();

    if (!empty($propertyId)) {
        /*if (!empty($filter['CODE'])) {
            $data['code'] = $data['code'] . '_' . count($propertyId);
        }*/

        $propertyId = ArrayHelper::getFirstValue($propertyId);
        $propertyId = $propertyId['ID'];
    }

    $type = explode(':', $data['type']);

    $fields = [
        'IBLOCK_ID' => $parameters['iblock'],
        'NAME' => Encoding::convertEncodingToCurrent($data['name']),
        'CODE' => $data['code'],
        'SORT' => $data['sort'],
        'ACTIVE' => $data['active'],
        'MULTIPLE' => $data['multiply'],
        'IS_REQUIRED' => $data['required'],
        'PROPERTY_TYPE' => $type[0],
        'USER_TYPE' => empty($type[1]) ? '' : $type[1],
        'SEARCHABLE' => $data['search'],
        'WITH_DESCRIPTION' => $data['description'],
        'FILTRABLE' => $data['listFilter'],
        'SECTION_PROPERTY' => $data['editPage'],
        'SMART_FILTER' => $data['smartFilter']
    ];

    $property = new CIBlockProperty;

    if ($update && !empty($propertyId)) {
        $result = $property->Update(
            $propertyId,
            $fields
        );

        if (!$result)
            $error = $property->LAST_ERROR;
        else
            $result = $propertyId;
    } elseif (empty($propertyId)) {
        $result = $property->add($fields);

        if (!$result)
            $error = $property->LAST_ERROR;
    }

    $code = '';

    if (!empty($result)) {
        if (empty($data['code']))
            $code = 'EMPTY_PROPERTY_' . $result;
        else
            $code = $data['code'];
    }

    if (!$result)
        $result = 0;

    $result = [
        'result' => $result,
        'code' => $code,
        'columnId' => $columnId,
        'error' => $error
    ];

    $resultJson = Json::encode($result, 320, true);

    echo '<script>JHelpers.SetCreatedProperty(' . $resultJson . ')</script>';
    die();
}


$iblockTypeOption = [
    'products' => Loc::getMessage('option.for.iblock.type.products'),
    'offers' => Loc::getMessage('option.for.iblock.type.offers')
];

$typeOptions = [
    Loc::getMessage('option.type.group.base') => [
        'S' => Loc::getMessage('option.type.string'),
        'N' => Loc::getMessage('option.type.number'),
        'L' => Loc::getMessage('option.type.list'),
        'F' => Loc::getMessage('option.type.file'),
        'G' => Loc::getMessage('option.type.link.section'),
        'E' => Loc::getMessage('option.type.link.element'),
    ],
    Loc::getMessage('option.type.group.user') => [
        'S:HTML' => Loc::getMessage('option.type.html'),
        'S:video' => Loc::getMessage('option.type.video'),
        'S:Date' => Loc::getMessage('option.type.date'),
        'S:DateTime' => Loc::getMessage('option.type.date.time'),
        'S:Money' => Loc::getMessage('option.type.money'),
        'S:map_yandex' => Loc::getMessage('option.type.map.yandex'),
        'S:map_google' => Loc::getMessage('option.type.map.google'),
        'S:UserID' => Loc::getMessage('option.type.link.user'),
        'G:SectionAuto' => Loc::getMessage('option.type.link.section.auto'),
        'S:TopicID' => Loc::getMessage('option.type.link.forum'),
        'E:SKU' => Loc::getMessage('option.type.link.sku'),
        'S:FileMan' => Loc::getMessage('option.type.link.server.file'),
        'E:EList' => Loc::getMessage('option.type.link.list'),
        'S:ElementXmlID' => Loc::getMessage('option.type.link.xml'),
        'E:EAutocomplete' => Loc::getMessage('option.type.link.element.auto'),
        'S:directory' => Loc::getMessage('option.type.highload'),
        'N:Sequence' => Loc::getMessage('option.type.counter'),
    ]
];

?>

<form class="m-intec-importexport p-field-create-property" action="" method="post" enctype="multipart/form-data" name="create_property">
    <input type="hidden" name="templateId" value="<?= $templateId ?>">
    <input type="hidden" name="columnId" value="<?= $columnId ?>">
    <input type="hidden" name="action" value="save">
    <div>
        <table width="100%">
            <tbody>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.for.iblock.type') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <select name='data[iblockType]'>
                            <?= Html::renderSelectOptions('string', $iblockTypeOption) ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.type') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <select name='data[type]'>
                            <?= Html::renderSelectOptions('string', $typeOptions) ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.active') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[active]', true) ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><b><?= Loc::getMessage('field.name') . ':' ?></b></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::textInput('data[name]', '', ['data-role' => 'name']) ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.code') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::textInput('data[code]', '', ['data-role' => 'code']) ?>
                        <input data-role="code.translit" type="button" name="translit" value="<?= Loc::getMessage('btn.translit') ?>" title="<?= Loc::getMessage('hint.translit') ?>">
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.sort') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::textInput('data[sort]', '500') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.multiply') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[multiply]') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.required') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[required]') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.participate.in.search') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[search]') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.show.on.list.filter') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[listFilter]') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.show.in.smart.filter') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[smartFilter]') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.description') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[description]') ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l"><?= Loc::getMessage('field.show.on.edit.page') . ':' ?></td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <?= Html::checkbox('data[editPage]') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>

<script type="text/javascript">
    (function ($, api) {

        $(document).ready(function(){

            $('[name="create_property"]').keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            var translitFields = {
                'name': $('[data-role="name"]'),
                'code': $('[data-role="code"]'),
                'button': $('[data-role="code.translit"]')
            };

            translitFields.button.on('click', function () {
                var name = translitFields.name.val();
                var newCode = BX.translit(name.trim(), {'change_case': 'U', 'replace_space': '_' });
                translitFields.code.val(newCode);
            });
        });
    })(jQuery, intec);
</script>

