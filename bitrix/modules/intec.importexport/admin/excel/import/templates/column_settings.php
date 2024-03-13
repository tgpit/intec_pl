<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use intec\Core;
use intec\core\helpers\Json;
use intec\core\helpers\JavaScript;
use intec\core\base\InvalidParamException;
use intec\importexport\models\excel\TableHelper;
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
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/column_settings.css');
CJSCore::Init(array('script_export'));

$request = Core::$app->request;
$action = $request->post('action');
$templateId = $request->post('templateId');
$fieldId = $request->post('fieldId');
$value = $request->post('value');
$columnCount = $request->post('columnCount');
$data = $request->post('data');

if($action == 'save') {
    $formattedData = [];

    if (!empty($data)) {
        for ($i = 0; $i < count($data['field']); $i++) {

            if ((empty($data['whenValue'][$i]) && $data['whenValue'][$i] !== '0') && (empty($data['thenValue'][$i]) && $data['thenValue'][$i] !== '0'))
                continue;

            $formattedData[] = [
                'field' => $data['field'][$i],
                'when' => $data['when'][$i],
                'whenValue' => Encoding::convertEncodingToCurrent($data['whenValue'][$i]),
                'then' => $data['then'][$i],
                'thenValue' => Encoding::convertEncodingToCurrent($data['thenValue'][$i])
            ];
        }
    }

    if (empty($formattedData))
        $formattedData = null;
    else
        $formattedData = Json::encode($formattedData, 320, true);


    echo '<script>JHelpers.SetExtraParams("' . $fieldId . '", ' . $formattedData . ')</script>';
    die();
}

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

$propertiesOptions = [];
$propertiesOptions['CURRENT'] = Loc::getMessage('option.condition.current');

for ($i = 0; $i < $columnCount; $i++) {
    $propertiesOptions[$i] = Loc::getMessage('option.condition.cell') . ' ' . ($i + 1)  . ' (' . TableHelper::getLetter($i + 1) . ')';
    $properties[$i]['name'] = Loc::getMessage('option.condition.cell') . ' ' . ($i + 1)  . ' (' . TableHelper::getLetter($i + 1) . ')';
    $properties[$i]['code'] = 'CELL_' . ($i);
}

$propertiesOptions['NOT_PREVIOUS'] = Loc::getMessage('option.condition.non.previous');
$propertiesForMenu = TableHelper::prepareToMenu($properties, true, true );

$rows = [[
    'id' => 0,
    'field' => 'CURRENT',
    'when' => 'equal',
    'whenValue' => '',
    'then' => 'replaceto',
    'thenValue' => ''
]];

if (!empty($value)) {
    $value = Encoding::convertEncodingToCurrent($value);
    try {
        $rows = Json::decode($value, true, true);
    } catch (InvalidParamException $exception) {

    }
}

$conditionOption = [
    'equal' => 'option.transform.condition.equal',
    'nequal' => 'option.transform.condition.not.equal',
    'more' => 'option.transform.condition.more',
    'less' => 'option.transform.condition.less',
    'moreq' => 'option.transform.condition.more.or.equal',
    'loreq' => 'option.transform.condition.less.or.equal',
    'between' => 'option.transform.condition.between',
    'substring' => 'option.transform.condition.substring',
    'nsubstring' => 'option.transform.condition.not.substring',
    'empty' => 'option.transform.condition.empty',
    'nempty' => 'option.transform.condition.not.empty',
    /*'regularexp' => 'option.transform.condition.regular.exp',
    'nregularexp' => 'option.transform.condition.not.regular.exp',*/
    'any' => 'option.transform.condition.any'
];

$conditionThenOption = [
    'string' => [
        'replaceto' => 'option.transform.condition.then.replace.to',
        'removesubs' => 'option.transform.condition.then.remove.substring',
        'replacesubsto' => 'option.transform.condition.then.replace.substring.to',
        'addtobegin' => 'option.transform.condition.then.add.to.begin',
        'addtoend' => 'option.transform.condition.then.add.to.end',
        'translit' => 'option.transform.condition.then.transliterate',
        'striptags' => 'option.transform.condition.then.strip.tags',
        //'cleartags' => 'option.transform.condition.then.clear.tags',
    ],
    'math' => [
        'round' => 'option.transform.condition.then.round',
        'multiply' => 'option.transform.condition.then.multiply',
        'divide' => 'option.transform.condition.then.divide',
        'add' => 'option.transform.condition.then.add',
        'subtract' => 'option.transform.condition.then.subtract',
    ],
    /*'other' => [
        'removefromfile' => 'option.transform.condition.then.remove.line.from.file',
        'setbg' => 'option.transform.condition.then.set.bg.color',
        'settext' => 'option.transform.condition.then.set.text.color',
        'addlink' => 'option.transform.condition.then.add.link',
        'php' => 'option.transform.condition.then.php',
    ]*/
];
?>

<form accept-charset="<?= LANG_CHARSET ?>" class="m-intec-importexport p-field-settings-table" action="" method="post" enctype="multipart/form-data" name="field_settings">
    <input type="hidden" name="templateId" value="<?= $templateId ?>">
    <input type="hidden" name="fieldId" value="<?= $fieldId ?>">
    <input type="hidden" name="action" value="save">
    <div class="test"></div>
    <div id="conditions">
        <table width="100%">
            <tbody>
                <tr class="heading">
                    <td colspan="2">
                        <?echo GetMessage("heading.transform.cell");?>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr class="condition" v-for="(row, index) in rows" :key="row.id">
                    <td colspan="2">
                        <div class="data-wrapper">
                            <span>
                                <?= Loc::getMessage('transform.condition.if') ?>
                            </span>
                            <select name="data[field][]" v-model="row.field">
                                <?php foreach ($propertiesOptions as $value => $message) { ?>
                                    <option value="<?= $value ?>"><?= $message ?></option>
                                <?php } ?>
                            </select>
                            <select name="data[when][]" v-model="row.when">
                                <?php foreach ($conditionOption as $value => $message) { ?>
                                    <option value="<?= $value ?>"><?= Loc::getMessage($message) ?></option>
                                <?php } ?>
                            </select>
                            <input type="text" name="data[whenValue][]" v-model="row.whenValue">
                            <span>
                                <?= Loc::getMessage('transform.condition.then') ?>
                            </span>
                            <select name="data[then][]" v-model="row.then">
                                <?php foreach ($conditionThenOption as $key => $group) { ?>
                                    <optgroup label="<?= Loc::getMessage('option.transform.condition.then.group.' . $key) ?>">
                                        <?php foreach ($group as $value => $message) { ?>
                                            <option value="<?= $value ?>"><?= Loc::getMessage($message) ?></option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            </select>
                            <input type="text" name="data[thenValue][]" v-model="row.thenValue" data-role="macros.target" :data-id="index">
                            <input class="choose-macros" value="..." type="button" @click="openList(index)">
                            <span class="button-wrapper">
                                <a href="javascript:void(0)" @click="downRow(row.id)" title="<?= Loc::getMessage('transform.condition.down') ?>" class="button down"></a>
                            </span>
                            <span class="button-wrapper">
                                <a href="javascript:void(0)" @click="upRow(row.id)" title="<?= Loc::getMessage('transform.condition.up') ?>" class="button up"></a>
                            </span>
                            <span class="button-wrapper delete">
                                <a href="javascript:void(0)" @click="delRow(row.id)" title="<?= Loc::getMessage('transform.condition.delete') ?>" class="button delete"></a>
                            </span>

                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <a href="javascript:void(0)" @click="addRow"><?= Loc::getMessage("transform.condition.add") ?></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>


<script type="text/javascript">
    (function ($, api) {
        $(document).ready(function(){
            var rows = <?= JavaScript::toObject($rows) ?>;
            var lines = <?= JavaScript::toObject($propertiesForMenu) ?>;

            //window.page = {};
            window.page.setMacros = function(value) {
                var select = $('.choose-macros.selected');
                var input = select.prev('[data-role="macros.target"]');
                var id = input.data('id');

                input.insertAtCaret('#' + value + '#');
                select.removeClass('selected');

                conditions.rows[id].thenValue = input.val();
            };

            $('[name="field_settings"]').keydown(function(event){
                if(event.keyCode == 13) {
                    event.preventDefault();
                    return false;
                }
            });

            var conditions = new Vue({
                el: '#conditions',
                data: {
                    rows: rows
                },
                methods: {
                    'addRow': function () {
                        this.rows.push({
                            'id': this.rows.length,
                            'field': 'CURRENT',
                            'when': 'equal',
                            'whenValue': '',
                            'then': 'replaceto',
                            'thenValue': ''
                        });
                    },
                    'upRow': function (rowIndex) {
                        var current = this.rows.splice(rowIndex, 1);
                        this.rows.splice(rowIndex - 1, 0, current[0]);
                        this.refreshId();
                    },
                    'downRow': function (rowIndex) {
                        var current = this.rows.splice(rowIndex, 1);
                        this.rows.splice(rowIndex + 1, 0, current[0]);
                        this.refreshId();
                    },
                    'delRow': function (rowIndex) {
                        this.rows.splice(rowIndex, 1);
                        this.refreshId();
                    },
                    'openList': function (rowIndex) {
                        var button = $('.condition').eq(rowIndex);
                        button = $('.choose-macros', button);
                        button = button[0];

                        JHelpers.ShowChooseVal(button, lines, '.choose-macros');
                    },
                    'refreshId': function () {
                        var counter = 0;

                        $.each(this.rows, function(index,value){
                            value.id = counter;
                            counter++;
                        });
                    }
                },
                mounted: function () {
                    //this.refreshId();
                }
            });
        });
    })(jQuery, intec);
</script>

