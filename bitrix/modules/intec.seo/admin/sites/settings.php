<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\seo\models\SiteSettings;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;
global $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('title'));

include(__DIR__.'/../requirements.php');
include(Core::getAlias('@intec/seo/module/admin/url.php'));

$request = Core::$app->request;
$errors = [];

$sites = Arrays::fromDBResult(CSite::GetList($by = 'order', $sort = 'asc'))->indexBy('ID');
$sitesSettings = SiteSettings::find()
    ->indexBy('siteId')
    ->all();

foreach ($sites as $site)
    if (!$sitesSettings->exists($site['ID'])) {
        $siteSettings = new SiteSettings([
            'siteId' => $site['ID']
        ]);

        $siteSettings->loadDefaultValues();
        $siteSettings->validate(['filterVisitsReferrers']);
        $sitesSettings->set($siteSettings->siteId, $siteSettings);
    }

if ($request->getIsPost()) {
    $data = $request->post('settings');

    foreach ($sitesSettings as $siteSettings) {
        /** @var SiteSettings $siteSettings */
        if ($sites->exists($siteSettings->siteId)) {
            $site = $sites->get($siteSettings->siteId);
            $siteData = ArrayHelper::getValue($data, $siteSettings->siteId);
            $siteSettings->load($siteData, '');

            if (!$siteSettings->save()) {
                $siteErrors = ArrayHelper::getValues($siteSettings->getFirstErrors());

                foreach ($siteErrors as $key => $siteError)
                    $siteErrors[$key] = (!empty($site['SITE_NAME']) ? $site['SITE_NAME'] : $site['NAME']).' ('.$site['ID'].')'.'. '.$siteError;

                $errors = ArrayHelper::merge($errors, $siteErrors);
            }
        } else if (!$siteSettings->getIsNewRecord()) {
            $siteSettings->delete();
        }
    }

    unset($siteSettings);
}

$tabs = [];

foreach ($sites as $site) {
    $tabName = $site['NAME'].' ('.$site['ID'].')';

    if (!empty($site['SITE_NAME']))
        $tabName = $site['SITE_NAME'].' ('.$site['ID'].')';

    $tabs[] = [
        'DIV' => $site['ID'],
        'ICON' => null,
        'TAB' => $tabName,
        'TITLE' => $tabName
    ];
}

$form = new CAdminForm('seoSitesForm', $tabs);
$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php') ?>
<?php if (!empty($errors)) { ?>
    <?php CAdminMessage::ShowMessage(implode('<br />', $errors)) ?>
<?php } ?>
<?php $form->Begin([
    'FORM_ACTION' => $request->getUrl()
]) ?>
<?php foreach ($sites as $site) { ?>
    <?php $siteSettings = $sitesSettings->get($site['ID']) ?>
    <?php /** @var SiteSettings $siteSettings */ ?>
    <?php $form->BeginNextFormTab() ?>
    <?php $form->BeginCustomField('filterIndexingDisabled'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterIndexingDisabled').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterIndexingDisabled]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][filterIndexingDisabled]', $siteSettings->filterIndexingDisabled, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.filterIndexingDisabled.description') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('filterIndexingDisabled'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterPaginationPart'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterPaginationPart').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput('settings['.$siteSettings->siteId.'][filterPaginationPart]', $siteSettings->filterPaginationPart) ?>

            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.filterPaginationPart.description') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('filterPaginationPart'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterPaginationText'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterPaginationText').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput('settings['.$siteSettings->siteId.'][filterPaginationText]', $siteSettings->filterPaginationText) ?>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.filterPaginationText.description') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('filterPaginationText'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterCanonicalUse'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterCanonicalUse').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterCanonicalUse]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][filterCanonicalUse]', $siteSettings->filterCanonicalUse, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('filterCanonicalUse'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterUrlQueryClean'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterUrlQueryClean').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterUrlQueryClean]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][filterUrlQueryClean]', $siteSettings->filterUrlQueryClean, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
        <tr>
            <td width="40%"></td>
            <td>
                <div class="adm-info-message-wrap">
                    <div class="adm-info-message" style="margin-top: 0">
                        <?= Loc::getMessage('fields.filterUrlQueryClean.description') ?>
                    </div>
                </div>
            </td>
        </tr>
    <?php $form->EndCustomField('filterUrlQueryClean'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterVisitsEnabled'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterVisitsEnabled').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterVisitsEnabled]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][filterVisitsEnabled]', $siteSettings->filterVisitsEnabled, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('filterVisitsEnabled'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterVisitsReferrers'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterVisitsReferrers').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterVisitsReferrers]', 0) ?>
                <?= Html::textarea('settings['.$siteSettings->siteId.'][filterVisitsReferrers]', $siteSettings->filterVisitsReferrers, [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '100px',
                        'resize' => 'vertical'
                    ]
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('filterVisitsReferrers'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterPages'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterPages').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterPages]', 0) ?>
                <?= Html::textarea('settings['.$siteSettings->siteId.'][filterPages]', $siteSettings->filterPages, [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '100px',
                        'resize' => 'vertical'
                    ]
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('filterPages'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('filterClearRedirectUse'.$siteSettings->siteId, $siteSettings->getAttributeLabel('filterClearRedirectUse').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][filterClearRedirectUse]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][filterClearRedirectUse]', $siteSettings->filterClearRedirectUse, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('filterClearRedirectUse'.$siteSettings->siteId) ?>
<?php } ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => false,
    'btnCancel' => false
]) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
