<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\SiteDomainTable;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\core\net\Url;
use intec\seo\models\filter\Sitemap;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

$request = Core::$app->request;
$error = null;

/** @var Sitemap $sitemap */
$sitemap = $request->get('sitemap');
$site = null;

if (!empty($sitemap)) {
    $sitemap = Sitemap::findOne($sitemap);

    if (!empty($sitemap))
        $site = CSite::GetByID($sitemap->siteId)->Fetch();

    if (empty($sitemap) || empty($site))
        LocalRedirect($arUrlTemplates['filter.sitemap']);
} else {
    $site = $request->get('site');

    if (!empty($site))
        $site = CSite::GetByID($site)->Fetch();

    if (empty($site))
        LocalRedirect($arUrlTemplates['filter.sitemap']);

    $sitemap = new Sitemap();
    $sitemap->loadDefaultValues();
    $sitemap->siteId = $site['ID'];
}

if (!$sitemap->getIsNewRecord())
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));

$active = $sitemap->getActive();
$path = Path::from($site['ABS_DOC_ROOT'].'/'.$site['DIR']);
$files = [];
$entries = FileHelper::getDirectoryEntries($path->getValue(), false);

foreach ($entries as $entry) {
    $entryPath = $path->add($entry);

    if ($entryPath->getExtension() !== 'xml' || !FileHelper::isFile($entryPath->getValue()))
        continue;

    $files[$entryPath->getName()] = $entryPath->getName();
}

unset($entryPath, $entry, $entries);

$domains = [
    Core::$app->request->getHostName()
];

if (!empty($site['SERVER_NAME']))
    $domains[$site['SERVER_NAME']] = $site['SERVER_NAME'];

$domain = Option::get( 'main', 'server_name', '' );

if (!empty($domain))
    $domains[] = $domain;

$result = SiteDomainTable::getList([
    'filter' => [
        'LID' => $site['ID']
    ],
    'select' => [
        'DOMAIN'
    ]
]);

while ($domain = $result->fetch())
    if (!empty($domain['DOMAIN']))
        $domains[] = $domain['DOMAIN'];

unset($result);

$domains = array_unique($domains);
$domains = array_combine($domains, $domains);

unset($domain);

if ($request->getIsPost()) {
    $post = $request->post();
    $data = ArrayHelper::getValue($post, $sitemap->formName());

    if (!Type::isArray($data))
        $data = [];

    $return = $request->post('apply');
    $return = empty($return);
    $sitemap->load($post);

    if (!$sitemap->validate()) {
        $error = $sitemap->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }

    if ($error === null && !$sitemap->getIsNewRecord()) {
        if (isset($data['active']) && $active != $data['active']) {
            if ($data['active']) {
                $active = true;

                if (!$sitemap->setActive(true)) {
                    $error = Loc::getMessage('errors.activate');
                }
            } else {
                $active = false;

                if (!$sitemap->setActive(false)) {
                    $error = Loc::getMessage('errors.deactivate');
                }
            }
        }
    }

    if ($error === null && $sitemap->save(false)) {
        if ($return)
            LocalRedirect($arUrlTemplates['filter.sitemap']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.sitemap.edit'], [
            'sitemap' => $sitemap->id
        ]));
    }
} else {
    if (!$sitemap->getIsNewRecord()) {
        if ($request->get('action') === 'generate') {
            if (!$sitemap->generateFile()) {
                $error = Loc::getMessage('errors.generate');
            } else {
                LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.sitemap.edit'], [
                    'sitemap' => $sitemap->id
                ]));
            }
        }
    }
}

$form = new CAdminForm('filterSitemapEditForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
]]);

$panel = [[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['filter.sitemap']
]];

if (!$sitemap->getIsNewRecord()) {
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
    'LINK' => StringHelper::replaceMacros($arUrlTemplates['filter.sitemap.add'], [
        'site' => $site['ID']
    ])
];

$panel = new CAdminContextMenu($panel);

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
    <?php if (!$sitemap->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $sitemap->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $sitemap->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
        <?php $form->BeginCustomField('active', Loc::getMessage('fields.active').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::hiddenInput($sitemap->formName().'[active]', 0) ?>
                    <?= Html::checkbox($sitemap->formName().'[active]', $active, [
                        'value' => 1
                    ]) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('active') ?>
    <?php } ?>
    <?php if (!$sitemap->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('generated', Loc::getMessage('fields.generated').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $sitemap->getIsFileExists() ? Loc::getMessage('answers.yes') : Loc::getMessage('answers.no') ?></td>
            </tr>
        <?php $form->EndCustomField('generated') ?>
    <?php } ?>
    <?php $form->BeginCustomField('name', $sitemap->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($sitemap->formName().'[name]', $sitemap->name, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('sourceFile', $sitemap->getAttributeLabel('sourceFile').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::dropDownList($sitemap->formName().'[scheme]', $sitemap->scheme, Sitemap::getSchemes()) ?>
                <span>://</span>
                <?= Html::dropDownList($sitemap->formName().'[domain]', $sitemap->domain, ArrayHelper::merge([
                    '' => '('.Loc::getMessage('answers.unset').')'
                ], $domains)) ?>
                <span>/</span>
                <?= Html::dropDownList($sitemap->formName().'[sourceFile]', $sitemap->sourceFile, ArrayHelper::merge([
                    '' => '('.Loc::getMessage('answers.unset').')'
                ], $files)) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('sourceFile') ?>
    <?php $form->BeginCustomField('targetFile', $sitemap->getAttributeLabel('targetFile').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($sitemap->formName().'[targetFile]', $sitemap->targetFile, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.targetFile.description') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('targetFile') ?>
    <?php $form->BeginCustomField('configured', $sitemap->getAttributeLabel('configured').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($sitemap->formName().'[configured]', 0) ?>
                <?= Html::checkbox($sitemap->formName().'[configured]', $sitemap->configured, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('configured') ?>
<?php

$buttons = [];

if (!$sitemap->getIsNewRecord()) {
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
    'back_url' => $arUrlTemplates['filter.sitemap']
], implode('', $buttons)) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
