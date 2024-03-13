<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages(__FILE__);

$arField = $this->getUniversalFields($this->intProfileId, $intIBlockId)[$strField];
$arValues = is_array($arField['ALLOWED_VALUES_CUSTOM_DATA']) ? $arField['ALLOWED_VALUES_CUSTOM_DATA'] : [];

?>
<?if(!empty($arValues)):?>
	<div class="adm-list-table-wrap" style="border-radius:0;">
		<table class="adm-list-table" data-role="allowed-values-table">
			<thead>
				<tr class="adm-list-table-header">
					<td class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_VALUE');?></div>
					</td>
					<td class="adm-list-table-cell">
						<div class="adm-list-table-cell-inner"><?=static::getMessage('COL_COMMENT');?></div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?foreach($arValues as $strValue => $strName):?>
					<tr class="adm-list-table-row">
						<td class="adm-list-table-cell"><b><?=$strValue;?></b></td>
						<td class="adm-list-table-cell"><?=$strName;?></td>
					</tr>
				<?endforeach?>
			</tbody>
		</table>
	</div>
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
