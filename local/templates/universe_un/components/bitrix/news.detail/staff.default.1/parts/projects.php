<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arResult
 */

?>
<div class="news-detail-projects">
	<?php $APPLICATION->IncludeComponent(
		'intec.universe:main.projects',
        $arResult['PROJECTS']['TEMPLATE'],
        $arResult['PROJECTS']['PARAMETERS'],
		$component
	) ?>
</div>
