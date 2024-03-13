<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\seo\models\text\Pattern;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

Loader::includeModule('fileman');

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title.add'));

include(__DIR__.'/../../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

$request = Core::$app->request;
$error = null;

/** @var Pattern $textPattern */
$textPattern = $request->get('textPattern');

if (!empty($textPattern)) {
    $textPattern = Pattern::findOne($textPattern);

    if (empty($textPattern))
        LocalRedirect($arUrlTemplates['texts.patterns']);
} else {
    $textPattern = new Pattern();
    $textPattern->loadDefaultValues();
}

if (!$textPattern->getIsNewRecord())
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));

if ($request->getIsPost()) {
    $post = $request->post();
    $return = $request->post('apply');
    $return = empty($return);
    $textPattern->load($post);

    if (isset($post['value']))
        $textPattern->value = $post['value'];

    if ($textPattern->save()) {
        if ($return)
            LocalRedirect($arUrlTemplates['texts.patterns']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['texts.patterns.edit'], [
            'textPattern' => $textPattern->id
        ]));
    } else {
        $error = $textPattern->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
}

$form = new CAdminForm('textsPatternsEditForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
]]);

$panel = new CAdminContextMenu([[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['texts.patterns']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['texts.patterns.add']
]]);

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
    <?php if (!$textPattern->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $textPattern->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $textPattern->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('code', $textPattern->getAttributeLabel('code').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($textPattern->formName().'[code]', $textPattern->code) ?></td>
        </tr>
    <?php $form->EndCustomField('code') ?>
    <?php $form->BeginCustomField('active', $textPattern->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($textPattern->formName().'[active]', 0) ?>
                <?= Html::checkbox($textPattern->formName().'[active]', $textPattern->active) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('active') ?>
    <?php $form->BeginCustomField('name', $textPattern->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($textPattern->formName().'[name]', $textPattern->name, [
                'value' => 1
            ]) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('value', $textPattern->getAttributeLabel('value').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= bitrix_sessid_post() ?>
                <?php CFileMan::AddHTMLEditorFrame(
                    'value',
                    $textPattern->value,
                    null,
                    'html',
                    [
                        'height' => 450,
                        'width' => '100%'
                    ],
                    'N',
                    0,
                    '',
                    '',
                    '',
                    true,
                    false,
                    [
                        'toolbarConfig' => 'admin',
                        'saveEditorState' => false,
                        'hideTypeSelector' => true
                    ]
                ) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('value') ?>
    <?php $form->BeginCustomField('sort', $textPattern->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($textPattern->formName().'[sort]', $textPattern->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
    <?php $form->BeginCustomField('information', null) ?>
        <tr>
            <td colspan="2">
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message">
                        <?= Loc::getMessage('fields.information') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('information') ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['texts.patterns']
]) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>