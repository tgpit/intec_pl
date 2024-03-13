<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\condition\Generator;
use intec\seo\models\filter\condition\generator\Blocks;
use intec\seo\models\filter\condition\generator\Section;
use intec\seo\models\filter\condition\generator\Site;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

Loader::includeModule('fileman');

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

Core::$app->web->js->loadExtensions(['jquery', 'jquery_extensions', 'intec_core', 'knockout', 'knockout_extensions']);
Core::$app->web->css->addFile('@intec/seo/resources/css/filter/conditions/generators/blocks.css');

$request = Core::$app->request;
$error = null;

/** @var Generator $generator */
$generator = $request->get('generator');

if (!empty($generator)) {
    $generator = Generator::findOne($generator);

    if (empty($generator))
        LocalRedirect($arUrlTemplates['filter.conditions.generators']);
} else {
    $generator = new Generator();
    $generator->loadDefaultValues();
}

if (!$generator->getIsNewRecord())
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));

$sites = Arrays::fromDBResult(CSite::GetList($by = 'sort', $order = 'asc'));
$generatorSites = $generator->getSites(true);
$generatorSections = $generator->getSections(true);
$generatorBlocks = $generator->getBlocks();

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
                    'properties' => Arrays::fromDBResult(CIBlockProperty::GetList([
                        'SORT' => 'ASC'
                    ], [
                        'IBLOCK_ID' => $iBlock['ID'],
                        'ACTIVE' => 'Y'
                    ]))->asArray(function ($index, $iBlockProperty) {
                        return [
                            'value' => [
                                'id' => Type::toInteger($iBlockProperty['ID']),
                                'code' => $iBlockProperty['CODE'],
                                'name' => $iBlockProperty['NAME']
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
    $data = ArrayHelper::getValue($post, $generator->formName());
    $return = $request->post('apply');
    $return = empty($return);
    $generator->load($post);
    $generator->conditionMetaDescriptionTop = $request->post('ConditionMetaDescriptionTop');
    $generator->conditionMetaDescriptionBottom = $request->post('ConditionMetaDescriptionBottom');
    $generator->conditionMetaDescriptionAdditional = $request->post('ConditionMetaDescriptionAdditional');

    if (!Type::isArray($data))
        $data = [];

    $generator->setBlocks(null);

    if (!empty($data['blocks']) && Type::isArray($data['blocks']))
        $generator->setBlocks(Blocks::create($data['blocks']));

    if ($generator->save()) {
        foreach ($generatorSites as $generatorSite)
            $generatorSite->delete();

        foreach ($generatorSections as $generatorSection)
            $generatorSection->delete();

        if (!empty($data['sites']) && Type::isArray($data['sites'])) {
            foreach ($data['sites'] as $site) {
                $site = new Site([
                    'generatorId' => $generator->id,
                    'siteId' => $site
                ]);

                $site->save();
            }

            unset($site);
        }

        if (!empty($data['sections']) && Type::isArray($data['sections'])) {
            foreach ($data['sections'] as $section) {
                $section = new Section([
                    'generatorId' => $generator->id,
                    'iBlockSectionId' => $section
                ]);

                $section->save();
            }
        }

        if ($return)
            LocalRedirect($arUrlTemplates['filter.conditions.generators']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.generators.edit'], [
            'generator' => $generator->id
        ]));
    } else {
        $error = $generator->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
} else {
    if (!$generator->getIsNewRecord()) {
        if ($request->get('action') === 'generate') {
            if (!$generator->generate()) {
                $error = Loc::getMessage('errors.generate');
            } else {
                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.conditions.generators.edit'], [
                    'generator' => $generator->id
                ]));
            }
        }
    }
}

$form = new CAdminForm('filterConditionGeneratorEditForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
], [
    'DIV' => 'meta',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.meta'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.meta'))
], [
    'DIV' => 'tags',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.tags'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.tags'))
], [
    'DIV' => 'generation',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.generation'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.generation'))
]]);


$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['filter.conditions.generators']
]];

if (!$generator->getIsNewRecord()) {
    $url = new Url($request->getUrl());
    $url->getQuery()->set('action', 'generate');

    $panel[] = [
        'TEXT' => Loc::getMessage('panel.actions.generate'),
        'ICON' => 'btn_green',
        'LINK' => $url->build()
    ];

    unset($url);
}

$panel[] = [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['filter.conditions.generators.add']
];

$panel = new CAdminContextMenu($panel);

unset($url);

$macros = $generator->getMacros();

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
    <?php if (!$generator->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $generator->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $generator->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('name', $generator->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($generator->formName().'[name]', $generator->name) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('sites', Loc::getMessage('fields.sites').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::dropDownList($generator->formName().'[sites]', $generatorSites->asArray(function ($index, $site) {
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
    <?php $form->BeginCustomField('blocks', $generator->getAttributeLabel('blocks').':', false) ?>
        <tr>
            <td width="40%"><?= $generator->getAttributeLabel('iBlockId').':' ?></td>
            <td>
                <!-- ko if: !iblocks.refreshing() -->
                    <select name="<?= $generator->formName().'[iBlockId]' ?>" data-bind="{
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
                    <select name="<?= $generator->formName().'[iBlockId]' ?>">
                        <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                    </select>
                <!-- /ko -->
            </td>
        </tr>
        <tr>
            <td width="40%"><?= Loc::getMessage('fields.sections').':' ?></td>
            <td>
                <!-- ko if: !iblock.refreshing() -->
                    <select name="<?= $generator->formName().'[sections][]' ?>" multiple="multiple" size="10" data-bind="{
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
                    <select name="<?= $generator->formName().'[sections][]' ?>" multiple="multiple" size="10">
                        <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                    </select>
                <!-- /ko -->
            </td>
        </tr>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <input type="hidden" name="<?= $generator->formName().'[operator]' ?>" data-bind="{
                    attr: {
                        'value': operator
                    }
                }" />
                <div class="m-intec-seo p-filter-conditions-blocks">
                    <div class="filter-conditions-blocks-items" data-bind="{
                        foreach: blocks
                    }">
                        <div class="filter-conditions-blocks-item">
                            <!-- ko if: $index() > 0 -->
                                <div class="filter-conditions-blocks-item-operator" data-bind="{
                                    attr: {
                                        'data-operator': $root.operator
                                    },
                                    event: {
                                        'click': function () {
                                            $root.operator.toggle();
                                        }
                                    },
                                    text: $root.operator() === 'and' ? '<?= Loc::getMessage('blocks.operators.and') ?>' : '<?= Loc::getMessage('blocks.operators.or') ?>'
                                }"></div>
                            <!-- /ko -->
                            <div class="filter-conditions-blocks-item-properties" data-bind="{
                                foreach: properties
                            }">
                                <div class="filter-conditions-blocks-item-property">
                                    <!-- ko if: !$root.iblock.refreshing() -->
                                        <select style="margin-right: 10px" data-bind="{
                                            attr: {
                                                'name': (function () {
                                                    return '<?= $generator->formName() ?>[blocks][' + $parentContext.$index() + '][properties][' + $index() + '][id]';
                                                })()
                                            },
                                            options: $root.iblock.properties,
                                            optionsCaption: <?= JavaScript::toObject('('.Loc::getMessage('answers.unset').')') ?>,
                                            optionsText: function (property) {
                                                return '[' + property.id + '] ' + property.name;
                                            },
                                            optionsValue: 'id',
                                            value: id
                                        }"></select>
                                    <!-- /ko -->
                                    <!-- ko if: $root.iblock.refreshing() -->
                                        <select data-bind="{
                                            attr: {
                                                'name': (function () {
                                                    return '<?= $generator->formName() ?>[blocks][' + $parentContext.$index() + '][properties][' + $index() + '][id]';
                                                })()
                                            }
                                        }">
                                            <option><?= '('.Loc::getMessage('answers.unset').')' ?></option>
                                        </select>
                                    <!-- /ko -->
                                    <span class="filter-conditions-blocks-link" data-bind="{
                                        event: {
                                            'click': function () {
                                                $parent.properties.remove($data);
                                            }
                                        }
                                    }">
                                        <?= Loc::getMessage('blocks.items.properties.remove') ?>
                                    </span>
                                </div>
                            </div>
                            <input type="button" value="<?= Loc::getMessage('blocks.items.properties.add') ?>" style="margin-right: 10px" data-bind="{
                                event: {
                                    'click': function () {
                                        $data.properties.create();
                                    }
                                }
                            }" />
                            <span class="filter-conditions-blocks-link" data-bind="{
                                event: {
                                    'click': function () {
                                        $parent.blocks.remove($data);
                                    }
                                }
                            }">
                                <?= Loc::getMessage('blocks.items.remove') ?>
                            </span>
                        </div>
                    </div>
                    <span class="filter-conditions-blocks-link" data-bind="{
                        event: {
                            'click': function () {
                                $data.blocks.create();
                            }
                        }
                    }">
                        <?= Loc::getMessage('blocks.items.add') ?>
                    </span>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('blocks') ?>
    <?php $form->BeginCustomField('conditionActive', $generator->getAttributeLabel('conditionActive').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($generator->formName().'[conditionActive]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionActive]', $generator->conditionActive, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionActive') ?>
    <?php $form->BeginCustomField('conditionName', $generator->getAttributeLabel('conditionName').':', true) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionName]', $generator->conditionName) ?>
                <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionName-menu" value="..." />
            </td>
        </tr>
    <?php $form->EndCustomField('conditionName') ?>
    <?php $form->BeginCustomField('conditionSearchable', $generator->getAttributeLabel('conditionSearchable').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">1</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($generator->formName().'[conditionSearchable]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionSearchable]', $generator->conditionSearchable, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionSearchable') ?>
    <?php $form->BeginCustomField('conditionIndexing', $generator->getAttributeLabel('conditionIndexing').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">2</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($generator->formName().'[conditionIndexing]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionIndexing]', $generator->conditionIndexing, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionIndexing') ?>
    <?php $form->BeginCustomField('conditionStrict', $generator->getAttributeLabel('conditionStrict').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">3</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($generator->formName().'[conditionStrict]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionStrict]', $generator->conditionStrict, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionStrict') ?>
    <?php $form->BeginCustomField('conditionRecursive', $generator->getAttributeLabel('conditionRecursive').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">4</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($generator->formName().'[conditionRecursive]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionRecursive]', $generator->conditionRecursive, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionRecursive') ?>
    <?php $form->BeginCustomField('conditionPriority', $generator->getAttributeLabel('conditionPriority').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">5</span></b>
            </td>
            <td><?= Html::textInput($generator->formName().'[conditionPriority]', $generator->conditionPriority) ?></td>
        </tr>
    <?php $form->EndCustomField('conditionPriority') ?>
    <?php $form->BeginCustomField('conditionFrequency', $generator->getAttributeLabel('conditionFrequency').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">6</span></b>
            </td>
            <td><?= Html::dropDownList($generator->formName().'[conditionFrequency]', $generator->conditionFrequency, Condition::getFrequencies()) ?></td>
        </tr>
    <?php $form->EndCustomField('conditionFrequency') ?>
    <?php $form->BeginCustomField('sort', $generator->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($generator->formName().'[sort]', $generator->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('conditionMetaTitle', $generator->getAttributeLabel('conditionMetaTitle').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionMetaTitle]', $generator->conditionMetaTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaTitle-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaTitle') ?>
    <?php $form->BeginCustomField('conditionMetaKeywords', $generator->getAttributeLabel('conditionMetaKeywords').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaKeywords">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionMetaKeywords]', $generator->conditionMetaKeywords, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaKeywords-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaKeywords') ?>
    <?php $form->BeginCustomField('conditionMetaDescription', $generator->getAttributeLabel('conditionMetaDescription').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaDescription">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textarea($generator->formName().'[conditionMetaDescription]', $generator->conditionMetaDescription, [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '150px',
                        'resize' => 'vertical',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaDescription-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaDescription') ?>
    <?php $form->BeginCustomField('conditionMetaSearchTitle', $generator->getAttributeLabel('conditionMetaSearchTitle').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaSearchTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionMetaSearchTitle]', $generator->conditionMetaSearchTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaSearchTitle-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaSearchTitle') ?>
    <?php $form->BeginCustomField('conditionMetaPageTitle', $generator->getAttributeLabel('conditionMetaPageTitle').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaPageTitle">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionMetaPageTitle]', $generator->conditionMetaPageTitle, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaPageTitle-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaPageTitle') ?>
    <?php $form->BeginCustomField('conditionMetaBreadcrumbName', $generator->getAttributeLabel('conditionMetaBreadcrumbName').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaBreadcrumbName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionMetaBreadcrumbName]', $generator->conditionMetaBreadcrumbName, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaBreadcrumbName-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaBreadcrumbName') ?>
    <?php $form->BeginCustomField('conditionMetaDescriptionTop', $generator->getAttributeLabel('conditionMetaDescriptionTop').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionTop">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?php CFileMan::AddHTMLEditorFrame(
                    'ConditionMetaDescriptionTop',
                    $generator->conditionMetaDescriptionTop,
                    null,
                    'html',
                    [
                        'height' => 150,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    SITE_ID,
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionTop-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaDescriptionTop') ?>
    <?php $form->BeginCustomField('conditionMetaDescriptionBottom', $generator->getAttributeLabel('conditionMetaDescriptionBottom').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionBottom">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?php CFileMan::AddHTMLEditorFrame(
                    'ConditionMetaDescriptionBottom',
                    $generator->conditionMetaDescriptionBottom,
                    null,
                    'html',
                    [
                        'height' => 150,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    SITE_ID,
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionBottom-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaDescriptionBottom') ?>
    <?php $form->BeginCustomField('conditionMetaDescriptionAdditional', $generator->getAttributeLabel('conditionMetaDescriptionAdditional').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionAdditional">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?php CFileMan::AddHTMLEditorFrame(
                    'ConditionMetaDescriptionAdditional',
                    $generator->conditionMetaDescriptionAdditional,
                    null,
                    'html',
                    [
                        'height' => 150,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    SITE_ID,
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionAdditional-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionMetaDescriptionAdditional') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('conditionTagName', $generator->getAttributeLabel('conditionTagName').':', false) ?>
        <tr id="intec-seo-filter-conditions-generators-edit-conditionTagName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionTagName]', $generator->conditionTagName, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td>
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-generators-edit-conditionTagName-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionTagName') ?>
    <?php $form->BeginCustomField('conditionTagMode', $generator->getAttributeLabel('conditionTagMode').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">7</span></b>
            </td>
            <td><?= Html::dropDownList($generator->formName().'[conditionTagMode]', $generator->conditionTagMode, Condition::getTagModes()) ?></td>
        </tr>
    <?php $form->EndCustomField('conditionTagMode') ?>
    <?php $form->BeginCustomField('conditionTagRelinkingStrict', $generator->getAttributeLabel('conditionTagRelinkingStrict').':', false) ?>
        <tr>
            <td width="40%">
                <?= $form->GetCustomLabelHTML() ?>
                <b><span class="required" style="vertical-align: super; font-size: smaller;">8</span></b>
            </td>
            <td>
                <?= Html::hiddenInput($generator->formName().'[conditionTagRelinkingStrict]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionTagRelinkingStrict]', $generator->conditionTagRelinkingStrict, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionTagRelinkingStrict') ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('conditionUrlActive', $generator->getAttributeLabel('conditionUrlActive').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td colspan="2">
                <?= Html::hiddenInput($generator->formName().'[conditionUrlActive]', 0) ?>
                <?= Html::checkbox($generator->formName().'[conditionUrlActive]', $generator->conditionUrlActive, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionUrlActive') ?>
    <?php $form->BeginCustomField('conditionUrlName', $generator->getAttributeLabel('conditionUrlName').':', false) ?>
        <tr id="intec-seo-filter-conditions-edit-conditionUrlName">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionUrlName]', $generator->conditionUrlName, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td width="1px">
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-edit-conditionUrlName-menu" value="..." />
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionUrlName') ?>
    <?php $form->BeginCustomField('conditionUrlSource', $generator->getAttributeLabel('conditionUrlSource').':', true) ?>
        <tr id="intec-seo-filter-conditions-edit-conditionUrlSource">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionUrlSource]', $generator->conditionUrlSource, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td width="1px">
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-edit-conditionUrlSource-menu" value="..." />
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.conditionUrlSource.description') ?>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    <?php $form->EndCustomField('conditionUrlSource') ?>
    <?php $form->BeginCustomField('conditionUrlTarget', $generator->getAttributeLabel('conditionUrlTarget').':', true) ?>
        <tr id="intec-seo-filter-conditions-edit-conditionUrlTarget">
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($generator->formName().'[conditionUrlTarget]', $generator->conditionUrlTarget, [
                    'style' => [
                        'width' => '100%',
                        'box-sizing' => 'border-box'
                    ]
                ]) ?>
            </td>
            <td width="1px">
                <div style="padding-left: 10px">
                    <input type="button" id="intec-seo-filter-conditions-edit-conditionUrlTarget-menu" value="..." />
                </div>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.conditionUrlTarget.description') ?>
                    </div>
                </div>
            </td>
            <td></td>
        </tr>
    <?php $form->EndCustomField('conditionUrlTarget') ?>
    <?php $form->BeginCustomField('conditionUrlGenerator', $generator->getAttributeLabel('conditionUrlGenerator').':', false) ?>
    <tr>
        <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
        <td colspan="2">
            <?= Html::dropDownList($generator->formName().'[conditionUrlGenerator]', $generator->conditionUrlGenerator, Condition::getUrlGenerators()) ?>
        </td>
    </tr>
    <?php $form->EndCustomField('conditionUrlGenerator') ?>
<?php

$buttons = [];

if (!$generator->getIsNewRecord()) {
    $url = new Url($request->getUrl());
    $url->getQuery()->set('action', 'generate');

    $buttons[] = Html::a(Loc::getMessage('panel.actions.generate'), $url->build(), [
        'class' => [
            'adm-btn'
        ],
        'style' => [
            'margin' => '2px',
            'float' => 'right'
        ]
    ]);

    unset($url);
}

?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['filter.conditions.generators']
], implode('', $buttons)) ?>
<div id="intec-seo-filter-conditions-generators-edit">
    <?php $form->Show() ?>
    <?php
        $createPopupItems = function ($macros, $selector) use(&$createPopupItems) {
            $items = [];

            foreach ($macros as $part) {
                $item = [
                    'TEXT' => $part['name']
                ];

                if ($part['type'] === 'group') {
                    $item['MENU'] = $createPopupItems($part['items'], $selector);
                } else if ($part['type'] === 'macro') {
                    $item['ONCLICK'] = 'window.page.insertMacroToField('.JavaScript::toObject($selector).', '.JavaScript::toObject($part['value']).')';
                }

                $items[] = $item;
            }

            return $items;
        };

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionName-menu',
            $createPopupItems([[
                'code' => 'sites',
                'type' => 'group',
                'name' => Loc::getMessage('fields.conditionName.macros.sites.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.conditionName.macros.sites.items.id'),
                    'value' => '#SITES_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.conditionName.macros.sites.items.name'),
                    'value' => '#SITES_NAME#'
                ]]
            ], [
                'code' => 'sections',
                'type' => 'group',
                'name' => Loc::getMessage('fields.conditionName.macros.sections.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.conditionName.macros.sections.items.id'),
                    'value' => '#SECTIONS_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.conditionName.macros.sections.items.name'),
                    'value' => '#SECTIONS_NAME#'
                ]]
            ], [
                'code' => 'properties',
                'type' => 'group',
                'name' => Loc::getMessage('fields.conditionName.macros.properties.name'),
                'items' => [[
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.conditionName.macros.properties.items.id'),
                    'value' => '#PROPERTIES_ID#'
                ], [
                    'type' => 'macro',
                    'name' => Loc::getMessage('fields.conditionName.macros.properties.items.name'),
                    'value' => '#PROPERTIES_NAME#'
                ]]
            ]], '#intec-seo-filter-conditions-generators-edit-conditionName')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaTitle-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaTitle')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaKeywords-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaKeywords')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaDescription-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaDescription')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaSearchTitle-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaSearchTitle')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaPageTitle-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaPageTitle')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaBreadcrumbName-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaBreadcrumbName')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionTop-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionTop')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionBottom-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionBottom')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionAdditional-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionMetaDescriptionAdditional')
        );

        $popup->Show();

        $popup = new CAdminPopupEx(
            'intec-seo-filter-conditions-generators-edit-conditionTagName-menu',
            $createPopupItems($macros, '#intec-seo-filter-conditions-generators-edit-conditionTagName')
        );

        $popup->Show();

    $popup = new CAdminPopupEx(
        'intec-seo-filter-conditions-edit-conditionUrlName-menu',
        $createPopupItems([[
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlName.macros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.iblock.items.iblockId'),
                'value' => '#IBLOCK_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.iblock.items.iblockCode'),
                'value' => '#IBLOCK_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.iblock.items.iblockTypeId'),
                'value' => '#IBLOCK_TYPE_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.iblock.items.iblockName'),
                'value' => '#IBLOCK_NAME#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.iblock.items.iblockExternalId'),
                'value' => '#IBLOCK_EXTERNAL_ID#'
            ]]
        ], [
            'code' => 'section',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlName.macros.section.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.section.items.sectionId'),
                'value' => '#SECTION_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.section.items.sectionCode'),
                'value' => '#SECTION_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.section.items.sectionCodePath'),
                'value' => '#SECTION_CODE_PATH#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.section.items.sectionName'),
                'value' => '#SECTION_NAME#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.section.items.sectionExternalId'),
                'value' => '#SECTION_EXTERNAL_ID#'
            ]]
        ], [
            'code' => 'property',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlName.macros.property.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.property.items.propertiesId'),
                'value' => '#PROPERTIES_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.property.items.propertiesCode'),
                'value' => '#PROPERTIES_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.property.items.propertiesName'),
                'value' => '#PROPERTIES_NAME#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlName.macros.property.items.propertiesCombination'),
                'value' => '#PROPERTIES_COMBINATION#'
            ]]
        ]], '#intec-seo-filter-conditions-edit-conditionUrlName')
    );

    $popup->Show();

    $popup = new CAdminPopupEx(
        'intec-seo-filter-conditions-edit-conditionUrlSource-menu',
        $createPopupItems([[
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlSource.macros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.iblock.items.iblockId'),
                'value' => '#IBLOCK_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.iblock.items.iblockCode'),
                'value' => '#IBLOCK_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.iblock.items.iblockTypeId'),
                'value' => '#IBLOCK_TYPE_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.iblock.items.iblockExternalId'),
                'value' => '#IBLOCK_EXTERNAL_ID#'
            ]]
        ], [
            'code' => 'section',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.items.id'),
                'value' => '#ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.items.sectionId'),
                'value' => '#SECTION_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.items.code'),
                'value' => '#CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.items.sectionCode'),
                'value' => '#SECTION_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.items.sectionCodePath'),
                'value' => '#SECTION_CODE_PATH#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlSource.macros.section.items.externalId'),
                'value' => '#EXTERNAL_ID#'
            ]]
        ]], '#intec-seo-filter-conditions-edit-conditionUrlSource')
    );

    $popup->Show();

    $popup = new CAdminPopupEx(
        'intec-seo-filter-conditions-edit-conditionUrlTarget-menu',
        $createPopupItems([[
            'code' => 'iblock',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlTarget.macros.iblock.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.iblock.items.iblockId'),
                'value' => '#IBLOCK_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.iblock.items.iblockCode'),
                'value' => '#IBLOCK_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.iblock.items.iblockTypeId'),
                'value' => '#IBLOCK_TYPE_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.iblock.items.iblockExternalId'),
                'value' => '#IBLOCK_EXTERNAL_ID#'
            ]]
        ], [
            'code' => 'section',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.items.id'),
                'value' => '#ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.items.sectionId'),
                'value' => '#SECTION_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.items.code'),
                'value' => '#CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.items.sectionCode'),
                'value' => '#SECTION_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.items.sectionCodePath'),
                'value' => '#SECTION_CODE_PATH#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.section.items.externalId'),
                'value' => '#EXTERNAL_ID#'
            ]]
        ], [
            'code' => 'property',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlTarget.macros.property.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.property.items.propertyId'),
                'value' => '#PROPERTY_ID#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.property.items.propertyCode'),
                'value' => '#PROPERTY_CODE#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.property.items.propertyValue'),
                'value' => '#PROPERTY_VALUE#'
            ]]
        ], [
            'code' => 'additional',
            'type' => 'group',
            'name' => Loc::getMessage('fields.conditionUrlTarget.macros.additional.name'),
            'items' => [[
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.additional.items.ranges'),
                'value' => '#RANGES#'
            ], [
                'type' => 'macro',
                'name' => Loc::getMessage('fields.conditionUrlTarget.macros.additional.items.prices'),
                'value' => '#PRICES#'
            ]]
        ]], '#intec-seo-filter-conditions-edit-conditionUrlTarget')
    );

    $popup->Show();
    ?>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="width: 100%; box-sizing: border-box">
            <?= Loc::getMessage('notes.fields') ?>
        </div>
    </div>
    <div class="adm-info-message-wrap">
        <div class="adm-info-message" style="width: 100%; box-sizing: border-box">
            <?= Loc::getMessage('notes.macros') ?>
        </div>
    </div>
</div>
<?php

$data = [];
$data['iblock'] = $generator->iBlockId;
$data['sections'] = $generatorSections->asArray(function ($index, $section) {
    /** @var Section $section */

    return [
        'value' => $section->iBlockSectionId
    ];
});

$data['operator'] = $generator->operator;
$data['blocks'] = $generatorBlocks->export();

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

        models.Block = function (data) {
            var self = this;

            self.properties = ko.observableArray();
            self.properties.create = $.proxy(function (data) {
                var property = new models.Property(api.extend({}, data));
                this.push(property);
                return property;
            }, self.properties);

            api.each(data.properties, function (index, property) {
                self.properties.push(new models.Property(property));
            });
        };

        models.Property = function (data) {
            this.id = ko.observable(data.id);
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
        root.iblock.properties = ko.observableArray();
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
                    iblock.refreshing(false);
                });
            } else {
                iblock.refreshing(false);
            }
        };

        root.iblock.id.subscribe(function () {
            root.iblock.refresh();
        });

        root.operator = ko.observable('and');
        root.operator.toggle = function () {
            if (root.operator() === 'and') {
                root.operator('or');
            } else {
                root.operator('and');
            }
        };

        root.blocks = ko.observableArray();
        root.blocks.create = $.proxy(function (data) {
            var block = new models.Block(api.extend({}, data));
            this.push(block);
            return block;
        }, root.blocks);

        root.iblock.id(data.iblock);
        root.iblock.sections.selected(data.sections);

        if (data.operator === 'and' || data.operator === 'or')
            root.operator(data.operator);

        api.each(data.blocks, function (index, block) {
            root.blocks.create(block);
        });

        ko.applyBindings(root, $('#intec-seo-filter-conditions-generators-edit').get(0));
    })(jQuery, intec);
</script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
