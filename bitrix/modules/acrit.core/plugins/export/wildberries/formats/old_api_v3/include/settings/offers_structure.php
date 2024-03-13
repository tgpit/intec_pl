<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<?
	$arOffersMode = $this->getStructureTypes();
	$arOffersMode = array(
		'REFERENCE' => array_values($arOffersMode),
		'REFERENCE_ID' => array_keys($arOffersMode),
	);
	print SelectBoxFromArray('PROFILE[PARAMS][OFFERS_STRUCTURE]', $arOffersMode,
		$this->arProfile['PARAMS']['OFFERS_STRUCTURE'], '', 'id="acrit_wb_offers_structure"');
	?>
</div>
