<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
    <div class="intec-content widget-accessories-error">
        <div class="intec-content-wrapper">
            <?php
                if (empty($arResult['ITEM'])) {
                    echo Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_ERROR_NO_ITEM');
                } elseif (empty($arResult['ITEMS'])) {
                    echo Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_ERROR_NO_ACCESSORIES');
                }
            ?>
        </div>
    </div>
<?php unset($sTemplate, $arProperties) ?>