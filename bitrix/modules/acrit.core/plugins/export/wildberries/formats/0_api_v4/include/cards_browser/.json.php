<?php
namespace Acrit\Core\Export\Plugins\WildberriesV4Helpers;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json;

Helper::loadMessages();

// DISPLAYS $arData

?>

<div><br/></div>

<div>
	<div>
		<?=static::getMessage('CARDS_BROWSER_VIEW_REQUEST_STATUS', ['#CODE#' => $obResponse->getStatus()]);?>
	</div>
</div>

<div><br/></div>


<div>
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_exp_wildberries_cards_browser_json_request">
			<?=static::getMessage('CARDS_BROWSER_VIEW_REQUEST_JSON');?>
		</a>
	</div>
	<div style="display:none;">
		<?Helper::P(Json::prettyPrint($arData, true));?>
	</div>
</div>
<script>
	$('a[data-role="acrit_exp_wildberries_cards_browser_json_request"]').bind('click', function(e){
		e.preventDefault();
		if(!$(this).parent().next().is(':animated')){
			$(this).parent().next().slideToggle();
		}
	});
</script>

<div><br/></div>

<div>
	<div>
		<a href="#" class="acrit-inline-link" data-role="acrit_exp_wildberries_cards_browser_json_response">
			<?=static::getMessage('CARDS_BROWSER_VIEW_RESPONSE_JSON');?>
		</a>
	</div>
	<div style="display:none;">
		<?Helper::P(Json::prettyPrint($obResponse->getJsonResult(), true));?>
	</div>
</div>
<script>
	$('a[data-role="acrit_exp_wildberries_cards_browser_json_response"]').bind('click', function(e){
		e.preventDefault();
		if(!$(this).parent().next().is(':animated')){
			$(this).parent().next().slideToggle();
		}
	});
</script>