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
use intec\seo\models\articles\Template;
use intec\seo\models\articles\template\Section;
use intec\seo\models\articles\template\SectionsForElements;
use intec\seo\models\articles\template\Element;
use intec\seo\models\articles\template\Article;
use intec\seo\models\articles\template\Site;

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
        LocalRedirect($arUrlTemplates['articles.templates']);
} else {
    if ($action !== 'copy') {
        $template = new Template();
        $template->loadDefaultValues();
    } else {
        LocalRedirect($arUrlTemplates['articles.templates']);
    }
}

if ($action === 'copy') {
    $template->name = $template->name.' ('.Loc::getMessage('fields.name.copy').')';
    $template->code = $template->code.'(copy)';
}

if (!$template->getIsNewRecord()) {
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));
}

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'));
$templateSites = $template->getSites(true);
$templateSections = $template->getSections(true);
$templateSectionsForElements = $template->getSectionsForElements(true);
$templateElements = $template->getElements(true);
$templateArticles = $template->getArticles(true);

$arArticlesItems = $templateArticles->asArray(function ($index, $article) {
    return [
        'value' => $article->iBlockElementId
    ];
});

if (!empty($arArticlesItems)) {
    $arArticlesItems = Arrays::fromDBResult(CIBlockElement::GetList([], [
        'ID' => $arArticlesItems
    ]))->asArray(function ($index, $item) {
        return [
            'value' => [
                'id' => Type::toInteger($item['ID']),
                'name' => $item['NAME']
            ]
        ];
    });
}

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
        } else if ($action === 'get.elements') {
            $arSections = $request->post('sections');

            if (!empty($arSections)) {
                $response['status'] = 'success';
                $response['data'] = [
                    'elements' => Arrays::fromDBResult(CIBlockElement::GetList([], [
                        'IBLOCK_SECTION_ID' => $arSections,
                        'GLOBAL_ACTIVE' => 'Y'
                    ]))->asArray(function ($index, $iBlockElement) {
                        return [
                            'value' => [
                                'id' => Type::toInteger($iBlockElement['ID']),
                                'code' => $iBlockElement['CODE'],
                                'name' => $iBlockElement['NAME']
                            ]
                        ];
                    })
                ];
            }
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
                    'elements' => Arrays::fromDBResult(CIBlockElement::GetList([], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'GLOBAL_ACTIVE' => 'Y'
                    ]))->asArray(function ($index, $iBlockElement) {
                        return [
                            'value' => [
                                'id' => Type::toInteger($iBlockElement['ID']),
                                'code' => $iBlockElement['CODE'],
                                'name' => $iBlockElement['NAME']
                            ]
                        ];
                    })
                ];
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

    if ($action === 'copy') {
        $template->id = null;
        $template->setIsNewRecord(true);
    }

    if ($template->save()) {
        if ($action !== 'copy') {
            if (isset($data['sites']))
                foreach ($templateSites as $templateSite)
                    $templateSite->delete();

            if (isset($data['sections']))
                foreach ($templateSections as $templateSection)
                    $templateSection->delete();

            if (isset($data['sectionsForElements']))
                foreach ($templateSectionsForElements as $templateSectionForElements)
                    $templateSectionForElements->delete();

            if (isset($data['elements']))
                foreach ($templateElements as $templateElement)
                    $templateElement->delete();

            if (isset($data['articles']))
                foreach ($templateArticles as $templateArticle)
                    $templateArticle->delete();
        }

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
        if (!empty($data['sectionsForElements']) && Type::isArray($data['sectionsForElements'])) {
            foreach ($data['sectionsForElements'] as $sectionForElements) {
                $section = new SectionsForElements([
                    'templateId' => $template->id,
                    'iBlockSectionId' => $sectionForElements
                ]);

                $section->save();
            }
        }

        if (!empty($data['elements']) && Type::isArray($data['elements'])) {
            foreach ($data['elements'] as $element) {
                $element = new Element([
                    'templateId' => $template->id,
                    'iBlockElementId' => $element
                ]);

                $element->save();
            }
        }

        if (!empty($data['articles']) && Type::isArray($data['articles'])) {
            $data['articles'] = array_unique($data['articles'], SORT_NUMERIC);

            foreach ($data['articles'] as $article) {
                $article = new Article([
                    'templateId' => $template->id,
                    'iBlockElementId' => $article
                ]);

                $article->save();
            }
        }

        if ($return)
            LocalRedirect($arUrlTemplates['articles.templates']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['articles.templates.edit'], [
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
    'LINK' => $arUrlTemplates['articles.templates']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['articles.templates.add']
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
    <tr>
        <td width="40%"><?= Loc::getMessage('fields.sectionsForElements').':' ?></td>
        <td colspan="2">
            <!-- ko if: !iblock.refreshing() -->
            <select name="<?= $template->formName().'[sectionsForElements][]' ?>" multiple="multiple" size="10" data-bind="{
                                options: iblock.sections,
                                optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                                optionsText: function (section) {
                                    var level = '';

                                    for (var i = 0; i < section.level; i++)
                                        level = level + '. ';

                                    return '[' + section.id + '] ' + level + section.name;
                                },
                                optionsValue: 'id',
                                selectedOptions: iblock.sectionsForElements.selected
                            }"></select>
            <!-- /ko -->
            <!-- ko if: iblock.refreshing() -->
            <select name="<?= $template->formName().'[sectionsForElements][]' ?>" multiple="multiple" size="10">
                <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
            </select>
            <!-- /ko -->
        </td>
    </tr>
    <tr>
        <td width="40%"><?= Loc::getMessage('fields.elements').':' ?></td>
        <td colspan="2">
            <!-- ko if: !iblock.refreshing() -->
            <select name="<?= $template->formName().'[elements][]' ?>" multiple="multiple" size="10" data-bind="{
                                    options: iblock.elements,
                                    optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                                    optionsText: function (element) {
                                        return '[' + element.id + '] ' + element.name;
                                    },
                                    optionsValue: 'id',
                                    selectedOptions: iblock.elements.selected
                                }"></select>
            <!-- /ko -->
            <!-- ko if: iblock.refreshing() -->
            <select name="<?= $template->formName().'[elements][]' ?>" multiple="multiple" size="10">
                <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
            </select>
            <!-- /ko -->
        </td>
    </tr>
    <tr>
        <td width="40%" class="adm-detail-valign-top"><?= Loc::getMessage('fields.articles'); ?></td>
        <td width="60%" class="adm-detail-content-cell-r">
            <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%" id="tb09cac5541d919086be354ca12e5b4567">
                <tbody>
                <?php
                if (empty($arArticlesItems))
                    $iCounter = 2;
                else
                    $iCounter = count($arArticlesItems);

                $sFormName = $template->formName();

                for ($iCount = 0; $iCount < $iCounter; $iCount++) {

                    $sSetting = '/bitrix/admin/iblock_element_search.php?lang=ru&amp;IBLOCK_ID=0&amp;n='.$sFormName.'[articles]&amp;k='.$iCount;
                    ?>
                    <tr>
                        <td>
                            <input name="<?= $sFormName ?>[articles][<?= $iCount ?>]" id="<?= $sFormName ?>[articles][<?= $iCount ?>]" value="<?= $arArticlesItems[$iCount]['id']?>" size="5" type="text">
                            <input type="button" value="..." onclick="jsUtils.OpenWindow('<?= $sSetting ?>', 900, 700);">
                            &nbsp;
                            <span id="sp_2a80d3c53b75b7935916adb9b2f13329_<?= $iCount ?>"><?= $arArticlesItems[$iCount]['name'] ?></span>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>
                        <input type="button" value="<?= Loc::getMessage('fields.articles.add'); ?>" onclick="jsUtils.OpenWindow('/bitrix/admin/iblock_element_search.php?lang=ru&amp;IBLOCK_ID=0&amp;n=<?= $sFormName ?>[articles]&amp;m=y&amp;k=<?= $iCounter ?>', 900, 700);">
                        <span id="sp_2a80d3c53b75b7935916adb9b2f13329_<?= $iCounter ?>"></span>
                    </td>
                </tr>
                </tbody>
            </table>
            <script type="text/javascript">
                var nomber = <?= JavaScript::toObject($iCounter) ?>;
                function InS2a80d3c53b75b7935916adb9b2f13329(id, name){
                    oTbl=document.getElementById('tb09cac5541d919086be354ca12e5b4567');
                    console.log(oTbl);
                    oRow=oTbl.insertRow(oTbl.rows.length-1);
                    oCell=oRow.insertCell(-1);
                    oCell.innerHTML='<input name="<?= $sFormName ?>[articles]['+nomber+']" value="'+id+'" id="<?= $sFormName ?>[articles]['+nomber+']" size="5" type="text">'+
                        '<input type="button" value="..." '+
                        'onClick="jsUtils.OpenWindow(\'/bitrix/admin/iblock_element_search.php?lang=ru&amp;IBLOCK_ID=0&amp;n=<?= $sFormName ?>[articles]&amp;k='+nomber+'\', '+
                        ' 900, 700);">'+'&nbsp;<span id="sp_2a80d3c53b75b7935916adb9b2f13329_'+nomber+'" >'+name+'</span>';nomber++;}
            </script>
        </td>
    </tr>
<?php $form->EndCustomField('rules') ?>
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
    'back_url' => $arUrlTemplates['Template[articles].templates']
]) ?>
    <div id="intec-seo-iblocks-elements-names-templates-edit">
        <?php $form->Show() ?>
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
$data['sectionsForElements'] = $templateSectionsForElements->asArray(function ($index, $sectionsForElements) {
    /** @var Section $section */

    return [
        'value' => $sectionsForElements->iBlockSectionId
    ];
});
$data['elements'] = $templateElements->asArray(function ($index, $elements) {
    /** @var Section $section */

    return [
        'value' => $elements->iBlockElementId
    ];
});
$data['articles'] = $arArticlesItems;

if (!empty($data['elements']) && empty($data['sectionsForElements'])) {
    $arElements = Arrays::fromDBResult(CIBlockElement::GetList([
        'LEFT_MARGIN' => 'ASC'
    ], [
        'ID' => $data['elements']
    ],[
        'IBLOCK_SECTION_ID'
    ]))->asArray();

    foreach ($arElements as $arElement) {
        $data['sectionsForElements'][] = $arElement['IBLOCK_SECTION_ID'];
    }

    $data['sectionsForElements'] = array_values(array_unique($data['sectionsForElements']));

    unset($arElements);
}
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
                    root.iblock.sectionsForElements.selector.link(value);
                    root.iblock.sectionsForElements.selector.link = null;
                    root.iblock.elements.selector.link(value);
                    root.iblock.elements.selector.link = null;
                }
            };

            window.intecSeoMetadataTemplatesEditElementsSelector = {
                'AddValue': function (value) {
                    if (!ko.isObservable(root.iblock.elements.selector.link))
                        return;

                    root.iblock.elements.selector.link(value);
                    root.iblock.elements.selector.link = null;
                    root.iblock.sectionsForElements.selector.link(value);
                    root.iblock.sectionsForElements.selector.link = null;
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
            root.iblock.sectionsForElements = ko.observableArray();
            root.iblock.elements = ko.observableArray();
            root.iblock.sections.selected = ko.observableArray();
            root.iblock.sectionsForElements.selected = ko.observableArray();
            root.iblock.elements.selected = ko.observableArray();
            root.iblock.sections.selector = {};
            root.iblock.sectionsForElements.selector = {};
            root.iblock.elements.selector = {};
            root.iblock.sections.selector.link = null;
            root.iblock.sectionsForElements.selector.link = null;
            root.iblock.elements.selector.link = null;
            root.iblock.sections.selector.select = function (observable) {
                var url = '/bitrix/admin/iblock_section_search.php?lang=<?= LANGUAGE_ID?>';

                if (!ko.isObservable(observable))
                    return;

                root.iblock.sections.selector.link = observable;
                url += '&lookup=intecSeoMetadataTemplatesEditSectionsSelector';

                jsUtils.OpenWindow(url);
            };
            root.iblock.elements.selector.select = function (observable) {
                var url = '/bitrix/admin/iblock_section_search.php?lang=<?= LANGUAGE_ID?>';

                if (!ko.isObservable(observable))
                    return;

                root.iblock.sections.selector.link = observable;
                url += '&lookup=intecSeoMetadataTemplatesEditSectionsSelector';

                jsUtils.OpenWindow(url);
            };

            root.iblock.refreshing = ko.observable(false);
            root.iblock.refresh = function () {
                var iblock = root.iblock;
                iblock.refreshing(true);

                if (this.id() > 0) {
                    request('get.iblock.entities', {
                        'iblock': this.id()
                    }, function (data) {
                        iblock.sections(data.sections);
                        iblock.refreshing(false);
                    });
                } else {
                    iblock.refreshing(false);
                }
            };
            root.iblock.sectionsForElements.selector.select = function (observable) {
                var url = '/bitrix/admin/iblock_section_search.php?lang=<?= LANGUAGE_ID?>';

                if (!ko.isObservable(observable))
                    return;

                root.iblock.sections.selector.link = observable;
                url += '&lookup=intecSeoMetadataTemplatesEditSectionsSelector';

                jsUtils.OpenWindow(url);
            };

            root.iblock.sectionsForElements.refreshing = ko.observable();
            root.iblock.sectionsForElements.selected.refresh = function () {
                var element = root.iblock.sectionsForElements;
                element.refreshing(true);
                if(this().length > 0 && !!this()[0]) {
                    request('get.elements', {
                        'sections': this()
                    }, function (data) {
                        root.iblock.elements(data.elements);
                        element.refreshing(false);
                    });
                } else {
                    element.refreshing(false);
                }
            };

            root.iblock.id.subscribe(function () {
                root.iblock.refresh();
            });

            root.iblock.sectionsForElements.selected.subscribe(function () {
                root.iblock.sectionsForElements.selected.refresh();
            });

            if (data.sections.length < 1)
                data.sections[0] = 0;

            if (data.sectionsForElements.length < 1)
                data.sectionsForElements[0] = 0;

            if (data.elements.length < 1)
                data.elements[0] = 0;

            root.iblock.id(data.iblock);
            root.iblock.sections.selected(data.sections);
            root.iblock.sectionsForElements.selected(data.sectionsForElements);

            setTimeout(function(){
                root.iblock.elements.selected(data.elements);
            }, 500);


            ko.applyBindings(root, $('#intec-seo-articles-templates-edit').get(0));
        })(jQuery, intec);
    </script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>