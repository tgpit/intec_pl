<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$arLimits = $this->getLimits();

?>

<?=$this->showLimits($arLimits);?>

<input type="button" data-role="acrit_exp_ozon_limits_check" value="<?=static::getMessage('LIMITS_REFRESH');?>"
	style="height:25px;">
