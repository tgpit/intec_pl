<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Helper::loadMessages(__FILE__);

/**
 * DownloadFields
 * @package Acrit\Core\Export
 */
class DownloadFields {

	protected $strModuleId;
	protected $intProfileId;
	protected $intIBlockId;

	protected $intTabSize = 4;
	protected $intTabMargin = 40;

	protected $arProfile;

	public function __construct($strModuleId, $intProfileId, $intIBlockId){
		$this->strModuleId = $strModuleId;
		$this->intProfileId = $intProfileId;
		$this->intIBlockId = $intIBlockId;
		if($intTabSize = Helper::getOption($strModuleId, 'download_fields_tab_size')){
			$this->intTabSize = $intTabSize;
		}
		if($intTabMargin = Helper::getOption($strModuleId, 'download_fields_tab_margin')){
			$this->intTabMargin = $intTabMargin;
		}
	}
	
	public function download(){
		Helper::obRestart();
		header('Content-Type: text/plain');
		header(sprintf('Content-Disposition: attachment; filename="profile_%d_fields_%d.txt"', $this->intProfileId, $this->intIBlockId));
		if($arProfile = $this->getProfile()){
			if($strPluginClass = $this->getPluginClass()){
				$obPlugin = new $strPluginClass($this->strModuleId);
				$obPlugin->setProfileArray($arProfile);
				if($arFields = $obPlugin->getFields($this->intProfileId, $this->intIBlockId, true)){
					foreach($arFields as $obField){
						$strName = $obField->getName();
						$strCode = $obField->getCode();
						if($obField->isHeader()){
							$this->printHeader($obField);
						}
						else{
							$this->printField($obField);
						}
						print PHP_EOL;
					}
				}
			}
		}
		die();
	}

	protected function getProfile(){
		$this->arProfile = Helper::call($this->strModuleId, 'Profile', 'getProfiles', [$this->intProfileId]);
		return $this->arProfile;
	}

	protected function getPluginClass(){
		$strClass = null;
		if(is_array($this->arProfile)){
			if($strFormat = $this->arProfile['FORMAT']){
				if($arPlugin = \Acrit\Core\Export\Exporter::getInstance($this->strModuleId)->getPluginInfo($strFormat)){
					$strClass = $arPlugin['CLASS'];
				}
			}
		}
		return $strClass;
	}

	protected function printHeader($obField){
		$strName = $obField->getName();
		print str_repeat('-', Helper::strlen($strName));
		print PHP_EOL;
		print $strName;
		print PHP_EOL;
		print str_repeat('-', Helper::strlen($strName));
		print PHP_EOL;
	}

	protected function printField($obField){
		$strName = $obField->getName();
		$strCode = $obField->getCode();
		$strNameDisplay = $strName;
		$strCodeDisplay = $strCode;
		if($obField->isRequired() || $obField->isCustomRequired()){
			$strNameDisplay .= str_repeat(' ', 2).'*';
		}
		if(!Helper::isEmpty($obField->getAllowedValues()) || $obField->isAllowedValuesCustom()){
			$strNameDisplay .= str_repeat(' ', 5).'<!>';
		}
		#
		print $strNameDisplay.PHP_EOL;
		print $strCodeDisplay;
		if($arIBlockField = $this->arProfile['IBLOCKS'][$this->intIBlockId]['FIELDS'][$strCode]){
			$strType = $arIBlockField['TYPE'];
			$intTabCount1 = ceil(($this->intTabMargin - Helper::strlen($strCodeDisplay)) / $this->intTabSize);
			$intTabCount2 = ceil($this->intTabMargin / $this->intTabSize);
			$this->checkFieldValueEmpty($arIBlockField);
			switch($strType){
				case 'FIELD':
					if(is_array($arIBlockField['VALUES']) && !empty($arIBlockField['VALUES'])){
						foreach($arIBlockField['VALUES'] as $intValueIndex => $arValue){
							$this->printValue($arValue, $intValueIndex ? $intTabCount2 : $intTabCount1);
						}
					}
					break;
				case 'CONDITION':
					$arValues = ['Y' => [], 'N' => []];
					foreach($arIBlockField['VALUES'] as $arValue){
						$arValues[$arValue['SUFFIX']][] = $arValue;
					}
					print str_repeat("\t", $intTabCount1);
					printf('{%s: true}', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_CONDITION'));
					print PHP_EOL;
					if(is_array($arValues['Y'])){
						foreach($arValues['Y'] as $arValue){
							$this->printValue($arValue, $intTabCount2+1);
						}
					}
					print str_repeat("\t", $intTabCount2);
					printf('{%s: false}', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_CONDITION'));
					print PHP_EOL;
					if(is_array($arValues['N'])){
						foreach($arValues['N'] as $arValue){
							$this->printValue($arValue, $intTabCount2+1);
						}
					}
					break;
				case 'MULTICONDITION':
					$arConditions = [];
					foreach(explode('{{{#SEPARATOR#}}}', $arIBlockField['CONDITIONS']) as $strCondition){
						if(preg_match('#^\#(.*?)\#\[(.*?)\]$#', $strCondition, $arMatch)){
							$arConditions[$arMatch[1]] = [
								'CONDITIONS' => $arMatch[2],
								'ITEMS' => [],
							];
						}
					}
					$arConditions['_ELSE_'] = ['ITEMS' => []];
					if(is_array($arIBlockField['VALUES']) && !empty($arIBlockField['VALUES'])){
						foreach($arIBlockField['VALUES'] as $intValueIndex => $arValue){
							if(is_array($arConditions[$arValue['SUFFIX']])){
								$arConditions[$arValue['SUFFIX']]['ITEMS'][] = $arValue;
							}
						}
					}
					$bFirst = true;
					foreach($arConditions as $strSuffix => $arCondition){
						print str_repeat("\t", $bFirst ? $intTabCount1 : $intTabCount2);
						print $strSuffix == '_ELSE_' ? sprintf('{%s}', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_ELSE')) : sprintf('{%s}', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_CONDITION'));
						print PHP_EOL;
						if(is_array($arCondition['ITEMS'])){
							foreach($arCondition['ITEMS'] as $arValue){
								$this->printValue($arValue, $intTabCount2+1);
							}
						}
						$bFirst = false;
					}
					break;
			}
		}
		else{
			print PHP_EOL;
			$arValue = [
				'TYPE' => 'FIELD',
				'TITLE' => sprintf('*%s*', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_EMPTY')),
				'EMPTY' => true,
			];
			$this->printValue($arValue, ceil($this->intTabMargin / $this->intTabSize));
		}
	}

	protected function checkFieldValueEmpty(&$arIBlockField){
		if(count($arIBlockField['VALUES']) == 1){
			foreach($arIBlockField['VALUES'] as $key => $arValue){
				switch($arValue['TYPE']){
					case 'FIELD':
						if(!Helper::strlen($arValue['TITLE'])){
							$arIBlockField['VALUES'][$key]['TITLE'] = sprintf('*%s*', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_EMPTY'));
							$arIBlockField['VALUES'][$key]['EMPTY'] = true;
						}
						break;
					case 'CONST':
						if(!Helper::strlen($arValue['CONST'])){
							$arIBlockField['VALUES'][$key]['CONST'] = sprintf('*%s*', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_EMPTY'));
							$arIBlockField['VALUES'][$key]['EMPTY'] = true;
						}
						break;
				}
			}
		}
	}

	protected function printValue($arValue, $intTabCount){
		$strValue = '';
		switch($arValue['TYPE']){
			case 'FIELD':
				if(Helper::strlen($arValue['TITLE'])){
					if(!$arValue['EMPTY']){
						$strValue .= sprintf('*%s* ', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_FIELD'));
					}
					$strValue .= $arValue['TITLE'];
				}
				break;
			case 'CONST':
				if(Helper::strlen($arValue['CONST'])){
					if(!$arValue['EMPTY']){
						$strValue .= sprintf('*%s* ', Helper::getMessage('ACRIT_EXP_DOWNLOAD_FIELDS_CONST'));
					}
					$strValue .= $arValue['CONST'];
				}
				break;
		}
		print str_repeat("\t", $intTabCount);
		print $strValue;
		print PHP_EOL;
		return !!Helper::strlen($strValue);
	}

}
?>