<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity\Query;
use Bitrix\Highloadblock\HighloadBlockTable;
use intec\Core;
use intec\core\base\conditions\GroupCondition;
use intec\core\bitrix\conditions\IBlockPropertyCondition;
use intec\core\bitrix\conditions\IBlockSectionCondition;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\iblocks\metadata\Template;
use intec\seo\models\iblocks\metadata\template\Section;
use intec\seo\models\iblocks\metadata\template\Site;

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
        LocalRedirect($arUrlTemplates['iblocks.metadata.templates']);
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
                $sku = false;

                if (Loader::includeModule('catalog'))
                    $sku = CCatalogSku::GetInfoByProductIBlock($iBlock['ID']);

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
                    'properties' => Arrays::fromDBResult(CIBlockProperty::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'ACTIVE' => 'Y'
                    ])),
                    'popups' => null
                ];

                if ($sku !== false)
                    $response['data']['properties']->addRange(Arrays::fromDBResult(CIBlockProperty::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $sku['IBLOCK_ID'],
                        'ACTIVE' => 'Y'
                    ])));

                $response['data']['properties'] = $response['data']['properties']->asArray(function ($index, $iBlockProperty) {
                    $values = null;

                    if ($iBlockProperty['PROPERTY_TYPE'] === 'L' && empty($iBlockProperty['USER_TYPE']) && !Type::isNumeric($iBlockProperty['USER_TYPE'])) {
                        $values = [];
                        $result = CIBlockPropertyEnum::GetList([
                            'SORT' => 'ASC'
                        ], [
                            'PROPERTY_ID' => $iBlockProperty['ID']
                        ]);

                        while ($value = $result->Fetch())
                            $values[] = [
                                'value' => $value['ID'],
                                'text' => $value['VALUE']
                            ];
                    } else if ($iBlockProperty['PROPERTY_TYPE'] === 'S' && $iBlockProperty['USER_TYPE'] === 'directory') {
                        $values = [];

                        try {
                            $entity = null;
                            $block = HighloadBlockTable::getList([
                                'filter' => [
                                    'TABLE_NAME' => $iBlockProperty['USER_TYPE_SETTINGS']['TABLE_NAME']
                                ]
                            ])->fetch();

                            if (!empty($block))
                                $entity = HighloadBlockTable::compileEntity($block);

                            if (!empty($entity)) {
                                $query = new Query($entity);
                                $query->setSelect(['*']);
                                $result = $query->exec();

                                while ($value = $result->fetch())
                                    if (!empty($value['UF_XML_ID']))
                                        $values[] = [
                                            'value' => $value['UF_XML_ID'],
                                            'text' => !empty($value['UF_NAME']) || Type::isInteger($value['UF_NAME']) ? $value['UF_NAME'] : $value['UF_XML_ID']
                                        ];
                            }
                        } catch (\Exception $exception) {}
                    }

                    return [
                        'value' => [
                            'id' => Type::toInteger($iBlockProperty['ID']),
                            'code' => $iBlockProperty['CODE'],
                            'type' => $iBlockProperty['PROPERTY_TYPE'],
                            'userType' => !empty($iBlockProperty['USER_TYPE']) || Type::isNumeric($iBlockProperty['USER_TYPE']) ? $iBlockProperty['USER_TYPE'] : null,
                            'name' => $iBlockProperty['NAME'],
                            'values' => $values
                        ]
                    ];
                });

                $template->iBlockId = $iBlock['ID'];
                $macros = $template->getSectionMacros();
                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaKeywords-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaKeywords')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaDescription-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaDescription')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaPageTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaPageTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewAlt-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewAlt')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailAlt-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailAlt')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailTitle')
                );

                $popup->Show();
                
                $macros = $template->getElementMacros();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaKeywords-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaKeywords')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaDescription-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaDescription')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaPageTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaPageTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewAlt-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewAlt')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewTitle')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailAlt-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailAlt')
                );

                $popup->Show();

                $popup = new CAdminPopupEx(
                    'intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailTitle-menu',
                    $createPopupItems($macros, '#intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailTitle')
                );

                $popup->Show();

                $response['data']['popups'] = ob_get_contents();

                ob_end_clean();
            } else {
                $response['message'] = 'Unknown information block';
            }
        } else if ($action === 'get.iblock.section') {
            $iBlockSection = $request->post('section');

            if (!empty($iBlockSection)) {
                $iBlockSection = CIBlockSection::GetList([], [
                    'ID' => $iBlockSection
                ])->Fetch();

                if (!empty($iBlockSection)) {
                    $response['status'] = 'success';
                    $response['data'] = [
                        'id' => Type::toInteger($iBlockSection['ID']),
                        'code' => $iBlockSection['CODE'],
                        'name' => $iBlockSection['NAME'],
                        'depth' => $iBlockSection['DEPTH_LEVEL']
                    ];
                }
            }

            if (empty($iBlockSection))
                $response['message'] = 'Unknown section of information block';
        } else if ($action === 'get.iblock.element') {
            $iBlockElement = $request->post('element');

            if (!empty($iBlockElement)) {
                $iBlockElement = CIBlockElement::GetList([], [
                    'ID' => $iBlockElement
                ])->Fetch();

                if (!empty($iBlockElement)) {
                    $response['status'] = 'success';
                    $response['data'] = [
                        'id' => Type::toInteger($iBlockElement['ID']),
                        'code' => $iBlockElement['CODE'],
                        'name' => $iBlockElement['NAME']
                    ];
                }
            }

            if (empty($iBlockElement))
                $response['message'] = 'Unknown element of information block';
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

    $convert = function ($rule) use (&$convert) {
        $result = null;
        $type = ArrayHelper::getValue($rule, 'type');

        if ($type === 'group') {
            $result = new GroupCondition();
            $result->operator = ArrayHelper::getValue($rule, 'operator');
            $result->result = ArrayHelper::getValue($rule, 'result');

            if (!empty($rule['conditions']) && Type::isArray($rule['conditions']))
                foreach ($rule['conditions'] as $child)
                    $result->conditions->add($convert($child));
        } else if ($type === 'section') {
            $value = ArrayHelper::getValue($rule, 'value');

            if (empty($value) && !Type::isNumeric($value))
                $value = null;

            $result = new IBlockSectionCondition();
            $result->operator = ArrayHelper::getValue($rule, 'operator');
            $result->value = $value;
        } else if ($type === 'property') {
            $value = ArrayHelper::getValue($rule, 'value');

            if (empty($value) && !Type::isNumeric($value))
                $value = null;

            $result = new IBlockPropertyCondition();
            $result->id = ArrayHelper::getValue($rule, 'id');
            $result->operator = ArrayHelper::getValue($rule, 'operator');
            $result->value = $value;

            if (empty($result->id))
                $result = null;
        }

        return $result;
    };

    $template->setRules(null);

    if (!empty($data['rule']))
        $template->setRules($convert($data['rule']));

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
            LocalRedirect($arUrlTemplates['iblocks.metadata.templates']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['iblocks.metadata.templates.edit'], [
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
], [
    'DIV' => 'sectionMeta',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.sectionMeta'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.sectionMeta'))
], [
    'DIV' => 'elementMeta',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.elementMeta'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.elementMeta'))
]];

$form = new CAdminForm('metadataTemplateEditForm', $form);

$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['iblocks.metadata.templates']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['iblocks.metadata.templates.add']
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
            <td><?= $template->id ?></td>
        </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('code', $template->getAttributeLabel('code').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($template->formName().'[code]', $template->code) ?></td>
        </tr>
    <?php $form->EndCustomField('code') ?>
    <?php $form->BeginCustomField('active', $template->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
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
            <td><?= Html::textInput($template->formName().'[name]', $template->name) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('sites', Loc::getMessage('fields.sites').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::dropDownList($template->formName().'[sites][]', $templateSites->asArray(function ($index, $site) {
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
    <?php $form->BeginCustomField('rules', $template->getAttributeLabel('rules').':', false) ?>
        <tr>
            <td width="40%"><?= $template->getAttributeLabel('iBlockId').':' ?></td>
            <td>
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
        <td>
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
        <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
        <td>
            <div class="m-intec-seo p-filter-conditions" data-bind="{
                template: {
                    'name': 'intec-seo-filter-conditions-templates-condition',
                    'data': rule
                }
            }"></div>
            <script type="text/html" id="intec-seo-filter-conditions-templates-condition">
                <!-- ko function: function () {
                    $context.$inputName = function (name) {
                        if ($parent === $root) {
                            return 'Template[rule][' + name + ']';
                        } else {
                            return $parentContext.$inputName('conditions') + '[' + $parent.conditions.indexOf($data) + '][' + name + ']';
                        }
                    };
                } -->
                <!-- /ko -->
                <div class="filter-conditions-item" data-bind="{
                    attr: {
                        'data-type': type
                    }
                }">
                    <input type="hidden" data-bind="{
                        attr: {
                            'name': $inputName('type')
                        },
                        value: type
                    }" />
                    <!-- ko if: type() === 'property' -->
                        <input type="hidden" data-bind="{
                            attr: {
                                'name': $inputName('id')
                            },
                            value: id
                        }" />
                    <!-- /ko -->
                    <!-- ko if: $context.$index && $index() > 0 && $parent !== $root; -->
                        <div class="filter-conditions-item-operator" data-bind="{
                            attr: {
                                'data-operator': $parent.operator
                            },
                            event: {
                                'click': function () {
                                    $parent.operator.toggle();
                                }
                            },
                            text: (function () {
                                var text = $parent.operator() === 'and' ? '<?= Loc::getMessage('conditions.operators.and') ?>' : '<?= Loc::getMessage('conditions.operators.or') ?>';

                                if (!$parent.result())
                                    text = text + ' <?= Loc::getMessage('conditions.results.not') ?>';

                                return text;
                            })()
                        }"></div>
                    <!-- /ko -->
                    <!-- ko if: type() === 'group' -->
                        <div class="filter-conditions-item-conditions">
                            <input type="hidden" data-bind="{
                                attr: {
                                    'name': $inputName('operator')
                                },
                                value: operator
                            }" />
                            <select data-bind="{
                                options: (function () {
                                    return [{
                                        'value': 'and',
                                        'text': '<?= Loc::getMessage('conditions.items.operators.and') ?>'
                                    }, {
                                        'value': 'or',
                                        'text': '<?= Loc::getMessage('conditions.items.operators.or') ?>'
                                    }];
                                })(),
                                optionsValue: 'value',
                                optionsText: 'text',
                                value: operator
                            }" style="margin-right: 5px"></select>
                            <input type="hidden" data-bind="{
                                attr: {
                                    'name': $inputName('result')
                                },
                                value: result() ? 1 : 0
                            }" />
                            <select data-bind="{
                                options: (function () {
                                    return [{
                                        'value': true,
                                        'text': '<?= Loc::getMessage('conditions.items.results.true') ?>'
                                    }, {
                                        'value': false,
                                        'text': '<?= Loc::getMessage('conditions.items.results.false') ?>'
                                    }];
                                })(),
                                optionsValue: 'value',
                                optionsText: 'text',
                                value: result
                            }"></select>
                        </div>
                    <!-- /ko -->
                    <!-- ko if: type() === 'group' -->
                    <!-- ko if: conditions.active().length > 0 -->
                        <div class="filter-conditions-item-items" data-bind="{
                            template: {
                                'name': 'intec-seo-filter-conditions-templates-condition',
                                'foreach': conditions.active
                            }
                        }"></div>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko if: type() === 'section' || type() === 'property' -->
                        <div class="filter-conditions-item-content">
                            <div class="filter-conditions-item-field">
                                <div class="filter-conditions-item-name" data-bind="{
                                    text: (function () {
                                        var result = '';

                                        if (type() === 'section') {
                                            result = '<?= Loc::getMessage('conditions.items.types.section') ?>';
                                        } else {
                                            var property = $data.property();

                                            result = '<?= Loc::getMessage('conditions.items.types.property') ?>';

                                            if (property !== null)
                                                result = result + ' [' + property.id + '] ' + property.name;
                                        }

                                        return result;
                                    })()
                                }"></div>
                            </div>
                            <div class="filter-conditions-item-field">
                                <div class="filter-conditions-item-compare">
                                    <select data-bind="{
                                        attr: {
                                            'name': $inputName('operator')
                                        },
                                        options: operators,
                                        optionsText: 'text',
                                        optionsValue: 'value',
                                        value: operator
                                    }"></select>
                                </div>
                            </div>
                            <div class="filter-conditions-item-field">
                                <!-- ko if: type() === 'section' -->
                                    <div class="filter-conditions-item-value">
                                        <!-- ko if: !$root.iblock.refreshing() -->
                                            <select data-bind="{
                                                attr: {
                                                    'name': $inputName('value')
                                                },
                                                options: $root.iblock.sections,
                                                optionsText: function (section) {
                                                    var level = '';

                                                    for (var i = 0; i < section.level; i++)
                                                        level = level + '. ';

                                                    return '[' + section.id + '] ' + level + section.name;
                                                },
                                                optionsValue: 'id',
                                                optionsCaption: '<?= Loc::getMessage('conditions.items.types.section.select') ?>',
                                                value: value
                                            }"></select>
                                        <!-- /ko -->
                                        <!-- ko if: $root.iblock.refreshing() -->
                                            <select data-bind="{
                                                attr: {
                                                    'name': $inputName('value')
                                                }
                                            }">
                                                <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                                            </select>
                                        <!-- /ko -->
                                    </div>
                                <!-- /ko -->
                                <!-- ko if: type() === 'property' && property() !== null -->
                                    <!-- ko if: property().values -->
                                        <select data-bind="{
                                            attr: {
                                                'name': $inputName('value')
                                            },
                                            options: property().values,
                                            optionsText: 'text',
                                            optionsValue: 'value',
                                            optionsCaption: '(<?= Loc::getMessage('answers.unset') ?>)',
                                            value: value
                                        }"></select>
                                    <!-- /ko -->
                                    <!-- ko if: !property().values && property().type !== 'E' && property().type !== 'G' -->
                                        <input type="text" data-bind="{
                                            attr: {
                                                'name': $inputName('value')
                                            },
                                            value: value
                                        }" />
                                    <!-- /ko -->
                                    <!-- ko if: property().type === 'E' || property().type === 'G' -->
                                        <input type="hidden" data-bind="{
                                            attr: {
                                                'name': $inputName('value')
                                            },
                                            value: value
                                        }" />
                                        <div class="filter-conditions-link" data-bind="{
                                            event: {
                                                'click': function () {
                                                    value(null);

                                                    if (property().type === 'G') {
                                                        $root.iblock.sections.selector.select(value);
                                                    } else {
                                                        $root.iblock.elements.selector.select(value);
                                                    }
                                                }
                                            },
                                            text: value.object() ? value.text : '...'
                                        }"></div>
                                    <!-- /ko -->
                                <!-- /ko -->
                            </div>
                            <div class="filter-conditions-item-field">
                                <div class="filter-conditions-link" data-bind="{
                                    event: {
                                        'click': function () {
                                            $parent.conditions.remove($data);
                                        }
                                    }
                                }"><?= Loc::getMessage('conditions.items.remove') ?></div>
                            </div>
                        </div>
                    <!-- /ko -->
                    <!-- ko if: type() === 'group' -->
                        <div class="filter-conditions-item-controls">
                            <!-- ko function: function () {
                                $context.$conditions = ko.computed(function () {
                                    var result = [];

                                    result.push({
                                        'type': 'group',
                                        'name': '<?= Loc::getMessage('conditions.items.types.group') ?>',
                                        'operator': 'and',
                                        'result': true
                                    });

                                    result.push({
                                        'type': 'section',
                                        'name': '<?= Loc::getMessage('conditions.items.types.section') ?>'
                                    });

                                    intec.each($root.iblock.properties(), function (index, property) {
                                        result.push({
                                            'type': 'property',
                                            'name': '<?= Loc::getMessage('conditions.items.types.property') ?> [' + property.id + '] ' + property.name,
                                            'id': property.id
                                        });
                                    });

                                    return result;
                                });

                                $context.$conditions.add = function (condition) {
                                    conditions.create(condition);
                                    $context.$conditions.adding(false);
                                };

                                $context.$conditions.adding = ko.observable(false);
                            } -->
                            <!-- /ko -->
                            <!-- ko if: !$conditions.adding() -->
                                <div class="filter-conditions-link" data-bind="{
                                    event: {
                                        'click': function () {
                                            $conditions.adding(true);
                                        }
                                    }
                                }" style="margin-right: 10px"><?= Loc::getMessage('conditions.items.add') ?></div>
                            <!-- /ko -->
                            <!-- ko if: $conditions.adding() -->
                                <select data-bind="{
                                    options: $conditions,
                                    optionsText: 'name',
                                    optionsCaption: '<?= Loc::getMessage('conditions.items.add.caption') ?>',
                                    value: (function () {
                                        var observer = ko.observable();

                                        observer.subscribe(function (value) {
                                            $context.$conditions.add(value);
                                        });

                                        return observer;
                                    })();
                                }" style="margin-right: 10px"></select>
                                <div class="filter-conditions-link" data-bind="{
                                    event: {
                                        'click': function () {
                                            $conditions.adding(false);
                                        }
                                    }
                                }" style="margin-right: 10px"><?= Loc::getMessage('conditions.items.add.cancel') ?></div>
                            <!-- /ko -->
                            <!-- ko if: $parent !== $root -->
                                <div class="filter-conditions-link" data-bind="{
                                    event: {
                                        'click': function () {
                                            $parent.conditions.remove($data);
                                        }
                                    }
                                }"><?= Loc::getMessage('conditions.items.remove') ?></div>
                            <!-- /ko -->
                        </div>
                    <!-- /ko -->
                </div>
            </script>
        </td>
    </tr>
    <?php $form->EndCustomField('rules') ?>
    <?php $form->BeginCustomField('sort', $template->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($template->formName().'[sort]', $template->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('sectionMetaTitle', $template->getAttributeLabel('sectionMetaTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaTitle]', $template->sectionMetaTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaTitle') ?>
    <?php $form->BeginCustomField('sectionMetaKeywords', $template->getAttributeLabel('sectionMetaKeywords').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaKeywords">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaKeywords]', $template->sectionMetaKeywords, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaKeywords-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaKeywords') ?>
    <?php $form->BeginCustomField('sectionMetaDescription', $template->getAttributeLabel('sectionMetaDescription').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaDescription">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textarea($template->formName().'[sectionMetaDescription]', $template->sectionMetaDescription, [
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
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaDescription-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaDescription') ?>
    <?php $form->BeginCustomField('sectionMetaPageTitle', $template->getAttributeLabel('sectionMetaPageTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPageTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaPageTitle]', $template->sectionMetaPageTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPageTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaPageTitle') ?>
    <?php $form->BeginCustomField('sectionMetaPicturePreviewAlt', $template->getAttributeLabel('sectionMetaPicturePreviewAlt').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewAlt">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaPicturePreviewAlt]', $template->sectionMetaPicturePreviewAlt, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewAlt-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaPicturePreviewAlt') ?>
    <?php $form->BeginCustomField('sectionMetaPicturePreviewTitle', $template->getAttributeLabel('sectionMetaPicturePreviewTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaPicturePreviewTitle]', $template->sectionMetaPicturePreviewTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPicturePreviewTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaPicturePreviewTitle') ?>
    <?php $form->BeginCustomField('sectionMetaPictureDetailAlt', $template->getAttributeLabel('sectionMetaPictureDetailAlt').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailAlt">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaPictureDetailAlt]', $template->sectionMetaPictureDetailAlt, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailAlt-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaPictureDetailAlt') ?>
    <?php $form->BeginCustomField('sectionMetaPictureDetailTitle', $template->getAttributeLabel('sectionMetaPictureDetailTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[sectionMetaPictureDetailTitle]', $template->sectionMetaPictureDetailTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-sectionMetaPictureDetailTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('sectionMetaPictureDetailTitle') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('elementMetaTitle', $template->getAttributeLabel('elementMetaTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaTitle]', $template->elementMetaTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaTitle') ?>
    <?php $form->BeginCustomField('elementMetaKeywords', $template->getAttributeLabel('elementMetaKeywords').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaKeywords">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaKeywords]', $template->elementMetaKeywords, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaKeywords-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaKeywords') ?>
    <?php $form->BeginCustomField('elementMetaDescription', $template->getAttributeLabel('elementMetaDescription').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaDescription">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textarea($template->formName().'[elementMetaDescription]', $template->elementMetaDescription, [
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
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaDescription-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaDescription') ?>
    <?php $form->BeginCustomField('elementMetaPageTitle', $template->getAttributeLabel('elementMetaPageTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaPageTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaPageTitle]', $template->elementMetaPageTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaPageTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaPageTitle') ?>
    <?php $form->BeginCustomField('elementMetaPicturePreviewAlt', $template->getAttributeLabel('elementMetaPicturePreviewAlt').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewAlt">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaPicturePreviewAlt]', $template->elementMetaPicturePreviewAlt, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewAlt-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaPicturePreviewAlt') ?>
    <?php $form->BeginCustomField('elementMetaPicturePreviewTitle', $template->getAttributeLabel('elementMetaPicturePreviewTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaPicturePreviewTitle]', $template->elementMetaPicturePreviewTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaPicturePreviewTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaPicturePreviewTitle') ?>
    <?php $form->BeginCustomField('elementMetaPictureDetailAlt', $template->getAttributeLabel('elementMetaPictureDetailAlt').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailAlt">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaPictureDetailAlt]', $template->elementMetaPictureDetailAlt, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailAlt-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaPictureDetailAlt') ?>
    <?php $form->BeginCustomField('elementMetaPictureDetailTitle', $template->getAttributeLabel('elementMetaPictureDetailTitle').':', false) ?>
        <tr id="intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($template->formName().'[elementMetaPictureDetailTitle]', $template->elementMetaPictureDetailTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <!-- ko if: $root.iblock.id() && !$root.iblock.refreshing() -->
                    <div style="padding-left: 10px">
                        <input type="button" id="intec-seo-iblocks-metadata-templates-edit-elementMetaPictureDetailTitle-menu" value="..." />
                    </div>
                <!-- /ko -->
            </td>
        </tr>
    <?php $form->EndCustomField('elementMetaPictureDetailTitle') ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['iblocks.metadata.templates']
]) ?>
<div id="intec-seo-iblocks-metadata-templates-edit">
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

$convert = function ($rule) use (&$convert) {
    $result = null;

    if ($rule instanceof GroupCondition) {
        $result = [
            'type' => 'group',
            'operator' => $rule->operator,
            'result' => $rule->result,
            'conditions' => []
        ];

        foreach ($rule->conditions as $condition) {
            $child = $convert($condition);

            if ($child !== null)
                $result['conditions'][] = $child;
        }
    } else if ($rule instanceof IBlockSectionCondition) {
        $result = [
            'type' => 'section',
            'operator' => $rule->operator,
            'value' => $rule->value
        ];
    } else if ($rule instanceof IBlockPropertyCondition) {
        $result = [
            'type' => 'property',
            'id' => $rule->id,
            'operator' => $rule->operator,
            'value' => $rule->value
        ];
    }

    return $result;
};

$data['rule'] = $convert($template->getRules());

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

        models.Condition = function (data) {
            var self = this;

            self.type = ko.computed(function () { return data.type; });

            if (self.type() === 'group') {
                self.operator = ko.observable('and');
                self.operator.toggle = function () {
                    if (self.operator() === 'and') {
                        if (self.result()) {
                            self.result(false);
                        } else {
                            self.result(true);
                            self.operator('or');
                        }
                    } else {
                        if (self.result()) {
                            self.result(false);
                        } else {
                            self.result(true);
                            self.operator('and');
                        }
                    }
                };

                self.result = ko.observable(true);
                self.conditions = ko.observableArray();
                self.conditions.active = ko.computed(function () {
                    var result = [];

                    api.each(self.conditions(), function (index, condition) {
                        if (condition.type() === 'property') {
                            if (condition.property() !== null)
                                result.push(condition);
                        } else {
                            result.push(condition);
                        }
                    });

                    return result;
                });

                self.conditions.create = $.proxy(function (data) {
                    var condition = new models.Condition(api.extend({}, data));
                    this.push(condition);
                    return condition;
                }, self.conditions);

                if (data.operator === 'and' || data.operator === 'or')
                    self.operator(data.operator);

                if (!data.result)
                    self.result(false);

                api.each(data.conditions, function (index, condition) {
                    self.conditions.create(condition);
                });
            } else if (self.type() === 'section') {
                self.operator = ko.observable(data.operator);
                self.value = ko.observable(data.value);
                self.section = ko.computed(function () {
                    var result = null;

                    api.each(root.iblock.sections(), function (index, section) {
                        if (section.id === self.value()) {
                            result = section;
                            return false;
                        }
                    });

                    return result;
                });

                self.operators = ko.computed(function () {
                    return [{
                        'value': '=',
                        'text': '<?= Loc::getMessage('conditions.items.types.section.operators.equal') ?>'
                    }, {
                        'value': '!',
                        'text': '<?= Loc::getMessage('conditions.items.types.section.operators.notEqual') ?>'
                    }];
                });
            } else if (self.type() === 'property') {
                var object = ko.observable();

                object.update = function () {
                    var property = self.property();

                    if (!api.isDeclared(property))
                        return;

                    object(null);

                    if (property.values) {
                        api.each(property.values, function (index, value) {
                            if (value.value === self.value()) {
                                object(value);
                                return false;
                            }
                        });
                    } else if (property.type === 'G') {
                        if (self.value() > 0)
                            request('get.iblock.section', {
                                'section': self.value()
                            }, function (result) {
                                object(result);
                            });
                    } else if (property.type === 'E') {
                        if (self.value() > 0)
                            request('get.iblock.element', {
                                'element': self.value()
                            }, function (result) {
                                object(result);
                            });
                    }
                };

                self.id = ko.observable(data.id);
                self.operator = ko.observable(data.operator);
                self.value = ko.observable(data.value);

                self.property = ko.computed(function () {
                    var result = null;

                    api.each(root.iblock.properties(), function (index, property) {
                        if (property.id === self.id()) {
                            result = property;
                            return false;
                        }
                    });

                    return result;
                });

                self.value.object = ko.computed(function () {
                    return object();
                });

                self.value.text = ko.computed(function () {
                    var property = self.property();
                    var object = self.value.object();

                    if (api.isDeclared(property) && object) {
                        if (property.values) {
                            return object.text;
                        } else if (property.type === 'E' || property.type === 'G') {
                            return '[' + object.id + '] ' + object.name;
                        }
                    }

                    return self.value();
                });

                self.property.subscribe(object.update);
                self.value.subscribe(object.update);

                self.operators = ko.computed(function () {
                    var property = self.property();
                    var result = [];

                    if (!api.isDeclared(property))
                        return result;

                    result = [{
                        'value': '=',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.equal') ?>'
                    }, {
                        'value': '!',
                        'text': '<?= Loc::getMessage('conditions.items.types.property.operators.notEqual') ?>'
                    }];

                    if (property.type === 'N') {
                        result = [{
                            'value': '<',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.less') ?>'
                        }, {
                            'value': '<=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.lessOrEqual') ?>'
                        }, {
                            'value': '>',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.more') ?>'
                        }, {
                            'value': '>=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.moreOrEqual') ?>'
                        }];
                    }

                    if (!api.isDeclared(property.values) && property.type !== 'E' && property.type !== 'G' && property.type !== 'N') {
                        result.push({
                            'value': '*=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.contain') ?>'
                        });

                        result.push({
                            'value': '!*=',
                            'text': '<?= Loc::getMessage('conditions.items.types.property.operators.notContain') ?>'
                        });
                    }

                    return result;
                });
            }
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

        root.iblock.elements = {};
        root.iblock.elements.selector = {};
        root.iblock.elements.selector.link = null;
        root.iblock.elements.selector.select = function (observable) {
            var url = '/bitrix/admin/iblock_element_search.php?lang=<?= LANGUAGE_ID?>';

            if (!ko.isObservable(observable))
                return;

            root.iblock.elements.selector.link = observable;
            url += '&lookup=intecSeoMetadataTemplatesEditElementsSelector';

            jsUtils.OpenWindow(url);
        };

        root.iblock.properties = ko.observableArray();
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
                    iblock.properties(data.properties);
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

        root.rule = ko.observable();

        root.iblock.id(data.iblock);
        root.iblock.sections.selected(data.sections);
        root.rule(new models.Condition(data.rule));

        ko.applyBindings(root, $('#intec-seo-iblocks-metadata-templates-edit').get(0));
    })(jQuery, intec);
</script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
