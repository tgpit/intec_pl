<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\io\Path;
use intec\regionality\seo\converters\RobotsConverter;
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
$file = new File($path->add('robots.php'));
$registered = $file->isHtaccessRuleRegistered(
    $path->add('.htaccess'),
    $path,
    $path->add('robots.txt')
);

$exists = $file->getIsExists();

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
        $converter = new RobotsConverter();
        $content = FileHelper::getFileData($path->add('robots.txt')->getValue());
        $content = $converter->convert($content);
        $file->write($content);
    } else if ($action === 'register') {
        $file->registerHtaccessRule(
            $path->add('.htaccess'),
            $path,
            $path->add('robots.txt')
        );
    } else if ($action === 'unRegister') {
        $file->unRegisterHtaccessRule(
            $path->add('.htaccess'),
            $path,
            $path->add('robots.txt')
        );
    } else if ($action === 'delete') {
        $file->delete();

        if ($registered)
            $file->unRegisterHtaccessRule(
                $path->add('.htaccess'),
                $path,
                $path->add('robots.txt')
            );
    }

    LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['sites.settings.robots'], [
        'site' => $site['ID']
    ]));
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
            <?= $path->add('robots.txt')->getValue() ?>
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
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => false,
    'btnCancel' => false
], Html::button(Loc::getMessage('actions.generate'), [
    'class' => 'adm-btn',
    'type' => 'submit',
    'name' => 'action',
    'value' => 'generate',
    'style' => [
        'margin-left' => '10px'
    ]
]).Html::a(Loc::getMessage('actions.back'), $arUrlTemplates['sites.settings'], [
    'class' => 'adm-btn'
])) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
