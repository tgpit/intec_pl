<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class SettingsMultiple extends SettingsBase {
	
	protected static $arSeparators = [
		'comma' => ',',
		'dot' => '.',
		'semicolon' => ';',
		'dash' => '-',
		'vertical_line' => '|',
		'underline' => '_',
		'slash' => '/',
		'backslash' => '\\',
		'hash' => '#',
		'new_line' => "\n",
		'new_line_2' => "\n\n",
		'space' => ' ',
		'other' => '',
		'empty' => '',
	];
	
	public static function getCode(){
		return 'MULTIPLE';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 20; # this settings must be first at all! (after 'empty')
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'GENERAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		$arOptions = array(
			'join' => static::getMessage('JOIN'),
			'first' => static::getMessage('FIRST'),
		);
		if($obField->isMultiple()){
			$arOptions['multiple'] = static::getMessage('MULTIPLE');
		}
		$strDivClass = static::getInputID().'_div';
		// 1. Display main <select>
		$arOptions = array(
			'REFERENCE' => array_values($arOptions),
			'REFERENCE_ID' => array_keys($arOptions),
		);
		$strName = static::getCode();
		$strID = static::getInputID();
		print SelectBoxFromArray($strName, $arOptions, $arParams[$strName], '', 'id="'.$strID.'"');
		print Helper::ShowHint(static::getHint());
		//
		$arSeparator = static::$arSeparators;
		foreach($arSeparator as $key => $value){
			$arSeparator[$key] = static::getMessage('SEPARATOR_'.toUpper($key));
		}
		$arSeparator = array(
			'REFERENCE' => array_values($arSeparator),
			'REFERENCE_ID' => array_keys($arSeparator),
		);
		print '<br/>';
		// 2. Display separator <select>
		$strName_Separator = static::getCode().'_separator';
		$strID_Separator = static::getInputID().'_separator';
		$strName_SeparatorOther = static::getCode().'_separator_other';
		$strID_SeparatorOther = static::getInputID().'_separator_other';
		if($arParams[$strName_Separator] == 'other' && !Helper::strlen($arParams[$strName_SeparatorOther])){
			$arParams[$strName_Separator] = 'empty';
		}
		?>
		<div class="<?=$strDivClass;?>" id="<?=$strID_Separator?>_wrapper">
			<span><?=static::getMessage('LABEL_SEPARATOR');?>:</span>
			<?
			print SelectBoxFromArray($strName_Separator, $arSeparator, $arParams[$strName_Separator], '', 
				'id="'.$strID_Separator.'"');
			?>
			<input name="<?=$strName_SeparatorOther;?>" type="text" id="<?=$strID_SeparatorOther;?>" size="5"
				value="<?=htmlspecialcharsbx($arParams[$strName_SeparatorOther]);?>" />
			<?=Helper::ShowHint(static::getMessage('HINT_SEPARATOR'));?>
		</div>
		<?
		// 3. Display scheme <div> and <input>
		$strName_Scheme = static::getCode().'_scheme';
		$strID_Scheme = static::getInputID().'_scheme';
		?>
		<div class="<?=$strDivClass;?>" id="<?=$strID_Scheme;?>_scheme">
			<span><?=static::getMessage('LABEL_SCHEME');?>:</span>
			<input name="<?=$strName_Scheme;?>" type="text" id="<?=$strID_Scheme;?>" size="20"
				placeholder="<?=static::getMessage('PLACEHOLDER_SCHEME');?>"
				value="<?=htmlspecialcharsbx($arParams[$strName_Scheme]);?>"/>
			<?=Helper::ShowHint(static::getMessage('HINT_SCHEME'));?>
		</div>
		<?
		// 4. Display split <div> and <input>
		$strName_SplitBy = static::getCode().'_split_by';
		$strID_SplitBy = static::getInputID().'_split_by';
		$strName_SplitRegExp = static::getCode().'_split_regexp';
		$strID_SplitRegExp = static::getInputID().'_split_regexp';
		$strName_SplitRegExpModifier = static::getCode().'_split_regexp_modifier';
		$strID_SplitRegExpModifier = static::getInputID().'_split_regexp_modifier';
		?>
		<div class="<?=$strDivClass;?>" id="<?=$strID_SplitBy;?>_wrapper">
			<span><?=static::getMessage('LABEL_SPLIT_BY');?>:</span>
			<input name="<?=$strName_SplitBy;?>" type="text" id="<?=$strID_SplitBy;?>" size="10"
				placeholder="<?=static::getMessage('PLACEHOLDER_SPLIT_BY');?>"
				value="<?=htmlspecialcharsbx($arParams[$strName_SplitBy]);?>"
				style="width:130px;" />
			&nbsp;
			<input type="checkbox" name="<?=$strName_SplitRegExp;?>" value="Y" id="<?=$strID_SplitRegExp;?>"
				<?if($arParams[$strName_SplitRegExp]=='Y'):?>checked<?endif?> />
			<label for="<?=$strID_SplitRegExp;?>"><?=static::getMessage('LABEL_REGEXP');?></label>
			&nbsp;
			<input name="<?=$strName_SplitRegExpModifier;?>" type="text" id="<?=$strID_SplitRegExpModifier;?>" size="10"
				placeholder="<?=static::getMessage('PLACEHOLDER_REGEXP_MODIFIER');?>"
				value="<?=htmlspecialcharsbx($arParams[$strName_SplitRegExpModifier]);?>" 
				style="width:45px;" />
			<?=Helper::ShowHint(static::getMessage('HINT_SPLIT_BY'));?>
		</div>
		<style>
			.<?=$strDivClass;?> {margin-top:3px;}
			.<?=$strDivClass;?> > * {vertical-align:middle;}
			.<?=$strDivClass;?> > span:first-child {display:inline-block; text-align:right; width:100px;}
		</style>
		<script>
		// 1. Select type
		$('#<?=$strID;?>').bind('change', function(){
			var
				divSeparator = $('#<?=$strID_Separator;?>_wrapper'),
				selectSeparator = $('select', divSeparator),
				divSplit = $('#<?=$strID_SplitBy;?>_wrapper');
			// Separator
			divSeparator.toggle($(this).val()=='join');
			selectSeparator.trigger('change');
			// Split
			divSplit.toggle($(this).val()=='multiple');
		}).trigger('change');
		// 2. Select separator
		$('#<?=$strID_Separator;?>').bind('change', function(){
			$('#<?=$strID_SeparatorOther;?>').toggle($(this).val()=='other' && $(this).is(':visible'));
		}).trigger('change');
		// 3. Select split by regexp
		$('#<?=$strID_SplitRegExp;?>').bind('change', function(){
			$('#<?=$strID_SplitRegExpModifier;?>').toggle($(this).prop('checked'));
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		$strCode = static::getCode();
		if(is_string($mValue) && $arParams[$strCode] == 'multiple' && Helper::strlen($arParams[$strCode.'_split_by'])){
			$mValue = [$mValue];
			static::splitValues($mValue, $arParams[$strCode.'_split_by'], 
				$arParams[$strCode.'_split_regexp'] == 'Y', $arParams[$strCode.'_split_regexp_modifier']);
		}
		if(is_array($mValue)) {
			$mParamValue = $arParams[$strCode];
			if($mParamValue=='first'){
				# Search first non-empty value
				$bFound = false;
				static::applyScheme($mValue, $arParams[$strCode.'_scheme']);
				foreach($mValue as $mValueItem){
					if (is_string($mValueItem) && Helper::strlen($mValueItem) || is_numeric($mValueItem) && $mValueItem>0 || is_array($mValueItem) && !empty($mValueItem)) {
						$mValue = $mValueItem;
						$bFound = true;
						break;
					}
				}
				if(!$bFound){
					$mValue = '';
				}
			}
			elseif($mParamValue=='multiple'){
				# Nothing, keep it as is
				$arValueTmp = array();
				foreach($mValue as $mValueItem){
					if(is_array($mValueItem)){
						foreach($mValueItem as $mValueSubItem){
							$arValueTmp[] = $mValueSubItem;
						}
					}
					else {
						$arValueTmp[] = $mValueItem;
					}
				}
				Helper::arrayRemoveEmptyValues($arValueTmp, false);
				static::applyScheme($arValueTmp, $arParams[$strCode.'_scheme']);
				static::splitValues($arValueTmp, $arParams[$strCode.'_split_by'], 
					$arParams[$strCode.'_split_regexp'] == 'Y', $arParams[$strCode.'_split_regexp_modifier']);
				$mValue = $arValueTmp;
			}
			else {
				# Join
				$arValueTmp = array();
				foreach($mValue as $mValueItem){
					if(is_array($mValueItem)){
						foreach($mValueItem as $mValueSubItem){
							$arValueTmp[] = $mValueSubItem;
						}
					}
					else{
						$arValueTmp[] = $mValueItem;
					}
				}
				Helper::arrayRemoveEmptyValues($arValueTmp, false);
				static::applyScheme($arValueTmp, $arParams[$strCode.'_scheme']);
				$strSeparator = $arParams[$strCode.'_separator'];
				if(is_null($strSeparator)){
					$strSeparator = 'comma';
				}
				$strSeparator = $strSeparator == 'other' 
					? $arParams[$strCode.'_separator_other'] 
					: static::$arSeparators[$strSeparator];
				$mValue = implode($strSeparator, $arValueTmp);
			}
		}
	}
	
	protected static function applyScheme(&$arValue, $strScheme){
		if(empty($arValue) || empty($strScheme)){
			return;
		}
		$arScheme = explode(',', $strScheme);
		$arScheme = array_map(function($mItem){
			$mItem = trim($mItem);
			$mItemTmp = array();
			if(preg_match('#^(\d+)\-(\d+)$#', $mItem, $arMatch)){
				$intFrom = $arMatch[1];
				$intTo = $arMatch[2];
				if($intTo == $intFrom){
					$mItemTmp[] = $intFrom;
				}
				elseif($intTo > $intFrom){
					for($i = $intFrom; $i <= $intTo; $i++){
						$mItemTmp[] = $i;
					}
				}
				elseif($intTo < $intFrom){
					for($i = $intFrom; $i >= $intTo; $i--){
						$mItemTmp[] = $i;
					}
				}
			}
			else{
				if(is_numeric($mItem)){
					$mItem = IntVal($mItem);
					if($mItem > 0){
						$mItemTmp[] = $mItem;
					}
				}
			}
			return $mItemTmp;
		}, $arScheme);
		$arSchemeTmp = array();
		foreach($arScheme as $arSchemeItem){
			foreach($arSchemeItem as $intValue){
				if(!in_array($intValue, $arSchemeTmp)){
					$arSchemeTmp[] = $intValue;
				}
			}
		}
		#
		$arValueTmp = array();
		$intIndex = 0;
		foreach($arValue as $mValue){
			$arValueTmp[++$intIndex] = $mValue;
		}
		$arValue = array();
		#
		foreach($arSchemeTmp as $intIndex){
			if(isset($arValueTmp[$intIndex])){
				$arValue[] = $arValueTmp[$intIndex];
			}
		}
		#
		unset($arScheme, $arSchemeTmp, $arSchemeItem, $arValueTmp, $mItem, $intValue, $intIndex);
	}
	
	protected static function splitValues(&$arValues, $strSplitter, $bUseRegExp=false, $strRegExpModifier=null){
		$arValuesTmp = [];
		if(Helper::strlen($strSplitter)){
			foreach($arValues as $strValue){
				if(is_string($strValue)){
					if($bUseRegExp){
						$strPattern = sprintf('#%s#%s', str_replace('#', '\#', $strSplitter), $strRegExpModifier);
						$arValueChains = preg_split($strPattern, $strValue);
					}
					else{
						$arValueChains = explode($strSplitter, $strValue);
					}
					if(is_array($arValueChains)){
						foreach($arValueChains as $key => $strValue){
							if(!Helper::strlen(trim($strValue))){
								unset($arValueChains[$key]);
							}
						}
						$arValuesTmp = array_merge($arValuesTmp, $arValueChains);
					}
				}
			}
			$arValues = $arValuesTmp;
		}
	}
	
}
