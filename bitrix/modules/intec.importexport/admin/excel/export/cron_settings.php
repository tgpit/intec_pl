<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\JavaScript;
use intec\importexport\models\excel\export\Template;

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
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/cron_settings.css');
CJSCore::Init(array('script_export'));

$request = Core::$app->request;

$templateList = Template::find()
    ->where([])
    ->indexBy('id')
    ->all()
    ->asArray(
        function ($index, $value) {
            return [
                'value' => [
                    'id' => $value['id'],
                    'label' => '[' . $value['id'] . '] ' . $value['name'],
                    'selected' => false
                ]
            ];
        });

$frequencyOptions = [
    [
        'value' => 'daily',
        'label' => Loc::getMessage('field.frequency.type.daily'),
        'selected' => true
    ], [
        'value' => 'hours',
        'label' => Loc::getMessage('field.frequency.type.hours'),
        'selected' => false
    ], [
        'value' => 'minutes',
        'label' => Loc::getMessage('field.frequency.type.minutes'),
        'selected' => false
    ], [
        'value' => 'expert',
        'label' => Loc::getMessage('field.frequency.type.expert'),
        'selected' => false
    ]
];

?>

<form class="m-intec-importexport p-field-cron-table" action="" method="post" enctype="multipart/form-data" name="cron_settings">
    <input type="hidden" name="action" value="save">
    <div id="cron">
        <div class="adm-info-message-wrap" :class="message.color" :style="message.style">
            <div class="adm-info-message">
                <div v-html="message.text" ></div>
                <div class="adm-info-message-icon">
                </div>
            </div>
        </div>
        <table width="100%">
            <tbody>
                <tr>
                    <td class="field-name"><?= Loc::getMessage('field.templates') ?>: </td>
                    <td width="60%">
                        <multiselect></multiselect>
                    </td>
                </tr>
                <tr>
                    <td class="field-name"><?= Loc::getMessage('field.frequency.type') ?>: </td>
                    <td width="60%">
                        <div class="type-select-block">
                            <select name="data[type]" @change="changeType()">
                                <?php foreach ($frequencyOptions as $value) { ?>
                                    <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                                <?php } ?>
                            </select>
                            <span class="field-daily-time" :style="typeStyle.daily">
                                <?= Loc::getMessage('field.frequency.type.daily.label')?>
                                <input type="text" name="data[dailyHours]" placeholder="0" maxlength="2" @input="validDaily('hours')">
                                :
                                <input type="text" name="data[dailyMinutes]" placeholder="00" maxlength="2" @input="validDaily('minutes')">
                            </span>
                            <span :style="typeStyle.hours">
                                <?= Loc::getMessage('field.frequency.type.hours.label')?>
                                <input type="text" name="data[hours]" placeholder="1" maxlength="10">
                            </span>
                            <span :style="typeStyle.minutes">
                                <?= Loc::getMessage('field.frequency.type.minutes.label')?>
                                <input type="text" name="data[minutes]" placeholder="15" maxlength="10">
                            </span>
                            <span :style="typeStyle.expert">
                                <?= Loc::getMessage('field.frequency.type.expert.label')?>
                                <input type="text" name="data[expert]" value="* * * * *">
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="field-name"><?= Loc::getMessage('field.php.path') ?>: </td>
                    <td width="60%">
                        <input type="text" name="data[phpPath]" value="/usr/bin/php">
                        <span id="hint_phpPath"></span>
                    </td>
                </tr>
                <!--<tr>
                    <td class="field-name"><?/*= Loc::getMessage('field.auto') */?>: </td>
                    <td width="60%">
                        <input type="checkbox">
                        <span id="hint_auto"></span>
                    </td>
                </tr>-->
                <tr>
                    <td colspan="2">
                        <div class="button-wrapper">
                            <input class="cron-button" type="submit" value="<?= Loc::getMessage('btn.title.delete.from.cron') ?>" @click="deleteSelectedFromCronList()">
                            <input class="cron-button" type="submit" value="<?= Loc::getMessage('btn.title.add.to.cron') ?>" @click="addToCronList()">
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody>
                <tr class="heading">
                    <td colspan="2">
                        <?echo GetMessage("heading.cron.list");?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table width="100%" class="cron-list-table">
                            <thead>
                                <tr>
                                    <td> <?= Loc::getMessage('heading.cron.list.time') ?> </td>
                                    <td> <?= Loc::getMessage('heading.cron.list.template') ?> </td>
                                    <td>

                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cronListItem in cronList">
                                    <td>
                                        {{ cronListItem.time }}
                                    </td>
                                    <td>
                                        {{ cronListItem.template }}
                                    </td>
                                    <td @click="deleteFromCronList(cronListItem)">
                                        X
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>

<script type="text/javascript">
    (function ($, api) {
        $(document).ready(function(){

            /*fix this later*/
            BX.hint_replace(BX('hint_phpPath'), '<?= Loc::getMessage('hint.php.path') ?>');
            BX.hint_replace(BX('hint_auto'), '<?= Loc::getMessage('hint.auto') ?>');

            var request = function (action, data, callback) {
                data = api.extend({}, data, {
                    'action': action
                });

                $.ajax({
                    'async': true,
                    'type': 'POST',
                    'url': '',
                    'data': data,
                    'dataType': 'json',
                    'cache': false,
                    'success': function (response) {
                        var data;

                        if (api.isObject(response)) {
                            if (response.status === 'success') {
                                data = response.data;

                                if (api.isFunction(callback))
                                    callback(data);
                            } else {
                                if (response.message) {
                                    console.error(response.message)
                                } else {
                                    console.error('Error occurred while request.');
                                }
                            }
                        } else {
                            console.error(response);
                        }
                    },
                    'error': function (data) {
                        console.error(data);
                    }
                });
            };

            Vue.component('multiselect', {
                data: function () {
                    return {
                        options: <?= JavaScript::toObject($templateList) ?>,
                        selectedOptions: [],
                        selectedOptionsList: [],
                        searchText: '',
                        openOptions: false
                    }
                },
                methods: {
                    'handleSelect': function (option) {
                        if (option.id !== 0)
                            option.selected = !option.selected;
                    },
                    'handleUnSelect': function (option) {
                        $('#option_' + option.id).prop('checked', false);

                        if (option.id !== 0)
                            option.selected = false;
                    },
                    'handleOpenOptions': function (action = 'none') {
                        if (action === 'close') {
                            this.openOptions =  false;
                            this.searchText = '';
                        } else if (action === 'open') {
                            this.openOptions =  true;
                        } else {
                            this.openOptions = !this.openOptions;

                            if (!this.openOptions)
                                this.searchText = '';
                        }
                    }
                },
                computed:{
                    filteredOptions: function(){
                        var search = this.searchText.toLowerCase();

                        var result = this.options.filter(function (elem) {
                            if(search === '') {
                                return true;
                            } else if (elem.label.toLowerCase().indexOf(search) > -1) {
                                return true;
                            } else {
                                if (String(elem.id).indexOf(search) > -1) {
                                    return true;
                                } else {
                                    return false;
                                }
                            }
                        });

                        if (result.length <= 0)
                            result = [{'label': 'nothing found', 'id': 0}];

                        return result;
                    },
                    filteredSelectedOptions: function(){
                        var result = [];

                        $.each(this.options, function (index, item) {
                            if (item.selected) {
                                result.push(item);
                            }
                        });

                        return result;
                    }
                },
                mounted: function () {

                },
                template:
                '<div class="multi-select">' +
                '    <div class="select-wrapper">' +
                '        <div class="select-item selected" v-for="selectedOption in filteredSelectedOptions">' +
                '            <span class="selected-value">' +
                '                {{selectedOption.label}}' +
                '            </span>' +
                '            <span class="selected-delete" @click="handleUnSelect(selectedOption)">' +
                '                X' +
                '            </span>' +
                '        </div>' +
                '        <div class="select-item input-wrapper">' +
                '            <input type="text" data-role="search-input" v-model="searchText" @focus="handleOpenOptions(\'open\')">' +
                '        </div>' +
                '    </div>' +
                '    <div class="options" :class="{ open: openOptions }">' +
                '       <div class="option" :class="{selected: filteredOption.selected}" v-for="filteredOption in filteredOptions">' +
                '           <input name="data[templates]" type="checkbox"  :id="\'option_\' + filteredOption.id" :value="filteredOption.id" v-model="selectedOptionsList" @click="handleSelect(filteredOption)">' +
                '           <label :for="\'option_\' + filteredOption.id">{{ filteredOption.label }}</label>' +
                '       </div>' +
                '    </div>' +
                '</div>'
            });

            var cron = new Vue({
                el: '#cron',
                data: {
                    typeStyle: {
                        daily: {
                            display: 'inline'
                        },
                        hours: {
                            display: 'none'
                        },
                        minutes: {
                            display: 'none'
                        },
                        expert: {
                            display: 'none'
                        }
                    },
                    message: {
                        style: {
                            display: 'none',
                        },
                        color: 'adm-info-message-red',
                        text: ''
                    },
                    cronList: []
                },
                methods: {
                    'changeType': function () {
                        this.typeStyle.daily.display = 'none';
                        this.typeStyle.hours.display = 'none';
                        this.typeStyle.minutes.display = 'none';
                        this.typeStyle.expert.display = 'none';

                        if (event.target.value === 'hours')
                            this.typeStyle.hours.display = 'inline';
                        else if (event.target.value === 'minutes')
                            this.typeStyle.minutes.display = 'inline';
                        else if (event.target.value === 'expert')
                            this.typeStyle.expert.display = 'inline';
                        else
                            this.typeStyle.daily.display = 'inline';
                    },
                    'validDaily': function (type) {

                        event.target.value = event.target.value.replace(/[^0-9]/g, '');
                        var value = parseInt(event.target.value);

                        if (type === 'hours') {
                            if (value >= 24)
                                event.target.value = 23;

                            if (value < 0)
                                event.target.value = 0;
                        } else if (type === 'minutes') {
                            if (value >= 60)
                                event.target.value = 59;

                            if (value < 0)
                                event.target.value = 0;
                        }
                    },
                    'updateCronList': function () {
                        var self = this;

                        request('updateCronList', {}, function (data) {
                            self.cronList = data;
                        });
                    },
                    'deleteSelectedFromCronList': function (data) {
                        var self = this;
                        var formData = $('[name="cron_settings"]').serializeArray();

                        request('deleteSelectedFromCronList', {'formData': formData}, function (data) {
                            if (data.success) {
                                self.message.color = 'adm-info-message-green';
                            } else {
                                self.message.color = 'adm-info-message-red';
                            }

                            self.message.style.display = 'block';

                            self.message.text = data.message;
                        });

                        this.updateCronList();
                    },
                    'deleteFromCronList': function (selectedData) {
                        var self = this;

                        request('deleteFromCronList', {'selectedData': selectedData}, function (data) {
                            if (data.success) {
                                self.message.color = 'adm-info-message-green';
                            } else {
                                self.message.color = 'adm-info-message-red';
                            }

                            self.message.style.display = 'block';

                            self.message.text = data.message;
                        });

                        this.updateCronList();
                    },
                    'addToCronList': function () {
                        var self = this;
                        var formData = $('[name="cron_settings"]').serializeArray();

                        request('addToCronList', {'formData': formData}, function (data) {

                            if (data.success) {
                                self.message.color = 'adm-info-message-green';
                            } else {
                                self.message.color = 'adm-info-message-red';
                            }

                            self.message.style.display = 'block';

                            self.message.text = data.message;
                        });

                        this.updateCronList();
                    },
                    'setMessage': function () {
                        console.log('setMessage');
                    }
                },
                mounted: function () {
                    this.updateCronList();
                }
            });

            $(document).mouseup( function(e){
                var div = $('.multi-select');

                if ( !div.is(e.target) && div.has(e.target).length === 0 ) {
                    cron.$children.forEach(function (item, i) {
                        item.handleOpenOptions('close');
                    });
                }
            });

        });
    })(jQuery, intec);
</script>

