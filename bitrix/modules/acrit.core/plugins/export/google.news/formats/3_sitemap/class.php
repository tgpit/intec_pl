<?
/**
 * Acrit Core: Google.news plugin
 * @documentation https://support.google.com/news/publisher-center/answer/9606710?hl=ru
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Exporter;

class GoogleNewsSitemap extends GoogleNews {
	
	const DATE_UPDATED = '2021-02-25';

	protected static $bSubclass = true;
	
	# General
	protected $strDefaultFilename = 'google_news_sitemap.xml';
	protected $arSupportedFormats = ['XML'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $strFileExt = 'xml';
	
	# Basic settings
	protected $bCategoriesExport = false;
	protected $bCurrenciesExport = false;
	
	# XML settings
	protected $strXmlItemElement = 'url';
	protected $intXmlDepthItems = 1;
	
	# Other export settings
	protected $bZip = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		
		# General
		$arResult['loc'] = ['FIELD' => 'DETAIL_PAGE_URL'];
		$arResult['news:news.news:publication.news:name'] = ['FIELD' => 'PROPERTY_PUBLISHER'];
		$arResult['news:news.news:publication.news:language'] = ['CONST' => LANGUAGE_ID];
		$arResult['news:news.news:publication_date'] = ['FIELD' => 'DATE_CREATE', 'FIELD_PARAMS' => [
			'DATEFORMAT' => 'Y',
			'DATEFORMAT_from' => '#DATETIME#',
			'DATEFORMAT_to' => 'c',
		]];
		$arResult['news:news.news:title'] = ['FIELD' => 'NAME'];
		
		#
		return $arResult;
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		$arSettings['GOOGLE_SITEMAP_MAX_COUNT'] = [
			'HTML' => '<input type="text" name="PROFILE[PARAMS][GOOGLE_SITEMAP_MAX_COUNT]"
							value="'.htmlspecialcharsbx($this->getMaxCount()).'"
							data-role="acrit_google_news_max_count" size="10" maxlength="6" />',
			'SORT' => 100,
		];
		$arSettings['GOOGLE_SITEMAP_MAX_SIZE'] = [
			'HTML' => '<input type="text" name="PROFILE[PARAMS][GOOGLE_SITEMAP_MAX_SIZE]"
							value="'.htmlspecialcharsbx($this->getMaxSize()).'"
							data-role="acrit_google_news_max_size" size="10" maxlength="6" />',
			'SORT' => 110,
		];
	}

	/**
	 * 
	 */
	protected function getMaxCount(){
		$intResult = intVal($this->arParams['GOOGLE_SITEMAP_MAX_COUNT']);
		if($intResult <= 0){
			$intResult = 50000;
		}
		return $intResult;
	}

	/**
	 * 
	 */
	protected function getMaxSize(){
		$intResult = intVal($this->arParams['GOOGLE_SITEMAP_MAX_SIZE']);
		if($intResult <= 0){
			$intResult = 50;
		}
		return $intResult;
	}
	
	/**
	 *	Build main xml structure
	 */
	protected function onUpGetXmlStructure(&$strXml){
		# Build xml
		$strXml = '<?xml version="1.0" encoding="UTF-8"?>'.static::EOL;
		$strXml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">'.static::EOL;
		$strXml .= '	#XML_ITEMS#'.static::EOL;
		$strXml .= '</urlset>'.static::EOL;
		
		# Prepare URL
		$strUrl = $this->arParams['XML_LINK'];
		if(!preg_match('#^http[s]?://#i', $strUrl)){
			$strUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y', 
				substr($strUrl, 0, 1) == '/' ? $strUrl : '/'.$strUrl);
		}
		# Replace macros
		$arReplace = [
			'#XML_ENCODING#' => $this->arParams['ENCODING'],
			'#XML_ID#' => Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS']=='Y'),
			'#XML_GENERATION_DATE#' => date('c'),
			'#XML_TITLE#' => $this->arParams['XML_TITLE'],
			'#XML_DESCRIPTION#' => $this->arParams['XML_DESCRIPTION'],
		];
		$strXml = str_replace(array_keys($arReplace), array_values($arReplace), $strXml);
	}

	/**
	 *  Support for generate multiple files
	 */
	protected function stepExport_XML_ExportPrepare(){
		$this->setFilenameSuffix($intSuffix = 1);
		$intCount = 0;
		$this->arData['SESSION']['EXPORT']['XML_SITEMAP_PARTS'] = [$intSuffix => $intCount];
		return Exporter::RESULT_SUCCESS;
	}
	protected function stepExport_XML_ExportItem($arItem){
		$arParts = &$this->arData['SESSION']['EXPORT']['XML_SITEMAP_PARTS'];
		$intSuffix = array_key_last($arParts);
		$strTmpFile = $this->getFilenameSuffix($intSuffix);
		$intCount = $arParts[$intSuffix];
		$intMaxCount = $this->getMaxCount();
		$intMaxSize = $this->getMaxSize();
		$intMaxSize *= 1024*1024;
		$intSizeTreshold = 10*1024;
		if($intCount >= $intMaxCount || filesize($strTmpFile) >= $intMaxSize-$intSizeTreshold){
			$this->stepExport_XML_ExportFooter();
			$this->setFilenameSuffix(++$intSuffix);
			$intCount = 0;
			$arParts[$intSuffix] = $intCount;
			$this->stepExport_XML_ExportHeader();
		}
		$mResult = parent::stepExport_XML_ExportItem($arItem);
		$arParts[$intSuffix]++;
		return $mResult;
	}
	protected function stepExport_ReplaceFile(&$arSession, $arStep){
		# 1. remove old files in export directory
		$strExportFilename = $this->getExportFileName();
		$strExportBasename = basename($strExportFilename);
		$strExportBasenameWithoutExt = pathinfo($strExportFilename, PATHINFO_FILENAME);
		$strExportBasenamePattern = '#^'.preg_quote($strExportBasenameWithoutExt).'\.(\d+)$#';
		$strExportDir = dirname($strExportFilename);
		if(is_dir($strExportPath = Helper::root().$strExportDir)){
			$arAllFilesInExportDir = Helper::scandir($strExportPath, ['RECURSIVELY' => false]);
			foreach($arAllFilesInExportDir as $strFile){
				$strBasename = basename($strFile);
				$strBasenameWithoutExt = pathinfo($strBasename, PATHINFO_FILENAME);
				if($strBasename == $strExportBasename || preg_match($strExportBasenamePattern, $strBasenameWithoutExt)){
					@unlink($strFile);
				}
			}
		}
		# 2. move last exported files
		foreach($this->arData['SESSION']['EXPORT']['XML_SITEMAP_PARTS'] as $strSuffix => $intCount){
			if($intCount){
				$strFilename = $this->getExportFilenameTmp($strSuffix);
				$strBasename = basename($strFilename);
				if(is_file($strFilenameOldAbs = Helper::root().$strFilename)){
					// replace 'google_news_sitemap.xml.1.tmp' to google_news_sitemap.1.xml
					if($strBasenameNew = $this->convertTmpBasename($strBasename)){
						$strFilenameNew = $strExportDir.'/'.$strBasenameNew;
						if(is_file($strFilenameNewAbs = Helper::root().$strFilenameNew)){
							@unlink($strFilenameNewAbs);
						}
						rename($strFilenameOldAbs, $strFilenameNewAbs);
					}
				}
			}
		}
		# Manual remove tmp dir
		$strTmpDir = Helper::call($this->strModuleId, 'Profile', 'getTmpDir', [$this->arProfile['ID'], true, false, $this->strModuleId]);
		if(is_dir($strTmpDir)){
			@rmdir($strTmpDir);
		}
		return Exporter::RESULT_SUCCESS;
	}
	protected function stepExport_RemoveTmpFiles(&$arSession, $arStep){
		return Exporter::RESULT_SUCCESS;
	}
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		# Obtain all files
		$arFiles = [];
		$strFile = $this->getExportFileName();
		$intFileIndex = 0;
		while(true){
			$intFileIndex++;
			$strFileItem = Helper::getFileNameWithIndex($strFile, $intFileIndex);
			if(!is_file(Helper::root().$strFileItem)){
				break;
			}
			$arFiles[$intFileIndex] = $strFileItem;
		}
		$intFilesCount = count($arFiles);
		# Display
		if($bSingle){
			$strFile = $this->getSingleFileOpenLink($strFile);
			return sprintf('%s (%s:&nbsp;%d)', $strFile, str_replace(' ', '&nbsp;', 
				static::getMessage('ACRIT_EXP_FILE_OPEN_FILES_COUNT')), count($arFiles));
		}
		foreach($arFiles as $intFileIndex => &$strFile){
			$this->addFileOpenLinkTimestamp($strFile);
			$strFile = $this->getExtFileOpenLink($strFile, sprintf('#%s.xml', $intFileIndex));
		}
		unset($strFile);
		return implode(' ', $arFiles);
	}
	protected function convertTmpBasename($strBasename){
		$strResult = null;
		if(preg_match('#^(.*?)\.(\w+)\.(\d+)\.tmp$#', $strBasename, $arMatch)){
			if($arMatch[3] > 1){
				$strResult = sprintf('%s.%s.%s', $arMatch[1], $arMatch[3], $arMatch[2]);
			}
			else{
				$strResult = sprintf('%s.%s', $arMatch[1], $arMatch[2]);
			}
		}
		return $strResult;
	}

}

?>