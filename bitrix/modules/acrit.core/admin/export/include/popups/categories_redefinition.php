<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\CategoryRedefinitionTable as CategoryRedefinition;
	

Loc::loadMessages(__FILE__);

# Save values
if($bSaveRedefinition) {
	if(is_array($arPost['s'])){
		$arSectionDefinitions = $arPost['s'];
		$arSectionPictures = is_array($arPost['p']) ? $arPost['p'] : [];
		if(!Helper::isUtf()){
			$arSectionDefinitions = Helper::convertEncoding($arSectionDefinitions, 'UTF-8', 'CP1251');
		}
		$arActualSectionsID = array();
		foreach($arSectionDefinitions as $intSectionID => $strSectionName){
			$arActualSectionsID[] = $intSectionID;
			$strSectionName = trim($strSectionName);
			$strPicture = trim($arSectionPictures[$intSectionID] ?? '');
			$arFields = array(
				'SECTION_NAME' => $strSectionName,
				'PICTURE' => $strPicture,
			);
			$arQuery = [
				'filter' => array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'SECTION_ID' => $intSectionID,
				),
				'limit' => '1',
				'select' => array(
					'ID',
					'SECTION_NAME',
					'PICTURE',
				),
			];
			$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
			if($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
				Helper::call($strModuleId, 'CategoryRedefinition', 'update', [$arCategoryRedefinition['ID'], $arFields]);
			}
			else {
				$arFields = array_merge($arFields, array(
					'PROFILE_ID' => $intProfileID,
					'IBLOCK_ID' => $intIBlockID,
					'SECTION_ID' => $intSectionID,
				));
				Helper::call($strModuleId, 'CategoryRedefinition', 'add', [$arFields]);
			}
		}
		# Delete old
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
				'!SECTION_ID' => $arActualSectionsID,
			),
			'select' => array(
				'ID',
			),
		];
		$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
		while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
			Helper::call($strModuleId, 'CategoryRedefinition', 'delete', [$arCategoryRedefinition['ID']]);
		}
		$obPlugin->onRedefinitionCategoryAfterSave($arProfile, $arSectionDefinitions);
	}
	elseif ($arPost['clear_all']=='Y'){
		$arQuery = [
			'filter' => array(
				'PROFILE_ID' => $intProfileID,
				'IBLOCK_ID' => $intIBlockID,
			),
			'select' => array(
				'ID',
			),
		];
		$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
		while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
			Helper::call($strModuleId, 'CategoryRedefinition', 'delete', [$arCategoryRedefinition['ID']]);
		}
	}
	// Remove old generated data
	Helper::call($strModuleId, 'ExportData', 'deleteGeneratedData', [$intProfileID, $intIBlockID]);
	return;
}

$arCategories = explode(',', $arPost['categories_id']);
Helper::arrayRemoveEmptyValues($arCategories);
$strMode = $arPost['categories_mode'];
$strSource = $arPost['categories_source'];

# Get all sections for iblock
$arSectionsAll = array();
$arFilter = array(
	'IBLOCK_ID' => $intIBlockID,
	'CHECK_PERMISSIONS' => 'N',
);
$arSelect = array(
	'ID',
	'NAME',
	'DEPTH_LEVEL',
	'IBLOCK_SECTION_ID',
);
$resSectionsAll = \CIBlockSection::getList(array('LEFT_MARGIN'=>'ASC'), $arFilter, false, $arSelect);
while($arSection = $resSectionsAll->getNext()){
	$arSectionsAll[$arSection['ID']] = $arSection;
}

# Function for recursive
function walkSection(&$arSection, $callback, &$arParams, $arChain=array()){ # ToDo: перенести в отдельный класс по Redefinition
	if(is_array($arSection)) {
		$arChain[] = $arSection['ID'];
		call_user_func_array($callback, array(&$arSection, &$arParams, $arChain));
		if(is_array($arSection['SECTIONS'])) {
			foreach($arSection['SECTIONS'] as &$arSubSection){
				walkSection($arSubSection, $callback, $arParams, $arChain);
			}
		}
	}
}

# PreProcess input data
switch($strSource){
	case 'selected':
		foreach($arSectionsAll as $intSectionID => $arSection){
			if(!in_array($intSectionID, $arCategories)){
				unset($arSectionsAll[$intSectionID]);
			}
		}
		break;
	case 'selected_with_subsections':
		$arTree = Helper::sectionsArrayToTree($arSectionsAll);
		$arParams = array(
			'CATEGORIES_SELECTED' => $arCategories,
			'CATEGORIES_RESULT' => array(),
		);
		#
		foreach($arTree as $key => &$arSection){
			walkSection($arSection, function(&$arSection, &$arParams, $arChain){
				if(is_array($arChain) && count(array_intersect($arParams['CATEGORIES_SELECTED'], $arChain))) {
					$arParams['CATEGORIES_RESULT'][] = $arSection['ID'];
				}
			}, $arParams);
		}
		#
		if(is_array($arParams['CATEGORIES_RESULT'])) {
			foreach($arSectionsAll as $intSectionID => $arSection){
				if(!in_array($intSectionID, $arParams['CATEGORIES_RESULT'])){
					unset($arSectionsAll[$intSectionID]);
				}
			}
		}
		break;
	default: // all
		break;
}

$arCategoryRedefinitionAll = array();
$arQuery = [
	'filter' => array(
		'PROFILE_ID' => $intProfileID,
		'IBLOCK_ID' => $intIBlockID,
	),
	'select' => array(
		'SECTION_ID',
		'SECTION_NAME',
		'PICTURE',
	),
];
#$resCategoryRedefinition = CategoryRedefinition::getList($arQuery);
$resCategoryRedefinition = Helper::call($strModuleId, 'CategoryRedefinition', 'getList', [$arQuery]);
while($arCategoryRedefinition = $resCategoryRedefinition->fetch()){
	$arCategoryRedefinitionAll[$arCategoryRedefinition['SECTION_ID']] = $arCategoryRedefinition;
}

$bStrictMode = $strMode == CategoryRedefinition::MODE_STRICT;

$bPictures = $obPlugin->getRedefinitionPicturesVisible();

?>

<div>
	<table class="acrit-exp-table-categories-redefinition<?if($bPictures):?> acrit-exp-table-categories-redefinition-with-pictures<?endif?>">
		<thead>
			<tr>
				<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_ID');?></th>
				<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_OLDNAME');?></th>
				<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_NEWNAME');?></th>
				<?if($bStrictMode):?>
					<th></th>
				<?endif?>
				<?if($bPictures):?>
					<th></th>
					<th><?=Loc::getMessage('ACRIT_EXP_POPUP_CATEGORIES_REDEFINITION_COLUMN_PICTURE');?></th>
					<th></th>
				<?endif?>
			</tr>
		</thead>
		<tbody>
			<?foreach($arSectionsAll as $arSection):?>
				<tr data-depth="<?=$arSection['DEPTH_LEVEL'];?>">
					<td>
						<?=$arSection['ID'];?>
					</td>
					<td>
					<?
						print str_repeat('.&nbsp;&nbsp;', $arSection['DEPTH_LEVEL']-1);
					?>
						<?=$arSection['NAME'];?>
					</td>
					<td>
						<input type="text" name="s[<?=$arSection['ID'];?>]"
							value="<?=htmlspecialcharsbx($arCategoryRedefinitionAll[$arSection['ID']]['SECTION_NAME']);?>"
							title="<?=htmlspecialcharsbx($arCategoryRedefinitionAll[$arSection['ID']]['SECTION_NAME']);?>"
							data-role="categories-redefinition-text"
						/>
						<a href="#" class="acrit-exp-table-categories-redefinition-clear" 
							data-role="categories-redefinition-button-clear">&times;</a>
					</td>
					<?if($bStrictMode):?>
						<td>
							<input type="button" value="" data-role="categories-redefinition-button-select"
								data-iblock-id="<?=$intIBlockID;?>" data-section-id="<?=$arSection['ID'];?>"
							/>
						</td>
					<?endif?>
					<?if($bPictures):?>
						<td></td>
						<td>
							<input type="text" name="p[<?=$arSection['ID'];?>]"
								value="<?=htmlspecialcharsbx($arCategoryRedefinitionAll[$arSection['ID']]['PICTURE']);?>"
								title="<?=htmlspecialcharsbx($arCategoryRedefinitionAll[$arSection['ID']]['PICTURE']);?>"
								data-role="categories-redefinition-picture"
							/>
							<a href="#" class="acrit-exp-table-categories-redefinition-picture-clear" 
								data-role="categories-redefinition-button-picture-clear">&times;</a>
						</td>
						<td>
							<input type="button" value="" data-role="categories-redefinition-button-picture-structure"
								data-iblock-id="<?=$intIBlockID;?>" data-section-id="<?=$arSection['ID'];?>"
								title="<?=Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_PICTURE_STRUCTURE');?>"
							/>
							<input type="button" value="" data-role="categories-redefinition-button-picture-medialib"
								data-iblock-id="<?=$intIBlockID;?>" data-section-id="<?=$arSection['ID'];?>"
								title="<?=Loc::getMessage('ACRIT_EXP_PROFILE_CATEGORY_REDEFINITION_PICTURE_MEDIALIB');?>"
							/>
						</td>
					<?endif?>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
	<?if($bPictures):?>
		<div style="display:none;">
			<?// General ?>
			<script>
				window.acritExpCategoryRedefinitionLastButton = null;
				$('input[data-role="categories-redefinition-button-picture-medialib"]').bind('click', function(e){
					window.acritExpCategoryRedefinitionLastButton = this;
					$('#bx_ml_acrit_exp_category_redefinition_btn_medialib').trigger('click');
				});
				$('input[data-role="categories-redefinition-button-picture-structure"]').bind('click', function(e){
					window.acritExpCategoryRedefinitionLastButton = this;
					$('#acrit_exp_category_redefinition_btn_structure').trigger('click');
				});
				// Structure
				function ACRIT_EXP_CATEGORY_REDEFINITION_ONRESULT_STRUCTURE(filename, path, site, title, menu){
					if(window.acritExpCategoryRedefinitionLastButton){
						let
							result = (path + '/' + filename).replace(/\/{2,}/g, '/');
						$(window.acritExpCategoryRedefinitionLastButton).parent().prev().children('input[type=text]').val(result);
					}
				}
				// Medialib
				function ACRIT_EXPCATEGORY_REDEFINITION__ONRESULT_MEDIALIB(item){
					if(window.acritExpCategoryRedefinitionLastButton){
						let
							result = item.src;
						$(window.acritExpCategoryRedefinitionLastButton).parent().prev().children('input[type=text]').val(result);
					}
				}
			</script>
			<?// Structure ?>
			<?if($GLOBALS['USER']->canDoOperation('fileman_view_file_structure')):?>
				<?=\CMedialib::showBrowseButton([
					'event' => 'ACRIT_EXP_CATEGORY_REDEFINITION_STRUCTURE',
					'mode' => 'file_dialog',
					'value' => '... 1',
					'id' => 'acrit_exp_category_redefinition_btn_structure',
					'bReturnResult' => true
				]);?>
				<?\CAdminFileDialog::showScript([
					'event' => 'ACRIT_EXP_CATEGORY_REDEFINITION_STRUCTURE',
					'arResultDest' => ['FUNCTION_NAME' => 'ACRIT_EXP_CATEGORY_REDEFINITION_ONRESULT_STRUCTURE'],
					'arPath' => [],
					'select' => 'F',
					'operation' => 'O',
					'showUploadTab' => true,
					'showAddToMenuTab' => false,
					'fileFilter' => $strFileFilter,
					'allowAllFiles' => true,
				]);?>
			<?endif?>
			<?// Medialib ?>
			<?if(Helper::getOption('fileman', 'use_medialib') != 'N'):?>
				<?if(\CMedialib::canDoOperation('medialib_view_collection', 0)):?>
					<?=\CMedialib::showBrowseButton([
						'mode' => 'medialib',
						'value' => '... 2',
						'event' => 'ACRIT_EXP_CATEGORY_REDEFINITION_MEDIALIB',
						'id' => 'acrit_exp_category_redefinition_btn_medialib',
						'MedialibConfig' => [
							'arResultDest' => ['FUNCTION_NAME' => 'ACRIT_EXPCATEGORY_REDEFINITION__ONRESULT_MEDIALIB'],
							'types' => ['image'],
						],
						'bReturnResult' => true
					]);?>
				<?endif?>
			<?endif?>
		</div>
	<?endif?>
</div>
<br/>

