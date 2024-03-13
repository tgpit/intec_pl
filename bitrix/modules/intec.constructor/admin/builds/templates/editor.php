<?php include(__DIR__.'/editor/environment.php') ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php') ?>
<?php

define('ADMIN_SECTION', true);

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use intec\Core;
use intec\core\handling\Handler;
use intec\core\helpers\Encoding;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;use intec\core\web\assets\vue\Application;
use intec\constructor\models\Build;
use intec\constructor\models\build\File;
use intec\constructor\models\build\Template as BuildTemplate;
use intec\constructor\models\build\layout\renderers\EditorRenderer;
use intec\constructor\models\build\layout\Zone;

if (!Loader::includeModule('intec.constructor'))
    return;

include(Core::getAlias('@intec/constructor/module/admin/url.php'));

Core::$app->web->js->loadExtensions(['intec_core', 'interact', 'axios', 'qs', 'velocity', 'vue', 'vue_vuetify', 'vue_vuescroll', 'vue_script2', 'vue_vuedraggable']);
Core::$app->web->css->addFile('@intec/core/resources/icons/materialdesign/style.css');
Core::$app->web->css->addFile('@intec/constructor/resources/css/editor.template.css');
Core::$app->web->css->addFile('@intec/constructor/resources/icons/fontawesome/style.css');

$application = new Application(__DIR__.'/editor', 'intec-editor');
$request = Core::$app->request;
$action = $request->get('action');
$build = $request->get('build');
$build = Build::find()
    ->where(['id' => $build])
    ->one();
/** @var Build $build */

if (empty($build))
    LocalRedirect($arUrlTemplates['builds']);

$files = $build->getFiles();

$template = $request->get('template');
$template = BuildTemplate::findOne($template);
/** @var BuildTemplate $template */

if (empty($template))
    LocalRedirect(
        StringHelper::replaceMacros(
            $arUrlTemplates['builds.templates'],
            array(
                'build' => $build->id
            )
        )
    );

$localization = Loc::loadLanguageFile(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title', [
    '#template#' => $template->name
]));

/** Обработка запросов AJAX */
if ($request->getIsPost() && $request->getIsAjax()) {
    ini_set('max_execution_time', 0);

    $response = null;
    $action = $request->post('action');
    
    if (empty($action))
        return;

    require(__DIR__.'/editor/ajax/Actions.php');

    $handler = new Handler(
        __DIR__.'/editor/ajax',
        'intec\constructor\builds\templates\editor\ajax'
    );

    $response = $handler->handle($action);
    $response = Encoding::convert($response, Encoding::UTF8, Encoding::getDefault());

    echo Json::encode($response);
    return;
} else if (!empty($action)) { /** Обработка обычных запросов */
    ini_set('max_execution_time', 0);

    require(__DIR__.'/editor/actions/Actions.php');

    $handler = new Handler(
        __DIR__.'/editor/actions',
        'intec\constructor\builds\templates\editor\actions'
    );

    $handler->handle($action);

    return;
}

$data = [
    'id' => $build->id,
    'code' => $build->code,
    'name' => $build->name,
    'path' => $build->getDirectory(false, true, '/'),
    'template' => [
        'id' => $template->id,
        'name' => $template->name,
        'layout' => [
            'code' => null,
            'name' => 'Work area',
            'picture' => null,
            'zones' => [[
                'code' => 'default',
                'name' => 'Default'
            ]]
        ],
        'settings' => $template->settings
    ],
    'settings' => [
        'requestQueriesCount' => Option::get('intec.constructor', 'editorRequestQueriesCount', 3),
        'requestQueriesInterval' => Option::get('intec.constructor', 'editorRequestQueriesInterval', 250)
    ]
];

$data['settings']['requestQueriesCount'] = Type::toInteger($data['settings']['requestQueriesCount']);
$data['settings']['requestQueriesInterval'] = Type::toInteger($data['settings']['requestQueriesInterval']);

$layout = $template->getLayout();

if (!empty($layout)) {
    $data['template']['layout'] = [
        'code' => $layout->getCode(),
        'name' => $layout->getName(),
        'picture' => $layout->getPicturePath()->toRelative()->asAbsolute()->getValue('/'),
        'zones' => []
    ];

    $layout->getZones()->each(function ($code, $zone) use (&$data) {
        /**
         * @var string $code
         * @var Zone $zone
         */

        $data['template']['layout']['zones'][] = [
            'code' => $zone->getCode(),
            'name' => $zone->getName()
        ];
    });
}

$data['localization'] = $localization;
$data['links'] = $arUrlTemplates;

$components = [
    'renderer' => $application->useComponent('renderer'),
    'slot' => $application->useComponent('slot'),
    'interface-dialogs-area-select' => $application->useComponent('interface-dialogs-area-select'),
    'interface-dialogs-block-convert' => $application->useComponent('interface-dialogs-block-convert'),
    'interface-dialogs-component-list' => $application->useComponent('interface-dialogs-component-list'),
    'interface-dialogs-component-settings' => $application->useComponent('interface-dialogs-component-settings'),
    'interface-dialogs-container-conditions' => $application->useComponent('interface-dialogs-container-conditions'),
    'interface-dialogs-container-paste' => $application->useComponent('interface-dialogs-container-paste'),
    'interface-dialogs-container-script' => $application->useComponent('interface-dialogs-container-script'),
    'interface-dialogs-container-structure' => $application->useComponent('interface-dialogs-container-structure'),
    'interface-dialogs-confirm' => $application->useComponent('interface-dialogs-confirm'),
    'interface-dialogs-gallery' => $application->useComponent('interface-dialogs-gallery'),
    'interface-menu' => $application->useComponent('interface-menu'),
    'editor-layout' => $application->useComponent('editor-layout'),
    'editor-layout-zone' => $application->useComponent('editor-layout-zone')
];

?><!DOCTYPE html>
<html>
    <head>
        <title><?= $APPLICATION->ShowTitle(false) ?></title>
        <?php $APPLICATION->ShowHead() ?>
        <?php foreach ($files as $file) { ?>
            <?php if ($file->getType() === File::TYPE_JAVASCRIPT) { ?>
                <script type="text/javascript" src="<?= $file->getPath(true, '/') ?>"></script>
            <?php } else if ($file->getType() === File::TYPE_CSS) { ?>
                <link rel="stylesheet" href="<?= $file->getPath(true, '/') ?>" />
            <?php } else if ($file->getType() === File::TYPE_SCSS) { ?>
                <style type="text/css"><?= Core::$app->web->scss->compileFile($file->getPath(), null, $properties) ?></style>
            <?php } else if ($file->getType() === File::TYPE_VIRTUAL) { ?>
                <?= $file->getContent() ?>
            <?php } ?>
        <?php } ?>
        <style type="text/css"><?= $template->getCss() ?></style>
        <style type="text/css"><?= $template->getLess() ?></style>
    </head>
    <body>
        <?= Html::beginTag('div', [
            'id' => 'editor',
            'class' => [
                'intec-editor',
                'intec-editor-template'
            ],
            'v-bind:data-menu-expanded' => 'interface.menu && interface.menu.tab && interface.menu.tab.isActive ? "true" : "false"',
            'v-bind:data-containers-structure-show' => 'template.settings.containersStructureShow ? "true" : "false"'
        ]) ?>

            <v-app class="intec-editor-wrapper" ref="app">
                <div v-if="$root.isSaving" class="intec-editor-overlay-save intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-a-h-center">
                    <div class="intec-editor-grid-item-auto">
                        <?= Html::tag('component', null, [
                            'is' => 'v-progress-circular',
                            'width' => '6',
                            'size' => '80',
                            'color' => '#3A86FF',
                            'indeterminate' => true
                        ]) ?>
                        <div class="intec-editor-overlay-save-text">
                            {{ $root.$localization.getMessage('application.overlay.save') }}
                        </div>
                    </div>
                </div>
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-editor-panel',
                        'intec-editor-grid' => [
                            '',
                            'nowrap',
                            'a-h-start',
                            'a-v-start'
                        ]
                    ]
                ]) ?>
                    <div class="intec-editor-panel-content intec-editor-panel-content-left intec-editor-grid-item">
                        <div class="intec-editor-grid intec-editor-grid-a-v-center">
                            <div class="intec-editor-grid-item">
                                <div class="intec-editor-panel-breadcrumb intec-editor-grid intec-editor-grid-i-h-20 intec-editor-grid-a-v-center">
                                    <div class="intec-editor-panel-breadcrumb-item intec-editor-grid-item-auto">
                                        <a href="<?= StringHelper::replaceMacros($arUrlTemplates['builds.edit'], [
                                            'build' => $build->id
                                        ]) ?>" class="intec-editor-panel-breadcrumb-item-content">
                                            <?= Html::encode($build->name) ?>
                                        </a>
                                    </div>
                                    <div class="intec-editor-panel-breadcrumb-item intec-editor-grid-item-auto">
                                        <a href="<?= StringHelper::replaceMacros($arUrlTemplates['builds.templates.edit'], [
                                            'build' => $build->id,
                                            'template' => $template->id
                                        ]) ?>" class="intec-editor-panel-breadcrumb-item-content">
                                            <?= Html::encode($template->name) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-panel-height intec-editor-grid-item">
                                <div class="intec-editor-panel-buttons intec-editor-panel-height intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-a-h-end">
                                    <div class="intec-editor-panel-height intec-editor-grid-item-auto">
                                        <button class="intec-editor-panel-button intec-editor-panel-height" v-if="$root.isBufferFilled" v-on:click="clearBuffer">
                                            <span class="intec-editor-panel-button-content intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                                <span class="intec-editor-panel-button-icon intec-editor-grid-item-auto">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M19.3601 2.72L20.7801 4.14L15.0601 9.85C16.1301 11.39 16.2801 13.24 15.3801 14.44L9.0601 8.12C10.2601 7.22 12.1101 7.37 13.6501 8.44L19.3601 2.72ZM5.9301 17.57C3.9201 15.56 2.6901 13.16 2.3501 10.92L7.2301 8.83L14.6701 16.27L12.5801 21.15C10.3401 20.81 7.9401 19.58 5.9301 17.57Z" fill="#8F8F8F"/>
                                                    </svg>
                                                </span>
                                                <span class="intec-editor-panel-button-text intec-editor-grid-item">
                                                    {{ $root.$localization.getMessage('panel.buttons.buffer') }}
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                    <div class="intec-editor-panel-height intec-editor-grid-item-auto">
                                        <button v-on:click="$root.save" class="intec-editor-panel-button intec-editor-panel-height">
                                            <span class="intec-editor-panel-button-content intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                                <span class="intec-editor-panel-button-icon intec-editor-grid-item-auto">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M16.4444 4H5.77778C4.79111 4 4 4.8 4 5.77778V18.2222C4 19.2 4.79111 20 5.77778 20H18.2222C19.2 20 20 19.2 20 18.2222V7.55556L16.4444 4ZM12 18.2222C10.5244 18.2222 9.33333 17.0311 9.33333 15.5556C9.33333 14.08 10.5244 12.8889 12 12.8889C13.4756 12.8889 14.6667 14.08 14.6667 15.5556C14.6667 17.0311 13.4756 18.2222 12 18.2222ZM14.6667 9.33333H5.77778V5.77778H14.6667V9.33333Z" fill="#8F8F8F"/>
                                                    </svg>
                                                </span>
                                                <span class="intec-editor-panel-button-text intec-editor-grid-item">
                                                    {{ $root.$localization.getMessage('panel.buttons.save') }}
                                                </span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-panel-content intec-editor-panel-content-right intec-editor-grid-item-auto">

                    </div>
                <?= Html::endTag('div') ?>
                <?= $components['interface-menu']->begin([
                    'ref' => 'menu'
                ]) ?>
                    <?php include(__DIR__.'/editor/tabs.php') ?>
                <?= $components['interface-menu']->end() ?>
                <div class="intec-editor-menu">
                    <div class="intec-editor-menu-wrapper">
                        <div class="intec-editor-menu-items" data-position="top">
                            <div class="intec-editor-menu-item">
                                <div class="intec-editor-menu-item-wrapper">
                                    <div class="intec-editor-menu-item-wrapper-2">
                                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M21.0248 0.415161H3.37161C1.79365 0.415161 0.514465 1.69435 0.514465 3.2723V20.9255C0.514465 22.5035 1.79365 23.7826 3.37161 23.7826H21.0248C22.6028 23.7826 23.882 22.5035 23.882 20.9255V3.2723C23.882 1.69435 22.6028 0.415161 21.0248 0.415161Z" stroke="transparent" fill="#9E2D6B"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.4513 4.57422C8.33216 4.57422 9.11513 5.2593 9.11513 6.04228C9.11513 6.92313 8.33216 7.60828 7.4513 7.60828C6.57045 7.60828 5.78748 6.92313 5.78748 6.04228C5.78748 5.2593 6.47256 4.57422 7.4513 4.57422ZM12.247 7.90188C14.2045 7.99976 16.1619 7.99976 18.2172 7.90188C18.413 7.90188 18.6087 8.09765 18.6087 8.39125V9.95719C18.6087 10.153 18.413 10.4466 18.2172 10.4466C17.7279 10.4466 17.3364 10.3487 16.847 10.3487C16.847 13.187 16.7492 16.0253 16.7492 18.8636C16.7492 19.0594 16.6513 19.0594 16.5534 19.0594C16.4555 19.0594 16.2598 19.0594 15.9662 19.0594C15.7704 19.0594 15.4768 19.0594 15.379 19.0594C15.1832 19.0594 14.9875 19.0594 14.7917 18.9615C14.4981 18.9615 14.3024 18.9615 14.2045 18.9615C14.1066 18.9615 14.0087 18.8636 13.813 18.7657C13.4215 18.2764 12.5407 17.1019 11.2683 15.438C10.8768 14.9487 10.3875 14.2636 9.6045 13.2848L9.50662 13.187C9.50662 13.187 9.40873 13.2848 9.40873 13.3827C9.40873 13.97 9.40873 14.9487 9.50662 16.1232C9.50662 17.2976 9.6045 18.2764 9.6045 18.8636C9.6045 19.0594 9.50662 19.1572 9.40873 19.1572H6.66828C6.47256 19.1572 6.47256 19.0594 6.47256 18.9615C6.47256 15.438 6.47256 12.0125 6.47256 8.48913C6.47256 8.39125 6.57045 8.29336 6.76616 8.29336C7.64702 8.39125 8.42999 8.19548 9.31085 8.09765C9.40873 8.09765 9.50662 8.19548 9.6045 8.39125L12.5407 12.3062C12.8343 12.6976 13.2258 13.187 13.7151 13.8721C13.813 14.0679 13.9108 14.0679 14.0087 14.0679C14.1066 14.0679 14.1066 13.8721 14.1066 13.4806C14.1066 12.8934 14.1066 12.1104 14.0087 11.0338C14.0087 10.838 14.0087 10.5444 13.9108 10.3487C13.4215 10.3487 13.03 10.4466 12.5407 10.4466C12.3449 10.4466 12.1492 10.2508 12.1492 9.95719V8.29336C11.8555 8.09765 12.0513 7.90188 12.247 7.90188Z" stroke="transparent" fill="white"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <?/*<template v-if="interface.menu">
                                <template v-for="tab in [interface.menu.getTab('tree')]">
                                    <component is="v-interface-menu-item" v-bind:active="tab.isActive" v-bind:interactive="true" v-bind:name="tab.name" v-on:click="tab.toggle()">
                                        <template v-slot:icon>
                                            <?= $components['slot']->apply([
                                                'v-bind:slot' => 'tab.icon'
                                            ]) ?>
                                        </template>
                                    </component>
                                </template>
                            </template>*/?>
                        </div>
                        <div class="intec-editor-menu-delimiter"></div>
                        <div class="intec-editor-menu-items" data-position="bottom">
                            <template v-if="interface.menu">
                                <template v-for="tab in [
                                    interface.menu.getTab('settings')
                                ]">
                                    <component is="v-interface-menu-item" v-bind:active="tab.isActive" v-bind:interactive="true" v-bind:name="tab.name" v-on:click="tab.toggle()">
                                        <template v-slot:icon>
                                            <?= $components['slot']->apply([
                                                'v-bind:slot' => 'tab.icon'
                                            ]) ?>
                                        </template>
                                    </component>
                                </template>
                            </template>
                            <component is="v-interface-menu-item" v-bind:active="template.settings.containersHiddenShow" v-bind:interactive="true" v-bind:name="$localization.getMessage('menu.items.hiding.name')" v-on:click="template.settings.containersHiddenShow = !template.settings.containersHiddenShow">
                                <template v-slot:icon>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11.8455 9.36842L14.7273 12.1958V12.0526C14.7273 11.3407 14.4399 10.658 13.9285 10.1546C13.417 9.65122 12.7233 9.36842 12 9.36842H11.8455ZM7.93636 10.0842L9.34545 11.4711C9.3 11.6589 9.27273 11.8468 9.27273 12.0526C9.27273 12.7645 9.56006 13.4473 10.0715 13.9507C10.583 14.454 11.2767 14.7368 12 14.7368C12.2 14.7368 12.4 14.71 12.5909 14.6653L14 16.0521C13.3909 16.3474 12.7182 16.5263 12 16.5263C10.7945 16.5263 9.63832 16.055 8.78588 15.216C7.93344 14.377 7.45455 13.2391 7.45455 12.0526C7.45455 11.3458 7.63636 10.6837 7.93636 10.0842ZM2.90909 5.13632L4.98182 7.17632L5.39091 7.57895C3.89091 8.74211 2.70909 10.2632 2 12.0526C3.57273 15.9805 7.45455 18.7632 12 18.7632C13.4091 18.7632 14.7545 18.4947 15.9818 18.0116L16.3727 18.3874L19.0273 21L20.1818 19.8637L4.06364 4L2.90909 5.13632ZM12 7.57895C13.2055 7.57895 14.3617 8.05028 15.2141 8.88926C16.0666 9.72824 16.5455 10.8661 16.5455 12.0526C16.5455 12.6253 16.4273 13.18 16.2182 13.6811L18.8818 16.3026C20.2455 15.1842 21.3364 13.7168 22 12.0526C20.4273 8.12474 16.5455 5.34211 12 5.34211C10.7273 5.34211 9.50909 5.56579 8.36364 5.96842L10.3364 7.89211C10.8545 7.69526 11.4091 7.57895 12 7.57895Z" stroke="none" />
                                    </svg>
                                </template>
                            </component>
                            <component is="v-interface-menu-item" v-bind:active="template.settings.containersStructureShow" v-bind:interactive="true" v-bind:name="$localization.getMessage('menu.items.structure.show')" v-on:click="template.settings.containersStructureShow = !template.settings.containersStructureShow">
                                <template v-slot:icon>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M23.1428 17.1428H21.4286V13.7142C21.4286 13.2408 21.0448 12.8571 20.5714 12.8571H12.8571V6.85712H17.1428C17.6162 6.85712 18 6.47337 18 5.99996V0.857109C18 0.383755 17.6162 0 17.1428 0H6.85712C6.38372 0 5.99996 0.383755 5.99996 0.857159V6.00001C5.99996 6.47342 6.38372 6.85717 6.85712 6.85717H11.1428V12.8571H3.42854C2.95513 12.8571 2.57138 13.2409 2.57138 13.7143V17.1429H0.857159C0.383755 17.1428 0 17.5266 0 18V23.1428C0 23.6162 0.383755 24 0.857159 24H6.00001C6.47342 24 6.85717 23.6162 6.85717 23.1428V18C6.85717 17.5266 6.47342 17.1428 6.00001 17.1428H4.28569V14.5714H11.1428V17.1428H9.42855C8.95514 17.1428 8.57139 17.5266 8.57139 18V23.1428C8.57139 23.6162 8.95514 24 9.42855 24H14.5714C15.0448 24 15.4286 23.6162 15.4286 23.1428V18C15.4286 17.5266 15.0448 17.1428 14.5714 17.1428H12.8571V14.5714H19.7143V17.1428H18C17.5266 17.1428 17.1428 17.5266 17.1428 18V23.1428C17.1428 23.6162 17.5266 24 18 24H23.1428C23.6162 24 24 23.6162 24 23.1428V18C24 17.5266 23.6162 17.1428 23.1428 17.1428Z" stroke="none"/>
                                    </svg>
                                </template>
                            </component>
                            <component is="v-interface-menu-item" v-bind:active="template.settings.developmentMode" v-bind:interactive="true" v-bind:name="$localization.getMessage('menu.items.development.mode')" v-on:click="template.settings.developmentMode = !template.settings.developmentMode">
                                <template v-slot:icon>
                                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M2.86458 0H22.1354C23.7156 0 25 1.28438 25 2.86458V22.1354C25 23.7156 23.7156 25 22.1354 25H2.86458C1.28437 25 0 23.7156 0 22.1354V2.86458C0 1.28438 1.28437 0 2.86458 0ZM10.199 8.36771C10.0365 7.81563 10.3521 7.23646 10.9042 7.07396C11.4542 6.91042 12.0344 7.22605 12.1979 7.77917L14.8021 16.6333C14.9646 17.1854 14.649 17.7646 14.0969 17.9271C13.5469 18.0896 12.9667 17.774 12.8031 17.2219L10.199 8.36771ZM6.81563 8.63854C7.01876 8.43541 7.28542 8.33333 7.55209 8.33333C7.81876 8.33333 8.08542 8.43541 8.28855 8.63854C8.69584 9.04583 8.69584 9.70416 8.28855 10.1115L5.9 12.5L8.28855 14.8885C8.69584 15.2958 8.69584 15.9542 8.28855 16.3615C7.88126 16.7687 7.22292 16.7687 6.81563 16.3615L3.69063 13.2365C3.28334 12.8292 3.28334 12.1708 3.69063 11.7635L6.81563 8.63854ZM17.4479 8.33333C17.1812 8.33333 16.9146 8.43541 16.7114 8.63854C16.3041 9.04583 16.3041 9.70416 16.7114 10.1115L19.1 12.5L16.7114 14.8885C16.3041 15.2958 16.3041 15.9542 16.7114 16.3615C17.1187 16.7687 17.7771 16.7687 18.1844 16.3615L21.3094 13.2365C21.7167 12.8292 21.7167 12.1708 21.3094 11.7635L18.1844 8.63854C17.9812 8.43541 17.7146 8.33333 17.4479 8.33333Z" stroke="none"/>
                                    </svg>
                                </template>
                            </component>
                            <component is="v-interface-menu-item" v-bind:interactive="true" v-bind:name="$localization.getMessage('menu.items.exit.name')" link="<?= StringHelper::replaceMacros($arUrlTemplates['builds.templates'], [
                                'build' => $build->id
                            ]) ?>">
                                <template v-slot:icon>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.09 15.59L11.5 17L16.5 12L11.5 7L10.09 8.41L12.67 11H3V13H12.67L10.09 15.59ZM19 3H5C4.46957 3 3.96086 3.21071 3.58579 3.58579C3.21071 3.96086 3 4.46957 3 5V9H5V5H19V19H5V15H3V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3Z" stroke="none" />
                                    </svg>
                                </template>
                            </component>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-area">
                    <div class="intec-editor-loader intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-a-h-center" v-if="$root.isBusy">
                        <div class="intec-editor-grid-item-auto">
                            <?= Html::tag('component', null, [
                                'is' => 'v-progress-circular',
                                'width' => '6',
                                'size' => '80',
                                'color' => '#3A86FF',
                                'indeterminate' => true
                            ]) ?>
                        </div>
                    </div>
                    <div class="intec-editor-area-wrapper" v-else>
                        <div class="intec-editor-area-content">
                            <div class="intec-editor-area-content-wrapper">
                                <?= $components['editor-layout']->begin([
                                    'ref' => 'layout',
                                    'v-bind:model' => 'template.layout'
                                ]) ?>
                                    <template v-slot:default="{ layout }">
                                        <?php if (!empty($layout)) {
                                            $layout->render(new EditorRenderer(), $template);
                                        } else { ?>
                                            <?= $components['editor-layout-zone']->apply([
                                                'v-bind:model' => 'layout.getZone(\'default\')'
                                            ]) ?>
                                        <? } ?>
                                    </template>
                                <?= $components['editor-layout']->end() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-modals">
                    <?= $components['interface-dialogs-area-select']->apply(['ref' => 'dialogsAreaSelect', 'v-bind:areas' => 'availableAreas']) ?>
                    <?= $components['interface-dialogs-block-convert']->apply(['ref' => 'dialogsBlockConvert']) ?>
                    <?= $components['interface-dialogs-component-list']->apply(['ref' => 'dialogsComponentList']) ?>
                    <?= $components['interface-dialogs-component-settings']->apply(['ref' => 'dialogsComponentSettings']) ?>
                    <?= $components['interface-dialogs-gallery']->apply(['ref' => 'dialogsGallery']) ?>
                    <?= $components['interface-dialogs-container-conditions']->apply(['ref' => 'dialogsContainerConditions']) ?>
                    <?= $components['interface-dialogs-container-paste']->apply(['ref' => 'dialogsContainerPaste']) ?>
                    <?= $components['interface-dialogs-container-script']->apply(['ref' => 'dialogsContainerScript']) ?>
                    <?= $components['interface-dialogs-container-structure']->apply(['ref' => 'dialogsContainerStructure']) ?>
                </div>
            </v-app>
        <?= Html::endTag('div') ?>
        <?php require(__DIR__.'/editor/script.php') ?>
        <?= $application->render() ?>
    </body>
</html>