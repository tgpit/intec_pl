<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

$strSearchText = null;
$intSearchLimit = 100;

if($bCategoriesFilter && isset($_POST['search']) && Helper::strlen($_POST['search']) > 0){
	$strSearchText = htmlspecialcharsbx($_POST['search']);
}

$arCategories = $obPlugin->getCategoriesListFiltered($intProfileID, $strSearchText, $intSearchLimit);

ob_start();
foreach($arCategories as $strCategoryName){
	?>
		<option value="<?=$strCategoryName;?>"><?=$strCategoryName;?></option>
	<?
}
$strOptionsHtml = ob_get_clean();

if(count($arCategories) == 0){
	ob_start();
	?>
		<option value="" disabled="disabled"><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_SELECT_NOT_FOUND');?></option>
	<?
	$strOptionsHtml = ob_get_clean().$strOptionsHtml;
}

if($bCategoriesFilter){
	print $strOptionsHtml;
	return;
}
?>

<table class="acrit-exp-table-category-redefinition-select-value">
	<tbody>
		<tr>
			<td>
				<input type="text" value="" data-role="category-redefinition-search"
					placeholder="<?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_SELECT_TEXT_PLACEHOLDER');?>" />
			</td>
		</tr>
		<tr>
			<td>
				<select size="10" data-role="category-redefinition-select">
					<?=$strOptionsHtml;?>
				</select>
			</td>
		</tr>
	</tbody>
</table>




