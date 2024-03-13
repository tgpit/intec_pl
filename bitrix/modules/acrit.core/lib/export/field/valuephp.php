<?
/**
 * Class to work with PHP values in fields
 */

namespace Acrit\Core\Export\Field;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Field\Field;

class ValuePhp extends ValueBase {

	protected static $arCacheElement;
	
	/**
	 *	Create
	 */
	public function __construct(){
		parent::__construct();
		$this->setMultiple(true);
	}
	
	/**
	 *	
	 */
	public static function getName(){
		return static::getMessage('NAME');
	}
	
	/**
	 *	
	 */
	public static function getCode(){
		return 'PHP';
	}
	
	/**
	 *	
	 */
	public static function getSort(){
		return 50;
	}
	
	/**
	 *	Set type
	 */
	// public function setValueType($strValueType){
	// 	$this->strValueType = $strValueType; // just FIELD || CONST
	// }
	
	/**
	 *	Show html-code for item
	 *	@return string [html]
	 */
	protected function displayItem(){
		ob_start();
		if(!is_array($this->arValues)){
			$this->arValues = array();
		}
		?>
		<div class="acrit-exp-field-value" data-role="field-value">
			<?foreach($this->arValues as $arValue):?>
				<textarea name="<?=static::INPUTNAME_DEFAULT;?>[<?=$this->intIBlockID;?>][<?=$this->strFieldCode;?>][const][<?=$this->strValueSuffix;?>][]" rows="13" class="acrit-exp-textarea-line" placeholder="<?=htmlspecialcharsbx(static::getMessage('PLACEHOLDER'))?>" data-role="field-php--value-const" style="font:normal 12px/15px 'Courier New', 'Courier', monospace; max-height:1000px;"><?=htmlspecialcharsbx($arValue['CONST']);?></textarea>
				<input type="hidden" name="<?=static::INPUTNAME_DEFAULT;?>[<?=$this->intIBlockID;?>][<?=$this->strFieldCode;?>][type][<?=$this->strValueSuffix;?>][]" value="CONST" data-role="field-php--value-type" />
				<input type="hidden" name="<?=static::INPUTNAME_DEFAULT;?>[<?=$this->intIBlockID;?>][<?=$this->strFieldCode;?>][params][<?=$this->strValueSuffix;?>][]" value="<?=Helper::compileParams($arValue['PARAMS']);?>" data-role="field-php--value-params" />
				<div style="padding-top:4px;text-align:right;">
					<a href="https://www.acrit-studio.ru/technical-support/faq-export/dopolnitelnye-vozmozhnosti/kak-rabotat-s-polem-tipa-php-kod/?utm_source=export" class="acrit-inline-link" target="_blank"><?=static::getMessage('URL_FAQ');?></a>
				</div>
				<?break;?>
			<?endforeach?>
		</div>
		<?
		return ob_get_clean();
	}
	
	/**
	 *	Display field
	 *	@return string [html]
	 */
	public function display(){
		return $this->displayItem();
	}
	
	/**
	 *	Process saved values!
	 */
	public function processValuesForElement(array $arElement, array $arProfile){
		$intProfileID = $arProfile['ID'];
		$arElementOriginal = $arElement;
		#
		$mResult = array();
		foreach($this->arValues as $arValue){
			$intElementId = $arElement['ID'];
			$intIBlockId = $arElement['IBLOCK_ID'];
			$intProfileId = $arProfile['ID'];
			#
			$arElement = $this->getElement($intElementId, $intIBlockId);
			#
			$obField = $this->obField;
			$obPlugin = is_object($obField) ? $obField->getPlugin() : null;
			#
			try{
				$strPhpCode = trim($arValue['CONST']);
				$strPhpCode = preg_replace('#^<\?(php)?\s*(.*?)\s*\?>$#s', '$2', $strPhpCode);
				$strPhpCodeExt = 'use \Acrit\Core\Helper;
\Bitrix\Main\Loader::includeModule("iblock");
\Bitrix\Main\Loader::includeModule("catalog");
\Bitrix\Main\Loader::includeModule("sale");
'.$strPhpCode.';';
				$mResult[] = eval($strPhpCodeExt);
			}
			catch(\Throwable $obError){
				$strError = static::getMessage('PHP_ERROR', [
					'#FIELD#' => is_object($obField) ? $obField->getCode() : '',
					'#ELEMENT_ID#' => $arElement['ID'],
					'#ERROR#' => $obError->getMessage(),
					'#LINE#' => $obError->getLine() - 4,
				]);
				\Acrit\Core\Log::getInstance($this->obField->getModuleId())->add($strError, $intProfileId);
			}
		}
		return $mResult;
	}

	/**
	 * Methods for use in field
	 */

	protected function getElement($intElementId, $intIBlockId){
		if(!is_array(static::$arCacheElement) || static::$arCacheElement['ID'] != $intElementId){
			static::$arCacheElement = \Acrit\Core\Export\Exporter::getInstance($this->obField->getModuleId())
				->getElementArray($intElementId, $intIBlockId);
		}
		return static::$arCacheElement;
	}
		
	
}

?>