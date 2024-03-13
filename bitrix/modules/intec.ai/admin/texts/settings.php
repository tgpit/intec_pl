<?php require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php') ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\IblockTable;
use Bitrix\Main\Page\Asset;
use intec\core\helpers\Json;
use Bitrix\Main\Text\Encoding;

global $APPLICATION;

Loc::loadMessages(__FILE__);

$APPLICATION->SetTitle(Loc::getMessage('intec.ai.admin.texts.title'));

include(__DIR__.'/../requirements.php');

CJSCore::Init(array("jquery"));

Asset::getInstance()->addJs('/bitrix/tools/intec.ai/texts/js/send-to-queue.js');

$tabs = [
	[
        "DIV" => "ai_texts",
        "TAB" => Loc::getMessage('intec.ai.admin.texts.title'),
        'ICON' => null,
        "TITLE" => Loc::getMessage('intec.ai.admin.texts.title')
    ],
];

$form = new CAdminForm('aiTexts', $tabs);
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

$secret = COption::GetOptionString("intec.ai", "ai.secret");

$valid = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	if (empty($_POST['iblock-id'])) {
		$valid = false;
	} else {
		COption::SetOptionString("intec.ai", "ai.iblockId", $_POST['iblock-id']);
	}

	if (empty($_POST['iblock-sections'])) {
		$valid = false;
	} else {
		COption::SetOptionString("intec.ai", "ai.iblockSections", serialize($_POST['iblock-sections']));
	}

	if (empty($_POST['iblock-elements'])) {
		$valid = false;
	} else {
		COption::SetOptionString("intec.ai", "ai.iblockElements", serialize($_POST['iblock-elements']));
	}

	if (empty($_POST['prompt-mask'])) {
		$valid = false;
	} else {
		COption::SetOptionString("intec.ai", "ai.promptMask", $_POST['prompt-mask']);
	}

	if (empty($_POST['select-property'])) {
		$valid = false;
	} else {
		COption::SetOptionString("intec.ai", "ai.selectProperty", $_POST['select-property']);
	}

	if (empty($secret)) {
		$valid = false;
		CAdminMessage::ShowMessage(Loc::getMessage('intec.ai.admin.texts.enter-secret'));
	} else {
		if (!$valid) CAdminMessage::ShowMessage(Loc::getMessage('intec.ai.admin.texts.validation'));
	}

	if ($valid) {
		$iblockId = $_POST['iblock-id'];
		$iblockElementsId = $_POST['iblock-elements'];
		$promptMask = $_POST['prompt-mask'];
		$iblockProperty = $_POST['select-property'];

        if (Encoding::detectUtf8($promptMask))
            $promptMask = Encoding::convertEncoding($promptMask, 'UTF-8', LANG_CHARSET);

		$arFilter = array(
			"IBLOCK_ID" => $iblockId,
			"ID" => $iblockElementsId,
		);

		$arSelect = array(
			"ID",
			'NAME'
		);

		$elements = [];

		$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		while ($ob = $res->GetNextElement()) {
			$arFields = $ob->GetFields();

			$elements[$arFields["ID"]] = $arFields["NAME"];
		}

		if (count($elements) <= 0) {
			$valid = false;
			if (!$valid) CAdminMessage::ShowMessage(Loc::getMessage('intec.ai.admin.texts.noElements'));
		}
	}
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
	$jsElements = Json::encode($elements, JSON_HEX_APOS, true);
?>
	<!---------------------------------------------------------------------------------------------------------->	
	<?php $form->BeginCustomField('ai.texts.progressbar', false) ?>
	<div id="gpt-progress">
		<div style="color: #000; font-size: 20px; padding: 3px 53px 12px 3px; position: relative; min-height: 25px;">
			<?= Loc::getMessage('intec.ai.admin.texts.progressbar.header') ?>
		</div>
		<div class="gpt-progressbar" style="width: 500px; height: 30px; border: 1px solid #606060; border-radius: 5px; overflow: hidden;">
			<div class="gpt-progressbar-inner" style="height: 100%; background-color: blue; width: 0%; background-color: #0b65c3; transition: 0.5s; box-shadow: inset 0 0 30px -4px #67c0ff;"></div>
		</div>
		<div class="gpt-progress-counter" style="text-align: center; margin: 15px 0; max-width: 500px">
			<span class="gpt-progress-done">0</span>
			<?= Loc::getMessage('intec.ai.admin.texts.progressbar.from') ?>
			<span class="gpt-progress-total"><?= count($elements) ?></span>
		</div>
		<div class="gpt-progress-errors"></div>
		<div class="gpt-progress-finish" style="display: none;">
			<div style="color: rgb(17, 126, 4); margin-bottom: 15px;">
				<?= Loc::getMessage('intec.ai.admin.texts.progressbar.finish') ?>
			</div>
			<div style="display: flex; justify-content: space-between; max-width: 500px">
				<div>
					<a href="/bitrix/admin/ai_texts.php?lang=<?= LANGUAGE_ID ?>" style="cursor: pointer">
						<?= Loc::getMessage('intec.ai.admin.texts.progressbar.button') ?>
					</a>
				</div>
				<div>
					<a href="/bitrix/admin/ai_tasks.php?lang=<?= LANGUAGE_ID ?>&desc=dateCreate&ai_tasks_order=desc" style="font-weight: bold">
						<?= Loc::getMessage('intec.ai.admin.texts.progressbar.toTasks') ?>
					</a>
				</div>
			</div>
		</div>
	</div>
	<script>
		let elements = JSON.parse('<?= $jsElements ?>');
		
		sendToQuene(elements, <?= json_encode($promptMask) ?>, '<?= $iblockProperty ?>');
	</script>
	<?php $form->EndCustomField('ai.texts.progressbar') ?>
	<!---------------------------------------------------------------------------------------------------------->
<?php
} else {
?>
	<!---------------------------------------------------------------------------------------------------------->	
	<?php $form->BeginCustomField('ai.texts.iblock', false) ?>
	<tr>
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.texts.iblock.header') ?><span style="color:red"> *</span>
		</td>
		<?php
			$iblocks = IblockTable::getList([
				'select' => ['ID', 'NAME']
			]);
		?>
		<td>
			<select name="iblock-id">
				<option value="none"><?= Loc::getMessage('intec.ai.admin.texts.iblock.choose') ?></option>
				<?php
				while ($iblock = $iblocks->fetch()) {
				?>
					<option value="<?= $iblock['ID'] ?>">[<?= $iblock['ID'] ?>] <?= $iblock['NAME'] ?></option>
				<?php } ?>
			</select>
		</td>
	</tr>
	<?php $form->EndCustomField('ai.texts.iblock') ?>
	<!---------------------------------------------------------------------------------------------------------->
	<?php $form->BeginCustomField('ai.texts.iblock-sections', false) ?>
	<tr style="display: none;">
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.texts.iblock-sections.header') ?><span style="color:red"> *</span>
		</td>
		<td>
			<select name="iblock-sections[]" multiple size="10">
				<option value="0"><?= Loc::getMessage('intec.ai.admin.texts.iblock-sections.root') ?> (0)</option>
			</select>
		</td>
	</tr>
	<?php $form->EndCustomField('ai.texts.iblock-sections') ?>
	<!---------------------------------------------------------------------------------------------------------->
	<?php $form->BeginCustomField('ai.texts.iblock-elements', false) ?>
	<tr style="display: none;">
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.texts.iblock-elements.header') ?><span style="color:red"> *</span>
		</td>
		<td>
			<select name="iblock-elements[]" multiple size="10"></select>
		</td>
	</tr>
	<?php $form->EndCustomField('ai.texts.iblock-elements') ?>
	<!---------------------------------------------------------------------------------------------------------->	
	<?php $form->BeginCustomField('ai.texts.select-property', false) ?>
	<tr>
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.texts.select-property.header') ?><span style="color:red"> *</span>
		</td>
		<td>
			<select name="select-property">
				<option value="PREVIEW_TEXT" class="default-option"><?= Loc::getMessage('intec.ai.admin.texts.select-property.preview') ?></option>
				<option value="DETAIL_TEXT" class="default-option"><?= Loc::getMessage('intec.ai.admin.texts.select-property.detail') ?></option>
				<option value="NAME" class="default-option"><?= Loc::getMessage('intec.ai.admin.texts.select-property.name') ?></option>
			</select>
		</td>
	</tr>
	<?php $form->EndCustomField('ai.texts.select-property') ?>
	<!---------------------------------------------------------------------------------------------------------->
	<?php $form->BeginCustomField('ai.texts.prompt-mask', false); ?>
	<tr>
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.texts.prompt-mask.header') ?><span style="color:red"> *</span>
		</td>
		<td>
			<textarea name="prompt-mask" style="width: 100%; height: 150px;"></textarea>
			<div class="adm-info-message">
				<?= Loc::getMessage('intec.ai.admin.texts.macroses') ?>
			</div>
		</td>
	</tr>
	<?php $form->EndCustomField('ai.texts.prompt-mask') ?>
	<!---------------------------------------------------------------------------------------------------------->
	<?php $form->BeginCustomField('ai.texts.prompt-example', false); ?>
	<tr>
		<td style="width: 40%">
			<?= Loc::getMessage('intec.ai.admin.texts.prompt-example.header') ?>
		</td>
		<td>
			<input type="radio" name="prompt-example" id="default"><label for="default"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.default') ?></label><br>
			<input type="radio" name="prompt-example" id="rewritingAnons"><label for="rewritingAnons"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.rewritingAnons') ?></label><br>
			<input type="radio" name="prompt-example" id="rewritingDetail"><label for="rewritingDetail"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.rewritingDetail') ?></label><br>
			<input type="radio" name="prompt-example" id="h1"><label for="h1"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.h1') ?></label><br>
			<input type="radio" name="prompt-example" id="title"><label for="title"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.title') ?></label><br>
			<input type="radio" name="prompt-example" id="keywords"><label for="keywords"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.keywords') ?></label><br>
			<input type="radio" name="prompt-example" id="description"><label for="description"><?= Loc::getMessage('intec.ai.admin.texts.choose-prompt-example.description') ?></label><br>
		</td>
	</tr>
	
	<?php $form->EndCustomField('ai.texts.prompt-example') ?>
	<!---------------------------------------------------------------------------------------------------------->
	<?php $form->BeginCustomField('ai.texts.start', false) ?>
	<tr>
		<td style="width: 40%">
			<input type="submit" id="start-generation" value="<?= Loc::getMessage('intec.ai.admin.texts.start-button') ?>">
		</td>
	</tr>
	<?php $form->EndCustomField('ai.texts.start') ?>
	<!---------------------------------------------------------------------------------------------------------->	
<?php } ?>
<?php $form->Show() ?>
<?php require 'parts/script.php'; ?>
<?php require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php') ?>