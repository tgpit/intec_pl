<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class XmlChars extends SettingsBase {
	
	public static function getCode(){
		return 'XMLCHARS';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 400;
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
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_span">	
			<?=static::getMessage('ALSO');?>
			&nbsp;
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_tab" value="Y"
					<?if($arParams[static::getCode().'_tab'] == 'Y'):?>checked="checked"<?endif?> />
				<?=static::getMessage('ALSO_TAB');?>
				<?=Helper::ShowHint(static::getMessage('ALSO_TAB_HINT'));?>
			</label>
			&nbsp;&nbsp;
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_newline" value="Y"
					<?if($arParams[static::getCode().'_newline'] == 'Y'):?>checked="checked"<?endif?> />
				<?=static::getMessage('ALSO_NEWLINE');?>
				<?=Helper::ShowHint(static::getMessage('ALSO_NEWLINE_HINT'));?>
			</label>
			&nbsp;&nbsp;
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_caret" value="Y"
					<?if($arParams[static::getCode().'_caret'] == 'Y'):?>checked="checked"<?endif?> />
				<?=static::getMessage('ALSO_CARET');?>
				<?=Helper::ShowHint(static::getMessage('ALSO_CARET_HINT'));?>
			</label>
			&nbsp;&nbsp;
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_space" value="Y"
					<?if($arParams[static::getCode().'_space'] == 'Y'):?>checked="checked"<?endif?> />
				<?=static::getMessage('ALSO_SPACE');?>
				<?=Helper::ShowHint(static::getMessage('ALSO_SPACE_HINT'));?>
			</label>
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var span = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				span.show();
			}
			else{
				span.hide();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()] == 'Y'){
			static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
				$strValue = static::removeForbiddenAsciiCharacters($strValue, $arParams);
			});
		}
	}
	
	protected static function removeForbiddenAsciiCharacters($strText, $arParams){
		$arExclude = [];
		if($arParams[static::getCode().'_tab'] != 'Y'){
			$arExclude[9] = "\t";
		}
		if($arParams[static::getCode().'_newline'] != 'Y'){
			$arExclude[10] = "\n";
		}
		if($arParams[static::getCode().'_caret'] != 'Y'){
			$arExclude[13] = "\r";
		}
		if($arParams[static::getCode().'_space'] != 'Y'){
			$arExclude[32] = " ";
		}
		for($i=1; $i<=32; $i++){
			if(array_key_exists($i, $arExclude)){
				continue;
			}
			$strText = str_replace(chr($i), '', $strText);
		}
		return $strText;
	}
	
}
