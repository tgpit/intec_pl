<?
namespace Acrit\Core\Export\Plugins;

$arTypes = $this->getHistorySaveTypes();
$arTypes = [
	'REFERENCE_ID' => array_keys($arTypes),
	'REFERENCE' => array_values($arTypes),
];

print SelectBoxFromArray('PROFILE[PARAMS][HISTORY_SAVE]', $arTypes,
	$this->arParams['HISTORY_SAVE'], '', 'data-role="acrit_exp_ozon_history_save"');

?>