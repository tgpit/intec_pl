<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use DateTime;
use DateTimeZone;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\db\ActiveRecords;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\filter\Visit;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title'));

include(__DIR__.'/../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

$request = Core::$app->request;

$list = 'seo_filter_visits';
$listSort = [];
$listSort['variable'] = $list.'_by';
$listSort['value'] = $request->get($listSort['variable'], 'dateVisit');
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
    'filterSessionIdValue',
    'filterReferrerUrlValue',
    'filterPageUrlValue',
    'filterDateCreateFrom',
    'filterDateCreateTo',
    'filterDateVisitFrom',
    'filterDateVisitTo'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter',[
        Loc::getMessage('filter.fields.id'),
        Loc::getMessage('filter.fields.sessionId'),
        Loc::getMessage('filter.fields.referrerUrl'),
        Loc::getMessage('filter.fields.pageUrl'),
        Loc::getMessage('filter.fields.dateCreatePeriod'),
        Loc::getMessage('filter.fields.dateVisitPeriod')
    ]
);

if ($id = $list->GroupAction()) {
    $action = $_REQUEST['action'];

    if (empty($id[0]) && count($id) === 1)
        $id = null;

    $visits = Visit::find();

    if (!empty($id))
        $visits->where(['id' => $id]);

    /** @var Visit[] $visits */
    $visits = $visits->all();

    foreach ($visits as $visit) {
        if ($action === 'delete') {
            $visit->delete();
        }
    }
}

unset($visit);
unset($visits);
unset($id);

$visits = Visit::find();

if (!empty($filterIdValue))
    $visits->andWhere(['=', 'id', $filterIdValue]);

if (!empty($filterSessionIdValue))
    $visits->andWhere(['=', 'sessionId', $filterSessionIdValue]);

if (!empty($filterReferrerUrlValue))
    $visits->andWhere(['like', 'referrerUrl', $filterReferrerUrlValue]);

if (!empty($filterPageUrlValue))
    $visits->andWhere(['like', 'pageUrl', $filterPageUrlValue]);

if (!empty($filterDateCreateFrom))
    $visits->andWhere(['>=', 'dateCreate', (new DateTime($filterDateCreateFrom, new DateTimeZone('UTC')))->setTime(0, 0)->format('Y-m-d H:i:s')]);

if (!empty($filterDateCreateTo))
    $visits->andWhere(['<=', 'dateCreate', (new DateTime($filterDateCreateTo, new DateTimeZone('UTC')))->setTime(23, 59)->format('Y-m-d H:i:s')]);

if (!empty($filterDateVisitFrom))
    $visits->andWhere(['>=', 'dateVisit', (new DateTime($filterDateVisitFrom, new DateTimeZone('UTC')))->setTime(0, 0)->format('Y-m-d H:i:s')]);

if (!empty($filterDateVisitTo))
    $visits->andWhere(['<=', 'dateVisit', (new DateTime($filterDateVisitTo, new DateTimeZone('UTC')))->setTime(23, 59)->format('Y-m-d H:i:s')]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($visits->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$visits->offset($navigation->getOffset())
    ->limit($navigation->getPageSize())
    ->indexBy('id');

if (!empty($listSort['value']))
    $visits->orderBy([$listSort['value'] => $listOrder['value'] == 'asc' ? SORT_ASC : SORT_DESC]);

/** @var ActiveRecords $visits */
$visits = $visits->all();

$headers = [[
    'id' => 'id',
    'content' => Loc::getMessage('list.headers.id'),
    'sort' => 'id',
    'default' => true
], [
    'id' => 'sessionId',
    'content' => Loc::getMessage('list.headers.sessionId'),
    'sort' => 'sessionId',
    'default' => true
], [
    'id' => 'referrerUrl',
    'content' => Loc::getMessage('list.headers.referrerUrl'),
    'sort' => 'referrerUrl',
    'default' => true
], [
    'id' => 'pageUrl',
    'content' => Loc::getMessage('list.headers.pageUrl'),
    'sort' => 'pageUrl',
    'default' => true
], [
    'id' => 'pageCount',
    'content' => Loc::getMessage('list.headers.pageCount'),
    'sort' => 'pageCount',
    'default' => true
], [
    'id' => 'dateCreate',
    'content' => Loc::getMessage('list.headers.dateCreate'),
    'sort' => 'dateCreate',
    'default' => true
], [
    'id' => 'dateVisit',
    'content' => Loc::getMessage('list.headers.dateVisit'),
    'sort' => 'dateVisit',
    'default' => true
]];

$list->AddHeaders($headers);

$list->AddGroupActionTable([
    'delete' => Loc::getMessage('list.actions.delete')
]);

$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($visits as $visit) {
    /** @var Visit $visit */
    $actions = [];
    $actions[] = [
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('list.rows.actions.delete'),
        'ACTION' => 'if(confirm(\''.Loc::getMessage('list.rows.actions.delete.confirm').'\'))'.$list->ActionDoGroup(
                $visit->id,
                'delete'
            )
    ];

    $row = $list->AddRow($visit->id, [
        'id' => $visit->id,
        'sessionId' => $visit->sessionId,
        'referrerUrl' => $visit->referrerUrl,
        'pageUrl' => $visit->pageUrl,
        'pageCount' => $visit->pageCount,
        'dateCreate' => !empty($visit->dateCreate) ? Core::$app->formatter->asDate($visit->dateCreate, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('list.rows.answers.no').')',
        'dateVisit' => !empty($visit->dateVisit) ? Core::$app->formatter->asDate($visit->dateVisit, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('list.rows.answers.no').')'
    ]);

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
            <td><?= Loc::getMessage('filter.fields.sessionId') ?>:</td>
            <td><?= Html::textInput('filterSessionIdValue', !empty($filterSessionIdValue) ? $filterSessionIdValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.referrerUrl') ?>:</td>
            <td><?= Html::textInput('filterReferrerUrlValue', !empty($filterReferrerUrlValue) ? $filterReferrerUrlValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.pageUrl') ?>:</td>
            <td><?= Html::textInput('filterPageUrlValue', !empty($filterPageUrlValue) ? $filterPageUrlValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.dateCreatePeriod') ?>:</td>
            <td><?= CalendarPeriod(
                'filterDateCreateFrom',
                !empty($filterDateCreateFrom) ? $filterDateCreateFrom : null,
                'filterDateCreateTo',
                !empty($filterDateCreateTo) ? $filterDateCreateTo : null,
                'find_form',
                'Y'
            ) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.dateVisitPeriod') ?>:</td>
            <td><?= CalendarPeriod(
                'filterDateVisitFrom',
                !empty($filterDateVisitFrom) ? $filterDateVisitFrom : null,
                'filterDateVisitTo',
                !empty($filterDateVisitTo) ? $filterDateVisitTo : null,
                'find_form',
                'Y'
            ) ?></td>
        </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>