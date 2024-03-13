<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\IblockTable;

global $APPLICATION;

Loc::loadMessages(__FILE__);

include(__DIR__.'/../requirements.php');

$APPLICATION->SetTitle(Loc::getMessage('intec.ai.admin.general_settings.title'));

$tabs = [
	[
        "DIV" => "ai_general_settings",
        "TAB" => Loc::getMessage('intec.ai.admin.general_settings.title'),
        'ICON' => null,
        "TITLE" => Loc::getMessage('intec.ai.admin.general_settings.title')
    ],
];

$form = new CAdminForm('aiGeneralSettings', $tabs);
$form->BeginPrologContent();
$form->EndPrologContent();
$form->BeginEpilogContent();
$form->EndEpilogContent();

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_after.php');

if (!empty($errors)) {
    CAdminMessage::ShowMessage(implode('<br />', $errors));
}

$form->Begin([
	'FORM_ACTION' => $APPLICATION->GetCurPage().'?lang='.LANGUAGE_ID
]);

$form->BeginNextFormTab();

$agentName = "\intec\ai\Module::generateFromQuene();";

$rsAgents = CAgent::GetList(array("ID" => "DESC"));
$arAgentParams = [];

while ($arAgent = $rsAgents->Fetch()) {
    if ($arAgent["NAME"] == $agentName) {
        $arAgentParams = $arAgent;
        break;
    }
}

$agentInterval = $arAgentParams['AGENT_INTERVAL'];
$agentActive = $arAgentParams['ACTIVE'];

$valid = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secret = $_POST['secret'];
    $agentInterval = $_POST['agentInterval'];
    $maxTokens = $_POST['maxTokens'];
    $proxyUse = (isset($_POST['proxy_use']));
    $proxy = $_POST['proxy'];

    if (!is_numeric($agentInterval) || ($agentInterval < 0) || empty($agentInterval)) {
        $valid = false;
        CAdminMessage::ShowMessage(Loc::getMessage('intec.ai.admin.general_settings.agentInterval.isNotNumber'));
    }

    if (!is_numeric($maxTokens) || ($maxTokens < 0) || empty($maxTokens)) {
        $valid = false;
        CAdminMessage::ShowMessage(Loc::getMessage('intec.ai.admin.general_settings.maxTokens.isNotNumber'));
    }

    if ($valid) {
        COption::SetOptionString("intec.ai", "ai.secret", $secret);
        COption::SetOptionString("intec.ai", "ai.maxTokens", $maxTokens);

        if (isset($_POST['active']) && ($_POST['active'] == 'Y')) {
            $agentActive = 'Y';
        } else {
            $agentActive = 'N';
        }

        if ($arAgentParams["ID"] !== null) {
            $arFields = array(
                'ACTIVE' => $agentActive,
                'AGENT_INTERVAL' => $agentInterval,
            );
            $res = CAgent::Update($arAgentParams["ID"], $arFields);
            if (!$res) {
                echo $APPLICATION->GetException();
            }
        }
    }

    if ($proxyUse) {
        COption::SetOptionString("intec.ai", "ai.proxyUse", true);
    } else {
        COption::SetOptionString("intec.ai", "ai.proxyUse", false);
    }

    COption::SetOptionString("intec.ai", "ai.proxy", $proxy);
}

$secret = COption::GetOptionString("intec.ai", "ai.secret");
$maxTokens = COption::GetOptionString("intec.ai", "ai.maxTokens");
$proxyUse = COption::GetOptionString("intec.ai", "ai.proxyUse");
$proxy = COption::GetOptionString("intec.ai", "ai.proxy");

?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.secret', false) ?>
<tr>
    <td style="width: 40%">
        <?= Loc::getMessage('intec.ai.admin.general_settings.secret.header') ?>
    </td>
    <td>
        <input type="text" name="secret" value="<?= $secret ?>" style="width: 100%; max-width: 400px;">
    </td>
</tr>
<?php $form->EndCustomField('ai.general_settings.secret') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.active', false) ?>
<tr>
    <td style="width: 40%">
        <?= Loc::getMessage('intec.ai.admin.general_settings.active.header') ?>
    </td>
    <td>
        <input type="checkbox" name="active" value="Y" <?= ($agentActive == 'Y') ? 'checked' : '' ?>>
        
    </td>
</tr>
<?php $form->EndCustomField('ai.general_settings.active') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.agentInterval', false) ?>
<tr>
    <td style="width: 40%">
        <?= Loc::getMessage('intec.ai.admin.general_settings.agentInterval.header') ?>
    </td>
    <td>
        <input type="text" name="agentInterval" value="<?= $agentInterval ?>">
    </td>
</tr>
<?php $form->EndCustomField('ai.general_settings.agentInterval') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.maxTokens', false) ?>
<tr>
    <td style="width: 40%">
        <?= Loc::getMessage('intec.ai.admin.general_settings.maxTokens.header') ?>
    </td>
    <td>
        <input type="text" name="maxTokens" value="<?= (empty($maxTokens)) ? '2000' : $maxTokens ?>">
    </td>
</tr>
<?php $form->EndCustomField('ai.general_settings.maxTokens') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.proxy_use', false) ?>
<tr>
    <td style="width: 40%">
        <?= Loc::getMessage('intec.ai.admin.general_settings.proxy_use.header') ?>
    </td>
    <td>
        <input type="checkbox" name="proxy_use" value="Y" <?= ($proxyUse) ? 'checked' : '' ?>>
        
    </td>
</tr>
<?php $form->EndCustomField('ai.general_settings.proxy_use') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.proxy', false); ?>
	<tr>
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.general_settings.proxy.header') ?>
		</td>
		<td>
			<textarea name="proxy" style="width: 100%; height: 150px;"><?= $proxy ?></textarea>
		</td>
	</tr>
<?php $form->EndCustomField('ai.general_settings.proxy') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->BeginCustomField('ai.general_settings.save', false) ?>
<tr>
    <td>
        <input type="submit" value="<?= Loc::getMessage('intec.ai.admin.general_settings.save-button') ?>">
    </td>
</tr>
<?php $form->EndCustomField('ai.general_settings.save') ?>
<!---------------------------------------------------------------------------------------------------------->
<?php $form->Show() ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>