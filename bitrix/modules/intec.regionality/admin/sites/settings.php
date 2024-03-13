<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\regionality\models\Region;
use intec\regionality\models\SiteSettings;
use intec\regionality\models\SiteSettingsLocatorExtension;
use intec\regionality\services\locator\Extension as LocatorExtension;
use intec\regionality\services\locator\Service as Locator;

/**
 * @var array $arUrlTemplates
 * @global CMain $APPLICATION
 * @global CUserTypeManager $USER_FIELD_MANAGER
 */

global $APPLICATION;
global $USER_FIELD_MANAGER;

Loc::loadMessages(__FILE__);

if (!CModule::IncludeModule('intec.regionality'))
    return;

include(Core::getAlias('@intec/regionality/module/admin/url.php'));

$request = Core::$app->request;
$errors = [];

$sites = Arrays::fromDBResult(CSite::GetList($by = 'order', $sort = 'asc'))->indexBy('ID');
$sitesSettings = SiteSettings::find()
    ->with(['locatorExtensions'])
    ->indexBy('siteId')
    ->all();

$locator = Locator::getInstance();
$locatorExtensions = $locator->getExtensions();

$regions = Region::find()
    ->where([
        'active' => 1
    ])
    ->with(['sites'])
    ->all();

foreach ($sites as $site)
    if (!$sitesSettings->exists($site['ID'])) {
        $siteSettings = new SiteSettings([
            'siteId' => $site['ID']
        ]);

        $siteSettings->loadDefaultValues();
        $sitesSettings->set($siteSettings->siteId, $siteSettings);
    }

$APPLICATION->SetTitle(Loc::getMessage('title'));

if ($request->getIsPost()) {
    $data = $request->post('settings');

    foreach ($sitesSettings as $siteSettings) {
        /** @var SiteSettings $siteSettings */
        if ($sites->exists($siteSettings->siteId)) {
            $site = $sites->get($siteSettings->siteId);
            $siteData = ArrayHelper::getValue($data, $siteSettings->siteId);
            $siteSettings->load($siteData, '');

            if ($siteSettings->save()) {
                $dataLocatorExtensions = ArrayHelper::getValue($siteData, 'locatorExtensions');

                if (!Type::isArray($dataLocatorExtensions))
                    $dataLocatorExtensions = [];

                $siteSettingsLocatorExtensions = $siteSettings->getLocatorExtensions(true);

                foreach ($siteSettingsLocatorExtensions as $siteSettingsLocatorExtension)
                    $siteSettingsLocatorExtension->delete();

                foreach ($locatorExtensions as $locatorExtension) {
                    if (!ArrayHelper::isIn($locatorExtension->code, $dataLocatorExtensions))
                        continue;

                    $siteSettingsLocatorExtension = new SiteSettingsLocatorExtension();
                    $siteSettingsLocatorExtension->siteId = $siteSettings->siteId;
                    $siteSettingsLocatorExtension->extensionCode = $locatorExtension->code;
                    $siteSettingsLocatorExtension->save();
                }
            } else {
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

$form = new CAdminForm('regionsSitesForm', $tabs);
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
    <?php $siteSettingsLocatorExtensions = $siteSettings->getLocatorExtensions()->all(); ?>
    <?php /** @var SiteSettings $siteSettings */ ?>
    <?php $form->BeginNextFormTab() ?>
    <?php if ($siteSettings->domainsUse) { ?>
        <?php $form->BeginCustomField('domain'.$siteSettings->siteId, $siteSettings->getAttributeLabel('domain').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::textInput('settings['.$siteSettings->siteId.'][domain]', $siteSettings->domain) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('domain'.$siteSettings->siteId) ?>
    <?php } ?>
    <?php $form->BeginCustomField('regionId'.$siteSettings->siteId, $siteSettings->getAttributeLabel('regionId').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td><?= Html::dropDownList(
                'settings['.$siteSettings->siteId.'][regionId]',
                $siteSettings->regionId,
                ArrayHelper::merge([
                    '' => '('.Loc::getMessage('answers.unset').')'
                ], $regions->asArray(function ($index, $region) use (&$siteSettings) {
                    /** @var Region $region */

                    if (!$region->isForSites($siteSettings->siteId))
                        return ['skip' => true];

                    return [
                        'key' => $region->id,
                        'value' => '['.$region->id.'] '.$region->name
                    ];
                })
            )) ?></td>
        </tr>
    <?php $form->EndCustomField('regionId'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('regionLocationResolve'.$siteSettings->siteId, $siteSettings->getAttributeLabel('regionLocationResolve').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][regionLocationResolve]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][regionLocationResolve]', $siteSettings->regionLocationResolve, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('regionLocationResolve'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('regionRememberTime'.$siteSettings->siteId, $siteSettings->getAttributeLabel('regionRememberTime').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textInput('settings['.$siteSettings->siteId.'][regionRememberTime]', $siteSettings->regionRememberTime) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('regionRememberTime'.$siteSettings->siteId) ?>
    <?php if ($siteSettings->domainsUse) { ?>
        <?php $form->BeginCustomField('regionResolveOrder'.$siteSettings->siteId, $siteSettings->getAttributeLabel('regionResolveOrder').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::dropDownList(
                        'settings['.$siteSettings->siteId.'][regionResolveOrder]',
                        $siteSettings->regionResolveOrder,
                        SiteSettings::getRegionResolveOrders()
                    ) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('regionResolveOrder'.$siteSettings->siteId) ?>
    <?php } ?>
    <?php $form->BeginCustomField('regionResolveIgnoreUse'.$siteSettings->siteId, $siteSettings->getAttributeLabel('regionResolveIgnoreUse').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][regionResolveIgnoreUse]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][regionResolveIgnoreUse]', $siteSettings->regionResolveIgnoreUse, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('regionResolveIgnoreUse'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('regionResolveIgnoreUserAgents'.$siteSettings->siteId, $siteSettings->getAttributeLabel('regionResolveIgnoreUserAgents').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::textarea('settings['.$siteSettings->siteId.'][regionResolveIgnoreUserAgents]', $siteSettings->regionResolveIgnoreUserAgents, [
                    'style' => [
                        'width' => '100%',
                        'min-height' => '100px',
                        'resize' => 'vertical'
                    ]
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('regionResolveIgnoreUserAgents'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('locatorExtensions'.$siteSettings->siteId, Loc::getMessage('fields.locatorExtensions.name').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][locatorExtensions]', null) ?>
                <?= Html::dropDownList(
                    'settings['.$siteSettings->siteId.'][locatorExtensions][]',
                    $siteSettingsLocatorExtensions->asArray(function ($index, $extension) {
                        /** @var SiteSettingsLocatorExtension $extension */
                        return [
                            'value' => $extension->extensionCode
                        ];
                    }),
                    ArrayHelper::merge([
                        '' => '('.Loc::getMessage('answers.unset').')'
                    ], $locatorExtensions->asArray(function ($index, $extension) {
                        /** @var LocatorExtension $extension */

                        if (!$extension->isAvailable)
                            return ['skip' => true];

                        return [
                            'key' => $extension->code,
                            'value' => $extension->name
                        ];
                    })), [
                        'multiple' => 'multiple'
                    ]
                ) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('locatorExtensions'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('domainsUse'.$siteSettings->siteId, $siteSettings->getAttributeLabel('domainsUse').':', false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][domainsUse]', 0) ?>
                <?= Html::checkbox('settings['.$siteSettings->siteId.'][domainsUse]', $siteSettings->domainsUse, [
                    'value' => 1
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('domainsUse'.$siteSettings->siteId) ?>
    <?php if ($siteSettings->domainsUse) { ?>
        <?php $form->BeginCustomField('domainsLinkingUse'.$siteSettings->siteId, $siteSettings->getAttributeLabel('domainsLinkingUse').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][domainsLinkingUse]', 0) ?>
                    <?= Html::checkbox('settings['.$siteSettings->siteId.'][domainsLinkingUse]', $siteSettings->domainsLinkingUse, [
                        'value' => 1
                    ]) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('domainsLinkingUse'.$siteSettings->siteId) ?>
        <?php $form->BeginCustomField('domainsLinkingReset'.$siteSettings->siteId, $siteSettings->getAttributeLabel('domainsLinkingReset').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][domainsLinkingReset]', 0) ?>
                    <?= Html::checkbox('settings['.$siteSettings->siteId.'][domainsLinkingReset]', $siteSettings->domainsLinkingReset, [
                        'value' => 1
                    ]) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('domainsLinkingReset'.$siteSettings->siteId) ?>
        <?php $form->BeginCustomField('domainsRedirectUse'.$siteSettings->siteId, $siteSettings->getAttributeLabel('domainsRedirectUse').':', false) ?>
            <tr>
                <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
                <td>
                    <?= Html::hiddenInput('settings['.$siteSettings->siteId.'][domainsRedirectUse]', 0) ?>
                    <?= Html::checkbox('settings['.$siteSettings->siteId.'][domainsRedirectUse]', $siteSettings->domainsRedirectUse, [
                        'value' => 1
                    ]) ?>
                </td>
            </tr>
        <?php $form->EndCustomField('domainsRedirectUse'.$siteSettings->siteId) ?>
    <?php } ?>
    <?php $form->BeginCustomField('robots'.$siteSettings->siteId, Loc::getMessage('fields.robots.name'), false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::a(Loc::getMessage('actions.configure'), StringHelper::replaceMacros($arUrlTemplates['sites.settings.robots'], [
                    'site' => $siteSettings->siteId
                ]), [
                    'class' => 'adm-btn'
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('robots'.$siteSettings->siteId) ?>
    <?php $form->BeginCustomField('sitemap'.$siteSettings->siteId, Loc::getMessage('fields.sitemap.name'), false) ?>
        <tr>
            <td width="40%"><?= $form->GetCustomLabelHTML() ?></td>
            <td>
                <?= Html::a(Loc::getMessage('actions.configure'), StringHelper::replaceMacros($arUrlTemplates['sites.settings.sitemap'], [
                    'site' => $siteSettings->siteId
                ]), [
                    'class' => 'adm-btn'
                ]) ?>
            </td>
        </tr>
    <?php $form->EndCustomField('sitemap'.$siteSettings->siteId) ?>
<?php } ?>
<?php $form->Buttons([
    'disabled' => false,
    'btnSaveAndAdd' => false,
    'btnApply' => false,
    'btnCancel' => false
]) ?>
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>
