<?
namespace Acrit\Core\Export\Plugins;

use \Acrit\Core\Helper;

$api_region_urls = array('lamoda.kz', 'lamoda.ru', 'lamoda.ua');

?>
<select name="PROFILE[PARAMS][API_REGION_URL]" id="field_API_REGION_URL" data-role="acrit_exp_lamoda_api_region_url" data-select2-id="field_API_REGION_URL" tabindex="-1" class="select2-hidden-accessible_" aria-hidden="true">
<?
	$selected = ' ';
	foreach ( $api_region_urls as $r_i => $r_url )
	{
		$selected = ' ';
		if ( $r_url == htmlspecialcharsbx($this->arProfile['PARAMS']['API_REGION_URL']) )
		{
			$selected = ' selected="selected" ';
		}
		echo '<option'.$selected.'value="'.$r_url.'">'.$r_url.'</option>';
	}
?>
</select>