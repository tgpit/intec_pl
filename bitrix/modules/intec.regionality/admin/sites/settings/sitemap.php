<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;
use intec\core\io\Path;
use intec\core\net\Url;
use intec\regionality\seo\converters\SitemapConverter;
use intec\regionality\seo\File;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

if (!CModule::IncludeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$site = $request->get('site');
$site = CSite::GetList($by = 'sort', $order = 'asc', [
    'ID' => $site
])->Fetch();

if (empty($site))
    LocalRedirect($arUrlTemplates['sites.settings']);

$path = Path::from($site['ABS_DOC_ROOT'].'/'.$site['DIR']);
$files = [];
$file = $request->get('file');
$registered = false;
$exists = false;

$entries = FileHelper::getDirectoryEntries($path->getValue(), false);

foreach ($entries as $entry) {
    $entryPath = $path->add($entry);

    if ($entryPath->getExtension() !== 'xml' || !FileHelper::isFile($entryPath->getValue()))
        continue;

    $files[$entryPath->getName(false)] = $entryPath->getName();
}

unset($entryPath, $entry, $entries);

if (!isset($files[$file]))
    $file = null;

if (!empty($file)) {
    $file = new File($path->add($file.'.php'));
    $registered = $file->isHtaccessRuleRegistered(
        $path->add('.htaccess'),
        $path,
        $path->add($file->getPath()->getName(false).'.xml')
    );

    $exists = $file->getIsExists();
}

$APPLICATION->SetTitle(Loc::getMessage('title', [
    '#SITE#' => !empty($site['SITE_NAME']) ? $site['SITE_NAME'].' ('.$site['ID'].')' : $site['NAME'].' ('.$site['ID'].')'
]));

if ($request->getIsPost()) {
    $action = $request->post('save');

    if (!empty($action)) {
        $action = 'save';
    } else {
        $action = $request->post('action');
    }

    if ($action === 'save') {
        $content = $request->post('content');
        $file->write($content);
    } else if ($action === 'generate') {
        $converter = new SitemapConverter();
        $content = FileHelper::getFileData($path->add($file->getPath()->getName(false).'.xml')->getValue());
        $parts = [];

        foreach ($files as $part) {
            $partPath = $path->add($part);
            $partContent = FileHelper::getFileData($partPath->getValue());

            if (!empty($partContent))
                $parts[Path::from($site['DIR'].'/'.$part)->asRelative()->getValue('/')] = $partContent;
        }

        unset($part, $partPath, $partContent);

        $content = $converter->convert($content, $parts);
        $file->write($content);
    } else if ($action === 'register') {
        $file->registerHtaccessRule(
            $path->add('.htaccess'),
            $path,
            $path->add($file->getPath()->getName(false).'.xml')
        );
    } else if ($action === 'unRegister') {
        $file->unRegisterHtaccessRule(
            $path->add('.htaccess'),
            $path,
            $path->add($file->getPath()->getName(false).'.xml')
        );
    } else if ($action === 'delete') {
        $file->delete();

        if ($registered)
            $file->unRegisterHtaccessRule(
                $path->add('.htaccess'),
                $path,
                $path->add($file->getPath()->getName(false).'.xml')
            );
    }

    LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['sites.settings.sitemap'], [
        'site' => $site['ID']
    ]).'&file='.Url::encode($file->getPath()->getName(false), true));
}

$form = new CAdminForm('regionsSiteRobotsForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Loc::getMessage('tabs.common')
]]);

$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php $form->Begin([
    'FORM_ACTION' => $request->getUrl()
]) ?>
<?php $form->BeginNextFormTab() ?>
<?php $form->BeginCustomField('file', Loc::getMessage('fields.file'), false) ?>
    <tr>
        <td width="40%"><?= $form->GetCustomLabelHTML() ?>:</td>
        <td>
            <?= Html::dropDownList('file', !empty($file) ? $file->getPath()->getName(false) : null, ArrayHelper::merge([
                '' => '('.Loc::getMessage('answers.unselected').')'
            ], $files), [
                'onchange' => '(function (control) {
                    var url = '.JavaScript::toObject(StringHelper::replaceMacros($arUrlTemplates['sites.settings.sitemap'], [
                        'site' => $site['ID']
                    ])).';
                    var value = control[control.selectedIndex].value;
                                
                    if (value != null && value.length > 0) {
                        if (url.indexOf(\'?\') >= 0) {
                            url += \'&\';
                        } else {
                            url += \'?\';
                        }
                        
                        window.location = url + \'file=\' + encodeURIComponent(value);
                    } else {
                        window.location = url;
                    }
                })(this)'
            ]) ?>
        </td>
    </tr>
<?php $form->EndCustomField('file') ?>
<?php if (!empty($file)) { ?>
    <?php $form->BeginCustomField('registered', Loc::getMessage('fields.registered'), false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?>:</td>
            <td>
                <span style="margin-right: 10px"><?= $registered ? Loc::getMessage('answers.yes') : Loc::getMessage('answers.no') ?></span>
                <?= $registered ? Html::button(Loc::getMessage('actions.unRegister'), [
                    'class' => 'adm-btn',
                    'type' => 'submit',
                    'name' => 'action',
                    'value' => 'unRegister'
                ]): Html::button(Loc::getMessage('actions.register'), [
                    'class' => 'adm-btn',
                    'type' => 'submit',
                    'name' => 'action',
                    'value' => 'register'
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('registered') ?>
    <?php $form->BeginCustomField('exists', Loc::getMessage('fields.exists'), false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?>:</td>
            <td>
                <span style="margin-right: 10px"><?= $exists ? Loc::getMessage('answers.yes') : Loc::getMessage('answers.no') ?></span>
                <?php if ($exists) { ?>
                    <?= Html::button(Loc::getMessage('actions.delete'), [
                        'class' => 'adm-btn',
                        'type' => 'submit',
                        'name' => 'action',
                        'value' => 'delete'
                    ]) ?>
                <?php } ?>
            </td>
        </tr>
    <?php $form->EndCustomField('exists') ?>
    <?php $form->BeginCustomField('originalFile', Loc::getMessage('fields.originalFile'), false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?>:</td>
            <td>
                <?= $path->add($file->getPath()->getName(false))->getValue().'.xml' ?>
            </td>
        </tr>
    <?php $form->EndCustomField('originalFile') ?>
    <?php $form->BeginCustomField('customFile', Loc::getMessage('fields.customFile'), false) ?>
    <tr>
        <td width="40%"><?= $form->GetCustomLabelHTML() ?>:</td>
        <td>
            <?= $file->getPath()->getValue() ?>
        </td>
    </tr>
    <?php $form->EndCustomField('customFile') ?>
    <?php $form->BeginCustomField('content', Loc::getMessage('fields.content'), false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?>:</td>
            <td>
                <?= Html::textarea('content', $file->read(), [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '400px',
                        'resize' => 'vertical'
                    ]
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('content') ?>
<?php } ?>
<?php $form->Buttons(!empty($file) ? [
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => false,
    'btnCancel' => false
] : false, (!empty($file) ? Html::button(Loc::getMessage('actions.generate'), [
    'class' => 'adm-btn',
    'type' => 'submit',
    'name' => 'action',
    'value' => 'generate',
    'style' => [
        'margin-left' => '10px'
    ]
]) : null).Html::a(Loc::getMessage('actions.back'), $arUrlTemplates['sites.settings'], [
    'class' => 'adm-btn'
])) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
