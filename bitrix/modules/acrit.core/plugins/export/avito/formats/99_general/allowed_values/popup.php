<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

$arField = $this->getUniversalFields($this->intProfileId, $intIBlockId)[$strField];
$arValues = is_array($arField['ALLOWED_VALUES_CUSTOM_DATA']) ? $arField['ALLOWED_VALUES_CUSTOM_DATA'] : [];
$bGroups = $arField['ALLOWED_VALUES_CUSTOM_BY_GROUPS'] === true;

?>
<?if($bGroups && !empty($arValues)):?>
	<?foreach($arValues as $arGroup):?>
		<?if(Helper::strlen($arGroup['name'])):?>
			<div style="font-size:120%;"><b><?=$arGroup['name'];?></b></div><br/>
		<?endif?>
		<?if(Helper::strlen($arGroup['values_title'])):?>
			<div><b><?=$arGroup['values_title'];?>:</b></div><br/>
		<?endif?>
		<div class="adm-list-table-wrap" style="border-radius:0;">
			<table class="adm-list-table" data-role="allowed-values-table">
				<thead>
					<tr class="adm-list-table-header">
						<td class="adm-list-table-cell">
							<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_VALUE');?></div>
						</td>
						<?if($bComments):?>
							<td class="adm-list-table-cell">
								<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_COMMENT');?></div>
							</td>
						<?endif?>
					</tr>
				</thead>
				<tbody>
					<?foreach($arGroup['values'] as $arValue):?>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell"><b><?=$arValue['value'];?></b></td>
							<?if($bComments):?>
								<td class="adm-list-table-cell"><?=$arValue['description'];?></td>
							<?endif?>
						</tr>
					<?endforeach?>
				</tbody>
			</table>
		</div>
		<br/>
		<br/>
	<?endforeach?>
<?elseif(!empty($arValues)):?>
	<?
	$bComments = !empty(array_filter(array_column($arValues, 'description')));	
	?>
	<?if(Helper::strlen($arField['ALLOWED_VALUES_CUSTOM_DATA_TITLE'])):?>
		<div><b><?=$arField['ALLOWED_VALUES_CUSTOM_DATA_TITLE'];?>:</b></div><br/>
	<?endif?>
	<div class="adm-list-table-wrap" style="border-radius:0;">
		<table class="adm-list-table" data-role="allowed-values-table">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_VALUE');?></div>
					</td>
					<?if($bComments):?>
						<td class="adm-list-table-cell">
							<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_COMMENT');?></div>
						</td>
					<?endif?>
				</tr>
			</thead>
			<tbody>
				<?foreach($arValues as $arValue):?>
					<tr class="adm-list-table-row">
						<td class="adm-list-table-cell"><b><?=$arValue['value'];?></b></td>
						<?if($bComments):?>
							<td class="adm-list-table-cell"><?=$arValue['description'];?></td>
						<?endif?>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	</div>
	<br/>
<?elseif(Helper::strlen($strUrl = $arField['ALLOWED_VALUES_CUSTOM_LINK'])):?>
	<?
	if(!preg_match('#^https?://#', $strUrl)){
		$strUrl = 'https://www.avito.ru'.$strUrl;
	}
	?>
	<?=static::getMessage('VALUES_LINK_INFO', [
		'#FIELD#' => $arField['NAME'],
		'#URL#' => $strUrl,
	]);?>
<?endif?>
<br/>
