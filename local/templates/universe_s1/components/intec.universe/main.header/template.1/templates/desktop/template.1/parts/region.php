<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */

?>
<?php if ($arResult['REGIONALITY']['USE']) { ?>
    <!--noindex-->
    <div class="widget-panel-item widget-panel-item-visible">
        <div class="widget-panel-item-wrapper widget-region intec-grid intec-grid-a-v-center">
            <div class="widget-panel-item-icon widget-region-icon intec-grid-item-auto intec-cl-svg-path-stroke">
                <?= FileHelper::getFileData(__DIR__.'/../../../../svg/region_icon.svg')?>
            </div>
            <div class="widget-panel-item-text intec-grid-item-auto">
                <?php $APPLICATION->IncludeComponent(
                    'intec.regionality:regions.select',
                    $arResult['REGIONALITY']['TEMPLATE'],
                    []
                ) ?>
            </div>
        </div>
    </div>
    <!--/noindex-->
<?php } ?>