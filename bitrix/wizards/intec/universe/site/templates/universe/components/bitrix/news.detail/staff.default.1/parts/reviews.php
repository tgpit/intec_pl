<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arResult
 */

?>
<div class="news-detail-reviews">
	<?php $APPLICATION->IncludeComponent(
		'intec.universe:main.reviews',
        $arResult['REVIEWS']['TEMPLATE'],
        $arResult['REVIEWS']['PARAMETERS'],
		$component
	) ?>
</div>
