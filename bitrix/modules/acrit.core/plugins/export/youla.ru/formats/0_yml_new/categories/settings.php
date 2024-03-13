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
				$strCategoryName = $intCategoryId;
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
