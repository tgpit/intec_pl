<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

$strJsonRequest = Json::encode($arJsonItemsFull, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if(!Helper::isUtf()){
	$strJsonRequest = Helper::convertEncoding($strJsonRequest, 'UTF-8', 'CP1251');
}
$strJsonRequest = htmlspecialcharsbx($strJsonRequest);

$strJsonRequestRaw = Json::encode($arJsonItemsFull);
$strJsonRequestRaw = htmlspecialcharsbx($strJsonRequestRaw);

$strErrorDetails = null;
if(is_array($arResult['error'])){
	$strError = $arResult['error']['code'];
	$strErrorDetails = $arResult['error']['message'];
}
$strDisplayMessageTitle = static::getMessage('ERROR_EXPORT_ITEMS_BY_API_POPUP', ['#ERROR#' => $strError]);

print Helper::showError($strDisplayMessageTitle, $strErrorDetails);
?>
<div>
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_ozon_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_ARRAY');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($arResult);?>
	</div>
</div>
<div style="margin-top:8px;">
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_ozon_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_JSON');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($strJsonRequest);?>
	</div>
</div>
<div style="margin-top:8px;">
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_ozon_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_JSON_RAW');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($strJsonRequestRaw);?>
	</div>
</div>
<div style="margin-top:8px;">
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_ozon_preview_error_json">
			<?=static::getMessage('ERROR_PREVIEW_HEADERS');?>
		</a>
	</div>
	<div style="display:none; word-break:break-all;">
		<?Helper::P($this->API->getHeaders());?>
	</div>
</div>
<script>
if(AcritExpPopupExecute.PARTS.CONTENT_DATA.clientWidth < 800){
	AcritExpPopupExecute.SetSize({width:800});
}
$('a[data-role="acrit_ozon_preview_error_json"]').bind('click', function(e){
	e.preventDefault();
	$(this).parent().next().toggle();
});
</script>
