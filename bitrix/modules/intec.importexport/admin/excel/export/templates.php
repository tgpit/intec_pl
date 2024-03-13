<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;
use intec\importexport\models\excel\export\Template;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title'));

include(__DIR__.'/../../requirements.php');
include(Core::getAlias('@intec/importexport/module/admin/url.php'));

$arJsConfig = [
    'script_export' => [
        'js' => '/bitrix/js/intec.importexport/script_export.js'
    ]
];

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}

Core::$app->web->js->loadExtensions(['vue', 'jquery', 'jquery_extensions', 'intec_core']);
Core::$app->web->css->addFile('@intec/importexport/resources/css/excel/cron_settings.css');
CJSCore::Init(array('script_export'));

$request = Core::$app->request;

$list = 'importexport_export_templates';

$listSort = [];
$listSort['variable'] = $list.'_by';
$listSort['value'] = $request->get($listSort['variable'], 'id');
$listOrder = [];
$listOrder['variable'] = $list.'_order';
$listOrder['value'] = $request->get($listOrder['variable'], 'asc');

/*for cron*/
$cronFrame = 'cron_frame_export.php';
$cronExportPath = '/bitrix/php_interface/include/intec.importexport/';
$crontabPath = '/bitrix/crontab/crontab.cfg';

if ($request->getIsAjax()) {
    if ($request->getIsPost()) {
        $action = $request->post('action');

        if ($action !== 'updateCronList') {
            $formData = $request->post('formData');
            $formatFormData = [];

            if (!empty($formData)) {
                foreach ($formData as $formItem) {
                    if (empty($formItem['value']))
                        continue;

                    if (StringHelper::startsWith($formItem['name'], 'data')) {

                        $name = StringHelper::cut($formItem['name'], 4);
                        $name = StringHelper::replace($name, ['[' => '', ']' => '']);

                        if (empty($formatFormData[$name])) {
                            $formatFormData[$name] = $formItem['value'];
                        } else {
                            if (!Type::isArray($formatFormData[$name])) {
                                $firstValue = $formatFormData[$name];
                                $formatFormData[$name] = [];
                                $formatFormData[$name][] = $firstValue;
                                unset($firstValue);
                            }

                            $formatFormData[$name][] = $formItem['value'];
                        }
                    }
                }
            }

            $formData = $formatFormData;
            $time = '';
            $template = $formData['templates'];
            $templateLogs = $formData['templates'];

            if (Type::isArray($formData['templates'])) {
                $template = implode(',', $template);
                $templateLogs = implode('_', $templateLogs);
            }


            /*default values*/
            if ($formData['type'] === 'daily') {
                if (empty(trim($formData['dailyHours'])))
                    $formData['dailyHours'] = 0;

                if (empty(trim($formData['dailyMinutes'])))
                    $formData['dailyMinutes'] = 0;

                $time = $formData['dailyMinutes'] . ' ' . $formData['dailyHours'] . ' * * *';
            } elseif ($formData['type'] === 'hours') {
                if (empty(trim($formData['hours'])))
                    $formData['dailyHours'] = 1;

                $time = '0 */' . $formData['dailyHours'] . ' * * *';
            } elseif ($formData['type'] === 'minutes') {
                if (empty(trim($formData['minutes'])))
                    $formData['minutes'] = 15;

                $time = '*/' . $formData['minutes'] . ' * * * *';
            } elseif ($formData['type'] === 'expert') {
                if (empty(trim($formData['expert'])))
                    $formData['expert'] = '* * * * *';

                $time = $formData['expert'];
            }

            if (empty(trim($formData['phpPath']))) {
                $formData['phpPath'] = '/usr/bin/php';
            }

            unset($formatFormData);
        }

        $response = [
            'status' => 'error'
        ];

        if ($action === 'updateCronList') {
            $response['status'] = 'success';

            $result = [];

            if (FileHelper::isEntry($_SERVER["DOCUMENT_ROOT"].$crontabPath)) {
                $crontabData = FileHelper::getFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath);
                $matchLine = [];
                $resultCount = preg_match_all("#^\s*(([\*/,\d]+\s+){5}).*?".preg_quote($cronExportPath).$cronFrame." +(\d[\d,]*) *>.*?$#im", $crontabData, $matchLine);
            }

            if ($resultCount > 0) {
                for ($i = 0; $i < $resultCount; $i++) {
                    $result[] = [
                        'time' => $matchLine[1][$i],
                        'template' => $matchLine[3][$i]
                    ];
                }
            }

            $response['data'] = $result;
        } elseif ($action === 'deleteSelectedFromCronList') {
            $response['status'] = 'success';
            $success = false;

            if (FileHelper::isEntry($_SERVER["DOCUMENT_ROOT"].$crontabPath) && !empty($template)) {
                $crontabData = FileHelper::getFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath);
                $changedData = preg_replace("#^.*?".preg_quote($cronExportPath).$cronFrame." +".preg_quote($template)." *>.*?$#im", "", $crontabData);
                $changeResult = FileHelper::setFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath, $changedData);

                if ($changeResult)
                    $success = true;
            }

            if ($success) {
                $message = Loc::getMessage('cron.current.delete.message.success');
                $message = StringHelper::replaceMacros($message, [
                    'CRONTAB_PATH' => $crontabPath,
                    'TIME' => $time,
                    'TEMPLATE' => $template,
                ]);
            } else {
                if (empty($template))
                    $message = Loc::getMessage('cron.non.template.error');
                else
                    $message = Loc::getMessage('cron.error');
            }

            $response['data'] = [
                'success' => $success,
                'message' => $message,
                '$changedData' => $changedData,
                '$changeResult' => $changeResult
            ];
        } elseif ($action === 'deleteFromCronList') {
            $response['status'] = 'success';

            $selectedData = $request->post('selectedData');
            $selectedData['template'] = trim($selectedData['template']);
            $selectedData['time'] = trim($selectedData['time']);
            $success = false;

            if (!empty($selectedData['time']) && !empty($selectedData['template'])) {
                if (FileHelper::isEntry($_SERVER["DOCUMENT_ROOT"].$crontabPath)) {
                    $crontabData = FileHelper::getFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath);
                    $changedData = preg_replace("#^\s*".preg_quote($selectedData['time'])."\s+.*?".preg_quote($cronExportPath).$cronFrame." +".preg_quote($selectedData['template'])." *>.*?$#im", "", $crontabData);
                    $changeResult = FileHelper::setFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath, $changedData);

                    if ($changeResult)
                        $success = true;
                }
            }

            if ($success) {
                $message = Loc::getMessage('cron.current.delete.message.success');
                $message = StringHelper::replaceMacros($message, [
                    'CRONTAB_PATH' => $crontabPath,
                    'TIME' => $selectedData['time'],
                    'TEMPLATE' => $selectedData['template'],
                ]);
            } else {
                $message = Loc::getMessage('cron.error');
            }

            $response['data'] = [
                'success' => $success,
                'message' => $message,
            ];
        } elseif ($action === 'addToCronList') {
            $response['status'] = 'success';

            if (!empty($template)) {
                $crontabData = FileHelper::getFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath);
                $matchLine = [];
                $resultCount = preg_match_all("#^\s*".preg_quote($time)."\s+.*?".preg_quote($cronExportPath).$cronFrame." +".preg_quote($template)." *>.*?$#im", $crontabData, $matchLine);
            }

            if (defined('BX_UTF'))
                $currentEncoding = "utf-8";
            elseif (defined("SITE_CHARSET") && (strlen(SITE_CHARSET) > 0))
                $currentEncoding = SITE_CHARSET;
            elseif (defined("LANG_CHARSET") && (strlen(LANG_CHARSET) > 0))
                $currentEncoding = LANG_CHARSET;
            elseif (defined("BX_DEFAULT_CHARSET"))
                $currentEncoding = BX_DEFAULT_CHARSET;
            else
                $currentEncoding = "windows-1251";

            $currentEncoding = strtolower($currentEncoding);

            if ($resultCount <= 0 && !empty($template)) {
                $newData = '#TIME# #PHP_PATH# -d default_charset=' . $currentEncoding . ' -d short_open_tag=on -d memory_limit=1024M -f #CRON_FRAME_PATH# #TEMPLATE# > #LOG_PATH#';

                $newData = StringHelper::replaceMacros($newData, [
                    'TIME' => $time,
                    'PHP_PATH' => $formData['phpPath'],
                    'CRON_FRAME_PATH' => $_SERVER["DOCUMENT_ROOT"] . $cronExportPath . $cronFrame,
                    'TEMPLATE' => $template,
                    'LOG_PATH' => $_SERVER["DOCUMENT_ROOT"] . $cronExportPath . 'export_logs/' . $templateLogs . '.txt',
                ]);

                if (substr($crontabData, -1) == '\n')
                    $test = FileHelper::setFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath, $newData . PHP_EOL, true);
                else
                    $test = FileHelper::setFileData($_SERVER["DOCUMENT_ROOT"].$crontabPath,  PHP_EOL . $newData . PHP_EOL, true);
            }

            if ($test) {
                $success = true;

                $message = Loc::getMessage('cron.add.message.success');
                $message = StringHelper::replaceMacros($message, [
                    'CRONTAB_PATH' => $crontabPath,
                    'TIME' => $time,
                    'PHP_PATH' => $formData['phpPath'],
                    'CRON_FRAME_PATH' => $_SERVER["DOCUMENT_ROOT"] . $cronExportPath . $cronFrame,
                    'TEMPLATE' => $template,
                    'LOG_PATH' => $_SERVER["DOCUMENT_ROOT"] . $cronExportPath . 'export_logs/' . $templateLogs . '.txt',
                ]);
            } else {
                $success = false;

                if (empty($template))
                    $message = Loc::getMessage('cron.non.template.error');
                else
                    $message = Loc::getMessage('cron.error');
            }

            $response['data'] = [
                'success' => $success,
                'message' => $message,
            ];
        }

        echo Json::encode($response, 320, true);
        return;
    }
}
/*for cron*/


$sort = new CAdminSorting(
    $list,
    $listSort['value'],
    $listOrder['value'],
    $listSort['variable'],
    $listOrder['variable']
);

$list = new CAdminList($list, $sort);

$filter = $list->InitFilter([
    'filterIdValue',
    'filterNameValue',
    'filterCreateDateValue',
    'filterEditDateValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.name'),
        Loc::getMessage('filter.fields.create.date'),
        Loc::getMessage('filter.fields.edit.date')
    ]
);

if ($list->EditAction()) {
    /** @var array $FIELDS */

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        /** @var ActiveRecords $templates */
        $templates = Template::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            /** @var Template $template */
            $template = $templates->get($id);

            if (empty($template))
                continue;

            $template->load($data, '');

            if (isset($data['active']))
                $template->active = $template->active === 'Y';

            if (!$template->save()) {
                $error = $template->getFirstErrors();
                $error = ArrayHelper::getFirstValue($error);
                $list->AddGroupError($error, $id);
            }
        }

        unset($error);
        unset($data);
        unset($id);
    }
} else if ($id = $list->GroupAction()) {
    $action = $_REQUEST['action'];

    if (empty($id[0]) && count($id) === 1)
        $id = null;

    $templates = Template::find();

    if (!empty($id))
        $templates->where(['id' => $id]);

    /** @var Template[] $templates */
    $templates = $templates->all();

    foreach ($templates as $template) {
        if ($action === 'delete') {
            $template->delete();
        }
    }
}

unset($template);
unset($templates);
unset($id);

$templates = Template::find();

if (!empty($filterIdValue))
    $templates->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterNameValue))
    $templates->andWhere(['like', 'name', $filterNameValue]);

if (!empty($filterCreateDateValue))
    $templates->andWhere(['like', 'createDate', $filterCreateDateValue]);

if (!empty($filterEditDateValue))
    $templates->andWhere(['like', 'editDate', $filterEditDateValue]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($templates->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$templates->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $templates->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $templates */
$templates = $templates->all();

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'name',
    'content' => Loc::getMessage('list.headers.name'),
    'sort' => 'name',
    'default' => true
], [
    'id' => 'createDate',
    'content' => Loc::getMessage('list.headers.create.date'),
    'sort' => 'createDate',
    'default' => true
], [
    'id' => 'editDate',
    'content' => Loc::getMessage('list.headers.edit.date'),
    'sort' => 'editDate',
    'default' => true
]];

$list->AddHeaders($headers);

$list->AddAdminContextMenu([[
    'TEXT' => Loc::getMessage('list.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['excel.export.templates.add'],
    'TITLE' => Loc::getMessage('list.actions.add')
], [
    'TEXT' => Loc::getMessage('list.actions.cron'),
    'TITLE' => Loc::getMessage('list.actions.cron'),
    'ONCLICK' => 'showCron();',
    'ICON' => '',
]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($templates as $template) {
    /** @var Template $template */
    $actions = [];
    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.edit'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['excel.export.templates.edit'], [
            'template' => $template->id
        ]))
    ];

    /*$actions[] = [
        'ICON' => 'copy',
        'TEXT' => Loc::getMessage('list.rows.actions.copy'),
        'ACTION' => $list->ActionRedirect(StringHelper::replaceMacros($arUrlTemplates['excel.export.templates.copy'], [
            'template' => $template->id
        ]))
    ];*/

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
                $template->id,
                'delete'
            )
    ];

    $row = $list->AddRow($template->id, [
        'id' => $template->id,
        'name' => $template->name,
        'createDate' => $template->createDate,
        'editDate' => $template->editDate,
    ]);

    $row->AddInputField('name');
    $row->AddActions($actions);
}

$list->CheckListMode();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
    <form name="find_form" method="get" action="<?= '/'.$request->getPathInfo() ?>">
        <?php $filter->Begin() ?>
        <tr>
            <td><?= Loc::getMessage('filter.fields.id') ?>:</td>
            <td><?= Html::textInput('filterIdValue', !empty($filterIdValue) ? $filterIdValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.name') ?>:</td>
            <td><?= Html::textInput('filterNameValue', !empty($filterNameValue) ? $filterNameValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.create.date') ?>:</td>
            <td><?= Html::textInput('filterCreateDateValue', !empty($filterCreateDateValue) ? $filterCreateDateValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.edit.date') ?>:</td>
            <td><?= Html::textInput('filterEditDateValue', !empty($filterEditDateValue) ? $filterEditDateValue : null) ?></td>
        </tr>
        <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
        <?php $filter->End() ?>
    </form>
<?php $list->DisplayList() ?>

<script type="text/javascript">
    BX.hint_replace(BX('hint_phpPath'), 'test');
    function showCron () {

        var title = 'cron settings';

        title = '<?= Loc::getMessage('title.cron') ?>';

        JHelpers.ShowCron({}, title, 'export');
    }
</script>

<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>