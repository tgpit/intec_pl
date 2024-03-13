<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\db\ActiveQuery;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\seo\models\filter\Url;
use intec\seo\models\filter\url\Scan;

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

Core::$app->web->js->loadExtensions(['jquery']);

$request = Core::$app->request;

if ($request->getIsPost()) {
    $action = $request->post('action');

    if (!empty($action)) {
        $response = [
            'status' => 'error',
            'message' => 'Undefined action'
        ];

        if ($action === 'configure') {
            $total = Url::find()->where([
                'active' => 1
            ])->count();

            $total = Type::toInteger($total);

            $response = [
                'status' => 'success',
                'data' => [
                    'total' => $total
                ]
            ];
        } else if ($action === 'run') {
            $current = $request->post('current');
            $current = Type::toInteger($current);
            $count = $request->post('count');
            $count = Type::toInteger($count);

            if ($current < 0)
                $current = 0;

            if ($count < 1)
                $count = 1;

            $urls = Url::find()->where([
                'active' => 1
            ]);

            $total = $urls->count();
            $total = Type::toInteger($total);

            $urls = $urls
                ->offset($current)
                ->limit($count)
                ->all();

            foreach ($urls as $url) {
                $scan = $url->scan();

                if (!empty($scan))
                    $scan->save();

                usleep(300000);
            }

            $current = $current + $count;
            $current = Type::toInteger($current);

            if ($current > $total)
                $current = $total;

            $response = [
                'status' => 'success',
                'data' => [
                    'total' => $total,
                    'current' => $current
                ]
            ];
        } else if ($action === 'clear') {
            $scans = Scan::find()->all();

            foreach ($scans as $scan)
                $scan->delete();

            $response = [
                'status' => 'success'
            ];
        }

        echo Json::encode($response, 320, true);
        exit();
    }
}

$list = 'seo_filter_debug';
$listSort = [];
$listSort['variable'] = $list.'_by';
$listSort['value'] = $request->get($listSort['variable'], null);
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
    'filterUrlIdValue',
    'filterUrlSourceValue',
    'filterUrlTargetValue',
    'filterStatusValue'
]);

$filter = new CAdminFilter(
    $list->table_id.'_filter', [
        Loc::getMessage('filter.fields.urlId'),
        Loc::getMessage('filter.fields.urlSource'),
        Loc::getMessage('filter.fields.urlTarget'),
        Loc::getMessage('filter.fields.status')
    ]
);

$scans = Scan::findLatest()->with(['url']);

/** @var ActiveQuery $scansResult */
$scansResult = $scans->join['result'][1]['result'];
$scansResult->joinWith('url', false, 'RIGHT JOIN');

if (!empty($filterUrlIdValue))
    $scansResult->andWhere(['=', Url::tableName().'.`id`', $filterUrlIdValue]);

if (!empty($filterUrlSourceValue))
    $scansResult->andWhere(['like', Url::tableName().'.`source`', $filterUrlSourceValue]);

if (!empty($filterUrlTargetValue))
    $scansResult->andWhere(['like', Url::tableName().'.`target`', $filterUrlTargetValue]);

if (!empty($filterStatusValue))
    $scans->andWhere(['=', 'status', $filterStatusValue]);

$navigation = new PageNavigation('nav');
$navigation->setRecordCount($scans->count());
$navigation->allowAllRecords(true);
$navigation->setPageSizes([
    10, 20, 50, 100, 200, 500
]);
$navigation->initFromUri();

$scans
    ->offset($navigation->getOffset())
    ->limit($navigation->getPageSize());

$scans->indexBy('id');
$scans = $scans->all();

$headers = [[
    'id' => 'urlId',
    'content' => Loc::getMessage('list.headers.urlId'),
    'default' => true
], [
    'id' => 'urlSource',
    'content' => Loc::getMessage('list.headers.urlSource'),
    'default' => true
], [
    'id' => 'urlTarget',
    'content' => Loc::getMessage('list.headers.urlTarget'),
    'default' => true
], [
    'id' => 'date',
    'content' => Loc::getMessage('list.headers.date'),
    'default' => true
], [
    'id' => 'status',
    'content' => Loc::getMessage('list.headers.status'),
    'default' => true
], [
    'id' => 'metaTitle',
    'content' => Loc::getMessage('list.headers.metaTitle'),
    'default' => true
], [
    'id' => 'metaKeywords',
    'content' => Loc::getMessage('list.headers.metaKeywords'),
    'default' => true
], [
    'id' => 'metaDescription',
    'content' => Loc::getMessage('list.headers.metaDescription'),
    'default' => true
], [
    'id' => 'metaPageTitle',
    'content' => Loc::getMessage('list.headers.metaPageTitle'),
    'default' => true
]];

$list->AddHeaders($headers);
$list->AddAdminContextMenu();
$list->setNavigation($navigation, Loc::getMessage('list.navigation.title'));

foreach ($scans as $scan) {
    /** @var Scan $scan */
    $url = $scan->getUrl(true);

    $row = $list->AddRow($scan->id, [
        'id' => $url->id,
        'urlId' => '['.$url->id.'] '.$url->name,
        'urlSource' => $url->source,
        'urlTarget' => $url->target,
        'date' => !empty($scan->date) ? Core::$app->formatter->asDate($scan->date, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('list.rows.answers.no').')',
        'status' => $scan->status,
        'metaTitle' => $scan->metaTitle,
        'metaKeywords' => $scan->metaKeywords,
        'metaDescription' => $scan->metaDescription,
        'metaPageTitle' => $scan->metaPageTitle
    ]);

    $row->AddViewField('urlId', Html::a('['.$url->id.'] '.$url->name, StringHelper::replaceMacros($arUrlTemplates['filter.url.edit'], [
        'url' => $url->id
    ])));
}

$list->CheckListMode();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<div class="adm-detail-content-item-block" style="width: 630px; margin-bottom: 20px">
    <div id="debug-message-view">

    </div>
    <div id="debug-status-view" style="margin-bottom: 20px">
        <div id="debug-status-title" style="margin-bottom: 5px">
            <?= Loc::getMessage('panel.status.title') ?>
        </div>
        <div id="debug-status-bar" style="width: 100%" class="adm-progress-bar-outer">
            <div id="debug-status-bar-progress" style="width: 0" class="adm-progress-bar-inner"></div>
            <div id="debug-status-bar-text" style="width: 100%" class="adm-progress-bar-inner-text">0%</div>
        </div>
        <div id="debug-status-message" style="display: none; margin-top: 5px"></div>
    </div>
    <div id="debug-control-view">
        <table>
            <tr>
                <td style="padding-bottom: 10px; padding-right: 10px">
                    <?= Loc::getMessage('panel.control.option.count') ?>
                </td>
                <td style="padding-bottom: 10px">
                    <input id="debug-control-option-count" type="text" value="20" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="margin: -2px">
                        <span id="debug-control-button-start" class="adm-btn adm-btn-green" style="margin: 2px">
                            <?= Loc::getMessage('panel.control.button.start') ?>
                        </span>
                        <span id="debug-control-button-clear" class="adm-btn" style="margin: 2px">
                            <?= Loc::getMessage('panel.control.button.clear') ?>
                        </span>
                        <span id="debug-control-button-stop" class="adm-btn" style="display: none; margin: 2px">
                            <?= Loc::getMessage('panel.control.button.stop') ?>
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <script type="text/javascript">
        (function ($) {
            $(function () {
                var debug;
                var request;
                var messages;

                messages = <?= JavaScript::toObject([
                    'configuring' => Loc::getMessage('panel.status.messages.configuring'),
                    'progress' => Loc::getMessage('panel.status.messages.progress'),
                    'clearing' => Loc::getMessage('panel.status.messages.clearing'),
                    'complete' => Loc::getMessage('panel.status.messages.complete'),
                    'error' => Loc::getMessage('panel.status.messages.error')
                ]) ?>;

                debug = {};
                debug.current = 0;
                debug.total = 0;
                debug.state = null;
                debug.nodes = {
                    'message': {
                        'view': $('#debug-message-view')
                    },
                    'status': {
                        'view': $('#debug-status-view'),
                        'title': $('#debug-status-title'),
                        'bar': {
                            'root': $('#debug-status-bar'),
                            'progress': $('#debug-status-bar-progress'),
                            'text': $('#debug-status-bar-text')
                        },
                        'message': $('#debug-status-message')
                    },
                    'control': {
                        'view': $('#debug-control-view'),
                        'options': {
                            'count': $('#debug-control-option-count')
                        },
                        'buttons': {
                            'start': $('#debug-control-button-start'),
                            'clear': $('#debug-control-button-clear'),
                            'stop': $('#debug-control-button-stop')
                        }
                    }
                };

                request = function (action, parameters, callback, error) {
                    var data = $.extend({}, parameters, {
                        'action': action
                    });

                    $.ajax({
                        'cache': false,
                        'data': data,
                        'dataType': 'json',
                        'type': 'POST',
                        'success': function (result) {
                            if (result.status === 'success') {
                                if ($.isFunction(callback))
                                    callback.call(window, result.data);
                            } else if (result.status === 'error') {
                                if ($.isFunction(error))
                                    error.call(window, result.message ? result.message : null);
                            }
                        },
                        'error': function (error) {
                            console.error(error);

                            if ($.isFunction(error))
                                error.call(window, null);
                        }
                    })
                };

                debug.getProgress = function () {
                    var result = 0;

                    if (debug.total === 0) {
                        result = 100;
                    } else {
                        result = Math.floor(debug.current / debug.total * 100);

                        if (result > 100)
                            result = 100;
                    }

                    return result;
                };

                debug.start = function () {
                    if (debug.state !== null)
                        return;

                    debug.state = 'running';
                    debug.count = debug.nodes.control.options.count.val();
                    debug.count = parseInt(debug.count);

                    if (debug.count < 1 || isNaN(debug.count))
                        debug.count = 1;

                    request('configure', {}, function (data) {
                        var handler;

                        debug.total = data.total;
                        debug.setMessage(messages.configuring);
                        debug.refresh();

                        if (debug.state !== 'running')
                            return;

                        if (debug.total === 0) {
                            debug.setMessage(messages.complete);
                            window.location.reload();

                            return;
                        }

                        handler = function () {
                            debug.setMessage(messages.progress.replace('#count#', debug.current).replace('#total#', debug.total));

                            if (debug.state !== 'running')
                                return;

                            request('run', {
                                'current': debug.current,
                                'count': debug.count
                            }, function (data) {
                                debug.total = data.total;
                                debug.current = data.current;

                                debug.refresh();

                                if (debug.current >= debug.total) {
                                    debug.setMessage(messages.complete);
                                    window.location.reload();

                                    return;
                                }

                                handler();
                            }, debug.error);
                        };

                        handler();
                    }, debug.error);
                };

                debug.clear = function () {
                    if (debug.state !== null)
                        return;

                    debug.state = 'clearing';
                    debug.setMessage(messages.clearing);
                    debug.current = 0;
                    debug.total = 1;
                    debug.refresh();

                    request('clear', {}, function () {
                        debug.current = 0;
                        debug.total = 0;
                        debug.setMessage(messages.complete);
                        debug.refresh();
                        window.location.reload();
                    }, debug.error)
                };

                debug.setMessage = function (text) {
                    var node = debug.nodes.status.message;

                    node.css({'display': text ? '' : 'none'});
                    node.text(text);
                };

                debug.error = function (message) {
                    debug.state = null;

                    if (message === null)
                        message = messages.error;

                    debug.setMessage(message);
                    debug.refresh();
                };

                debug.refresh = function () {
                    var progress = debug.getProgress();

                    debug.nodes.status.bar.text.text(progress + '%');
                    debug.nodes.status.bar.progress.css({
                        'width': Math.round(((debug.nodes.status.bar.root.width() - 4) * progress / 100)) + 'px'
                    });

                    if (debug.state === 'running') {
                        debug.nodes.control.options.count.prop('disabled', true);
                        debug.nodes.control.buttons.start.css({'display': 'none'});
                        debug.nodes.control.buttons.clear.css({'display': 'none'});
                        debug.nodes.control.buttons.stop.css({'display': ''});
                    } else if (debug.state === 'clearing') {
                        debug.nodes.control.options.count.prop('disabled', true);
                        debug.nodes.control.buttons.start.css({'display': 'none'});
                        debug.nodes.control.buttons.clear.css({'display': 'none'});
                        debug.nodes.control.buttons.stop.css({'display': 'none'});
                    } else {
                        debug.nodes.control.options.count.prop('disabled', false);
                        debug.nodes.control.buttons.start.css({'display': ''});
                        debug.nodes.control.buttons.clear.css({'display': ''});
                        debug.nodes.control.buttons.stop.css({'display': 'none'});
                    }
                };

                debug.stop = function () {
                    debug.state = null;
                };

                debug.nodes.control.buttons.start.on('click', debug.start);
                debug.nodes.control.buttons.stop.on('click', debug.stop);
                debug.nodes.control.buttons.clear.on('click', debug.clear);
            });
        })(jQuery);
    </script>
</div>
<form name="find_form" method="get" action="<?= '/'.$request->getPathInfo() ?>">
    <?php $filter->Begin() ?>
        <tr>
            <td><?= Loc::getMessage('filter.fields.urlId') ?>:</td>
            <td><?= Html::textInput('filterUrlIdValue', !empty($filterUrlIdValue) ? $filterUrlIdValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.urlSource') ?>:</td>
            <td><?= Html::textInput('filterUrlSourceValue', !empty($filterUrlSourceValue) ? $filterUrlSourceValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.urlTarget') ?>:</td>
            <td><?= Html::textInput('filterUrlTargetValue', !empty($filterUrlTargetValue) ? $filterUrlTargetValue : null) ?></td>
        </tr>
        <tr>
            <td><?= Loc::getMessage('filter.fields.status') ?>:</td>
            <td><?= Html::textInput('filterStatusValue', !empty($filterStatusValue) ? $filterStatusValue : null) ?></td>
        </tr>
    <?php $filter->Buttons(['table_id' => $list->table_id, 'url' => '/'.$request->getPathInfo(), 'form' => 'find_form']) ?>
    <?php $filter->End() ?>
</form>
<?php $list->DisplayList() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
