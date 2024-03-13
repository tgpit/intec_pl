<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\handling\Handler;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\Json;
use intec\core\helpers\Type;

if (!isset($_GET['siteId']) || !isset($_GET['templateId']))
    return;

define('SITE_ID', $_GET['siteId']);
define('SITE_TEMPLATE_ID', $_GET['templateId']);
define('STOP_STATISTICS', true);

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

if (!Loader::includeModule('intec.core'))
    return;

require_once(__DIR__.'/parts/preload.php');

$request = Core::$app->request;
$page = $request->get('page');
$page = Encoding::convert($page, null, Encoding::UTF8);

if (empty($page) && $request->getIsAjax() && $request->getIsPost()) {
    $response = [];
    $actions = $request->post('actions');
    $actions = Encoding::convert($actions, null, Encoding::UTF8);
    $handler = new Handler(
        __DIR__.'/ajax',
        'intec\template\ajax'
    );

    if (!Type::isArray($actions))
        $actions = [];

    foreach ($actions as $action) {
        if (!Type::isArray($action)) {
            $response[] = null;
            continue;
        }

        $action = ArrayHelper::merge([
            'name' => null,
            'data' => null
        ], $action);

        if (empty($action['name']) && !Type::isNumeric($action['name'])) {
            $response[] = null;
            continue;
        }

        if (!Type::isArray($action['data']))
            $action['data'] = [];

        $response[] = $handler->handle($action['name'], $action['data']);
    }

    $response = Encoding::convert($response, Encoding::UTF8, Encoding::getDefault());
    $response = Json::encode($response, 320);

    echo $response;
} else if (!empty($page)) {
    $data = $request->get();
    $data = Encoding::convert($data, null, Encoding::UTF8);

    unset($data['page']);
    unset($data['siteId']);
    unset($data['templateId']);

    $handler = new Handler(
        __DIR__.'/pages',
        'intec\template\pages'
    );

    $handler->handle($page, $data);
}
