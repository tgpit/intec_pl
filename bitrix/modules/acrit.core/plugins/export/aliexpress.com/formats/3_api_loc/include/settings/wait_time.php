<?
namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper;
?>
<input type="text" name="PROFILE[PARAMS][WAIT_TIME]" size="10" spellcheck="false"
	value="<?=isset($this->arParams['WAIT_TIME']) ? (int)$this->arParams['WAIT_TIME'] : 180;?>" />
