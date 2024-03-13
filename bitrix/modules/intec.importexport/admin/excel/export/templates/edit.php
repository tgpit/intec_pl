<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\Type;
use intec\core\helpers\JavaScript;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\base\InvalidParamException;
use intec\importexport\models\excel\TableHelper;
use intec\importexport\models\excel\IBlockHelper;
use intec\importexport\models\excel\export\Template;
use intec\importexport\models\excel\export\Filter;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @todo Проверить/пофиксить “Путь у картинок разделов”
 */

global $APPLICATION;

if (!Loader::includeModule('iblock'))
    return;

$isBase = Loader::includeModule('catalog');

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../../requirements.php');
include(Core::getAlias('@intec/importexport/module/admin/url.php'));
require_once(dirname(__FILE__).'/../../../../classes/models/excel/export/Export.php');

$arJsConfig = [
    'script_export' => [
        'js' => '/bitrix/js/intec.importexport/script_export.js'
    ]
];

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

Core::$app->web->js->loadExtensions(['vue', 'jquery', 'jquery_extensions', 'intec_core', 'knockout', 'knockout_extensions']);
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/export/templates/edit.css');
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/column_settings.css');
CJSCore::Init(array('script_export', 'translit'));


$request = Core::$app->request;
$action = $request->get('action');
$error = [];

$template = $request->get('template');
$parameters = [];

$new = false;

/*prepare data*/
if (!empty($template)) {
    $template = Template::findOne($template);

    if (empty($template))
        LocalRedirect($arUrlTemplates['excel.export.templates']);

    if (!$template->getIsNewRecord()) {
        $APPLICATION->SetTitle(Loc::getMessage('title.edit'));

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

        try {
            $columnSettings = Json::decode($template->getAttribute('columnSettings'), true, true);
        } catch (InvalidParamException $exception) {
            $columnSettings = [];
        }

    }
} else {
    $template = new Template();
    $template->loadDefaultValues();
    $new = true;
}


/*data for vue*/
$arIBlocksTypes = IBlockHelper::getIBlockTypes();

$arIBlocksTypesJs = [];

foreach ($arIBlocksTypes as $key => $value) {
    $arIBlocksTypesJs[] = [
        'key' => $key,
        'value' => $value
    ];
}

/*checkbox*/
/*if ($parameters['useNonPrice'] === null)
    $parameters['useNonPrice'] = false;*/

/*request for vue*/
$ajaxExport = new Export($parameters, $tableParameters);
if ($request->getIsAjax()) {
    if ($request->getIsPost()) {
        $action = $request->post('action');
        $response = [
            'status' => 'error'
        ];

        if ($action === 'get.iblocks') {
            $response['status'] = 'success';
            $type = $request->post('type');


            $arIBlocks = Arrays::fromDBResult(CIBlock::GetList([
                'SORT' => 'ASC'
            ], ['TYPE' => $type]))->indexBy('ID')->asArray(
                function ($index, $iBlock) {
                    return [
                        'value' => [
                            'id' => Type::toInteger($iBlock['ID']),
                            'code' => $iBlock['CODE'],
                            'name' => '[' . $iBlock['ID'] . '] ' . $iBlock['NAME']
                        ]
                    ];
                });

            $response['data'] = $arIBlocks;

        } elseif ($action === 'save.export') {
            $response['status'] = 'success';

            $id = $request->post('id');
            //$step = $request->post('step');

            $isNew = empty($_SESSION['INTEC_EXPORT']);
            $result = $ajaxExport->generateExcelByIdOnSteps($id, $isNew);

            $response['data'] = $result;
        } elseif ($action === 'loadTable') {
            $response['status'] = 'success';

            if ($parameters['wrapMultipleElements'])
                $delimiter = $parameters['delimiter'] . ' ';
            else
                $delimiter = $parameters['delimiter'];

            $elements = IBlockHelper::getElements($parameters['iblock'], ["ID"=>"ASC"], [], [], $parameters['showInTable'], 0, true);
            $elements = IBlockHelper::prepareElement($elements, $delimiter);

            if ($isBase)
                $elements = IBlockHelper::getCatalogPropertyElements($elements);

            $elements = IBlockHelper::getSeoPropertyElements($elements);
            $elements = IBlockHelper::getSectionsProperties($elements, $parameters['iblock'], $parameters['level']);

            if ($isBase)
                $elements = IBlockHelper::getOffers($elements, $parameters['iblock'], $parameters['offersFormat'], $parameters['offersPriceType'], $delimiter, $parameters['useNonPrice']);

            $response['data']['elements'] = $elements;

            /*table data*/
            $tableData = [
                [
                    'id' => 1,
                    'selected' => 'ID',
                    'header' => 'A',
                    'sortable' => true,
                    'selectedName' => '',
                    'title' => [
                        'delete' => Loc::getMessage('hint.column.delete') . 'A"]',
                        'sort'=> Loc::getMessage('hint.column.sort') . 'A"]'
                    ]
                ], [
                    'id' => 2,
                    'selected' => 'NAME',
                    'header' => 'B',
                    'sortable' => true,
                    'selectedName' => '',
                    'title' => [
                        'delete' => Loc::getMessage('hint.column.delete') . 'B"]',
                        'sort'=> Loc::getMessage('hint.column.sort') . 'B"]'
                    ]
                ],[
                    'id' => 3,
                    'selected' => 'PREVIEW_PICTURE',
                    'header' => 'C',
                    'sortable' => true,
                    'title' => [
                        'delete' => Loc::getMessage('hint.column.delete') . 'C"]',
                        'sort'=> Loc::getMessage('hint.column.sort') . 'C"]'
                    ]
                ],[
                    'id' => 4,
                    'selected' => 'SECTION_PROPERTY_LEVEL_0_NAME',
                    'header' => 'D',
                    'sortable' => true,
                    'title' => [
                        'delete' => Loc::getMessage('hint.column.delete') . 'E"]',
                        'sort'=> Loc::getMessage('hint.column.sort') . 'E"]'
                    ]
                ]
            ];

            $tableNothingFound = [
                [
                    'id' => 0,
                    'name' => Loc::getMessage('column.nothing.found'),
                    'code' => 'NOTHING',
                    'disable' => true
                ]
            ];

            if (!empty($tableParameters['data'])) {
                $tableData = [];
                $propertiesCodes = [];

                foreach ($tableParameters['data'] as $key => $param) {
                    if (StringHelper::startsWith($param, 'PROPERTY_')) {
                        $propertiesCodes[] = $param;
                    }
                }

                $propertiesNames = IBlockHelper::getPropertiesNames($parameters['iblock'], $tableParameters['data'], $parameters['level']);

                foreach ($tableParameters['data'] as $key => $param) {
                    $newName = '';
                    $settings = '';

                    if (!empty($columnSettings['newName'][$key]))
                        $newName = $columnSettings['newName'][$key];

                    if (!empty($columnSettings['settings'][$key]))
                        $settings = $columnSettings['settings'][$key];

                    if (StringHelper::startsWith($param, 'PROPERTY_')) {

                        $tableData[] = [
                            'id' => $key + 1,
                            'selected' => $param,
                            'name' => $propertiesNames[$param],
                            'selectedName' => $propertiesNames[$param],
                            'newName' => $newName,
                            'settings' => $settings,
                            'header' => TableHelper::getLetter($key + 1),
                            'sortable' => IBlockHelper::getSortable($param),
                            'title' => [
                                'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                                'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                            ]
                        ];
                    } elseif (StringHelper::startsWith($param, 'CATALOG_PROPERTY_') ||
                        StringHelper::startsWith($param, 'SEO_PROPERTY_') ||
                        StringHelper::startsWith($param, 'SECTION_PROPERTY_')) {
                        $tableData[] = [
                            'id' => $key + 1,
                            'selected' => $param,
                            'name' => $propertiesNames[$param],
                            'newName' => $newName,
                            'selectedName' => $propertiesNames[$param],
                            'settings' => $settings,
                            'header' => TableHelper::getLetter($key + 1),
                            'sortable' => false,
                            'title' => [
                                'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                                'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                            ]
                        ];
                    } elseif (empty($param)) {
                        $tableData[] = [
                            'id' => $key + 1,
                            'selected' => $param,
                            'newName' => '',
                            'name' => Loc::getMessage('new.column.default.name'),
                            'settings' => $settings,
                            'header' => TableHelper::getLetter($key + 1),
                            'sortable' => IBlockHelper::getSortable($param),
                            'title' => [
                                'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                                'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                            ]
                        ];
                    } else {
                        $tableData[] = [
                            'id' => $key + 1,
                            'selected' => $param,
                            'name' => IBlockHelper::getMainName($param),
                            'newName' => $newName,
                            'selectedName' => IBlockHelper::getMainName($param),
                            'settings' => $settings,
                            'header' => TableHelper::getLetter($key + 1),
                            'sortable' => IBlockHelper::getSortable($param),
                            'title' => [
                                'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                                'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                            ]
                        ];
                    }
                }

                unset($propertiesCodes, $propertiesNames, $newName);
            }

            $response['data']['columns'] = $tableData;
        } elseif ($action === 'updateListOnFilter') {
            $response['status'] = 'success';
            $success = false;

            $formData = $request->post('formData');
            $selectedFormData = [];

            if (!empty($formData)) {
                foreach ($formData as $formItem) {
                    if (empty($formItem['value']))
                        continue;

                    if (StringHelper::startsWith($formItem['name'], 'data[tableParameters][filter]')) {
                        $length = StringHelper::length('data[tableParameters][filter]');
                        $name = StringHelper::cut($formItem['name'], $length);
                        $name = StringHelper::replace($name, ['[' => '', ']' => '']);

                        if (empty($selectedFormData[$name])) {
                            $selectedFormData[$name] = $formItem['value'];
                        } else {
                            if (!Type::isArray($selectedFormData[$name])) {
                                $firstValue = $selectedFormData[$name];
                                $selectedFormData[$name] = [];
                                $selectedFormData[$name][] = $firstValue;
                                unset($firstValue);
                            }

                            $selectedFormData[$name][] = $formItem['value'];
                        }
                    }
                }
            }

            $filter = Filter::getFilter($selectedFormData, $parameters['iblock']);


            $elements = IBlockHelper::getElements($parameters['iblock'], ["ID"=>"ASC"], $filter, [], $parameters['showInTable'], 0, true);
            $elements = IBlockHelper::prepareElement($elements, $parameters['delimiter']);

            if ($isBase)
                $elements = IBlockHelper::getCatalogPropertyElements($elements);

            $elements = IBlockHelper::getSeoPropertyElements($elements);
            $elements = IBlockHelper::getSectionsProperties($elements, $parameters['iblock'], $parameters['level']);

            if ($isBase)
                $elements = IBlockHelper::getOffers($elements, $parameters['iblock'], $parameters['offersFormat'], $parameters['offersPriceType'], $delimiter, $parameters['useNonPrice']);

            $response['data']['elements'] = $elements;

            $response['data'] = $elements;
        }

        echo Json::encode($response, 320, true);
        return;
    }
}

/*requests*/
if ($step <= 0)
    $step = 1;

if ($request->getIsPost()) {
    $post = $request->post();

    $step = $post['control']['STEP']['CURRENT'];
    $controlAction = $post['control']['ACTION'];

    if(!empty($controlAction['BACK']))
        $step = $step - 2;

    if(!empty($controlAction['SAVE']))
        $step = $step - 1;

    if(!empty($controlAction['MAIN']))
        $step = 1;

    $data = ArrayHelper::getValue($post, 'data');

    if ($post['tabControl_active_tab'] == 'table' && !empty($data['tableParameters'])) {
        $jsonParameters = Json::encode($data['tableParameters']);

        if ($jsonParameters !== $template->tableParams) {
            $template->updateAttributes(['tableParams' => $jsonParameters]);
            $edited = true;
        }

        if ($data['tableParameters'] !== $tableParameters)
            $tableParameters = $data['tableParameters'];
    }

    if ($post['tabControl_active_tab'] == 'table' && !empty($data['columnSettings'])) {
        $jsonColumnSettings = Json::encode($data['columnSettings'], 320, true);

        if ($jsonParameters !== $template->columnSettings) {
            $template->updateAttributes(['columnSettings' => $jsonColumnSettings]);
            $edited = true;
        }

        if ($data['columnSettings'] !== $columnSettings)
            $columnSettings = $data['columnSettings'];
    }

    if ($data['parameters']['iblock'] == 0 && $post['tabControl_active_tab'] == 'main')
        $error[] = Loc::getMessage('error.iblock');

    if (!empty($data['parameters']['path'])) {
        /*fix this later*/
        $translitParams = [
            'max_len' => 100,
            'change_case' => 'false',
            'replace_space' => '_',
            'replace_other' => '',
            'delete_repeat_replace' => true,
            'safe_chars' => '\/.'
        ];
        $data['parameters']['path'] = CUtil::translit($data['parameters']['path'], 'ru', $translitParams);
    }

    if (empty($data['parameters']['path']) && $post['tabControl_active_tab'] == 'main')
        $error[] = Loc::getMessage('error.file.path');

    if (!empty($data['parameters']['path'])) {
        $isType = explode('.', $data['parameters']['path']);
        $isType = $isType[count($isType) - 1];

        if ($isType !== $data['parameters']['type'])
            $data['parameters']['path'] = $data['parameters']['path'] . '.' . $data['parameters']['type'];
    }

    if ($post['tabControl_active_tab'] == 'main') {

        $showInTable = Type::toInteger($data['parameters']['showInTable']);

        if (empty($showInTable) || $showInTable < 1)
            $data['parameters']['showInTable'] = 25;

        $unloadInStep = Type::toInteger($data['parameters']['unloadInStep']);

        if (empty($unloadInStep) || $unloadInStep < 1)
            $data['parameters']['unloadInStep'] = 10;

        $level = Type::toInteger($data['parameters']['level']);

        if (empty($level) && $level < 0)
            $data['parameters']['level'] = 5;
        elseif ($level < 0)
            $data['parameters']['level'] = 5;
        else
            $data['parameters']['level'] = $level;

        if (empty($data['parameters']['delimiter']))
            $data['parameters']['delimiter'] = ';';

        unset($level, $unloadInStep, $showInTable);
    }

    $jsonParameters = Json::encode($data['parameters']);
    $return = $request->post('apply');
    $return = empty($return);
    $template->load($post);

    if (!Type::isArray($data))
        $data = [];

    $edited = false;

    $template->update(true, ['name']);

    if (isset($data['name']) && !empty($data['name'])) {
        if ($data['name'] !== $template->name) {
            $template->updateAttributes(['name' => $data['name']]);
            $edited = true;
        }
    }

    if (isset($data['parameters'])) {

        if ($jsonParameters !== $template->params) {
            $template->updateAttributes(['params' => $jsonParameters]);
            $edited = true;
        }
    }

    if (empty($template->createDate))
        $template->updateAttributes(['createDate' => date('Y-m-d H:i:s', time())]);

    if ($edited)
        $template->updateAttributes(['editDate' => date('Y-m-d H:i:s', time())]);

    if ($new) {
        if ($template->save()) {
            if (empty($error)) {
                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['excel.export.templates.create'], [
                    'template' => $template->id,
                    'step' => 2
                ]));
            } else {
                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['excel.export.templates.create'], [
                    'template' => $template->id,
                    'step' => 1
                ]));
            }
        } else {
            $error[] = $template->getFirstErrors();
        }
    }


    try {
        $parameters = Json::decode($template->getAttribute('params'));
    } catch (InvalidParamException $exception) {
        $parameters = [];
    }
}

if (!empty($parameters) && $step == 1) {
    if (empty($parameters['iblock']) || $parameters['iblock'] == 0)
        $error[] = Loc::getMessage('error.iblock');

    if (empty($parameters['path']))
        $error[] = Loc::getMessage('error.file.path');
}

if (!empty($error)) {
    if(!empty($controlAction['BACK']))
        $step = $step - 2;

    if(!empty($controlAction['NEXT']))
        $step = $step - 1;
}


if ($step == 2) {

    if (!empty($parameters['iblock'])) {

        $properties = IBlockHelper::getAllProperties($parameters['iblock'], true, $parameters['level']);

        if ($parameters['wrapMultipleElements'])
            $delimiter = $parameters['delimiter'] . ' ';
        else
            $delimiter = $parameters['delimiter'];
    }

    $tableNothingFound = [
        [
            'id' => 0,
            'name' => Loc::getMessage('column.nothing.found'),
            'code' => 'NOTHING',
            'disable' => true
        ]
    ];

    $menuActionList = [
        'addBaseProperties' => [
            'name' => Loc::getMessage('fields.menu.base'),
            'action' => 'addBaseProperties'
        ],
        'addProperties' => [
            'name' => Loc::getMessage('fields.menu.properties'),
            'action' => 'addProperties'
        ],
        'addSeoProperties' => [
            'name' => Loc::getMessage('fields.menu.seo'),
            'action' => 'addSeoProperties'
        ],
        'addCatalogProperties' => [
            'name' => Loc::getMessage('fields.menu.catalog'),
            'action' => 'addCatalogProperties'
        ]
    ];
}

if ($step == 3) {
    unset($_SESSION['INTEC_EXPORT']);
}

/*admin menu settings*/
$tabs = [[
    "ICON" => null,
    "DIV" => "main",
    "TAB" => Loc::getMessage('tabs.main'),
    "TITLE" => Html::encode(Loc::getMessage('tabs.main')),
], [
    "ICON" => null,
    "DIV" => "table",
    "TAB" => Loc::getMessage('tabs.setting') ,
    "TITLE" => Html::encode(Loc::getMessage('tabs.setting')),
], [
    "ICON" => null,
    "DIV" => "processing",
    "TAB" => Loc::getMessage('tabs.result'),
    "TITLE" => Html::encode(Loc::getMessage('tabs.result')),
]];

$tabControl = new CAdminTabControl("tabControl", $tabs, false, true);

$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['excel.export.templates']
]/*, [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['excel.export.templates.add']
]*/];

$panel = new CAdminContextMenu($panel);


/*field data*/
$filedData = $parameters;

if (empty($template->id)) {
    $filedData['type'] = 'xlsx';
    $filePath = '/upload/intec.importexport/export_' . mt_rand(10, 999) . time() . '.' . $filedData['type'];
    $filedData['path'] = $filePath;
    $filedData['delimiter'] = ';';
    $filedData['level'] = 5;
    $filedData['wrapMultipleElements'] = true;
    $filedData['useNonPrice'] = true;
    $filedData['unloadInStep'] = 100;
    $filedData['showInTable'] = 25;
}

if (empty($template->name)) {
    $autoName = 'superOriginalName'; // 'default' or 'superOriginalName'

    if ($autoName === 'superOriginalName') {
        $templateForName = Template::find();
        $allTemplates = $templateForName->all()->asArray();
        $name = Loc::getMessage('fields.name.default') . (count($allTemplates) + 1);

        $duplicates = $templateForName->where(['name' => $name])->all()->asArray();

        if (count($duplicates) > 0) {
            $count = 1;
            $isDuplicate = true;

            while ($isDuplicate) {
                $testName = $name . ' (' . $count . ')';
                $duplicates = $templateForName->where(['name' => $testName])->all()->asArray();

                if (count($duplicates) <= 0)
                    $isDuplicate = false;

                $count++;
            }

            $name = $testName;
        }

        $template->name = $name;

        unset($count, $allTemplates, $templateForName, $name, $isDuplicate, $duplicates);
    } else {
        $templateForName = Template::find();
        $allTemplates = $templateForName->all()->asArray();
        $name = Loc::getMessage('fields.name.default') . (count($allTemplates) + 1);
        $template->name = $name;

        unset($allTemplates, $templateForName, $name);
    }
}

/*field settings*/
$typeOptions = [
    [
        'value' => 'xlsx',
        'label' => '.XLSX',
        'selected' => $parameters['type'] === 'xlsx' || empty($parameters['type'])
    ], [
        'value' => 'xls',
        'label' => '.XLS',
        'selected' => $parameters['type'] === 'xls'
    ], [
        'value' => 'csv',
        'label' => '.CSV',
        'selected' => $parameters['type'] === 'csv'
    ]
];
$csvDelimiterOptions = [
    [
        'value' => ';',
        'label' => Loc::getMessage('option.export.csv.delimiter.semicolon'),
        'selected' => $parameters['type'] === ';' || empty($parameters['type'])
    ], [
        'value' => ',',
        'label' => Loc::getMessage('option.export.csv.delimiter.comma'),
        'selected' => $parameters['type'] === ','
    ], [
        'value' => '\t',
        'label' => Loc::getMessage('option.export.csv.delimiter.tab'),
        'selected' => $parameters['type'] === '\t'
    ]
];
$csvEnclosureOptions = [
    [
        'value' => 'dquote',
        'label' => Loc::getMessage('option.export.csv.enclosure.double.quote'),
        'selected' => $parameters['type'] === 'dquote' || empty($parameters['type'])
    ], [
        'value' => 'quote',
        'label' => Loc::getMessage('option.export.csv.enclosure.quote'),
        'selected' => $parameters['type'] === 'quote'
    ], [
        'value' => 'none',
        'label' => Loc::getMessage('option.export.csv.enclosure.none'),
        'selected' => $parameters['type'] === 'none'
    ]
];
$offersFormatOptions = [
    [
        'value' => 'line',
        'label' => Loc::getMessage('option.export.offers.format.line'),
        'selected' => $parameters['offersFormat'] === 'line' || empty($parameters['offersFormat'])
    ], [
        'value' => 'minimal',
        'label' => Loc::getMessage('option.export.offers.format.minimal'),
        'selected' => $parameters['offersFormat'] === 'minimal'
    ]
];

if ($isBase) {
    $priceTypes = Arrays::fromDBResult(CCatalogGroup::GetList(['SORT' => 'ASC']))->asArray();
    $priceTypeOptions[] = [
        'value' => 'PURCHASING',
        'label' => Loc::getMessage('option.export.purchasing.price'),
        'selected' => $parameters['offersPriceType'] == 0 || empty($parameters['offersPriceType'])
    ];

    foreach ($priceTypes as $priceType) {
        $priceTypeOptions[] = [
            'value' => $priceType['ID'],
            'label' => Loc::getMessage('option.price.name').' "'.($priceType['NAME_LANG'] ? $priceType['NAME_LANG'] : $priceType['NAME']).'"',
            'selected' => $parameters['offersPriceType'] == $priceType['ID']
        ];
    }
    unset($priceTypes);
}



/* filter */
if ($step == 2) {
    $list = new CAdminList('intecTableFilter', []);

    $filterList = Filter::getFilterListNames($parameters['iblock']);

    $filter = new CAdminFilter(
        'intecTableFilter', $filterList
    );
}


?>

<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php $panel->Show() ?>

<?php if (!empty($error)) { ?>
    <?php
        $error = array_unique($error);
        $error = implode('<br>', $error);
        CAdminMessage::ShowMessage($error)
    ?>
<?php } ?>
    <form method="POST" Action="<?echo $request->getUrl() ?>" ENCTYPE="multipart/form-data" name="post_form">
        <?php if ($step == 2) { ?>
            <?php $filter->Begin() ?>
                <?= Filter::showFilter($template->id) ?>
            <?php $filter->Buttons([]) ?>
            <?php $filter->End() ?>
        <?php } ?>

        <?php $tabControl->Begin(); ?>
        <?php $tabControl->BeginNextTab() ?>
        <?= Html::hiddenInput('data[id]', $template->id) ?>
        <?php if ($step == 1) { ?>
            <tbody>
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.main.settings') ?></td>
            </tr>
            <?php if (!empty($template->id)) { ?>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.id').': ' ?></td>
                    <td width="60%" >
                        <?= $template->id ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!empty($template->createDate)) { ?>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.create.date').': ' ?></td>
                    <td width="60%" >
                        <?= Html::hiddenInput('data[createDate]', $template->createDate) ?>
                        <?= $template->createDate ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!empty($template->editDate)) { ?>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.edit.date').': ' ?></td>
                    <td width="60%" >
                        <?= Html::hiddenInput('data[editDate]', $template->editDate) ?>
                        <?= $template->editDate ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td width="40%" align="right"><b><?= Loc::getMessage('fields.name').': ' ?></b></td>
                <td width="60%" >
                    <?= Html::textInput('data[name]', $template->name) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.file.type').': ' ?></td>
                <td width="60%">
                    <select id="fileType" name="data[parameters][type]">
                        <?php foreach ($typeOptions as $value) { ?>
                            <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><b><?= Loc::getMessage('fields.export.file.path').': ' ?></b></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][path]', $filedData['path'], ['id' => 'filePath', 'style' => ['width' => '400px'], 'size' => 50] ) ?>
                    <span id="hint_file_path"></span>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.delimiter').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][delimiter]', $filedData['delimiter'], ['style' => ['width' => '20px'],]) ?>
                    <span id="hint_delimiter"></span>
                </td>

            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.section.level').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][level]', $filedData['level']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><b><?= Loc::getMessage('fields.export.iblock').': ' ?></b></td>
                <td width="60%">
                    <div id="iblock">
                        <select style="width: 200px" name='data[parameters][iblockType]' v-model="selectedTypes" v-on:change="handleSelectType(0)">
                            <option v-for="iblockType in iblockTypes" v-bind:value="iblockType.key">{{iblockType.value}}</option>
                        </select>
                        <select style="width: 200px" name='data[parameters][iblock]' v-model="selectedIblocks">
                            <option v-for="iblock in iblocks" v-bind:value="iblock.id">{{iblock.name}}</option>
                        </select>
                    </div>
                </td>
            </tr>
            </tbody>
            <tbody data-role="csv.block" style="display: <?= $filedData['type'] !== 'csv' ? 'none' : null ?>">
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.csv.settings') ?></td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.csv.delimiter').': ' ?></td>
                <td width="60%">
                    <select id="fileType" name="data[parameters][csvDelimiter]">
                        <?php foreach ($csvDelimiterOptions as $value) { ?>
                            <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.csv.enclosure').': ' ?></td>
                <td width="60%">
                    <select id="fileType" name="data[parameters][csvEnclosure]">
                        <?php foreach ($csvEnclosureOptions as $value) { ?>
                            <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            </tbody>
            <?php if ($isBase) { ?>
                <tbody>
                <tr class="heading">
                    <td colspan="2"><?= Loc::getMessage('heading.offers') ?></td>
                </tr>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.export.offers.format').': ' ?></td>
                    <td width="60%">
                        <select id="offersFormat" name="data[parameters][offersFormat]">
                            <?php foreach ($offersFormatOptions as $value) { ?>
                                <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr data-role="offers.price.type" style="display: <?= $filedData['offersFormat'] !== 'minimal' ? 'none' : null ?>">
                    <td width="40%" align="right"><?= Loc::getMessage('fields.export.offers.format').': ' ?></td>
                    <td width="60%">
                        <select name="data[parameters][offersPriceType]">
                            <?php foreach ($priceTypeOptions as $value) { ?>
                                <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr data-role="offers.price.type" style="display: <?= $filedData['offersFormat'] !== 'minimal' ? 'none' : null ?>">
                    <td width="40%" align="right"><?= Loc::getMessage('fields.export.use.non.price').': ' ?></td>
                    <td width="60%">
                        <?= Html::checkbox('data[parameters][useNonPrice]', $filedData['useNonPrice']) ?>
                    </td>
                </tr>
                </tbody>
            <?php } ?>
            <tbody>
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.additional.settings') ?></td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.wrap.multiple.elements').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][wrapMultipleElements]', $filedData['wrapMultipleElements']) ?>
                    <span id="hint_fields_wrap_multiple_elements"></span>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.unload.in.step').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][unloadInStep]', $filedData['unloadInStep']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.export.show.in.table').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][showInTable]', $filedData['showInTable']) ?>
                </td>
            </tr>
            </tbody>
        <?php } ?>
        <?php $tabControl->BeginNextTab() ?>
        <?php if ($step == 2) { ?>

            <tr data-role="preloader.block">
                <td>
                    <div class="preloader-layer" data-role="preloader" data-active="true">
                        <div class="preloader">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr data-role="menu.block" style="display: none;">
                <td>
                <span class="m-intec-importexport table-menu" @blur="menuOpen = false" tabindex="-1" id="menu">
                    <div class="table-menu-button" :class="{ open: menuOpen }" @click="handleOpen" title="<?= Loc::getMessage('hint.menu') ?>">
                        <span class="table-menu-button-icon"></span>
                    </div>
                    <div class="table-menu-list" :class="{ show: menuOpen }">
                        <?php foreach ($menuActionList as $actionItem) { ?>
                            <div class="table-menu-list-item" @click="<?= $actionItem['action'] ?>">
                                <?= $actionItem['name'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </span>
                </td>
            </tr>
            <tr data-role="table.block" style="display: none;">
                <td>
                    <div class="table-scroll" data-role="up.scroll"><div></div></div>
                    <div id="app" class="m-intec-importexport p-table" data-role="main.scroll">
                        <input type="hidden" name="data[tableParameters][param][general][sortBy]" :value="sortBy">
                        <input type="hidden" name="data[tableParameters][param][general][sortOrder]" :value="sortOrder">
                        <div class="table">
                            <div class="thead">
                                <div class="th-column" v-for="(column, index) in columns" :key="column.id">
                                    <div class="th-column-header">{{column.header}}</div>
                                    <div class="th-column-controls" data-role="main.block">
                                        <div class="th-column-icon sort" :class="{ asc: sortOrder == 'ASC' && column.sorted, desc: sortOrder == 'DESC' && column.sorted }" v-if="column.sortable" v-on:click="sortColumn(index)" :title="column.title.sort"></div>
                                        <div class="th-column-icon settings" v-on:click="columnSetting(index)" :title="column.title.settings">
                                            <input class="settings-value" :id="'settings_' + index" name="data[columnSettings][settings][]" type="hidden" :value="column.settings">
                                        </div>

                                        <div class="th-column-select-wrapper">
                                            <app-select :loadlist="column"></app-select>
                                        </div>

                                        <div class="th-column-icon delete" v-if="column.id > 1" v-on:click="delColumn(index)" :title="column.title.delete"></div>
                                        <div class="th-column-icon add" v-on:click="addColumn(index)" title="<?= Loc::getMessage('hint.column.add') ?>"></div>
                                    </div>
                                    <div class="th-column-name-field">
                                        <input class="column-name" name="data[columnSettings][newName][]" type="text" :value="column.newName" :placeholder="column.selectedName" @change="handlerChangeNewName(index)">
                                    </div>
                                </div>
                            </div>
                            <div class="tbody">
                                <div class="tr-column" v-for="(item, index) in changedItems" :key="index">
                                    <div class="td-column" v-for="(column, indexColumn) in item" :key="indexColumn">
                                        <div class="table-cell-wrapper">
                                            <div class="table-cell-content">
                                                {{ column }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
        <?php $tabControl->BeginNextTab() ?>
        <?php if ($step == 3) { ?>
            <tr>
                <td>
                    <div id="progress" class="m-intec-importexport p-exporting">
                        <div class="custom-progress-bar">
                            <div class="custom-progress-bar-inner" :style="progressStyle"></div>
                        </div>
                        <div class="custom-progress-bar-info">
                            <span class="custom-progress-bar-info-current"> {{ numberCurrent }} </span>
                            <span><?= Loc::getMessage('progress.bar.info.from') ?></span>
                            <span class="custom-progress-bar-info-from"> {{ numberAll }} </span>
                        </div>
                        <div class="custom-progress-bar-result">
                            <span class="custom-progress-bar-result-message" :style="resultStyle.result"> {{ resultMessage }}</span>
                            <a class="custom-progress-bar-result-link" :title="downloadLink" :style="resultStyle.link" :href="downloadLink" download> <?= Loc::getMessage('progress.bar.info.link') ?> </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>

        <?php $tabControl->Buttons(); ?>

        <?php if ($step == 2) { ?>
            <input type="submit" name="control[ACTION][BACK]" value="<?= Loc::getMessage('main.control.back') ?>">
            <input type="submit" name="control[ACTION][SAVE]" value="<?= Loc::getMessage('main.control.save') ?>" style="float: right;">
        <?php } if ($step < 3) { ?>
            <input type="hidden" name="control[STEP][CURRENT]" value="<?echo $step + 1; ?>">
            <input type="submit" name="control[ACTION][NEXT]" value="<?= Loc::getMessage($step == 2 ?  'main.control.export' : 'main.control.next') ?>" class="adm-btn-save">
        <?php } else { ?>
            <input type="hidden" name="control[STEP][CURRENT]" value="1">
            <input type="submit" name="control[ACTION][MAIN]" value="<?= Loc::getMessage('main.control.main') ?>" class="adm-btn-save">
        <?php } ?>

        <?php $tabControl->End(); ?>
    </form>

    <script type="text/javascript">
        (function ($, api) {

            $(document).ready(function(){
                <?php if ($step < 2) { ?>
                tabControl.SelectTab("main");
                tabControl.DisableTab("table");
                tabControl.DisableTab("processing");
                <?php } elseif ($step == 2) { ?>
                tabControl.SelectTab("table");
                tabControl.DisableTab("main");
                tabControl.DisableTab("processing");
                <?php } elseif ($step > 2) { ?>
                tabControl.SelectTab("processing");
                tabControl.DisableTab("main");
                tabControl.DisableTab("table");
                <?php } ?>

                BX.hint_replace(BX('hint_file_path'), '<?= Loc::getMessage("hint.file.path"); ?>');
                BX.hint_replace(BX('hint_fields_wrap_multiple_elements'), '<?= Loc::getMessage("hint.fields.wrap.multiple.elements"); ?>');
                BX.hint_replace(BX('hint_delimiter'), '<?= Loc::getMessage("hint.delimiter"); ?>');

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
                                    data = response.data;

                                    if (api.isFunction(callback))
                                        callback(data);
                                } else {
                                    if (response.message) {
                                        console.error(response.message)
                                    } else {
                                        console.error(response);
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


                <?php if ($step == 1) { ?>
                /*change file type*/
                $('#fileType').on('change', function () {
                    var text = $('#filePath').val();

                    if (text.trim().length <= 0)
                        return false;

                    var splited = text.split('.');
                    var lengt = splited.length;

                    if (lengt > 1) {
                        lengt = splited[lengt-1].length;
                        text = text.substring(0,text.length - lengt) + this.value;
                    } else {
                        text = text.trim() + '.' + this.value;
                    }

                    $('#filePath').val(text);

                    if (this.value === 'csv')
                        $('[data-role="csv.block"]').css('display', 'table-row-group');
                    else
                        $('[data-role="csv.block"]').css('display', 'none');
                });

                /*change offer format*/
                $('#offersFormat').on('change', function () {

                    if (this.value === 'minimal')
                        $('[data-role="offers.price.type"]').css('display', 'table-row');
                    else
                        $('[data-role="offers.price.type"]').css('display', 'none');
                });

                /*change iblock*/
                var testTypes = <?= JavaScript::toObject($arIBlocksTypesJs) ?>;

                testTypes.unshift({
                    'key': 0,
                    'value': "<?= Loc::getMessage('fields.export.select.iblock.type') ?>"
                });

                var iblock = new Vue({
                    el: '#iblock',
                    data: {
                        iblockTypes: testTypes,
                        selectedTypes: 0,
                        iblocks: [{
                            'id': 0,
                            'name': "<?= Loc::getMessage('fields.export.select.iblock') ?>"
                        }],
                        selectedIblocks: 0
                    },
                    methods: {
                        'handleSelectType': function (defaultSelect) {
                            var self = this;

                            request('get.iblocks', {'type': this.selectedTypes}, function (data) {
                                self.iblocks = data;
                                self.iblocks.unshift({
                                    'id': 0,
                                    'name': "<?= Loc::getMessage('fields.export.select.iblock') ?>"
                                });

                                self.selectedIblocks = defaultSelect;
                            });
                        }
                    },
                    mounted: function () {
                        var iblockType = '<?= !empty($filedData['iblockType']) ?>';

                        if (iblockType) {
                            this.selectedTypes = '<?= $filedData['iblockType'] ?>';
                            this.handleSelectType(<?= $filedData['iblock'] ?>);
                        }
                    }
                });
                <?php } ?>

                /*table list*/
                <?php if ($step == 2) { ?>
                var menuActionList = <?= JavaScript::toObject($menuActionList) ?>;

                var columns, items;

                request('loadTable', {}, function (data) {
                    items = data.elements;
                    columns = data.columns;

                    preloaderChange();
                    tableLoaded();
                });

                function preloaderChange () {
                    $('[data-role="preloader.block"]').css('display', 'none');
                    $('[data-role="table.block"]').removeAttr('style');
                    $('[data-role="menu.block"]').removeAttr('style');
                }

                function tableLoaded () {
                    var menu = new Vue({
                        el: '#menu',
                        data: {
                            'menuOpen': false,
                            'menuActionList': menuActionList
                        },
                        methods: {
                            'handleOpen': function () {
                                var self = this;
                                this.menuOpen = !this.menuOpen;
                            },
                            'getSelected': function () {
                                var selected = [];

                                $.each(app.$children, function(index,value){
                                    selected.push(value.selected.id);
                                });

                                return selected;
                            },
                            'addColumns': function (groupItems) {
                                /* nenf fix this */

                                $.each(groupItems, function (index, value) {
                                    app.itemCount++;

                                    app.columns.splice(app.columns.length + 1, 0, {
                                        'id': app.itemCount,
                                        'selected': value['code'],
                                        'name': value['name'],
                                        'selectedName': value['name'],
                                        'sortable': false,
                                        'title': {
                                            'delete': '',
                                            'sort': ''
                                        }
                                    });
                                });

                                app.updateRowHeader();
                            },
                            'addBaseProperties': function () {
                                var selected = this.getSelected();
                                var groupItems = JHelpers.GetGroupItems('GROUP_BASE', list, selected);
                                this.addColumns(groupItems);
                            },
                            'addProperties': function () {
                                var selected = this.getSelected();
                                var groupItems = JHelpers.GetGroupItems('GROUP_PROPERTIES', list, selected);
                                this.addColumns(groupItems);
                            },
                            'addSeoProperties': function () {
                                var selected = this.getSelected();
                                var groupItems = JHelpers.GetGroupItems('GROUP_SEO', list, selected);
                                this.addColumns(groupItems);
                            },
                            'addCatalogProperties': function () {
                                var selected = this.getSelected();
                                var groupItems = JHelpers.GetGroupItems('GROUP_CATALOG', list, selected);
                                this.addColumns(groupItems);
                            }
                        },
                        mounted: function () {

                        }
                    });

                    var list = <?= JavaScript::toObject($properties) ?>;
                    var nothingFound = <?= JavaScript::toObject($tableNothingFound) ?>;
                    var countId = 0;
                    var firstLoad = true;

                    Vue.component('app-select', {
                        data: function () {
                            return {
                                id: 0,
                                list: list,
                                arSelected: {},
                                selected: {
                                    'name': '<?= Loc::getMessage('new.column.default.name') ?>',
                                    'code': ''
                                },
                                open: false,
                                selectedId: '',
                                searchText: '',
                                menuStyle: {
                                    width: '430px'
                                }
                            }
                        },
                        props: ['loadlist'],
                        methods: {
                            'handleSelect': function (listItem) {
                                var self = this;

                                this.selected = listItem;
                                this.selectedId = listItem.id;
                                this.open = false;

                                app.columns.forEach(function(item, i) {
                                    if (item.id === self.id) {
                                        item.selected = self.selected.code;
                                        item.sortable = self.selected.sortable;
                                        item.selectedName = self.selected.name;
                                    }
                                });
                            },
                            'handleSelectOnLoad': function (listItem) {
                                if (!!listItem.selected) {
                                    var rootBlock = $('.custom-select:eq(' + (listItem.id - 1) + ')');
                                    var block = $('[data-value="' + listItem.selected + '"]', rootBlock);

                                    setTimeout(function () {
                                        block.trigger('click');
                                    }, 0);
                                }
                            },
                            'handleOpen': function () {
                                var self = this;
                                this.open = !this.open;
                                var root = $(this.$el);
                                var selectedBlock = $('.option.selected', root);
                                var scroll = $('.scroll', root);

                                if (this.open) {
                                    setTimeout(function(){
                                        var position = selectedBlock.position();

                                        if (!!position && position.top !== 0){
                                            scroll.scrollTop(position.top + scroll.scrollTop());
                                        }
                                    },0);
                                }

                                app.$children.forEach(function (item, i) {
                                    if (item._uid !== self._uid) {
                                        item.searchText = '';
                                        item.open = false;
                                    }
                                });

                                var main = root.closest('[data-role="main.block"]');

                                var mainWidth = $(main).outerWidth();
                                var mainHeight = $(main).outerHeight();
                                var mainPadding = ($(main).outerWidth() - $(main).width()) / 2;
                                var sortWidth = $('.th-column-icon.sort', main).outerWidth(true);
                                var settingsWidth = $('.th-column-icon.settings', main).outerWidth(true);
                                var left = 0;

                                if (settingsWidth > 0)
                                    left = left + settingsWidth;
                                if (sortWidth > 0)
                                    left = left + sortWidth;
                                if (mainPadding > 0)
                                    left = left + mainPadding;

                                this.menuStyle.width = mainWidth + 'px';
                                this.menuStyle.left = -left + 'px';
                                this.menuStyle.marginTop = (mainHeight / 2) + 'px';
                            }
                        },
                        computed:{
                            filteredList: function(){
                                var search = this.searchText.toLowerCase();

                                var group = {
                                    'is': false,
                                    'empty': true,
                                    'name': '',
                                    'from': 0,
                                    'to': 0,
                                    'code': '',
                                    'list': []
                                };

                                var result = this.list.filter(function (elem) {

                                    if (search === '') {
                                        return true;
                                    }

                                    if (elem.code.toLowerCase().indexOf('group_') > -1) {
                                        group.to = parseInt(elem.groupItemsId.to);
                                        group.from = parseInt(elem.groupItemsId.from);
                                        group.name = elem.name;
                                        group.code = elem.code;
                                        group.is = true;
                                        group.empty = true;

                                        return true;
                                    }

                                    if (group.name.toLowerCase().indexOf(search) > -1 || group.code.toLowerCase().indexOf(search) > -1) {
                                        if (group.from > 0 && elem.id >= group.from && elem.id <= group.to) {
                                            return true;
                                        }
                                    }

                                    if (elem.name.toLowerCase().indexOf(search) > -1 || elem.code.toLowerCase().indexOf(search) > -1) {
                                        return true;
                                    }

                                    if (!group.is && !elem.code) {
                                        return true;
                                    }
                                });

                                var test = [];

                                for (var i = 0; i < result.length - 1; i++) {
                                    if (result[i].code.toLowerCase().indexOf('group_') > -1) {
                                        if (result[i + 1].code.toLowerCase().indexOf('group_') > -1) {
                                            test.unshift(i);
                                        }
                                    }
                                }

                                $.each(test, function(index, value) {
                                    result.splice(value, 1);
                                });

                                if (result[result.length - 1].code.toLowerCase().indexOf('group_') > -1) {
                                    result.splice(result.length - 1, 1);
                                }

                                if (result.length <= 1)
                                    result = nothingFound;

                                return result;
                            }
                        },
                        mounted: function () {
                            var self = this;
                            countId++;
                            self.id = countId;
                            self.handleSelectOnLoad(self.loadlist);

                            Vue.nextTick(function () {
                                self.selected = {
                                    'code': self.loadlist.selected,
                                    'name': self.loadlist.name,
                                    'sortable': self.loadlist.sortable,
                                    'id': self.loadlist.id
                                };
                            });
                        },
                        template:
                        '<div class="column-select-block">' +
                        '<input type="hidden" name="data[tableParameters][data][]" :value="selected.code">' +
                        '<div class="custom-select">' +
                        '<div class="selected-item" :class="{ open: open }"  data-role="root" @click="handleOpen"><span>{{ selected.name }}</span></div>' +
                        '<div class="options" :style="menuStyle" :class="{ show: open }" data-role="options">' +
                        '<div class="search">' +
                        '<input type="text" v-model="searchText" placeholder="<?= Loc::getMessage('column.search') ?>">' +
                        '<div class="search-icon"></div>' +
                        '</div>' +
                        '<div class="scroll">' +
                        '<div class="option" :class="{ disable: listItem.disable, selected: listItem.id === selectedId }"  v-for="listItem in filteredList" :data-value="listItem.code" @click="handleSelect(listItem)" >{{ listItem.name }}</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>'
                    });

                    var itemsArray = [];
                    var delimiter = '<?= $delimiter ?>';

                    for (var item in items) {
                        itemsArray.push(items[item]);
                    }

                    items = itemsArray;

                    var app = new Vue({
                        el: '#app',
                        data: {
                            changed: 0,
                            items: items,
                            columns: columns,
                            sortBy: '',
                            sortOrder: 'ASC',
                            itemCount: 0,
                            preloaderActive: 'true'
                        },
                        methods: {
                            'columnSetting': function (columnIndex) {

                                var title = '';
                                var templateId = <?= $template->id ?>;
                                var value = $('#settings_' + columnIndex).val();

                                if (!!this.columns[columnIndex].selectedName)
                                    title = this.columns[columnIndex].selectedName;
                                else
                                    title = this.columns[columnIndex].name;

                                title = '<?= Loc::getMessage('title.field.settings') ?>' + title + '"';

                                JHelpers.ShowColumnSettings({'fieldId': columnIndex, 'templateId': templateId, 'value': value}, title, 'export');
                            },
                            'addColumn': function (columnIndex) {
                                this.itemCount++;
                                this.columns.splice(columnIndex + 1, 0, {
                                    'id': this.itemCount,
                                    'selected': '',
                                    'name': '<?= Loc::getMessage('new.column.default.name') ?>',
                                    'sortable': false,
                                    'title': {
                                        'delete': '',
                                        'sort': ''
                                    }
                                });

                                this.updateRowHeader();
                            },
                            'delColumn': function (columnIndex) {
                                this.columns.splice(columnIndex, 1);
                                this.updateRowHeader();
                            },
                            'sortColumn': function (columnIndex) {

                                this.columns.forEach(function (item) {
                                    delete item['sorted'];
                                });

                                this.columns[columnIndex].sorted = true;

                                if (this.sortBy === this.columns[columnIndex].selected) {
                                    if (this.sortOrder === 'ASC') {
                                        this.sortOrder = 'DESC';
                                        this.changedItems.reverse();
                                    } else {
                                        this.sortOrder = 'ASC';
                                        JHelpers.SortBy(this.changedItems, columnIndex);
                                    }
                                } else {
                                    this.sortOrder = 'ASC';
                                    JHelpers.SortBy(this.changedItems, columnIndex);
                                }

                                this.sortBy = this.columns[columnIndex].selected;
                            },
                            'updateRowHeader': function () {
                                var self = this;

                                this.columns.forEach(function(item, i) {
                                    var letter = JHelpers.GetRowLetter(i + 1);

                                    self.columns[i].header = letter;
                                    self.columns[i].title.delete = '<?= Loc::getMessage('hint.column.delete') ?>' + letter + '"]';
                                    self.columns[i].title.sort = '<?= Loc::getMessage('hint.column.sort') ?>' + letter + '"]';
                                });

                                setTimeout(function (){
                                    JHelpers.SetWidthList();
                                }, 0);
                            },
                            'handlePreloader': function () {
                                if (this.preloaderActive === 'true')
                                    this.preloaderActive = 'false';
                                else
                                    this.preloaderActive = 'true';
                            },
                            'handlerChangeNewName': function (index) {
                                this.columns[index].newName = event.target.value;
                            },
                            'updateRows': function (data) {


                                items = data;

                                console.log(items);
                                this.changed++;
                            }
                        },
                        watch: {
                            /*columns: {
                                deep: true
                            }*/
                        },
                        computed:{
                            changedItems: function(){
                                var self = this;
                                var result = [];
                                var item;
                                var hook = this.changed;

                                $.each(items, function(itemIndex,itemValue) {
                                    item = [];

                                    $.each(self.columns, function(index,value) {

                                        if (!value.settings) {
                                            if (!itemValue[value.selected]) {
                                                item.push('');
                                            } else {
                                                item.push(itemValue[value.selected]);
                                            }
                                        } else {
                                            var settings = JSON.parse(value.settings);
                                            var res = JHelpers.applySettings(itemValue, value, settings, delimiter);

                                            item.push(res);
                                        }
                                    });

                                    result.push(item);
                                });

                                return result;
                            }
                        },
                        mounted: function () {
                            var self = this;

                            $.each(self.columns, function (index, column) {
                                if (!!column.settings) {
                                    $('#settings_' + index).parent().addClass('selected');
                                } else {
                                    $('#settings_' + index).parent().removeClass('selected');
                                }
                            });

                            this.itemCount = this.columns.length;
                        }
                    });


                    $(document).on('click', function (e) {
                        if ($(e.target).closest('.custom-select').length <= 0)
                            app.$children.forEach(function (item, i) {
                                item.searchText = '';
                                item.open = false;
                            });
                    });

                    JHelpers.BindResizeEvent();
                    JHelpers.SetWidthList();

                    var scrollBlocks = {
                        'up': $('[data-role="up.scroll"]'),
                        'main': $('[data-role="main.scroll"]')
                    };

                    function scrollMain () {
                        scrollBlocks.main.scrollLeft(scrollBlocks.up.scrollLeft());
                    }
                    function scrollUp () {
                        scrollBlocks.up.scrollLeft(scrollBlocks.main.scrollLeft());
                    }

                    scrollBlocks.up.on('mouseenter', function () {
                        scrollBlocks.up.on('scroll', scrollMain);
                        scrollBlocks.main.off('scroll', scrollUp);
                    });
                    scrollBlocks.main.on('mouseenter', function(){
                        scrollBlocks.main.on('scroll', scrollUp);
                        scrollBlocks.up.off('scroll', scrollMain);
                    });

                    window.page = {};
                    window.page.setSettings = function(id, value) {
                        if (!!value) {
                            $('#settings_' + id).parent().addClass('selected');
                        } else {
                            $('#settings_' + id).parent().removeClass('selected');
                        }

                        app.$set(app.columns[parseInt(id)], 'settings', value);
                    };

                    $('[name="set_filter"]').on('click', function () {
                        event.preventDefault();
                        var formData = $('[name="post_form"]').serializeArray();

                        request('updateListOnFilter', {'formData': formData}, function (data) {
                            var dataItems = data;
                            var itemsArray = [];

                            console.log(data);

                            for (var item in dataItems) {
                                itemsArray.push(dataItems[item]);
                            }

                            app.updateRows(itemsArray);
                        });

                        return false;
                    });

                    $('[name="del_filter"]').on('click', function () {
                        event.preventDefault();

                        request('updateListOnFilter', {'formData': []}, function (data) {

                            var dataItems = data;
                            var itemsArray = [];
                            var formRoot = $('#intecTableFilter_content');

                            $.each($('input[type="text"]', formRoot), function (index, item) {
                                item.value = '';
                            });

                            $.each($('select', formRoot), function (index, formItem) {
                                $('option', formItem).prop('selected', false);
                                $('option:first', formItem).prop('selected', true);
                            });

                            for (var item in dataItems) {
                                itemsArray.push(dataItems[item]);
                            }

                            app.updateRows(itemsArray);
                        });

                        return false;
                    });
                }
                <?php } ?>

                /*export*/
                <?php if ($step == 3) { ?>

                var id = <?= $template->id ?>;
                var elementsCount = <?= JavaScript::toObject(IBlockHelper::getElementsCount($parameters['iblock'], ['ID'=>'ASC'], Filter::getFilter($tableParameters['filter'], $parameters['iblock']))) ?>;

                var progress = new Vue({
                    el: '#progress',
                    data: {
                        progressStyle: {
                            width: '0'
                        },
                        resultStyle: {
                            link: {
                                display: 'none'
                            },
                            result: {
                                display: 'none',
                                color: 'inherit'
                            }
                        },
                        numberCurrent: 0,
                        numberAll: elementsCount,
                        resultMessage: '',
                        downloadLink: '',
                        loaded: ''
                    },
                    methods: {
                        'updateProgress': function () {
                            var self = this;

                            request('save.export', {'id': id, 'step': 1}, function (data) {
                                var saveElement = data.step * data.currentStep;

                                if (saveElement <= elementsCount)
                                    self.numberCurrent = saveElement;
                                else
                                    self.numberCurrent = elementsCount;

                                if (saveElement <= elementsCount)
                                    self.progressStyle.width = JHelpers.PercentCalc(elementsCount, saveElement, true);
                                else
                                    self.progressStyle.width = '100%';

                                if (data.status !== 'end') {
                                    setTimeout(function () {
                                        self.updateProgress();
                                    }, 0);
                                } else {
                                    self.resultMessage = data.resultMessage;

                                    if (data.error.is) {
                                        self.resultStyle.result.color = '#ff3335';
                                        self.resultStyle.result.display = 'block';
                                    } else {
                                        self.downloadLink = data.downloadLink;
                                        self.resultStyle.link.display = 'block';
                                        self.resultStyle.result.display = 'block';
                                        self.resultStyle.result.color = '#117e04';
                                    }
                                }
                            });
                        }
                    },
                    mounted: function () {
                        this.updateProgress();
                    }
                });

                <?php } ?>
            });
        })(jQuery, intec);
    </script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>