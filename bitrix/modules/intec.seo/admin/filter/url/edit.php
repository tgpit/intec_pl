<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\seo\models\filter\Condition;
use intec\seo\models\filter\Url;

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

/** @var Url $url */
$url = $request->get('url');

if (!empty($url)) {
    $url = Url::findOne($url);

    if (empty($url))
        LocalRedirect($arUrlTemplates['filter.url']);
} else {
    $url = new Url();
    $url->populateRelation('scans', []);
    $url->loadDefaultValues();
}

$scans = $url->getScans()->orderBy(['date' => SORT_DESC])->all();
$conditions = Condition::find()->all()->indexBy('id');

if (!$url->getIsNewRecord())
    $APPLICATION->SetTitle(Loc::getMessage('title.edit'));

if ($request->getIsPost()) {
    $post = $request->post();
    $return = $request->post('apply');
    $return = empty($return);
    $url->load($post);

    if ($url->save()) {
        if ($return)
            LocalRedirect($arUrlTemplates['filter.url']);

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['filter.url.edit'], [
            'url' => $url->id
        ]));
    } else {
        $error = $url->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
}

$form = new CAdminForm('filterUrlEditForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
],/* [
    'DIV' => 'meta',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.meta'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.meta'))
], */[
    'DIV' => 'debug',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.debug'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.debug'))
]]);

$panel = new CAdminContextMenu([[
    'TEXT' => Loc::getMessage('panel.actions.back'),
    'ICON' => 'btn_list',
    'LINK' => $arUrlTemplates['filter.url']
], [
    'TEXT' => Loc::getMessage('panel.actions.add'),
    'ICON' => 'btn_new',
    'LINK' => $arUrlTemplates['filter.url.add']
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
    <?php if (!$url->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $url->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $url->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('active', $url->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($url->formName().'[active]', 0) ?>
                <?= Html::checkbox($url->formName().'[active]', $url->active, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('active') ?>
    <?php $form->BeginCustomField('conditionId', $url->getAttributeLabel('conditionId').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::dropDownList($url->formName().'[conditionId]', $url->conditionId, ArrayHelper::merge([
                    '' => '('.Loc::getMessage('answers.unset').')'
                ], $conditions->asArray(function ($id, $condition) {
                    /** @var Condition $condition */

                    return [
                        'key' => $id,
                        'value' => '['.$id.'] '.$condition->name
                    ];
                }))) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('conditionId') ?>
    <?php $form->BeginCustomField('name', $url->getAttributeLabel('name').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($url->formName().'[name]', $url->name) ?></td>
        </tr>
    <?php $form->EndCustomField('name') ?>
    <?php $form->BeginCustomField('source', $url->getAttributeLabel('source').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($url->formName().'[source]', $url->source, [
                    'style' => [
                        'width' => '100%'
                    ]
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('source') ?>
    <?php $form->BeginCustomField('target', $url->getAttributeLabel('target').':', true) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput($url->formName().'[target]', $url->target, [
                    'style' => [
                        'width' => '100%'
                    ]
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('target') ?>
    <?php if (!$url->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('dateCreate', $url->getAttributeLabel('dateCreate').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= !empty($url->dateCreate) ? Core::$app->formatter->asDate($url->dateCreate, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('answers.no').')' ?></td>
            </tr>
        <?php $form->EndCustomField('dateCreate') ?>
        <?php $form->BeginCustomField('dateChange', $url->getAttributeLabel('dateChange').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= !empty($url->dateChange) ? Core::$app->formatter->asDate($url->dateChange, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('answers.no').')' ?></td>
            </tr>
        <?php $form->EndCustomField('dateChange') ?>
    <?php } ?>
    <?php $form->BeginCustomField('mapping', $url->getAttributeLabel('mapping').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($url->formName().'[mapping]', 0) ?>
                <?= Html::checkbox($url->formName().'[mapping]', $url->mapping, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('mapping') ?>
    <?php if (!empty($url->iBlockElementsCount)) { ?>
        <?php $form->BeginCustomField('iBlockElementsCount', $url->getAttributeLabel('iBlockElementsCount').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $url->iBlockElementsCount ?></td>
            </tr>
        <?php $form->EndCustomField('iBlockElementsCount') ?>
    <?php } ?>
<?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('debug', Loc::getMessage('fields.debug').':', false) ?>
        <tr>
            <td colspan="2">
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="internal">
                    <tr class="heading">
                        <td><?= Loc::getMessage('fields.debug.fields.date') ?></td>
                        <td><?= Loc::getMessage('fields.debug.fields.status') ?></td>
                        <td><?= Loc::getMessage('fields.debug.fields.metaTitle') ?></td>
                        <td><?= Loc::getMessage('fields.debug.fields.metaKeywords') ?></td>
                        <td><?= Loc::getMessage('fields.debug.fields.metaDescription') ?></td>
                        <td><?= Loc::getMessage('fields.debug.fields.metaPageTitle') ?></td>
                    </tr>
                    <?php if (!$scans->isEmpty()) { ?>
                        <?php foreach ($scans as $scan) { ?>
                            <tr>
                                <td><?= !empty($scan->date) ? Core::$app->formatter->asDate($scan->date, 'php:d.m.Y H:i:s') : '('.Loc::getMessage('answers.no').')' ?></td>
                                <td><?= Html::encode($scan->status) ?></td>
                                <td><?= Html::encode($scan->metaTitle) ?></td>
                                <td><?= Html::encode($scan->metaKeywords) ?></td>
                                <td><?= Html::encode($scan->metaDescription) ?></td>
                                <td><?= Html::encode($scan->metaPageTitle) ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6"><?= Loc::getMessage('fields.debug.messages.dataUnavailable') ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </td>
        </tr>
    <?php $form->EndCustomField('debug') ?>
<?php /*$form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('metaTitle', $url->getAttributeLabel('metaTitle').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($url->formName().'[metaTitle]', $url->metaTitle) ?></td>
        </tr>
    <?php $form->EndCustomField('metaTitle') ?>
    <?php $form->BeginCustomField('metaKeywords', $url->getAttributeLabel('metaKeywords').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($url->formName().'[metaKeywords]', $url->metaKeywords) ?></td>
        </tr>
    <?php $form->EndCustomField('metaKeywords') ?>
    <?php $form->BeginCustomField('metaDescription', $url->getAttributeLabel('metaDescription').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textarea($url->formName().'[metaDescription]', $url->metaDescription, [
                'style' => [
                    'width' => '100%',
                    'min-height' => '300px',
                    'resize' => 'vertical'
                ]
            ]) ?></td>
        </tr>
    <?php $form->EndCustomField('metaDescription') ?>
    <?php $form->BeginCustomField('metaPageTitle', $url->getAttributeLabel('metaPageTitle').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($url->formName().'[metaPageTitle]', $url->metaPageTitle) ?></td>
        </tr>
    <?php $form->EndCustomField('metaPageTitle') ?>
    <?php $form->BeginCustomField('metaBreadcrumbName', $url->getAttributeLabel('metaBreadcrumbName').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($url->formName().'[metaBreadcrumbName]', $url->metaBreadcrumbName) ?></td>
        </tr>
    <?php $form->EndCustomField('metaBreadcrumbName') ?>
<?php*/ $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => $arUrlTemplates['filter.url']
]) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
