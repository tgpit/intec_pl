<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>

<?php foreach ($arResult['INFO_BLOCKS'] as &$arBlock) { ?>
    <?php if (!empty($arBlock['PROPS'])) { ?>
        <div class="sale-personal-order-detail-block" data-role="block" data-block="information">
            <div class="sale-personal-order-detail-block-title">
                <div class="intec-grid intec-grid-nowrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-8">
                    <div class="intec-grid-item">
                        <?= $arBlock['NAME'] ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <div class="sale-personal-order-detail-block-button intec-cl-svg-path-stroke intec-cl-svg-rect-stroke intec-ui-picture" data-role="collapse" data-state="true">
                            <?= $arSvg['BLOCK_TOGGLE'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sale-personal-order-detail-block-content" data-role="content">
                <div class="sale-personal-order-detail-block-information">
                    <?php foreach ($arBlock['PROPS'] as &$arOrderProperty) { ?>
                        <div class="sale-personal-order-detail-block-information-field intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                            <div class="sale-personal-order-detail-block-information-field-header intec-grid-item-1 intec-grid-item-425-2">
                                <?= Html::encode($arOrderProperty['NAME']) ?>:
                            </div>
                            <div class="sale-personal-order-detail-block-information-field-text intec-grid-item-1 intec-grid-item-425-2">
                                <?php
                                if ($arOrderProperty['TYPE'] === 'Y/N') {
                                    echo Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_INFORMATION_LOGIC_VALUE_'.($arOrderProperty['VALUE'] === 'Y' ? 'YES' : 'NO'));
                                } else if (
                                    $arOrderProperty['MULTIPLE'] == 'Y' &&
                                    $arOrderProperty['TYPE'] !== 'FILE' &&
                                    $arOrderProperty['TYPE'] !== 'LOCATION'
                                ) {
                                    $arOrderPropertyValues = unserialize($arOrderProperty['VALUE']);

                                    if (!empty($arOrderPropertyValues))
                                        echo implode('<br />', $arOrderPropertyValues);

                                    unset($arOrderPropertyValues);
                                } else if ($arOrderProperty['TYPE'] === 'FILE') {
                                    echo $arOrderProperty['VALUE'];
                                } else {
                                    echo Html::encode($arOrderProperty['VALUE']);
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php unset($arOrderProperty) ?>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>
<?php unset($arBlock) ?>
