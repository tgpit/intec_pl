<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @global $APPLICATION
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\constructor\models\Build;
use intec\template\Template;

if (!Loader::includeModule('intec.core')) {
    echo Loc::getMessage('template.errors.module', ['#MODULE#' => 'intec.core']);
    die();
}

if (!Loader::includeModule('intec.universe')) {
    echo Loc::getMessage('template.errors.module', ['#MODULE#' => 'intec.universe']);
    die();
}

if (
    !Loader::includeModule('intec.constructor') &&
    !Loader::includeModule('intec.constructorlite')
) {
    echo Loc::getMessage('template.errors.modules', ['#MODULES#' => '"intec.constructor", "intec.constructorlite"']);
    die();
}

IntecUniverse::Initialize();

global $APPLICATION;
global $USER;
global $directory;
global $properties;
global $template;
global $part;

$build = Build::getCurrent();

if (empty($build)) {
    echo Loc::getMessage('template.errors.build');
    die();
}

$directory = $build->getDirectory();

Core::setAlias('@intec/template', $directory.'/classes');
require($directory.'/helper/functions.php');

$areas = $APPLICATION->GetShowIncludeAreas();
$APPLICATION->SetShowIncludeAreas(false);
$menu = new CMenu('left');
$page = $build->getPage();
$properties = $page->getProperties();
$properties
    ->setRange([
        'template-images-lazyload-stub' => SITE_TEMPLATE_PATH.'/images/picture.loading.svg',
        'template-breadcrumb-show' => true,
        'template-title-show' => true,
        'template-page-type' => null
    ])
    ->setRange($APPLICATION->IncludeComponent(
        'intec.universe:system.settings',
        '.default',
        [
            'MODE' => 'configure'
        ],
        false,
        ['HIDE_ICONS' => 'Y']
    ))
    ->setRange([
        'template-menu-show' =>
            $page->getProperties()->get('template-menu-show') &&
            $menu->Init($APPLICATION->GetCurDir(), true) &&
            !empty($menu->arMenu),
        'base-settings-show' => IntecUniverse::SettingsDisplay(null, SITE_ID),
        'yandex-metrika-use' => IntecUniverse::YandexMetrikaUse(null, SITE_ID),
        'yandex-metrika-id' => IntecUniverse::YandexMetrikaId(null, SITE_ID),
        'yandex-metrika-click-map' => IntecUniverse::YandexMetrikaClickMap(null, SITE_ID),
        'yandex-metrika-ecommerce' => IntecUniverse::YandexMetrikaEcommerce(null, SITE_ID),
        'yandex-metrika-track-hash' => IntecUniverse::YandexMetrikaTrackHash(null, SITE_ID),
        'yandex-metrika-track-links' => IntecUniverse::YandexMetrikaTrackLinks(null, SITE_ID),
        'yandex-metrika-track-webvisor' => IntecUniverse::YandexMetrikaWebvisor(null, SITE_ID),
        'google-tag-use' => IntecUniverse::GoogleTagUse(null, SITE_ID),
        'google-tag-id' => IntecUniverse::GoogleTagId(null, SITE_ID),
        'base-optimization-use' => IntecUniverse::OptimizationUse(null, SITE_ID)
    ]);

$properties->set('base-optimization-use', $properties->get('base-optimization-use') && Core::$app->browser->isMatch('lighthouse'));

if ($properties->get('base-optimization-use')) {
    $properties->set('base-settings-show', 'none');
    $properties->set('yandex-metrika-use', false);

    AddEventHandler('main', 'OnEndBufferContent', [Template::className(), 'optimize']);
}

if ($APPLICATION->GetCurPage(false) === '/') {
    if ($properties->get('template-page-type') === null) {
        if ($properties->get('pages-main-template') === 'narrow.left') {
            $properties->set('template-page-type', 'narrow');
        } else {
            $properties->set('template-page-type', 'flat');
        }
    }
}

$APPLICATION->SetShowIncludeAreas($areas);

unset($areas);
unset($menu);
