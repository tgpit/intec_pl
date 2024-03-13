<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\CategoryTable as Category;
	
$intIBlockID = $arParams['IBLOCK_ID'];
$arIBlockParams = $arParams['IBLOCK_PARAMS'];

?>

<tr id="tr_CATEGORIES_ALWAYS_ALL">
	<td>
		<label for="checkbox_CATEGORIES_ALWAYS_ALL">
			<?=Helper::showHint(static::getMessage('CAT_ALWAYS_ALL_DESC'));?>
			<?=static::getMessage('CAT_ALWAYS_ALL_NAME');?>
		</label>
	</td>
	<td>
		<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][CATEGORIES_ALWAYS_ALL]" 
			value="N">
		<input type="checkbox" name="iblockparams[<?=$intIBlockID;?>][CATEGORIES_ALWAYS_ALL]"
			id="checkbox_CATEGORIES_ALWAYS_ALL" 
			value="Y"<?if($arIBlockParams['CATEGORIES_ALWAYS_ALL'] == 'Y'):?> checked<?endif?>>
	</td>
</tr>
