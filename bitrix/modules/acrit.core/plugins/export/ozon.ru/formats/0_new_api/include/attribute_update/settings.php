<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\CategoryTable as Category;
	
$intIBlockID = $arParams['IBLOCK_ID'];
$arIBlockParams = $arParams['IBLOCK_PARAMS'];
?>

<tr id="tr_CATEGORIES_ALTERNATIVE">
	<td>
		<?=Helper::showHint(static::getMessage('CATEGORIES_ALTERNATIVE_DESC'));?>
		<label for="checkbox_CATEGORIES_ALTERNATIVE">
			<?=static::getMessage('CATEGORIES_ALTERNATIVE');?>
		</label>
	</td>
	<td>
		<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][CATEGORIES_ALTERNATIVE]" value="N" />
		<input type="checkbox" name="iblockparams[<?=$intIBlockID;?>][CATEGORIES_ALTERNATIVE]" value="Y" 
			data-role="ozon_categories_alternative"
			<?if($arIBlockParams['CATEGORIES_ALTERNATIVE']=='Y'):?>checked="checked"<?endif?>
			id="checkbox_CATEGORIES_ALTERNATIVE"
		/>
	</td>
</tr>
<tr id="tr_CATEGORIES_ALTERNATIVE_SELECT" style="display:none;">
	<td>
		<?=Helper::showHint(static::getMessage('CATEGORIES_ALTERNATIVE_SELECT_DESC'));?>
		<?=static::getMessage('CATEGORIES_ALTERNATIVE_SELECT');?>
	</td>
	<td>
		<div>
			<input type="button" value="<?=static::getMessage('CATEGORIES_ALTERNATIVE_SELECT_BUTTON');?>"
				data-role="categories-alternative-select" />
		</div>
	</td>
</tr>
<tr id="tr_CATEGORIES_ALTERNATIVE_LIST" style="display:none;">
	<td></td>
	<td>
		<?
		if(empty($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'])){
			$arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'] = [''];
		}
		?>
		<div data-role="categories-alternative-list">
			<?foreach($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'] as $intCategoryId):?>
				<?
				$strCategoryName = '';
				if($intCategoryId > 0){
					$strCategoryName = $this->formatCategoryName($intCategoryId);
				}
				?>
				<div data-role="categories-alternative-item">
					<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][CATEGORIES_ALTERNATIVE_LIST][]" 
						value="<?=$intCategoryId;?>">
					<div data-role="categories-alternative-item-name"><?=$strCategoryName?></div>
					<div data-role="categories-alternative-item-delete">
						<a class="acrit-inline-link"><?=static::getMessage('CATEGORIES_ALTERNATIVE_SELECT_DELETE');?></a>
					</div>
				</div>
			<?endforeach?>
		</div>
	</td>
</tr>

<tr class="heading"><td colspan="2"><?=static::getMessage('HEADING_ATTRIBUTES');?></td></tr>

<tr id="tr_CATEGORIES_UPDATE_ATTRIBUTES">
	<td>
		<?=Helper::showHint(static::getMessage('CAT_UPDATE_ATTR_DESC'));?>
		<?=static::getMessage('CAT_UPDATE_ATTR_NAME');?>
	</td>
	<td>
		<input type="button" value="<?=static::getMessage('CAT_UPDATE_ATTR_BTN_START');?>"
			data-role="categories-update-attributes-start" />
		<input type="button" value="<?=static::getMessage('CAT_UPDATE_ATTR_BTN_STOP');?>"
			data-role="categories-update-attributes-stop" class="hidden" />
		<div data-role="categories-update-attributes-loader" class="hidden"></div>
		<span data-role="categories-update-attributes-datetime" style="margin-left:10px;">
			<?
			$strCurrHash = md5(serialize($this->getUsedCategories($intIBlockID, true)));
			$strSavedHash = Helper::getOption($this->strModuleId, 'ozon_attributes_update_hash_'.$this->intProfileId);
			$strSavedTime = Helper::getOption($this->strModuleId, 'ozon_attributes_update_time_'.$this->intProfileId);
			$strDatetime = '---';
			if($strCurrHash == $strSavedHash){
				$strDatetime = \Bitrix\Main\Type\Datetime::createFromTimestamp($strSavedTime)->toString();
			}
			print static::getMessage('CAT_UPDATE_ATTR_DATE', ['#DATETIME#' => $strDatetime]);
			?>
		</span>
	</td>
</tr>
<tr id="tr_CATEGORIES_UPDATE_ATTRIBUTES_STATUS" style="display:none;">
	<td></td>
	<td>
		<div data-role="categories-update-attributes-result"></div>
	</td>
</tr>

<tr id="tr_GROUP_ATTRIBUTES">
	<td>
		<?=Helper::showHint(static::getMessage('GROUP_ATTRIBUTES_DESC'));?>
		<label for="checkbox_GROUP_ATTRIBUTES">
			<?=static::getMessage('GROUP_ATTRIBUTES');?>
		</label>
	</td>
	<td>
		<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][GROUP_ATTRIBUTES]" value="N" />
		<input type="checkbox" name="iblockparams[<?=$intIBlockID;?>][GROUP_ATTRIBUTES]" value="Y" 
			data-role="ozon_group_attributes"
			<?if($arIBlockParams['GROUP_ATTRIBUTES']=='Y'):?>checked="checked"<?endif?>
			id="checkbox_GROUP_ATTRIBUTES"
		/>
	</td>
</tr>

<tr id="tr_ATTRIBUTES_CANCEL_REQUIRED">
	<td>
		<?=Helper::showHint(static::getMessage('ATTRIBUTES_CANCEL_REQUIRED_DESC'));?>
		<?=static::getMessage('ATTRIBUTES_CANCEL_REQUIRED');?>
	</td>
	<td>
		<input type="text" name="iblockparams[<?=$intIBlockID;?>][ATTRIBUTES_CANCEL_REQUIRED]" size="40"
			value="<?=htmlspecialcharsbx($arIBlockParams['ATTRIBUTES_CANCEL_REQUIRED']);?>" 
			data-role="ozon_categories_attributes_cancel_required"
		/>
	</td>
</tr>

<tr id="tr_ATTRIBUTES_CANCEL_VALIDATE">
	<td>
		<?=Helper::showHint(static::getMessage('ATTRIBUTES_CANCEL_VALIDATE_DESC'));?>
		<?=static::getMessage('ATTRIBUTES_CANCEL_VALIDATE');?>
	</td>
	<td>
		<input type="text" name="iblockparams[<?=$intIBlockID;?>][ATTRIBUTES_CANCEL_VALIDATE]" size="40"
			value="<?=htmlspecialcharsbx($arIBlockParams['ATTRIBUTES_CANCEL_VALIDATE']);?>" 
			data-role="ozon_categories_attributes_cancel_validate"
		/>
	</td>
</tr>

<tr id="tr_BARCODE_NOT_REQUIRED">
	<td>
		<?=Helper::showHint(static::getMessage('BARCODE_NOT_REQUIRED_DESC'));?>
		<?=static::getMessage('BARCODE_NOT_REQUIRED');?>
	</td>
	<td>
		<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][BARCODE_NOT_REQUIRED]" value="N" />
			<input type="checkbox" name="iblockparams[<?=$intIBlockID;?>][BARCODE_NOT_REQUIRED]" value="Y" 
				data-role="ozon_barcode_not_required"
				<?if($arIBlockParams['BARCODE_NOT_REQUIRED']=='Y'):?>checked="checked"<?endif?>
				id="checkbox_barcode_not_required"
			/>
	</td>
</tr>

<tr id="tr_CHECK_REMOTE_ATTRIBUTE_VALUES">
	<td>
		<?=Helper::showHint(static::getMessage('CHECK_REMOTE_ATTRIBUTE_VALUES_DESC'));?>
		<?=static::getMessage('CHECK_REMOTE_ATTRIBUTE_VALUES');?>
	</td>
	<td>
		<a href="#" data-role="ozon_check_remote_attribute_values" class="acrit-inline-link">
			<?=static::getMessage('CHECK_REMOTE_ATTRIBUTE_VALUES_LINK');?>
		</a>
	</td>
</tr>

<tr id="tr_CHECK_REMOTE_ATTRIBUTE_VALUES_2" style="display:none">
	<td></td>
	<td>
		<textarea style="height:200px; max-height:2000px; min-height:50px; resize:vertical; width:90%;"
			data-role="ozon_remote_attribute_values"
		></textarea>
	</td>
</tr>

<script>
BX.message({
	'ACRIT_OZON_ATTRIBUTE_VALUES_CHECK_CATEGORY_ID': '<?=static::getMessage('CHECK_REMOTE_ATTRIBUTE_VALUES_CATEGORY_ID');?>',
	'ACRIT_OZON_ATTRIBUTE_VALUES_CHECK_ATTRIBUTE_ID': '<?=static::getMessage('CHECK_REMOTE_ATTRIBUTE_VALUES_ATTRIBUTE_ID');?>',
});
</script>