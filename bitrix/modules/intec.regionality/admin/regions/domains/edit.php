<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\regionality\models\Region;
use intec\regionality\models\region\Domain;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 */

global $APPLICATION;

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$error = null;
$sites = Arrays::fromDBResult(CSite::GetList($by = 'order', $sort = 'asc'))->indexBy('ID');

/** @var Region $region */
$region = $request->get('region');
$region = Region::findOne($region);

if (empty($region))
    LocalRedirect($arUrlTemplates['regions']);

/** @var Domain $domain */
$domain = $request->get('domain');

if (!empty($domain)) {
    $domain = $region->getDomains(false)->where([
        'id' => $domain
    ])->one();

    if (empty($domain))
        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.domains'], [
            'region' => $region->id
        ]));
} else {
    $domain = new Domain();
    $domain->loadDefaultValues();
    $domain->regionId = $region->id;
}

if ($domain->getIsNewRecord()) {
    $APPLICATION->SetTitle(Loc::getMessage('title.add', ['#region#' => $region->name]));
} else {
    $APPLICATION->SetTitle(Loc::getMessage('title.edit', ['#region#' => $region->name]));
}

if ($request->getIsPost()) {
    $post = $request->post();
    $return = $request->post('apply');
    $return = empty($return);
    $domain->load($post);

    if ($domain->save()) {
        if ($return)
            LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.domains'], [
                'region' => $region->id
            ]));

        LocalRedirect(StringHelper::replaceMacros($arUrlTemplates['regions.domains.edit'], [
            'region' => $region->id,
            'domain' => $domain->id
        ]));
    } else {
        $error = $domain->getFirstErrors();
        $error = ArrayHelper::getFirstValue($error);
    }
}

$form = new CAdminForm('regionsDomainsEditForm', [[
    'DIV' => 'common',
    'ICON' => null,
    'TAB' => Loc::getMessage('tabs.common'),
    'TITLE' => Html::encode(Loc::getMessage('tabs.common'))
]]);

$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

$sections = include(__DIR__.'/../sections.php');
$panel = new CAdminContextMenu([[
    'TEXT' => Loc::getMessage('menu.back'),
    'ICON' => 'btn_list',
    'LINK' => StringHelper::replaceMacros($arUrlTemplates['regions.domains'], [
        'region' => $region->id
    ])
], [
    'TEXT' => Loc::getMessage('menu.add'),
    'ICON' => 'btn_new',
    'LINK' => StringHelper::replaceMacros($arUrlTemplates['regions.domains.add'], [
        'region' => $region->id
    ])
]]);

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php $sections($region, 'domains', ['margin-bottom' => '10px']) ?>
<?php $panel->Show() ?>
<?php if (!empty($error)) { ?>
    <?php CAdminMessage::ShowMessage($error) ?>
<?php } ?>
<?php $form->Begin([
    'FORM_ACTION' => $request->getUrl()
]) ?>
<?php $form->BeginNextFormTab() ?>
    <?php if (!$domain->getIsNewRecord()) { ?>
        <?php $form->BeginCustomField('id', $domain->getAttributeLabel('id').':', true) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td><?= $domain->id ?></td>
            </tr>
        <?php $form->EndCustomField('id') ?>
    <?php } ?>
    <?php $form->BeginCustomField('active', $domain->getAttributeLabel('active').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($domain->formName().'[active]', 0) ?>
                <?= Html::checkbox($domain->formName().'[active]', $domain->active) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('active') ?>
    <?php $form->BeginCustomField('default', $domain->getAttributeLabel('default').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput($domain->formName().'[default]', 0) ?>
                <?= Html::checkbox($domain->formName().'[default]', $domain->default) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('default') ?>
    <?php $form->BeginCustomField('siteId', $domain->getAttributeLabel('siteId').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::dropDownList(
                    $domain->formName().'[siteId]',
                    $domain->siteId,
                    $sites->asArray(function ($index, $site) {
                        return [
                            'key' => $site['ID'],
                            'value' => '['.$site['ID'].'] '.(!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME'])
                        ];
                    })
                ) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('siteId') ?>
    <?php $form->BeginCustomField('value', $domain->getAttributeLabel('value').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($domain->formName().'[value]', $domain->value) ?></td>
        </tr>
    <?php $form->EndCustomField('value') ?>
    <?php $form->BeginCustomField('sort', $domain->getAttributeLabel('sort').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::textInput($domain->formName().'[sort]', $domain->sort) ?></td>
        </tr>
    <?php $form->EndCustomField('sort') ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => true,
    'btnCancel' => true,
    'back_url' => StringHelper::replaceMacros($arUrlTemplates['regions.domains'], [
        'region' => $region->id
    ])
]) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>