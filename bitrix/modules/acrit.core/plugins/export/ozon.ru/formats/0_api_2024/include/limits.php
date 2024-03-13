<?php
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$strDate = null;
$intWidth = 0;
$bSuccess = is_array($arLimits) && isset($arLimits['total']['limit']);
$arLimitsOutput = [];
if($bSuccess){
	foreach(['daily_create', 'daily_update', 'total'] as $strType){
		$arLimit = $arLimits[$strType] ?? [];
		try{
			$strDate = $arLimit['reset_at'];
			$arLimit['_reset_at'] = $arLimit['reset_at'];
			$arLimit['reset_at'] = (new \Bitrix\Main\Type\Datetime($strDate, 'Y-m-d\TH:i:sP'))->toString();
		}
		catch(\Throwable $obError){}
		$arLimit['width'] = round($arLimit['limit'] > 0 ? (($arLimit['limit'] - $arLimit['usage']) / $arLimit['limit']) * 100 : 0);
		$arLimitsOutput[$strType] = $arLimit;
	}
}

?>

<div data-role="acrit_exp_ozon_limits_wrapper">
	<?if($bSuccess):?>
		<?foreach($arLimitsOutput as $strType => $arLimit):?>
			<div data-role="acrit_exp_ozon_limits">
				<div data-role="acrit_exp_ozon_limits_bar" style="width:<?=$intWidth;?>%;"></div>
				<div data-role="acrit_exp_ozon_limits_text">
					<?=static::getMessage('LIMITS_TEXT'.($arLimit['limit'] == -1 ? '_INFINITE' : ''), [
						'#TYPE#' => static::getMessage('LIMITS_TYPE_'.$strType),
						'#USAGE#' => $arLimit['usage'],
						'#VALUE#' => $arLimit['limit'],
						'#REMAINING#' => $arLimit['limit'] - $arLimit['usage'],
						// '#RESET_AT#' => $strDate,
					]);?>
				</div>
			</div>
		<?endforeach?>
	<?else:?>
		<?=static::getMessage('LIMITS_TEXT_ERROR');?>
	<?endif?>
</div>
