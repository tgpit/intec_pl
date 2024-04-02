<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Cli;

Loc::loadMessages(__FILE__);

$strLang = 'ACRIT_EXP_CRON_SEND_TO_EMAIL_';

$arCronSendToEmail = $obPlugin->getCronSendEmailTo();

$arCronSendToEmail = array_merge([
	[]
], $arCronSendToEmail);

?>

<div class="acrit-exp-cron-send-to-email">
	<table class="adm-list-table" data-role="acrit-exp-cron-send-to-email-table">
		<thead>
			<tr class="adm-list-table-header">
				<td class="adm-list-table-cell">
					<?=Loc::getMessage($strLang.'FROM');?>
				</td>
				<td class="adm-list-table-cell">
					<?=Loc::getMessage($strLang.'TO');?>
				</td>
				<td class="adm-list-table-cell">
					<?=Loc::getMessage($strLang.'SUBJECT');?>
				</td>
				<td class="adm-list-table-cell"></td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<input type="button" data-role="cron-send-to-email-add" value="<?=Loc::getMessage($strLang.'ADD');?>">
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?foreach($arCronSendToEmail as $key => $arItem):?>
				<tr class="adm-list-table-row"<?if(!$key):?> style="display:none;"<?endif?>>
					<td class="adm-list-table-cell">
						<input type="text" data-role="cron-send-to-email-from" size="30"
							name="PROFILE[PARAMS][CRON_EMAIL][FROM][]"
							value="<?=htmlspecialcharsbx($arItem['FROM']);?>">
					</td>
					<td class="adm-list-table-cell">
						<input type="text" data-role="cron-send-to-email-to" size="30"
							name="PROFILE[PARAMS][CRON_EMAIL][TO][]"
							value="<?=htmlspecialcharsbx($arItem['TO']);?>">
					</td>
					<td class="adm-list-table-cell">
						<input type="text" data-role="cron-send-to-email-subject" size="40"
							name="PROFILE[PARAMS][CRON_EMAIL][SUBJECT][]"
							value="<?=htmlspecialcharsbx($arItem['SUBJECT']);?>">
					</td>
					<td class="adm-list-table-cell">
						<input type="button" data-role="cron-send-to-email-delete" title="<?=Loc::getMessage($strLang.'DELETE');?>">
					</td>
				</tr>
			<?endforeach?>
			<tr data-role="cron-send-to-email-empty">
				<td colspan="4"><?=Loc::getMessage($strLang.'EMPTY');?></td>
			</tr>
		</tbody>
	</table>
</div>
