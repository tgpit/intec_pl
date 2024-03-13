<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\iblocks\elements\names\Template;
use intec\seo\models\iblocks\elements\names\template\Section;
use intec\seo\models\iblocks\elements\names\template\Site;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

Core::$app->web->js->loadExtensions(['jquery', 'jquery_extensions', 'intec_core', 'knockout', 'knockout_extensions']);
Core::$app->web->css->addFile('@intec/seo/resources/css/filter/conditions/conditions.css');

$request = Core::$app->request;
$action = $request->get('action');
$error = null;

/** @var Template $template */
$template = $request->get('template');

if (!empty($template)) {
    $template = Template::findOne($template);

    if (empty($template))
        LocalRedirect($arUrlTemplates['iblocks.elements.names.templates']);
} else {
    $template = new Template();
    $template->loadDefaultValues();
}

if (!$template->getIsNewRecord()) {
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));
}

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'));
$templateSites = $template->getSites(true);
$templateSections = $template->getSections(true);

$createPopupItems = function ($macros, $selector) use (&$createPopupItems) {
    $items = [];

    foreach ($macros as $part) {
        $item = [
            'TEXT' => $part['name']
        ];

        if ($part['type'] === 'group') {
            $item['MENU'] = $createPopupItems($part['items'], $selector);
        } else if ($part['type'] === 'macro') {
            $item['ONCLICK'] = 'window.page.insertMacroToField(' . JavaScript::toObject($selector) . ', ' . JavaScript::toObject($part['value']) . ')';
        }

        $items[] = $item;
    }

    return $items;
};

if ($request->getIsAjax()) {
    if ($request->getIsPost()) {
        $action = $request->post('action');
        $response = [
            'status' => 'error'
        ];

        if ($action === 'get.iblocks') {
            $response['status'] = 'success';
            $response['data'] = [
                'iblocks' => Arrays::fromDBResult(CIBlock::GetList([
                    'SORT' => 'ASC'
                ]))->asArray(function ($index, $iBlock) {
                    return [
                        'value' => [
                            'id' => Type::toInteger($iBlock['ID']),
                            'code' => $iBlock['CODE'],
                            'name' => $iBlock['NAME']
                        ]
                    ];
                })
            ];
        } else if ($action === 'get.iblock.entities') {
            $iBlock = $request->post('iblock');

            if (!empty($iBlock))
                $iBlock = CIBlock::GetList([], [
                    'ID' => $iBlock
                ])->Fetch();

            if (!empty($iBlock)) {
                $response['status'] = 'success';
                $response['data'] = [
                    'sections' => Arrays::fromDBResult(CIBlockSection::GetList([
                        'LEFT_MARGIN' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'GLOBAL_ACTIVE' => 'Y'
                    ]))->asArray(function ($index, $iBlockSection) {
                        return [
                            'value' => [
                                'id' => Type::toInteger($iBlockSection['ID']),
                                'code' => $iBlockSection['CODE'],
                                'name' => $iBlockSection['NAME'],
                                'level' => $iBlockSection['DEPTH_LEVEL']
                            ]
                        ];
                    }),
                    'popups' => null
                ];

                $template->iBlockId = $iBlock['ID'];
                $macros = $template->getMacros();
                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-elements-names-templates-edit-value-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-elements-names-templates-edit-value')
                );

                $popup->Show();

                $response['data']['popups'] = ob_get_contents();

                ob_end_clean();
            } else {
                $response['message'] = 'Unknown information block';
            }
        }

        echo Json::encode($response, 320, true);
        return;
    }
}

if ($request->getIsPost()) {
    $post = $request->post();
    $data = ArrayHelper::getValue($post, $template->formName());
    $return = $request->post('apply');
    $return = empty($return);
    $template->load($post);

    if (!Type::isArray($data))
        $data = [];

    if ($template->save()) {
        if (isset($data['sites']))
            foreach ($templateSites as $templateSite)
                $templateSite->delete();

        if (isset($data['sections']))
            foreach ($templateSections as $templateSection)
                $templateSection->delete();

        if (!empty($data['sites']) && Type::isArray($data['sites'])) {
            foreach ($data['sites'] as $site) {
                $site = new Site([
                    'templateId' => $template->id,
                    'siteId' => $site
                ]);

                $site->save();
            }

            unset($site);
        }

        if (!empty($data['sections']) && Type::isArray($data['sections'])) {
            foreach ($data['sections'] as $section) {
                $section = new Section([
                    'templateId' => $template->id,
                    'iBlockSectionId' => $section
                ]);

                $section->save();
            }
        }

        if ($return)
            LocalRedirect($arUrlTemplates['iblocks.elements.names.templates']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['iblocks.elements.names.templates.edit'], [
            'template' => $template->id
        ]));
    } else {
        $error = $template->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
}

$form = [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
]];

$form = new CAdminForm('metadataTemplateEditForm', $form);

$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['iblocks.elements.names.templates']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['iblocks.elements.names.templates.add']
]];

$panel = new CAdminContextMenu($panel);

$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php $panel->Show() ?>
<?php if (!empty($error)) { ?>
    <?php CAdminMessage::ShowMessage($error) ?>
<?php } ?>
<?php $form->Begin([
    'FORM_ACTION' => $request->getUrl()
]) ?>
<?php $form->BeginNextFormTab() ?>
    <?php if (!$template->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $template->getAttributeLabel('id').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2"><?= $template->id ?></td>
        </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('code', $template->getAttributeLabel('code').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2"><?= Html::textInput($template->formName().'[code]', $template->code) ?></td>
        </tr>
    <?php $form->EndCustomField('code') ?>
    <?php $form->BeginCustomField('active', $template->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2">
                <?= Html::hiddenInput($template->formName().'[active]', 0) ?>
                <?= Html::checkbox($template->formName().'[active]', $template->active, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('active') ?>
    <?php $form->BeginCustomField('name', $template->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2"><?= Html::textInput($template->formName().'[name]', $template->name) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('sites', Loc::getMessage('fields.sites').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2"><?= Html::dropDownList($template->formName().'[sites][]', $templateSites->asArray(function ($index, $site) {
                    /** @var Site $site */

                    return [
                        'value' => $site->siteId
                    ];
                }), ArrayHelper::merge([
                    '' => '('.Loc::getMessage('answers.unset').')'
                ], $sites->asArray(function ($index, $site) {
                    return [
                        'key' => $site['ID'],
                        'value' => '['.$site['ID'].'] '.(!empty($site['NAME_LANG']) ? $site['NAME_LANG'] : $site['NAME'])
                    ];
                })), [
                    'multiple' => 'multiple'
                ]) ?></td>
        </tr>
    <?php $form->EndCustomField('sites') ?>
    <?php $form->BeginCustomField('rules', Loc::getMessage('fields.rules').':', false) ?>
        <tr>
            <td width="40%"><?= $template->getAttributeLabel('iBlockId').':' ?></td>
            <td colspan="2">
                <!-- ko if: !iblocks.refreshing() -->
                    <select name="<?= $template->formName().'[iBlockId]' ?>" data-bind="{
                        options: iblocks,
                        optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                        optionsText: function (iblock) {
                            return '[' + iblock.id + '] ' + iblock.name;
                        },
                        optionsValue: 'id',
                        value: iblock.id
                    }"></select>
                <!-- /ko -->
                <!-- ko if: iblocks.refreshing() -->
                    <select name="<?= $template->formName().'[iBlockId]' ?>">
                        <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                    </select>
                <!-- /ko -->
            </td>
        </tr>
        <tr>
            <td width="40%"><?= Loc::getMessage('fields.sections').':' ?></td>
            <td colspan="2">
                <!-- ko if: !iblock.refreshing() -->
                    <select name="<?= $template->formName().'[sections][]' ?>" multiple="multiple" size="10" data-bind="{
                        options: iblock.sections,
                        optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                        optionsText: function (section) {
                            var level = '';

                            for (var i = 0; i < section.level; i++)
                                level = level + '. ';

                            return '[' + section.id + '] ' + level + section.name;
                        },
                        optionsValue: 'id',
                        selectedOptions: iblock.sections.selected
                    }"></select>
                <!-- /ko -->
                <!-- ko if: iblock.refreshing() -->
                <select name="<?= $template->formName().'[sections][]' ?>" multiple="multiple" size="10">
                    <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                </select>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('rules') ?>
    <?php $form->BeginCustomField('value', $template->getAttributeLabel('value').':', false) ?>
        <tr id="intec-seo-iblocks-elements-names-templates-edit-value">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textarea($template->formName().'[value]', $template->value, [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '150px',
                        'resize' => 'vertical',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px; width: 1px">
                        <input type="button" id="intec-seo-iblocks-elements-names-templates-edit-value-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('value') ?>
    <?php $form->BeginCustomField('quantity', $template->getAttributeLabel('quantity').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2"><?= Html::textInput($template->formName().'[quantity]', $template->quantity) ?></td>
        </tr>
    <?php $form->EndCustomField('quantity') ?>
    <?php $form->BeginCustomField('sort', $template->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2"><?= Html::textInput($template->formName().'[sort]', $template->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['iblocks.elements.names.templates']
]) ?>
<div id="intec-seo-iblocks-elements-names-templates-edit">
    <?php $form->Show() ?>
    <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
        <div data-bind="{
            html: $root.iblock.popups
        }"></div>
    <!-- /ko -->
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="width: 100%; box-sizing: border-box">
            <?= Loc::getMessage('notes.macros') ?>
        </div>
    </div>
</div>
<?php

$data = [];
$data['iblock'] = $template->iBlockId;
$data['sections'] = $templateSections->asArray(function ($index, $section) {
    /** @var Section $section */

    return [
        'value' => $section->iBlockSectionId
    ];
});

?>
<script type="text/javascript">
    (function ($, api) {
        window.page = {};
        window.page.insertMacroToField = function (selector, macro) {
            var field = $(selector);

            if (field.length === 0)
                return;

            var frame = field.find('iframe');
            var input = field.find('input[type=text]');
            var textArea = field.find('textarea');

            if (frame.length > 0) {
                frame.contents().find('body').append(macro);
                textArea.insertAtCaret(macro);
            } else {
                textArea.insertAtCaret(macro);
                input.insertAtCaret(macro);
            }
        };

        window.intecSeoMetadataTemplatesEditSectionsSelector = {
            'AddValue': function (value) {
                if (!ko.isObservable(root.iblock.sections.selector.link))
                    return;

                root.iblock.sections.selector.link(value);
                root.iblock.sections.selector.link = null;
            }
        };

        window.intecSeoMetadataTemplatesEditElementsSelector = {
            'AddValue': function (value) {
                if (!ko.isObservable(root.iblock.elements.selector.link))
                    return;

                root.iblock.elements.selector.link(value);
                root.iblock.elements.selector.link = null;
            }
        };

        var root = {};
        var data = <?= JavaScript::toObject($data) ?>;
        var models = {};
        var request = function (action, data, callback) {
            data = api.extend({}, data, {
                'action': action
            });

            $.ajax({
                'async': true,
                'type': 'POST',
                'data': data,
                'dataType': 'json',
                'cache': false,
                'success': function (response) {
                    var data;

                    if (api.isObject(response)) {
                        if (response.status === 'success') {
                            data = api.isObject(response.data) ? response.data : {};

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

        root.iblocks = ko.observableArray();
        root.iblocks.refreshing = ko.observable(false);
        root.iblocks.refresh = function () {
            var iblocks = root.iblocks;

            iblocks.refreshing(true);
            iblocks.removeAll();

            request('get.iblocks', {}, function (data) {
                iblocks(data.iblocks);
                iblocks.refreshing(false);
            });
        };

        root.iblocks.refresh();

        root.iblock = {};
        root.iblock.id = ko.observable();
        root.iblock.sections = ko.observableArray();
        root.iblock.sections.selected = ko.observableArray();
        root.iblock.sections.selector = {};
        root.iblock.sections.selector.link = null;
        root.iblock.sections.selector.select = function (observable) {
            var url = '/bitrix/admin/iblock_section_search.php?lang=<?= LANGUAGE_ID?>';

            if (!ko.isObservable(observable))
                return;

            root.iblock.sections.selector.link = observable;
            url += '&lookup=intecSeoMetadataTemplatesEditSectionsSelector';

            jsUtils.OpenWindow(url);
        };

        root.iblock.popups = ko.observable();
        root.iblock.refreshing = ko.observable(false);
        root.iblock.refresh = function () {
            var iblock = root.iblock;

            iblock.refreshing(true);

            if (this.id() > 0) {
                request('get.iblock.entities', {
                    'iblock': this.id()
                }, function (data) {
                    iblock.sections(data.sections);
                    iblock.popups(data.popups);
                    iblock.refreshing(false);
                });
            } else {
                iblock.refreshing(false);
            }
        };

        root.iblock.id.subscribe(function () {
            root.iblock.refresh();
        });

        root.iblock.id(data.iblock);
        root.iblock.sections.selected(data.sections);

        ko.applyBindings(root, $('#intec-seo-iblocks-elements-names-templates-edit').get(0));
    })(jQuery, intec);
</script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
