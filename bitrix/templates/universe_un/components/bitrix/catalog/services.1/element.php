<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$bSeo = Loader::includeModule('intec.seo');

include(__DIR__.'/parts/element/element.php');

?>
<div class="ns-bitrix c-catalog c-catalog-catalog-1 p-element">
    <?php $arElement['ID'] = $APPLICATION->IncludeComponent(
        'bitrix:catalog.element',
        $arElement['TEMPLATE'],
        $arElement['PARAMETERS'],
        $component
    ) ?>
    <?php if ($bSeo) { ?>
        <?php $APPLICATION->IncludeComponent('intec.seo:iblocks.metadata.loader', '', [
            'IBLOCK_ID' => $arIBlock['ID'],
            'ELEMENT_ID' => $arElement['ID'],
            'TYPE' => 'element',
            'MODE' => 'single',
            'METADATA_SET' => 'Y',
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME']
        ], $component) ?>
    <?php } ?>
</div>