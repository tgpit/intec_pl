<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Page\Asset;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\ai\models\Task;
use \Bitrix\Main\Loader;
use intec\core\helpers\StringHelper;
use Bitrix\Main\Text\Encoding;

include(__DIR__.'/../requirements.php');

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('intec.ai.admin.tasks.title'));

$request = Core::$app->request;

$list = 'ai_tasks';
$listSort = [];
$listSort['variable'] = $list.'_by';
$listSort['value'] = $request->get($listSort['variable'], 'id');
$listOrder = [];
$listOrder['variable'] = $list.'_order';
$listOrder['value'] = $request->get($listOrder['variable'], 'asc');

$sort = new CAdminSorting(
    $list,
    $listSort['value'],
    $listSort['variable'],
    $listOrder['value'],
    $listOrder['variable']
);

$list = new CAdminList($list, $sort);

$filter = $list->InitFilter([
    'filterIdValue',
    'filterdateCreateFromValue',
    'filterdateCreateToValue',
    'filterElementIdValue',
    'filterIblockPropertyValue',
    'filterPromptValue',
    'filterGenerationResultValue',
    'filterDoneValue',
    'filterErrorValue',
    'filterTaskUpdatedValue',
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.dateCreate.from'),
        Loc::getMessage('filter.fields.dateCreate.to'),
        Loc::getMessage('filter.fields.elementId'),
        Loc::getMessage('filter.fields.iblockProperty'),
        Loc::getMessage('filter.fields.prompt'),
        Loc::getMessage('filter.fields.generationResult'),
        Loc::getMessage('filter.fields.done'),
        Loc::getMessage('filter.fields.error'),
        Loc::getMessage('filter.fields.taskUpdated')
    ]
);

if ($list->EditAction()) {

    if (!empty($FIELDS)) {
        $id = ArrayHelper::getKeys($FIELDS);
        
        $tasks = Task::find()
            ->where(['id' => $id])
            ->indexBy('id')
            ->all();

        foreach ($FIELDS as $id => $data) {
            $task = $tasks->get($id);

            if (empty($task))
                continue;

            if (isset($data['prompt']))
                $task->prompt = $data['prompt'];

            if (isset($data['generationResult']))
                $task->generationResult = $data['generationResult'];

            if (isset($data['done']))
                $task->done = $data['done'];

            if (!$task->save()) {
                $error = $task->getFirstErrors();
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

    $tasks = Task::find();

    if (!empty($id))
        $tasks->where(['id' => $id]);

    $tasks = $tasks->all();

    foreach ($tasks as $task) {
        if ($action === 'delete') {
            $task->delete();
        } else if ($action === 'taskUpdate') {
            $task->taskUpdate();
        } else if ($action === 'taskSendBack') {
            $task->taskSendBack();
        }
    }
}

if (isset($_GET['del_filter']) && $_GET['del_filter'] === 'Y') {
    $filterDateCreateFromValue = null;
    $filterDateCreateToValue = null;
}

$tasks = Task::find();

if (!empty($filterIdValue))
    $tasks->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterDateCreateFromValue)) {
    $date = DateTime::createFromFormat('d.m.Y', $filterDateCreateFromValue);
    if ($date) {
        $formattedDate = $date->format('Y-m-d').' 00:00:00';
        $tasks->andWhere(['>=', 'dateCreate', $formattedDate]);
    }
}

if (!empty($filterDateCreateToValue)) {
    $date = DateTime::createFromFormat('d.m.Y', $filterDateCreateToValue);
    if ($date) {
        $formattedDate = $date->format('Y-m-d').' 00:00:00';
        $tasks->andWhere(['<=', 'dateCreate', $formattedDate]);
    }
}

if (!empty($filterElementIdValue))
    $tasks->andWhere(['=', 'elementId', $filterElementIdValue]);

if (!empty($filterIblockPropertyValue))
    $tasks->andWhere(['=', 'iblockProperty', $filterIblockPropertyValue]);

if (!empty($filterPromptValue)) {
    if (Encoding::detectUtf8($filterPromptValue))
        $filterPromptValue = Encoding::convertEncoding($filterPromptValue, 'UTF-8', LANG_CHARSET);
    $tasks->andWhere(['like', 'prompt', $filterPromptValue]);
}

if (!empty($filterGenerationResultValue)) {
    if (Encoding::detectUtf8($filterGenerationResultValue))
        $filterGenerationResultValue = Encoding::convertEncoding($filterGenerationResultValue, 'UTF-8', LANG_CHARSET);
    $tasks->andWhere(['like', 'generationResult', $filterGenerationResultValue]);
}

if (!empty($filterDoneValue))
    $tasks->andWhere(['=', 'done', $filterDoneValue]);

if (!empty($filterErrorValue))
    $tasks->andWhere(['like', 'error', $filterErrorValue]);

if (!empty($filterTaskUpdatedValue))
    $tasks->andWhere(['=', 'taskUpdated', $filterTaskUpdatedValue]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($tasks->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$tasks->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $tasks->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

$tasks = $tasks->all();

$headers = [
    [
        'id' => 'id',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.id'),
        'sort' => 'id',
        'default' => true
    ],
    [
        'id' => 'dateCreate',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.dateCreate'),
        'sort' => 'dateCreate',
        'default' => true
    ],
    [
        'id' => 'elementId',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.elementId'),
        'sort' => 'elementId',
        'default' => true
    ],
    [
        'id' => 'iblockProperty',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.iblockProperty'),
        'sort' => 'iblockProperty',
        'default' => true
    ],
    [
        'id' => 'prompt',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.prompt'),
        'sort' => 'prompt',
        'default' => true
    ],
    [
        'id' => 'generationResult',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.generationResult'),
        'sort' => 'generationResult',
        'default' => true
    ],
    [
        'id' => 'done',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.done'),
        'sort' => 'done',
        'default' => true
    ],
    [
        'id' => 'error',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.error'),
        'sort' => 'error',
        'default' => true
    ],
    [
        'id' => 'isUpdated',
        'content' => Loc::getMessage('intec.ai.admin.tasks.header.isUpdated'),
        'sort' => 'isUpdated',
        'default' => true
    ],
];

$list->AddHeaders($headers);

// $list->AddAdminContextMenu([[
//     'TEXT' => Loc::getMessage('list.actions.add'),
//     'ICON' => 'btn_new',
//     'LINK' => '/bitrix/admin/ai_task_edit.php?lang=ru',
//     'TITLE' => Loc::getMessage('list.actions.add')
// ]]);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete'),
    'taskUpdate' => Loc::getMessage('list.actions.taskUpdate'),
    'taskSendBack' => Loc::getMessage('list.actions.taskSendBack')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

$uniqueIblockProperties = [];

foreach ($tasks as $task) {
    $res = CIBlockElement::GetByID($task->elementId);
    if($ar_res = $res->GetNext())
    {
        $IBLOCK_ID = $ar_res['IBLOCK_ID'];
        $IBLOCK_TYPE = $ar_res['IBLOCK_TYPE_ID'];
        $ELEMENT_NAME = $ar_res['NAME'];
    }

    $GENERATION_RESULT = trim($task->generationResult);

    if (Encoding::detectUtf8($GENERATION_RESULT))
        $GENERATION_RESULT = Encoding::convertEncoding($GENERATION_RESULT, 'UTF-8', LANG_CHARSET);

    if (!in_array($task->iblockProperty, $uniqueIblockProperties)) {
        if ($task->iblockProperty == 'PREVIEW_TEXT') {
            $uniqueIblockProperties['PREVIEW_TEXT'] = Loc::getMessage('intec.ai.admin.tasks.header.previewText').' [PREVIEW_TEXT]';
        } else if ($task->iblockProperty == 'DETAIL_TEXT') {
            $uniqueIblockProperties['DETAIL_TEXT'] = Loc::getMessage('intec.ai.admin.tasks.header.detailText').' [DETAIL_TEXT]';
        } else if ($task->iblockProperty == 'NAME') {
            $uniqueIblockProperties['NAME'] = Loc::getMessage('intec.ai.admin.tasks.header.name').' [NAME]';
        } else {
            $uniqueIblockPropertyCode = str_replace('[CUSTOM_PROPERTY]', '', $task->iblockProperty);
            if (!empty($ELEMENT_NAME)) {
                $arFilter = Array("ID" => $task->elementId);
                $arSelect = Array("ID", "IBLOCK_ID", "PROPERTY_".$uniqueIblockPropertyCode);
                $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
                if ($arFields = $res->GetNext()) {
                    $iblockId = $arFields['IBLOCK_ID'];
                    
                    $arFilter = Array("IBLOCK_ID" => $iblockId, "CODE" => $uniqueIblockPropertyCode);
                    $dbRes = CIBlockProperty::GetList(Array(), $arFilter);
                    if ($arRes = $dbRes->GetNext()) {
                        $uniqueIblockProperties[$task->iblockProperty] = $arRes['NAME'].' ['.$uniqueIblockPropertyCode.']';
                    } else {
                        $uniqueIblockProperties[$task->iblockProperty] = Loc::getMessage('intec.ai.admin.tasks.header.propertyNotFound').' ['.$uniqueIblockPropertyCode.']';
                    }
                }
            } else {
                $uniqueIblockProperties[$task->iblockProperty] = Loc::getMessage('intec.ai.admin.tasks.header.propertyNotFound').' ['.$uniqueIblockPropertyCode.']';
            }
            unset($uniqueIblockPropertyCode);
        }
    }

    $isUpdated = 'N';

    $rowPrompt = $task->prompt;

    if (Encoding::detectUtf8($rowPrompt))
        $rowPrompt = Encoding::convertEncoding($task->prompt, 'UTF-8', LANG_CHARSET);

    $addRowParams = [
        'id' => $task->id,
        'dateCreate' => !empty($task->dateCreate) ? Core::$app->formatter->asDate($task->dateCreate, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('intec.ai.admin.tasks.no').')',
        'elementId' => 0,
        'iblockProperty' => $uniqueIblockProperties[$task->iblockProperty],
        'prompt' => $rowPrompt,
        'generationResult' => $GENERATION_RESULT,
        'done' => $task->done,
        'error' => $task->error,
        'isUpdated' => ''
    ];

    if (!empty($ELEMENT_NAME)) {
        $IBLOCK_PROPERTY = str_replace('[CUSTOM_PROPERTY]', 'PROPERTY_', $task->iblockProperty);

        $arSelect = Array("ID", "IBLOCK_ID", "NAME", $IBLOCK_PROPERTY);
        $arFilter = Array("ID" => $task->elementId);
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        $arFields = $res->GetNext();
        $PROPERTY_VALUE = trim($arFields[$IBLOCK_PROPERTY]);

        if (empty($PROPERTY_VALUE)) {
            $PROPERTY_VALUE = $arFields[$IBLOCK_PROPERTY.'_VALUE'];
            if (is_array($PROPERTY_VALUE)) {
                $PROPERTY_VALUE = trim($PROPERTY_VALUE['TEXT']);
            } else {
                $PROPERTY_VALUE = trim($PROPERTY_VALUE);
            }
        }

        if (Encoding::detectUtf8($PROPERTY_VALUE))
            $PROPERTY_VALUE = Encoding::convertEncoding($PROPERTY_VALUE, 'UTF-8', LANG_CHARSET);

        similar_text(preg_replace('/\s+/', '', strip_tags($PROPERTY_VALUE)), preg_replace('/\s+/', '', strip_tags($GENERATION_RESULT)), $perc);
        if ($perc > 90) {
            $task->taskUpdated = 'Y';
            $task->save();
        } else {
            $task->taskUpdated = 'N';
            $task->save();
        }

        $addRowParams['elementId'] = $task->elementId;
        $addRowParams['isUpdated'] = ($task->taskUpdated == 'Y') ? Loc::getMessage('intec.ai.admin.tasks.yes') : Loc::getMessage('intec.ai.admin.tasks.no');

        $row = $list->AddRow($task->id, $addRowParams);

        $editLink = '/bitrix/admin/iblock_element_edit.php?IBLOCK_ID='.$IBLOCK_ID.'&type='.$IBLOCK_TYPE.'&ID='.$task->elementId.'&lang=ru';

        $row->AddViewField('elementId', Html::a($ELEMENT_NAME.' ['.$task->elementId.']', $editLink, [
            'target' => '_blank'
        ]));
    } else {
        $task->taskUpdated = 'N';
        $task->save();

        $addRowParams['elementId'] = Loc::getMessage('intec.ai.admin.tasks.header.elementNotFound').' ['.$task->elementId.']';
        $addRowParams['isUpdated'] = ($task->taskUpdated == 'Y') ? Loc::getMessage('intec.ai.admin.tasks.yes') : Loc::getMessage('intec.ai.admin.tasks.no');

        $row = $list->AddRow($task->id, $addRowParams);
    }

    $actions = [];
    // $actions[] = [
    //     'ICON' => 'edit',
    //     'TEXT' => Loc::getMessage('list.rows.actions.edit'),
    //     'ACTION' => $list->ActionRedirect('/bitrix/admin/ai_task_edit.php?lang=ru&task='.$task->id)
    // ];

    // $actions[] = [
    //     'SEPARATOR' => 'Y'
    // ];

    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
            $task->id,
            'delete'
        )
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'edit',
        'TEXT' => Loc::getMessage('list.rows.actions.taskUpdate'),
        'ACTION' => $list->ActionDoGroup(
            $task->id,
            'taskUpdate'
        )
    ];

    $actions[] = [
        'SEPARATOR' => 'Y'
    ];

    $actions[] = [
        'ICON' => 'move',
        'TEXT' => Loc::getMessage('list.rows.actions.taskSendBack'),
        'ACTION' => $list->ActionDoGroup(
            $task->id,
            'taskSendBack'
        )
    ];

    $row->AddCheckField('done');
    $row->AddInputField('prompt');
    $row->AddInputField('generationResult');
    $row->AddActions($actions);
}

$list->CheckListMode();

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');
?>
<form name="find_form" method="get" action="<?= '/'.$request->getPathInfo() ?>">
    <?php $filter->Begin() ?>
    <tr>
        <td><?= Loc::getMessage('filter.fields.id') ?>:</td>
        <td><?= Html::textInput('filterIdValue', !empty($filterIdValue) ? $filterIdValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.dateCreate.from') ?>:</td>
        <td><?= CAdminCalendar::CalendarDate('filterDateCreateFromValue', $filterDateCreateFromValue) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.dateCreate.to') ?>:</td>
        <td><?= CAdminCalendar::CalendarDate('filterDateCreateToValue', $filterDateCreateToValue) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.elementId') ?>:</td>
        <td><?= Html::textInput('filterElementIdValue', !empty($filterElementIdValue) ? $filterElementIdValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.iblockProperty') ?>:</td>
        <td>
            <select name="filterIblockPropertyValue" class="adm-select">
                <option value=""><?= '('.Loc::getMessage('intec.ai.admin.tasks.unset').')' ?></option>
                <?php foreach ($uniqueIblockProperties as $key => $uniqueIblockProperty) { ?>
                    <option value="<?= $key ?>"><?= $uniqueIblockProperty ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.prompt') ?>:</td>
        <td><?= Html::textInput('filterPromptValue', !empty($filterPromptValue) ? $filterPromptValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.generationResult') ?>:</td>
        <td><?= Html::textInput('filterGenerationResultValue', !empty($filterGenerationResultValue) ? $filterGenerationResultValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.done') ?>:</td>
        <td>
            <select name="filterDoneValue" class="adm-select">
                <option value=""><?= '('.Loc::getMessage('intec.ai.admin.tasks.unset').')' ?></option>
                <option value="N"><?= Loc::getMessage('filter.fields.done.no') ?></option>
                <option value="Y"><?= Loc::getMessage('filter.fields.done.yes') ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.error') ?>:</td>
        <td><?= Html::textInput('filterErrorValue', !empty($filterErrorValue) ? $filterErrorValue : null) ?></td>
    </tr>
    <tr>
        <td><?= Loc::getMessage('filter.fields.taskUpdated') ?>:</td>
        <td>
            <select name="filterTaskUpdatedValue" class="adm-select">
                <option value=""><?= '('.Loc::getMessage('intec.ai.admin.tasks.unset').')' ?></option>
                <option value="N"><?= Loc::getMessage('filter.fields.done.no') ?></option>
                <option value="Y"><?= Loc::getMessage('filter.fields.done.yes') ?></option>
            </select>
        </td>
    </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');