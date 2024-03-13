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
use intec\importexport\models\excel\import\Template;
use intec\importexport\models\excel\IBlockHelper;
use intec\importexport\models\excel\import\ImportHelper;
use intec\importexport\models\excel\TableHelper;


/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../../requirements.php');
include(Core::getAlias('@intec/importexport/module/admin/url.php'));
require_once(dirname(__FILE__).'/../../../../classes/models/excel/import/Import.php');

$arJsConfig = array(
    'script_export' => array(
        'js' => '/bitrix/js/intec.importexport/script_export.js'
    )
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

Core::$app->web->js->loadExtensions(['vue', 'jquery', 'jquery_extensions', 'intec_core']);
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/import/templates/edit.css');
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/column_settings.css');
CJSCore::Init(array('script_export', 'translit'));


$request = Core::$app->request;
$action = $request->get('action');
$error = null;

$template = $request->get('template');
$parameters = [];

$new = false;

/*prepare data*/
if (!empty($template)) {
    $template = Template::findOne($template);

    if (empty($template))
        LocalRedirect($arUrlTemplates['excel.import.templates']);

    if (!$template->getIsNewRecord()) {
        $APPLICATION->SetTitle(Loc::getMessage('title.edit'));

        try {
            $parameters = Json::decode($template->getAttribute('params'));
        } catch (InvalidParamException $exception) {
            $parameters = [];
        }

        try {
            $tableParameters = Json::decode($template->getAttribute('tableParams'));
            if (!empty($tableParameters)) {
                if (!Type::isArray($tableParameters['data'][0])) {
                    foreach ($tableParameters['data'] as &$tableParameter) {
                        $tableParameter = [$tableParameter];
                    }
                }
            }
        } catch (InvalidParamException $exception) {
            $tableParameters = [];
        }

        try {
            $columnSettings = Json::decode($template->getAttribute('columnSettings'), true, true);
        } catch (InvalidParamException $exception) {
            $columnSettings = [];
        }

        try {
            $rowSettings = Json::decode($template->getAttribute('rowSettings'), true, true);
        } catch (InvalidParamException $exception) {
            $rowSettings = [];
        }
    }
} else {
    $template = new Template();
    $template->loadDefaultValues();
    $new = true;
}

/*data for vue*/
if (!Loader::includeModule('iblock'))
    return;

$isBase = Loader::includeModule('sale');

$arIBlocksTypes = IBlockHelper::getIBlockTypes();

$arIBlocksTypesJs = [];

foreach ($arIBlocksTypes as $key => $value) {
    $arIBlocksTypesJs[] = [
        'key' => $key,
        'value' => $value
    ];
}

/*request for vue*/
$ajaxImport = new Import();
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

        } elseif ($action === 'check.offers') {
            $response['status'] = 'success';
            $iBlockId = $request->post('iBlockId');
            $hasOffers = false;

            if (!empty($iBlockId) && $isBase) {
                $hasOffers = CCatalogSKU::GetInfoByProductIBlock($iBlockId);
                $hasOffers = !empty($hasOffers);
            }

            $response['data'] = ['hasOffers' => $hasOffers];
        } elseif ($action === 'get.identification.elements') {
            $response['status'] = 'success';
            $iBlockId = $request->post('IBlock');

            $mainProperties = [
                'ID',
                'NAME',
                'CODE',
                'XML_ID',
                'ACTIVE_FROM',
                'ACTIVE_TO',
                'TAGS'
            ];

            $result = IBlockHelper::getMainProperties(false, $mainProperties);
            $properties = IBlockHelper::getBaseProperties($iBlockId);

            foreach ($properties as &$property) {
                $property['name'] = Loc::getMessage('prefix.property') . ' "' . $property['name'] . '"';
            }

            $result = array_merge($result, $properties);
            unset($properties);

            /*$defaultOptionsOffers = IBlockHelper::getOffersMainProperties(false, $mainProperties);
            $properties = IBlockHelper::getOffersBaseProperties($filedData['iblock']);*/


            $resultOffers = IBlockHelper::getOffersMainProperties(false, $mainProperties);
            $properties = IBlockHelper::getOffersBaseProperties($iBlockId);

            foreach ($properties as &$property) {
                $property['name'] = Loc::getMessage('prefix.property') . ' "' . $property['name'] . '"';
            }

            $resultOffers = array_merge($result, $properties);

            $result = [
                'base' => $result,
                'offers' => $resultOffers
            ];

            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'add.rows') {
            $response['status'] = 'success';

            $id = $request->post('id');
            $step = $request->post('step');

            $result = $ajaxImport->getImportFileData($id, $step, false, false, true);

            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'import') {
            $response['status'] = 'success';

            $id = $request->post('id');
            $step = $request->post('step');
            $prevStatistic = $request->post('prevStatistic');

            $ajaxImport->importByIdOnStep($template->id, $step, $prevStatistic);
            $result['statistic'] = $ajaxImport->getStatistic();

            if (!empty($prevStatistic)) {
                foreach ($prevStatistic as $key => $value) {
                    if ($key === 'elementIds') {
                        $result['statistic'][$key] = array_merge($value, $result['statistic'][$key]);
                    } else {
                        $result['statistic'][$key] = Type::toInteger($result['statistic'][$key]) + Type::toInteger($value);
                    }
                }
            }

            $result['step'] = $step;
            $errors = $ajaxImport->getErrors();
            $result['isError'] = $errors['is'];
            $result['isCriticalError'] = $errors['critical'];
            $errorsList = [];

            if ($errors['is']) {
                foreach ($errors['errors'] as $rowNumber => $error) {
                    $rowNumberText = Loc::getMessage('error.row.number') . $rowNumber . ': ';

                    if (!empty($error['message']))
                        $errorsList[] = ['rowNumber' => $rowNumber, 'message' => $rowNumberText . $error['message']];
                    elseif (!empty($error['exception']))
                        $errorsList[] = ['rowNumber' => $rowNumber, 'message' => $rowNumberText . $error['exception']];
                    elseif (!empty($error['systemMessage']))
                        $errorsList[] = ['rowNumber' => $rowNumber, 'message' => $rowNumberText . $error['systemMessage']];
                    else
                        $errorsList[] = ['rowNumber' => $rowNumber, 'message' => Loc::getMessage('error.unknown')];
                }
            }

            $result['errors'] = $errorsList;


            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'non.file.element') {
            $response['status'] = 'success';

            $iblockId = $parameters['iblock'];
            $nonFile = [
                'zeroPrice' => $parameters['zeroPriceNoneFile'],
                'zeroQuantity' => $parameters['zeroQuantityNoneFile'],
                'deactivate' => $parameters['deactivateNoneFile'],
                'delete' => $parameters['deleteNoneFile']
            ];

            $elementIds = $request->post('elementIds');

            $elementIds = IBlockHelper::getExceptionIds($iblockId, $elementIds);
            $result = ImportHelper::actionOnNonFileElements($iblockId, $elementIds, $nonFile['deactivate'], $nonFile['zeroQuantity'], $nonFile['zeroPrice'], $nonFile['delete']);

            if ($parameters['sectionDeactivateEmpty'])
                ImportHelper::actionOnSection($parameters['iblock'], [], $parameters['sectionActivateNoneEmpty'], $parameters['sectionDeactivateEmpty'], $parameters['sectionDeleteEmpty'], $parameters['sectionDeactivateEmptyActive'], true);
            
            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'get.count.by.step') {
            /*slow version*/
            $response['status'] = 'success';

            $step = $request->post('step');
            $count = $request->post('count');

            $result = $ajaxImport->getImportCountByStep($template->id, $step, $count);

            $result = [
                'loaded' => $result['end'],
                'count' => $result['count']
            ];

            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'get.count') {
            $response['status'] = 'success';

            $result = $ajaxImport->getImportCount($template->id);

            $result = [
                'loaded' => $result['end'],
                'count' => $result['count']
            ];

            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'getFileData') {
            $response['status'] = 'success';

            $id = $request->post('id');
            $step = $request->post('step');

            $result = $ajaxImport->getImportFileData($id, $step);

            $response['data'] = $result;

            unset($properties);
        } elseif ($action === 'refreshProperties') {
            $response['status'] = 'success';

            $data = $request->post('data');
            $propertyPrefix = 'PROPERTY_';

            try {
                $data = Json::decode($data);
            } catch (InvalidParamException $exception) {
                $data = [];
            }

            if (StringHelper::startsWith($data['code'], 'EMPTY_PROPERTY'))
                $propertyPrefix = '';

            $properties = IBlockHelper::getAllProperties($parameters['iblock'], true, $parameters['level']);
            array_unshift($properties, ['code' => '', 'name' => Loc::getMessage('new.column.default.name')]);


            $selected = [
                'id' => '',
                'code' => '',
                'name' => ''
            ];

            foreach ($properties as $property) {
                if ($property['code'] === $propertyPrefix . $data['code']) {
                    $selected = [
                        'id' => $property['id'],
                        'code' => $property['code'],
                        'name' => $property['name']
                    ];
                }
            }

            $response['data'] = [
                'properties' => $properties,
                'selected' => $selected,
                'columnId' => $data['columnId']
            ];

            unset($properties, $list, $counter, $prevValues, $newList);
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

    //$data['parameters']['file'] = CFile::MakeFileArray($data['parameters']['file']);

    if (!empty($_FILES) && $_FILES['data']['error']['parameters']['file'] === 0) {
        $folder = '';
        $useTemplateFolder = false;

        if ($useTemplateFolder) {
            $params = [
                'max_len' => 100,
                'change_case' => 'L',
                'replace_space' => '_',
                'replace_other' => '_',
                'delete_repeat_replace' => true,
                'safe_chars' => ''
            ];

            $folder = CUtil::translit($template->name, 'ru', $params);
        }

        $fileId = ImportHelper::saveUploadFile($_FILES['data'], $parameters['fileId'], $folder);

        if (!!$fileId)
            $data['parameters']['fileId'] = $fileId;
        else
            $error[] = Loc::getMessage('error.type');

        unset($folder);
    } elseif (!empty($data['parameters']['file'])) {
        if (!ImportHelper::typeCheckOnName($data['parameters']['file'])) {
            $error[] = Loc::getMessage('error.type');
            unset($data['parameters']['file']);
        }
    } elseif (!empty($parameters['fileId'])) {
        $data['parameters']['fileId'] = $parameters['fileId'];
    } elseif (!empty($parameters['file'])) {
        $data['parameters']['file'] = $parameters['file'];
    } else {
        $error[] = Loc::getMessage('error.non.file');
    }

    if ($post['tabControl_active_tab'] == 'table' && !empty($data['tableParameters'])) {

        foreach ($data['tableParameters']['data'] as &$item) {
            if ($item === 'false')
                $item = '';
        }
        unset($item);

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

    if ($post['tabControl_active_tab'] == 'table' && !empty($data['rowSettings'])) {

        /*if ($data['rowSettings']['count'] < $rowSettings['count'])
            $data['rowSettings']['count'] = $rowSettings['count'];*/

        if (!empty($data['rowSettings']['all']))
            $data['rowSettings']['all'] = 'true';
        else
            $data['rowSettings']['all'] = 'false';

        $jsonRowSettings = Json::encode($data['rowSettings'], 320, true);

        if ($jsonParameters !== $template->rowSettings) {
            $template->updateAttributes(['rowSettings' => $jsonRowSettings]);
            $edited = true;
        }

        if ($data['rowSettings'] !== $rowSettings)
            $rowSettings = $data['rowSettings'];
    }

    if (empty($parameters['file']) && !empty(CFile::MakeFileArray($parameters['file'])) && $post['tabControl_active_tab'] == 'main') {
        if (empty($_FILES) && $post['tabControl_active_tab'] == 'main' ||
            empty($parameters['fileId']) && $_FILES['data']['error']['parameters']['file'] == 4 && $post['tabControl_active_tab'] == 'main' ) {
            $error[] = Loc::getMessage('error.select.file');
        }
    }


    if ($data['parameters']['iblock'] == 0 && $post['tabControl_active_tab'] == 'main')
        $error[] = Loc::getMessage('error.iblock');

    if (empty($data['parameters']['identificationElements']) && $post['tabControl_active_tab'] == 'main')
        $error[] = Loc::getMessage('error.identification.elements');

    $hasOffers = false;

    if (!empty($data['parameters']['iblock']) && $isBase) {
        $hasOffers = CCatalogSKU::GetInfoByProductIBlock($data['parameters']['iblock']);
        $hasOffers = !empty($hasOffers);
    }

    if ($hasOffers && empty($data['parameters']['identificationOffers']) && $post['tabControl_active_tab'] == 'main') {
        $error[] = Loc::getMessage('error.identification.offers');
    }

    if ($post['tabControl_active_tab'] == 'main') {
        if (empty(Type::toInteger($data['parameters']['step2ShowCount'])) || Type::toInteger($data['parameters']['step2ShowCount']) < 1)
            $data['parameters']['step2ShowCount'] = 20;

        if (empty(Type::toInteger($data['parameters']['importInStep'])) || Type::toInteger($data['parameters']['importInStep']) < 1)
            $data['parameters']['importInStep'] = 20;

        $level = Type::toInteger($data['parameters']['level']);

        if (empty($level) && $level < 0)
            $data['parameters']['level'] = 5;
        elseif ($level < 0)
            $data['parameters']['level'] = 5;
        else
            $data['parameters']['level'] = $level;

        if (empty($data['parameters']['delimiter']))
            $data['parameters']['delimiter'] = ';';
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

    if (isset($data['parameters']) && $post['tabControl_active_tab'] === 'main') {
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
                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['excel.import.templates.create'], [
                    'template' => $template->id,
                    'step' => 2
                ]));
            } else {
                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['excel.import.templates.create'], [
                    'template' => $template->id,
                    'step' => 1
                ]));
            }
        } else {
            $error[] = $template->getFirstErrors();
            //$error = ArrayHelper::getFirstValue($error);
        }
    }
}

if (!empty($parameters) && $step == 1) {
    if (empty($parameters['file']) && !empty(CFile::MakeFileArray($parameters['file']))) {
        if (empty($_FILES) ||
            empty($parameters['fileId']) && $_FILES['data']['error']['parameters']['file'] == 4) {
            $error[] = Loc::getMessage('error.select.file');
        }
    }

    if (empty($parameters['iblock']) || $parameters['iblock'] == 0)
        $error[] = Loc::getMessage('error.iblock');

    /*if ($data['parameters']['iblock'] == 0 && $post['tabControl_active_tab'] == 'main')
        $error[] = Loc::getMessage('error.iblock');*/

    if (empty($parameters['identificationElements']))
        $error[] = Loc::getMessage('error.identification.elements');
}

if (!empty($error)) {
    if(!empty($controlAction['BACK']))
        $step = $step - 2;

    if(!empty($controlAction['NEXT']))
        $step = $step - 1;
}



/*field data*/
$filedData = $parameters;

if (empty($template->id)) {
    $filedData['level'] = 5;
    $filedData['delimiter'] = ';';
    $filedData['activate'] = true;
    $filedData['step2ShowCount'] = 20;
    $filedData['importInStep'] = 20;
    $filedData['sectionActivateNoneEmpty'] = true;
}

$filedFilePath = null;

if (!empty($filedData['fileId']))
    $filedFilePath = $filedData['fileId'];

if (!empty($filedData['file']))
    $filedFilePath = $filedData['file'];


$mainProperties = [
    'ID',
    'NAME',
    'CODE',
    'XML_ID',
    'ACTIVE_FROM',
    'ACTIVE_TO',
    'TAGS'
];

$defaultOptions = IBlockHelper::getMainProperties(false, $mainProperties);

if (!empty($filedData['iblock'])) {
    $properties = IBlockHelper::getBaseProperties($filedData['iblock']);

    foreach ($properties as &$property) {
        $property['name'] = Loc::getMessage('prefix.property') . ' "' . $property['name'] . '"';
    }

    $defaultOptions = array_merge($defaultOptions, $properties);
    unset($properties);
}

if (empty($filedData['identificationElements']))
    $filedData['identificationElements'] = [];

$defaultOptionsOffers = [];

if ($isBase) {
    $defaultOptionsOffers = IBlockHelper::getOffersMainProperties(false, $mainProperties);

    if (!empty($filedData['iblock'])) {
        $properties = IBlockHelper::getOffersBaseProperties($filedData['iblock']);

        foreach ($properties as &$property) {
            $property['name'] = Loc::getMessage('prefix.property') . ' "' . $property['name'] . '"';
        }

        $defaultOptionsOffers = array_merge($defaultOptionsOffers, $properties);
        unset($properties);
    }

    if (empty($filedData['identificationOffers']))
        $filedData['identificationOffers'] = [];
}




/*table file data*/

if ($step == 1) {
    if ($isBase) {
        $currencyOptions = Arrays::fromDBResult(CCurrency::GetList())->asArray(function ($index, $currency) use ($parameters) {
            return [
                'value' => [
                    'value' => $currency['CURRENCY'],
                    'label' => '[' . $currency['CURRENCY'] . '] ' . $currency['FULL_NAME'],
                    'selected' => $parameters['defaultCurrency'] === $currency['CURRENCY']
                ]
            ];
        });
    }
}

if ($step == 2) {

    try {
        $parameters = Json::decode($template->getAttribute('params'));
    } catch (InvalidParamException $exception) {

    }

    $import = new Import();
    $tableFileData = $import->getImportFileData($template->id, 0, true);
    $tableFirstRow = ArrayHelper::getFirstValue($tableFileData['data']);

    $properties = IBlockHelper::getAllProperties($parameters['iblock'], true, $parameters['level']);

    $tableNothingFound = [
        [
            'id' => 0,
            'name' => Loc::getMessage('column.nothing.found'),
            'code' => 'NOTHING',
            'disable' => true
        ]
    ];

    foreach ($tableFirstRow as $key => &$row) {
        $settings = '';

        if (!empty($columnSettings['settings'][$key]))
            $settings = $columnSettings['settings'][$key];

        $row = [
            'id' => $key,
            'header' => TableHelper::getLetter($key + 1),
            'selected' => '',
            'name' => $row,
            'settings' => $settings
        ];
    }

    if (!empty($tableParameters['data'])) {
        $tableData = [];
        $propertiesCodes = [];
        $tableCount = count($tableFirstRow);
        $count = 0;

        foreach ($tableParameters['data'] as $key => $param) {
            if (Type::isString($param) && !empty($param)) {
                if (StringHelper::startsWith($param, 'PROPERTY_')) {
                    $propertiesCodes[] = $param;
                }
            }
        }

        $propertiesNames = IBlockHelper::getPropertiesNames($parameters['iblock'], $tableParameters['data'], $parameters['level']);

        foreach ($tableParameters['data'] as $groupKey => $groupParam) {
            foreach ($groupParam as $key => $param) {
                $settings = '';

                if (!empty($columnSettings['settings'][$groupKey]))
                    $settings = $columnSettings['settings'][$groupKey];

                if (StringHelper::startsWith($param, 'PROPERTY_')) {
                    $tableData[$key][$groupKey] = [
                        'id' => $key,
                        'selected' => $param,
                        'name' => $propertiesNames[$param],
                        'settings' => $settings,
                        'header' => TableHelper::getLetter($groupKey + 1),
                        'sortable' => IBlockHelper::getSortable($param),
                        'title' => [
                            'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                            'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                        ]
                    ];
                } elseif (StringHelper::startsWith($param, 'CATALOG_PROPERTY_') ||
                    StringHelper::startsWith($param, 'SEO_PROPERTY_') ||
                    StringHelper::startsWith($param, 'SECTION_PROPERTY_')) {
                    $tableData[$key][$groupKey] = [
                        'id' => $key,
                        'selected' => $param,
                        'name' => $propertiesNames[$param],
                        'settings' => $settings,
                        'header' => TableHelper::getLetter($groupKey + 1),
                        'sortable' => false,
                        'title' => [
                            'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                            'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                        ]
                    ];
                } else {
                    $tableData[$key][$groupKey] = [
                        'id' => $key,
                        'selected' => $param,
                        'name' => IBlockHelper::getMainName($param),
                        'settings' => $settings,
                        'header' => TableHelper::getLetter($groupKey + 1),
                        'sortable' => IBlockHelper::getSortable($param),
                        'title' => [
                            'delete' => Loc::getMessage('hint.column.delete') . TableHelper::getLetter($key + 1) . '"]',
                            'sort' => Loc::getMessage('hint.column.sort') . TableHelper::getLetter($key + 1) . '"]'
                        ]
                    ];
                }
                $idCounter++;
            }
            $count++;

            if ($count >= $tableCount) //come up with another option later
                break;
        }

        if ($tableCount > $count) {
            if (!empty($tableCount) && $tableCount > 0 && !empty($count) && $count > 0) {
                foreach ($tableData as &$tableDataGroup) {
                    for ($i = $count; $i < $tableCount; $i++) {
                        $tableDataGroup[] = [
                            'id' => $i + 1,
                            'selected' => false,
                            'name' => Loc::getMessage('new.column.default.name'),
                            'header' => TableHelper::getLetter($i + 1),
                            'sortable' => false
                        ];
                    }
                }
            }
        }

        $idCounter = 1;

        foreach ($tableData as &$tableDataGroup) {
            foreach ($tableDataGroup as &$tableDataGroupItem) {
                $tableDataGroupItem['id'] = $idCounter;
                $idCounter++;
            }
        }

        unset($idCounter);

        foreach ($tableData as &$tableDatum) {
            foreach ($tableDatum as &$data) {
                if (empty($data['selected'])) {
                    $data['name'] = Loc::getMessage('new.column.default.name');
                }
            }
        }

        unset($propertiesCodes, $propertiesNames, $newName, $tableCount, $count);
    } else {
        $tableGroupData = [];
        $tableCount = count($tableFirstRow);

        for ($i = 0; $i < $tableCount; $i++) {
            $tableGroupData[] = [
                'id' => $i + 1,
                'selected' => false,
                'name' => Loc::getMessage('new.column.default.name'),
                'header' => TableHelper::getLetter($i + 1),

                'sortable' => false
            ];
        }

        $tableData = [$tableGroupData];

        unset($tableCount, $tableGroupData);
    }

    $menuActionList = [
        'autoColumnsByFirstRow' => [
            'name' => Loc::getMessage('fields.table.menu.auto.columns'),
            'title' => Loc::getMessage('hint.menu.auto.columns'),
            'action' => 'autoColumnsByFirstRow'
        ],
        'unselectedColumns' => [
            'name' => Loc::getMessage('fields.table.menu.delete.columns'),
            'action' => 'unselectedColumns'
        ]
    ];
}

if ($step == 3) {
    unset($_SESSION['INTEC_IMPORT']);
}

if (empty($template->name)) {

    $allTemplates = Template::find();
    $allTemplates = $allTemplates->all();
    $lastTemplate = $allTemplates[count($allTemplates) - 1];

    if (!empty($lastTemplate) && count($allTemplates) > 0)
        $lastTemplateId = $lastTemplate->getAttribute('id');

    $lastTemplateId = $lastTemplateId + 1;
    $name = Loc::getMessage('fields.name.default') . $lastTemplateId;
    $template->name = $name;

    unset($templateForName, $allTemplates, $lastTemplate, $lastTemplateId, $name);
}

$rowSettingsEmpty = empty($rowSettings);

if ($rowSettingsEmpty) {
    $rowSettings = [
        'count' => $parameters['step2ShowCount'],
        'selected' => [],
        'all' => 'true'
    ];
}

if (!empty($rowSettings['all']))
    $massRowSelect = $rowSettings['all'] == 'true';
else
    $massRowSelect = true;


for ($i = 0; $i < $rowSettings['count']; $i++) {
    if ($rowSettingsEmpty)
        $rowSelect[$i] = $i > 0;
    else
        $rowSelect[$i] = !empty($rowSettings['selected'][$i]);
}

unset($rowSettingsEmpty);

/*import (statistic) data*/
if ($step == 3) {
    $hasNonFileAction = $parameters['zeroPriceNoneFile'] || $parameters['zeroQuantityNoneFile'] ||
        $parameters['deactivateNoneFile'] || $parameters['deleteNoneFile'];

    $statistic = [
        'added' => [
            'message' => Loc::getMessage('statistic.added'),
            'value' => 0
        ],
        'updated' => [
            'message' => Loc::getMessage('statistic.updated'),
            'value' => 0
        ],
        'offersAdded' => [
            'message' => Loc::getMessage('statistic.added.offers'),
            'value' => 0
        ],
        'offersUpdated' => [
            'message' => Loc::getMessage('statistic.updated.offers'),
            'value' => 0
        ],
        'sectionAdded' => [
            'message' => Loc::getMessage('statistic.added.sections'),
            'value' => 0
        ],
        'deleted' => [
            'message' => Loc::getMessage('statistic.deleted'),
            'value' => 0
        ],
        'errors' => [
            'message' => Loc::getMessage('statistic.errors'),
            'value' => 0
        ],
        'totalRows' => [
            'message' => Loc::getMessage('statistic.total.rows'),
            'value' => 0
        ],
        /*'startTime' => [
            'message' => Loc::getMessage('statistic.start.time'),
            'value' => 0
        ],
        'leadTime' => [
            'message' => Loc::getMessage('statistic.lead.time'),
            'value' => 0
        ]*/
    ];
}


/*admin menu settings*/
$arTabs = [[
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

$tabControl = new CAdminTabControl("tabControl", $arTabs, false, true);

$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['excel.import.templates']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['excel.import.templates.add']
]];

$panel = new CAdminContextMenu($panel);


/*field settings*/
$csvDelimiterOptions = [
    [
        'value' => ';',
        'label' => Loc::getMessage('option.import.csv.delimiter.semicolon'),
        'selected' => $parameters['csvDelimiter'] === ';' || empty($parameters['csvDelimiter'])
    ], [
        'value' => ',',
        'label' => Loc::getMessage('option.import.csv.delimiter.comma'),
        'selected' => $parameters['csvDelimiter'] === ','
    ], [
        'value' => '\t',
        'label' => Loc::getMessage('option.import.csv.delimiter.tab'),
        'selected' => $parameters['csvDelimiter'] === '\t'
    ]
];
$csvEnclosureOptions = [
    [
        'value' => 'dquote',
        'label' => Loc::getMessage('option.import.csv.enclosure.double.quote'),
        'selected' => $parameters['csvEnclosure'] === 'dquote' || empty($parameters['csvEnclosure'])
    ], [
        'value' => 'quote',
        'label' => Loc::getMessage('option.import.csv.enclosure.quote'),
        'selected' => $parameters['csvEnclosure'] === 'quote'
    ], [
        'value' => 'none',
        'label' => Loc::getMessage('option.import.csv.enclosure.none'),
        'selected' => $parameters['csvEnclosure'] === 'none'
    ]
];

$priceModeOptions = [
    [
        'value' => 'none',
        'label' => Loc::getMessage('fields.import.price.round.mod.none'),
        'selected' => $parameters['priceRoundMod'] === 'none' || empty($parameters['priceRoundMod'])
    ], [
        'value' => 'default',
        'label' => Loc::getMessage('fields.import.price.round.mod.default'),
        'selected' => $parameters['priceRoundMod'] === 'default'
    ], [
        'value' => 'up',
        'label' => Loc::getMessage('fields.import.price.round.mod.up'),
        'selected' => $parameters['priceRoundMod'] === 'up'
    ], [
        'value' => 'down',
        'label' => Loc::getMessage('fields.import.price.round.mod.down'),
        'selected' => $parameters['priceRoundMod'] === 'down'
    ]
];

/*debug*/
/*echo '<pre>';
$test = new Import();
$testRes = $test->importByIdOnStep($template->id);
print_r($test->getErrors());
print_r($test->getStatistic());
print_r($testRes);
echo '</pre>';*/

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

<?php if ($step == 1 || $step == 2) { ?>
    <?php if ($filedData['deleteMode']) { ?>
        <?php CAdminMessage::ShowMessage(Loc::getMessage('alert.delete.mode')); ?>
    <?php } ?>
<?php } ?>

<form class="m-intec-importexport p-form" method="POST" ENCTYPE="multipart/form-data" name="post_form">
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
                    <td width="60%">
                        <?= $template->id ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!empty($template->createDate)) { ?>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.create.date').': ' ?></td>
                    <td width="60%">
                        <?= Html::hiddenInput('data[createDate]', $template->createDate) ?>
                        <?= $template->createDate ?>
                    </td>
                </tr>
            <?php } ?>
            <?php if (!empty($template->editDate)) { ?>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.edit.date').': ' ?></td>
                    <td width="60%">
                        <?= Html::hiddenInput('data[editDate]', $template->editDate) ?>
                        <?= $template->editDate ?>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td width="40%" align="right"><b><?= Loc::getMessage('fields.name').': ' ?></b></td>
                <td width="60%">
                    <?= Html::textInput('data[name]', $template->name) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><b><?= Loc::getMessage('fields.import.file.load').': ' ?></b></td>
                <td width="60%">
                    <div class="adm-input-file-control adm-input-file-top-shift">
                        <?php Cmodule::IncludeModule('fileman'); ?>
                        <?= CFileInput::Show('data[parameters][file]', $filedFilePath, [
                            'IMAGE' => 'N',
                            'PATH' => 'Y',
                            'FILE_SIZE' => 'Y',
                            'DIMENSIONS' => 'N'
                        ], [
                            'not_update' => true,
                            'upload' => true,
                            'medialib' => false,
                            'file_dialog' => true,
                            'cloud' => true,
                            'del' => false,
                            'description' => false,
                        ]); ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><b><?= Loc::getMessage('fields.import.iblock').': ' ?></b></td>
                <td width="60%">
                    <div id="iblock">
                        <select style="width: 200px" name='data[parameters][iblockType]' v-model="selectedTypes" v-on:change="handleSelectType(0)">
                            <option v-for="iblockType in iblockTypes" v-bind:value="iblockType.key">{{iblockType.value}}</option>
                        </select>
                        <select style="width: 200px" name='data[parameters][iblock]' v-model="selectedIblock" v-on:change="handleSelectIBlockId(0)">
                            <option v-for="iblock in iblocks" v-bind:value="iblock.id">{{iblock.name}}</option>
                        </select>
                    </div>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.level').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][level]', $filedData['level']) ?>
                </td>
            </tr>
        </tbody>
        <tbody id="identification">
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.elements.settings') ?></td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.identification.elements').': ' ?></td>
                <td width="60%">
                    <select name='data[parameters][identificationElements][]' multiple v-model="selected">
                        <option v-for="option in optionList" v-bind:value="option.code">{{option.name}}</option>
                    </select>
                </td>
            </tr>
            <?php if ($isBase) { ?>
                <tr data-role="offer-field" data-hide="true">
                    <td width="40%" align="right"><?= Loc::getMessage('fields.identification.offers').': ' ?></td>
                    <td width="60%">
                        <select name='data[parameters][identificationOffers][]' multiple v-model="offersSelected">
                            <option v-for="option in offersOptionList" v-bind:value="option.code">{{option.name}}</option>
                        </select>
                    </td>
                </tr>
                <tr data-role="offer-field" data-hide="true">
                    <td width="40%" align="right"><?= Loc::getMessage('fields.dont.create.new.offers').': ' ?></td>
                    <td width="60%">
                        <?= Html::checkbox('data[parameters][dontCreateNewOffers]', $filedData['dontCreateNewOffers']) ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        <tbody>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.csv.delimiter').': ' ?></td>
                <td width="60%">
                    <select id="fileType" name="data[parameters][csvDelimiter]">
                        <?php foreach ($csvDelimiterOptions as $value) { ?>
                            <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.csv.enclosure').': ' ?></td>
                <td width="60%">
                    <select id="fileType" name="data[parameters][csvEnclosure]">
                        <?php foreach ($csvEnclosureOptions as $value) { ?>
                            <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.delimiter').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][delimiter]', $filedData['delimiter'], ['style' => ['width' => '20px'],]) ?>
                    <span id="hint_delimiter"></span>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.auto.generate.code').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][autoGenerateCode]', $filedData['autoGenerateCode']) ?>
                    <span id="hint_autoGenerateCode"></span>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.dont.create.new.elements').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][dontCreateNewElements]', $filedData['dontCreateNewElements']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.dont.update.exist.elements').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][dontUpdateExistElements]', $filedData['dontUpdateExistElements']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.delete.mode').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][deleteMode]', $filedData['deleteMode'], ['id' => 'deleteMode']) ?>
                </td>
            </tr>
            <?php if ($isBase) { ?>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.import.price.round.mod').': ' ?></td>
                    <td width="60%">
                        <select id="fileType" name="data[parameters][priceRoundMod]">
                            <?php foreach ($priceModeOptions as $value) { ?>
                                <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td width="40%" align="right"><?= Loc::getMessage('fields.import.price.currency').': ' ?></td>
                    <td width="60%">
                        <select id="fileType" name="data[parameters][defaultCurrency]">
                            <?php foreach ($currencyOptions as $value) { ?>
                                <option value="<?= $value['value'] ?>" <?= $value['selected'] ? 'selected' : null ?>><?= $value['label'] ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
        <tbody>
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.activity.settings') ?></td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.new.deactivate').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][newDeactivate]', $filedData['newDeactivate']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.zero.quantity.deactivate').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][zeroQuantityDeactivate]', $filedData['zeroQuantityDeactivate']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.zero.price.deactivate').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][zeroPriceDeactivate]', $filedData['zeroPriceDeactivate']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.activate').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][activate]', $filedData['activate']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.deactivate.none.file').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][deactivateNoneFile]', $filedData['deactivateNoneFile']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.zero.quantity.none.file').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][zeroQuantityNoneFile]', $filedData['zeroQuantityNoneFile']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.zero.price.none.file').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][zeroPriceNoneFile]', $filedData['zeroPriceNoneFile']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.delete.none.file').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][deleteNoneFile]', $filedData['deleteNoneFile']) ?>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.section.settings') ?></td>
            </tr>
            <!--<tr>
                <td width="40%" align="right"><?/*= Loc::getMessage('fields.import.section.identification').': ' */?></td>
                <td width="60%">
                    <?/*= Html::checkbox('data[parameters][sectionIdentification]', $filedData['sectionIdentification']) */?>
                </td>
            </tr>-->
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.dont.create.new').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][sectionDontCreateNew]', $filedData['sectionDontCreateNew']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.none.update').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][sectionNoneUpdate]', $filedData['sectionNoneUpdate']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.activate.none.empty').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][sectionActivateNoneEmpty]', $filedData['sectionActivateNoneEmpty']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.deactivate.empty').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][sectionDeactivateEmpty]', $filedData['sectionDeactivateEmpty']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.deactivate.empty.active').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][sectionDeactivateEmptyActive]', $filedData['sectionDeactivateEmptyActive']) ?>
                    <span id="hint_section_deactivate_empty_active"></span>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.section.delete.empty').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][sectionDeleteEmpty]', $filedData['sectionDeleteEmpty']) ?>
                </td>
            </tr>
        </tbody>
        <tbody>
            <tr class="heading">
                <td colspan="2"><?= Loc::getMessage('heading.additional.settings') ?></td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.import.in.step').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][importInStep]', $filedData['importInStep']) ?>
                    <span id="hint_import_in_step"></span>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.step.2.show.count').': ' ?></td>
                <td width="60%">
                    <?= Html::textInput('data[parameters][step2ShowCount]', $filedData['step2ShowCount']) ?>
                </td>
            </tr>
            <tr>
                <td width="40%" align="right"><?= Loc::getMessage('fields.step.2.elements.hide').': ' ?></td>
                <td width="60%">
                    <?= Html::checkbox('data[parameters][step2ElementsHide]', $filedData['step2ElementsHide']) ?>
                    <span id="hint_step_2_show_count"></span>
                </td>
            </tr>
        </tbody>

    <?php } ?>
<?php $tabControl->BeginNextTab() ?>
    <?php if ($step == 2) { ?>
        <tr data-role="menu.block">
            <td>
                <span class="m-intec-importexport table-menu" @blur="menuOpen = false" tabindex="-1" id="menu">
                    <div class="table-menu-button" :class="{ open: menuOpen }" @click="handleOpen" title="<?= Loc::getMessage('hint.menu') ?>">
                        <span class="table-menu-button-icon"></span>
                    </div>
                    <div class="table-menu-list" :class="{ show: menuOpen }">
                        <?php foreach ($menuActionList as $actionItem) { ?>
                            <div class="table-menu-list-item" title="<?= $actionItem['title'] ?>" @click="<?= $actionItem['action'] ?>">
                                <?= $actionItem['name'] ?>
                            </div>
                        <?php } ?>
                    </div>
                </span>
            </td>
        </tr>
        <tr>
            <td>
                <div class="table-scroll" data-role="up.scroll"><div></div></div>
                <div id="importTable" class="m-intec-importexport p-table" data-role="main.scroll">
                    <input type="hidden" name="data[rowSettings][count]" :value="rowCount">
                    <div class="table">
                        <div class="thead" v-for="(mainColumns, mainIndex) in columns">
                            <div class="th-column row-setting">
                                <input v-if="mainIndex <= 0" type="checkbox" @click="handleMassRowSelect()" v-model="massRowSelect" name="data[rowSettings][all]" value="1" id="row_setting_all" class="adm-designed-checkbox column-checkbox" >
                                <label v-if="mainIndex <= 0" for="row_setting_all" title="" class="adm-designed-checkbox-label column-checkbox"></label>
                            </div>
                            <div class="th-column" v-for="(column, index) in mainColumns" :key="column.id">
                                <div v-if="mainIndex <= 0" class="th-column-header">{{column.header}}</div>
                                <div class="th-column-controls" data-role="main.block">
                                    <div v-if="mainIndex <= 0" class="th-column-icon settings" v-on:click="columnSetting(index)">
                                        <input class="settings-value" :id="'settings_' + index" name="data[columnSettings][settings][]" type="hidden" :value="column.settings">
                                    </div>
                                    <div v-if="mainIndex > 0" class="th-column-icon space"></div>
                                    <div class="th-column-select-wrapper">
                                        <app-select :loadlist="column" :columnnumber="index"></app-select>
                                    </div>
                                    <div v-if="mainIndex + 1 == columns.length" class="th-column-icon add th-column-add-more-properties" v-on:click="addMoreProps"></div>
                                </div>
                            </div>
                        </div>
                        <div class="tbody">
                            <div class="tr-column" v-for="(item, index) in changedItems" :key="index">
                                <div class="td-column row-setting">
                                    <input type="checkbox" :name="'data[rowSettings][selected][' + index +']'" :value="1" :id="'row_setting_' + index" class="adm-designed-checkbox column-checkbox" v-model="rowSelect[index]">
                                    <label :for="'row_setting_' + index" title="" class="adm-designed-checkbox-label column-checkbox"></label>
                                </div>
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
                    <div class="btn-wrapper">
                        <div class="adm-btn adm-btn-add" @click="handleAddRows()">
                            <?= Loc::getMessage('main.control.load.more.rows') ?>
                        </div>
                    </div>
                    <div class="preloader-layer" data-role="preloader" v-bind:data-active="preloaderActive">
                        <div class="preloader">
                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>
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
                <div id="progress" class="m-intec-importexport p-importing">
                <div class="progress-side">
                        <div class="custom-progress-bar">
                            <div class="preloader-layer" data-role="preloader" v-bind:data-active="preloaderActive">
                                <div class="preloader">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                            <div class="custom-progress-bar-inner" :style="progressStyle"></div>
                        </div>
                        <div class="custom-progress-bar-result">
                            <span class="custom-progress-bar-result-message" :style="resultStyle.result" v-html="resultMessage "></span>
                            <a class="custom-progress-bar-result-link" :style="resultStyle.link" :href="downloadLink" download>
                                <?= Loc::getMessage('progress.bar.info.link') ?>
                            </a>
                        </div>
                        <div class="custom-progress-bar-statistic">
                            <div v-for="(statistic) in resultStatistic">{{statistic.message}} {{statistic.value}}</div>
                        </div>
                    </div>
                    <div class="log-side">
                        <div v-for="(error) in errors" class="progress-error"> {{error}} </div>
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
    <input type="submit" name="control[ACTION][NEXT]" value="<?= Loc::getMessage($step == 2 ?  'main.control.import' : 'main.control.next') ?>" class="adm-btn-save">
<?php } else { ?>
    <input type="hidden" name="control[STEP][CURRENT]" value="1">
    <input type="submit" name="control[ACTION][MAIN]" value="<?= Loc::getMessage('main.control.main') ?>" class="adm-btn-save">
<?php } ?>

<?php $tabControl->End(); ?>
</form>

<script type="text/javascript">
(function ($, api) {
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
    BX.hint_replace(BX('hint_delimiter'), '<?= Loc::getMessage("hint.delimiter"); ?>');
    BX.hint_replace(BX('hint_autoGenerateCode'), '<?= Loc::getMessage("hint.autoGenerateCode"); ?>');
    BX.hint_replace(BX('hint_import_in_step'), '<?= Loc::getMessage("hint.import.in.step"); ?>');
    BX.hint_replace(BX('hint_section_deactivate_empty_active'), '<?= Loc::getMessage("hint.section.deactivate.empty.active"); ?>');
    BX.hint_replace(BX('hint_step_2_show_count'), '<?= Loc::getMessage("hint.step.2.show.count"); ?>');

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
    /*change iblock*/
    var testTypes = <?= JavaScript::toObject($arIBlocksTypesJs) ?>;

    testTypes.unshift({
        'key': 0,
        'value': "<?= Loc::getMessage('fields.import.select.iblock.type') ?>"
    });

    var isBase = <?= JavaScript::toObject($isBase) ?>;

    var iblock = new Vue({
        el: '#iblock',
        data: {
            iblockTypes: testTypes,
            selectedTypes: 0,
            iblocks: [{
                'id': 0,
                'name': "<?= Loc::getMessage('fields.import.select.iblock') ?>"
            }],
            selectedIblock: 0,
            offersStyle: {
                display: 'none'
            }
        },
        methods: {
            'handleSelectType': function (defaultSelect) {
                var self = this;

                request('get.iblocks', {'type': this.selectedTypes}, function (data) {
                    self.iblocks = data;
                    self.iblocks.unshift({
                        'id': 0,
                        'name': "<?= Loc::getMessage('fields.import.select.iblock') ?>"
                    });

                    self.selectedIblock = defaultSelect;
                    self.checkOffers();
                });
            },
            'handleSelectIBlockId': function (defaultSelect) {
                identification.handleSelectIBlock(this.selectedIblock);
                this.checkOffers();
            },
            'checkOffers': function () {
                if (isBase) {
                    request('check.offers', {'iBlockId': this.selectedIblock}, function (data) {
                        var offersFields;

                        if (data.hasOffers) {
                            offersFields = $('[data-role="offer-field"][data-hide="true"]');

                            if (offersFields.length > 0) {
                                offersFields.each(function () {
                                    $(this).attr('data-hide', 'false');
                                });
                            }
                        } else {
                            offersFields = $('[data-role="offer-field"][data-hide="false"]');

                            if (offersFields.length > 0) {
                                offersFields.each(function () {
                                    $(this).attr('data-hide', 'true');
                                });
                            }
                        }
                    });
                }
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

    /**/
    var defaultOptions = <?= JavaScript::toObject($defaultOptions) ?>;
    var defaultSelect = <?= JavaScript::toObject($filedData['identificationElements']) ?>;
    var defaultOptionsOffers = <?= JavaScript::toObject($defaultOptionsOffers) ?>;
    var defaultSelectOffers = <?= JavaScript::toObject($filedData['identificationOffers']) ?>;

    var identification = new Vue({
        el: '#identification',
        data: {
            optionList: defaultOptions,
            selected: defaultSelect,
            offersOptionList: defaultOptionsOffers,
            offersSelected: defaultSelectOffers
        },
        methods: {
            'handleSelectIBlock': function (select) {
                var self = this;

                request('get.identification.elements', {'IBlock': select}, function (data) {
                    self.optionList = data.base;
                    self.offersOptionList = data.offers;
                });
            }
        },
        mounted: function () {

        }
    });

    var deleteMode = {
        'block': $('#deleteMode'),
        'text': '<?= Loc::getMessage('confirm.delete.mode') ?>'
    };

    deleteMode.block.on('click', function (e) {
        if ($(this).is(':checked')){
            var isAccept = confirm(deleteMode.text);

            if (!isAccept)
                e.preventDefault();
        }
    });

    <?php } ?>

    <?php if ($step == 2) { ?>
        var menuActionList = <?= JavaScript::toObject($menuActionList) ?>;
        var list = <?= JavaScript::toObject($properties) ?>;
        var nothingFound = <?= JavaScript::toObject($tableNothingFound) ?>;
        var countId = 0;

        list.unshift({
            'name': '<?= Loc::getMessage('new.column.default.name') ?>',
            'code': ''
        });

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
                'autoColumnsByFirstRow': function () {
                    var firstRow = app.tableData[0];

                    $.each(firstRow, function (index, item) {
                        var children = app.$children[index];

                        if (typeof item === 'number')
                            item = item.toString();

                        if (!children.selected.code) {
                            var search = item.toLowerCase();

                            var result = list.filter(function (elem) {
                                if (elem.code.toLowerCase().indexOf('group_') > -1)
                                    return false;

                                if(search === '')
                                    return false;
                                else if (elem.name.toLowerCase().indexOf(search) > -1)
                                    return true;
                                else
                                    return elem.code.toLowerCase().indexOf(search) > -1;
                            });

                            if (result.length > 0) {
                                app.$children[index].selected = result[0];
                                var mainBlock = app.$children[index].$el.closest('[data-role="main.block"]');
                                if (!app.$children[index].selected.code || app.$children[index].selected.code == 'false') {
                                    $(mainBlock).removeClass('selected');
                                } else {
                                    $(mainBlock).addClass('selected');
                                }
                            }
                        }
                    });

                    app.handleDuplicateSelected();
                },
                'unselectedColumns': function () {
                    $.each(app.$children, function (index, children) {
                        if (!!children.selected.code) {
                            children.selected = {
                                'id': 1,
                                'code': '',
                                'name': '<?= Loc::getMessage('new.column.default.name') ?>',
                                'sortable': false
                            };

                            var mainBlock = children.$el.closest('[data-role="main.block"]');
                            $(mainBlock).removeClass('selected');
                        }
                    });

                    app.handleDuplicateSelected();
                }
            },
            mounted: function () {

            }
        });


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
                        width: '430px',
                    },
                    formName: 'data[tableParameters][data][]'
                }
            },
            props: ['loadlist', 'columnnumber'],
            methods: {
                'handleSelect': function (listItem) {
                    var self = this;
                    var mainBlock = $(this.$el).closest('[data-role="main.block"]');

                    this.selected = listItem;
                    this.selectedId = listItem.id;
                    this.open = false;

                    if (!this.selected.code || this.selected.code == 'false') {
                        $(mainBlock).removeClass('selected');
                    } else {
                        $(mainBlock).addClass('selected');
                    }

                    app.tableData.forEach(function(item, i) {
                        if (item.id === self._uid) {
                            item.selected = self.selected.code;
                            item.sortable = self.selected.sortable;
                        }
                    });

                    app.handleDuplicateSelected();
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

                    if (this.open) {
                        setTimeout(function () {
                            $('.search input', this.$el).focus();
                        }, 1);
                    }
                },
                'handleCreateProperty': function () {
                    var title = '';
                    var templateId = <?= $template->id ?>;

                    title = '<?= Loc::getMessage('title.create.new.field') ?>';

                    JHelpers.ShowCreateProperty({'templateId': templateId, 'columnId': this.id}, title);
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

                self.formName = 'data[tableParameters][data][' + self._props.columnnumber + '][]';

                self.handleSelectOnLoad(self.loadlist);

                Vue.nextTick(function () {
                    var name = '<?= Loc::getMessage('new.column.default.name') ?>';

                    if (!!self.loadlist.name)
                        name = self.loadlist.name;

                    self.selected = {
                        'code': self.loadlist.selected,
                        'name': name,
                        'sortable': self.loadlist.sortable,
                        'id': self.loadlist.id
                    };
                });
            },
            template:
            '<div style="min-width: 200px;">' +
                '<input type="hidden" :name="formName" :value="selected.code">' +
                '<div class="custom-select">' +
                    '<div class="selected-item" :class="{ open: open }"  data-role="root" @click="handleOpen">{{ selected.name }}</div>' +
                    '<div class="options" :class="{ show: open }" data-role="options" :style="menuStyle">' +
                        '<div class="search">' +
                            '<input type="text" v-model="searchText">' +
                            '<div class="search-icon"></div>' +
                        '</div>' +
                        '<div class="scroll">' +
                            '<div class="option create-new"  @click="handleCreateProperty()" ><?= Loc::getMessage("create.property") ?></div>' +
                            '<div class="option" :class="{ disable: listItem.disable, selected: listItem.id === selectedId }"  v-for="listItem in filteredList" :data-value="listItem.code" @click="handleSelect(listItem)" >{{ listItem.name }}</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        });


        var tableData = <?= JavaScript::toObject($tableFileData['data']) ?>;
        var id = <?= $template->id ?>;
        var tableDataStep = 1;
        var hideElements = <?= JavaScript::toObject(empty($parameters['step2ElementsHide'])) ?>;
        var columns = <?= JavaScript::toObject($tableData) ?>;
        var massRowSelect = <?= JavaScript::toObject($massRowSelect) ?>;
        var rowSelect = <?= JavaScript::toObject($rowSelect) ?>;
        var emptyName = <?= JavaScript::toObject(Loc::getMessage('new.column.default.name')) ?>;

        var app = new Vue({
            el: '#importTable',
            data: {
                columns: columns,
                tableData: tableData,
                preloaderActive: 'false',
                massRowSelect: massRowSelect,
                rowSelect: rowSelect,
                rowCount: 0
            },
            methods: {
                'columnSetting': function (columnIndex) {

                    var title = JHelpers.GetRowLetter(columnIndex + 1);
                    var templateId = <?= $template->id ?>;
                    var value = $('#settings_' + columnIndex).val();

                    title = '<?= Loc::getMessage('title.column.settings') ?>' + title + '"';

                    JHelpers.ShowColumnSettings({'fieldId': columnIndex, 'templateId': templateId, 'value': value, 'columnCount': columns[0].length}, title, 'import');
                },
                'addMoreProps': function () {
                    var self = this;
                    var columnLength = self.columns[0].length;
                    var newColumn = [];
                    var countIds = $('[data-role="main.block"]').length;

                    for (var i = 1; i <= columnLength; i++) {
                        newColumn.push({
                            'id': countIds + i,
                            'name': emptyName
                        });
                    }

                    self.columns.push(newColumn);
                },
                'handleAddRows': function (firstRun = false) {
                    var self = this;
                    this.handlePreloader();

                    if (firstRun) {
                        if (hideElements) {
                            request('add.rows', {'id': id, 'step': 1}, function (data) {
                                if (!!data.data && data.data.length > 0)
                                    self.tableData = data.data;

                                self.rowCount = self.tableData.length;
                            });
                        } else {
                            self.rowCount = 1;
                        }
                        self.handlePreloader();
                    } else {
                        tableDataStep++;

                        request('add.rows', {'id': id, 'step': tableDataStep}, function (data) {
                            if (!!data.data && data.data.length > 0)
                                self.tableData = self.tableData.concat(data.data);

                            var testL = self.rowSelect.length;
                            self.rowSelect.length = self.tableData.length;

                            self.rowSelect.fill(self.massRowSelect, testL, self.rowSelect.length);

                            self.rowCount = self.tableData.length;
                            self.handlePreloader();
                        });
                    }
                },
                'handlePreloader': function () {
                    if (this.preloaderActive === 'true')
                        this.preloaderActive = 'false';
                    else
                        this.preloaderActive = 'true';
                },
                'handleMassRowSelect': function () {
                    var self = this;

                    $.each(this.rowSelect, function(index) {
                        self.rowSelect[index] = !self.massRowSelect;
                    });
                },
                'handleDuplicateSelected': function () {
                    var self = this;
                    var items = [];
                    var duplicate = [];

                    $.each(this.$children, function (index, item) {

                        if (!item.selected.code || item.selected.code == 'false') {
                            $(item.$el).closest('[data-role="main.block"]').css('background', '');
                        } else {
                            if (items.length > 0) {
                                var result = items.filter(function (elem) {
                                    if (item.selected.id == elem.item.id && item.selected.code == elem.item.code) {
                                        if(duplicate.indexOf(elem.index) === -1) {
                                            duplicate.push(elem.index);
                                        }
                                        return true;
                                    }
                                });

                                if (result.length > 0) {
                                    if(duplicate.indexOf(index) === -1) {
                                        duplicate.push(index);
                                    }
                                } else {
                                    $(item.$el).closest('[data-role="main.block"]').css('background', '');
                                }
                            }
                            $(item.$el).closest('[data-role="main.block"]').css('background', '');


                            items.push({'index': index, 'item': item.selected});
                        }
                    });

                    $.each(duplicate, function (index, value) {
                        $(self.$children[value].$el).closest('[data-role="main.block"]').css('background', '#faeee6');
                    });
                }
            },
            computed:{
                changedItems: function(){
                    var self = this;
                    var result = [];
                    var delimiter = ';';
                    var columnLength = self.columns[0].length;
                    var item;

                    $.each(self.tableData, function(itemIndex, itemValue) {
                        item = [];

                        $.each(itemValue, function(index, value) {
                            if (index >= columnLength)
                                return ;

                            if (!self.columns[0][index].settings) {
                                item.push(value);
                            } else {
                                var settings = JSON.parse(self.columns[0][index].settings);
                                var res = JHelpers.applyImportSettings(value, itemValue, index, self.columns[0][index], settings, delimiter);

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

                $.each(self.columns[0], function (index, column) {
                    if (!!column.settings) {
                        $('#settings_' + index).parent().addClass('selected');
                    } else {
                        $('#settings_' + id).parent().removeClass('selected');
                    }
                });

                setTimeout(this.handleAddRows(true), 1000);
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
        setTimeout(function() {
            JHelpers.SetWidthList();
        }, 1000);

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

            app.$set(app.columns[0][parseInt(id)], 'settings', value);
        };

        window.page.setCreatedProperty = function(value) {
            request('refreshProperties', {'data': value}, function (data) {

                $.each(app.$children, function(index) {

                    if (this.id == data.columnId) {
                        this.selected = data.selected;
                    }

                    this.list = data.properties;
                });
            });
        };

    <?php } ?>

    <?php if ($step == 3) { ?>
        var id = <?= $template->id ?>;
        var statistic = <?= JavaScript::toObject($statistic) ?>;
        var hasNonFileAction = <?= JavaScript::toObject($hasNonFileAction) ?>;

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
                errors: [],
                resultStatistic: statistic,
                numberCurrent: 0,
                numberAll: 0,
                resultMessage: '',
                downloadLink: '',
                loaded: '',
                count: 'loading',
                preloaderActive: 'true'
            },
            methods: {
                'addError': function (errors) {
                    var self = this;

                    $.each(errors, function (index, error) {
                        self.errors.push(error.message);
                    });
                },
                'updateProgress': function (step = 1, prevStatistic = null) {
                    var self = this;

                    request('import', {'id': id, 'step': step, 'prevStatistic': prevStatistic}, function (data) {
                        self.updateStatistic(data.statistic);

                        if (!!data.isError && !data.isCriticalError)
                            self.addError(data.errors);

                        if (!data.statistic.finished && !data.isCriticalError) {
                            setTimeout(function () {
                                self.updateProgress(Number(data.step) + 1, data.statistic);
                                self.progressStyle.width = JHelpers.PercentCalc(self.count, data.statistic.totalRows + 1, true);
                            }, 1000);
                        } else {
                            self.nonFileElement(data.statistic, data.isCriticalError, data.errors);
                        }
                    });
                },
                'nonFileElement': function (statistic, isError, error) {
                    var self = this;
                    var elementIds = <?= JavaScript::toObject($_SESSION['INTEC_IMPORT']['elementsIds']) ?>;

                    self.resultStyle.result.display = 'block';

                    if (hasNonFileAction) {
                        self.resultMessage = '<?= Loc::getMessage('progress.bar.info.non.file.action') ?>';
                        self.resultStyle.result.color = 'black';
                        self.preloaderActive = 'true';

                        request('non.file.element', {'elementIds': elementIds}, function (data) {
                            console.log(data);
                            self.preloaderActive = 'false';
                            if (statistic.errors.length > 0 || isError) {
                                if (isError)
                                    self.resultMessage = error[0].message;
                                else
                                    self.resultMessage = '<?= Loc::getMessage('progress.bar.info.finished.error') ?>';

                                self.resultStyle.result.color = 'red';
                            } else {
                                self.resultMessage = '<?= Loc::getMessage('progress.bar.info.finished') ?>';
                                self.progressStyle.width = '100%';
                                self.resultStyle.result.color = 'green';
                            }
                        });
                    } else {
                        if (statistic.errors.length > 0 || isError) {
                            if (isError)
                                self.resultMessage = error[0].message;
                            else
                                self.resultMessage = '<?= Loc::getMessage('progress.bar.info.finished.error') ?>';

                            self.resultMessage = self.resultMessage.replace('<br>','');

                            self.resultStyle.result.color = 'red';
                        } else {
                            self.resultMessage = '<?= Loc::getMessage('progress.bar.info.finished') ?>';
                            self.progressStyle.width = '100%';
                        }

                    }
                },
                'getCount': function (count) {
                    var self = this;

                    request('get.count', {}, function (data) {

                        if (data.loaded) {
                            self.count = data.count;
                            self.updateProgress();
                            self.preloaderActive = 'false';
                        } else {
                            console.log('prepare data error');
                        }
                    });
                },
                'updateStatistic': function (data) {
                    this.resultStatistic.added.value = data.added;
                    this.resultStatistic.updated.value = data.updated;
                    this.resultStatistic.deleted.value = data.deleted;
                    this.resultStatistic.offersAdded.value = data.offersAdded;
                    this.resultStatistic.offersUpdated.value = data.offersUpdated;
                    this.resultStatistic.sectionAdded.value = data.sectionCreate;
                    this.resultStatistic.errors.value = data.errors;
                    this.resultStatistic.totalRows.value =
                                                    data.errors + data.deleted +
                                                    data.updated + data.added +
                                                    data.offersAdded + data.offersUpdated;
                }
            },
            mounted: function () {
                this.getCount();
            }
        });
    <?php } ?>


})(jQuery, intec);
</script>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>