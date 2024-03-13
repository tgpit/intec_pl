<?
/**
 * Class for settings of fields and values
 */

namespace Acrit\Core\Export\Settings;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class SettingsMath extends SettingsBase {
	
	public static function getCode(){
		return 'MATH';
	}
	
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	public static function getHint(){
		return static::getMessage('DESC');
	}
	
	public static function getSort(){
		return 1100;
	}
	
	public static function getGroup(){
		return array(
			'CODE' => 'ADDITIONAL',
		);
	}
	
	public static function isForFields(){
		return true;
	}
	
	public static function isForValues(){
		return true;
	}
	
	public static function isShown($obField, $arParams){
		if(in_array($obField->getModuleId(), array_slice(\Acrit\Core\Export\Exporter::getInstance($obField->getModuleId())->getExportModules(true), -4))){
			return true;
		}
		return false;
	}
	
	public static function showSettings($strFieldCode, $obField, $arParams){
		?>
		<input type="checkbox" name="<?=static::getCode();?>" value="Y" id="<?=static::getInputID();?>"
			<?if($arParams[static::getCode()]=='Y'):?> checked="checked"<?endif?> />
		<?=Helper::ShowHint(static::getHint());?>
		&nbsp;
		<span id="<?=static::getInputID();?>_span">
			<label>
				<input type="checkbox" name="<?=static::getCode();?>_eval" value="Y" id="<?=static::getInputID();?>_eval"
					<?if($arParams[static::getCode().'_eval']=='Y'):?> checked="checked"<?endif?> />
				<?=static::getMessage('EVAL_NAME');?>
			</label>
			<?=Helper::ShowHint(static::getMessage('EVAL_DESC'));?>
		</span>
		<script>
		$('#<?=static::getInputID();?>').bind('change', function(){
			var eval = $('#<?=static::getInputID();?>_span');
			if($(this).is(':checked')){
				eval.show();
			}
			else{
				eval.hide();
			}
		}).trigger('change');
		</script>
		<?
	}
	
	public static function process(&$mValue, $arParams, $obField=null){
		if($arParams[static::getCode()] == 'Y') {
			if(in_array($obField->getModuleId(), array_slice(\Acrit\Core\Export\Exporter::getInstance($obField->getModuleId())->getExportModules(true), -4))){
				#
				static::processMultipleValue($mValue, $arParams, $obField, function(&$strValue, $arParams, $obField){
					$bEval = $arParams[static::getCode().'_eval'] == 'Y';
					if($bEval) {
						$strExpression = htmlspecialchars_decode($strValue, ENT_QUOTES | ENT_HTML5);
						$strExpression = 'return '.$strExpression.';';
						try{
							$strValue = eval($strExpression);
						}
						catch(\Error $obError){
							$errorMessage = sprintf('[ID: %s, %s] %s, expression: %s', $obField->getElementId(), $obField->getCode(), $obError->getMessage(), $strExpression);
							Log::getInstance($obField->getModuleId())->add($errorMessage, $obField->getProfileID());
							$strValue = '';
						}
						catch(\Exception $obError){
							$errorMessage = sprintf('[ID: %s, %s] %s, expression: %s', $obField->getElementId(), $obField->getCode(), $obError->getMessage(), $strExpression);
							Log::getInstance($obField->getModuleId())->add($errorMessage, $obField->getProfileID());
							$strValue = '';
						}
					}
					else {
						$strValue = static::calculateValue($strValue, $arParams, $obField);
					}
				});
				#
			}
		}
	}
	
	protected static function calculateValue($mValue, $arParams, $obField){
		$strDir = realpath(__DIR__.'/../../../include/php_shunting_yard');
		#
		require_once $strDir.'/src/RR/Shunt/Context.php';
		require_once $strDir.'/src/RR/Shunt/Parser.php';
		require_once $strDir.'/src/RR/Shunt/Scanner.php';
		require_once $strDir.'/src/RR/Shunt/Token.php';
		require_once $strDir.'/src/RR/Shunt/Exception/ParseError.php';
		require_once $strDir.'/src/RR/Shunt/Exception/RuntimeError.php';
		require_once $strDir.'/src/RR/Shunt/Exception/SyntaxError.php';
		#
		$obContext = new \RR\Shunt\Context;
		foreach(array('abs','sqrt','pow','round','floor','ceil','max','min') as $strPhpMathFunction){
			$obContext->def($strPhpMathFunction);
		}
		try {
			$mValue = \RR\Shunt\Parser::parse($mValue, $obContext);
		}
		catch(\Error $obError){
			$strMessage = static::getMessage('ERROR', array(
				'#VALUE#' => print_r($mValue, true),
				'#ERROR#' => $Error->getMessage(),
			));
			if(is_object($obField) && Helper::strlen($obField->getModuleId())){
				$strMessage = sprintf('[ID: %s, %s] %s', $obField->getElementId(), $obField->getCode(), $strMessage);
				Log::getInstance($obField->getModuleId())->add($strMessage, $obField->getProfileID());
			}
			$mValue = '';
		}
		catch(\Exception $Error){
			$strMessage = static::getMessage('ERROR', array(
				'#VALUE#' => print_r($mValue, true),
				'#ERROR#' => $Error->getMessage(),
			));
			if(is_object($obField) && Helper::strlen($obField->getModuleId())){
				$strMessage = sprintf('[ID: %s, %s] %s', $obField->getElementId(), $obField->getCode(), $strMessage);
				Log::getInstance($obField->getModuleId())->add($strMessage, $obField->getProfileID());
			}
			$mValue = '';
		}
		unset($obContext, $strDir, $strPhpMathFunction);
		return $mValue;
	}
	
}
