<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>

<div class="news-detail-services widget">
    <div class="news-detail-services-wrapper intec-content intec-content-visible">
        <div class="news-detail-services-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arResult['BLOCKS']['SERVICES']['HEADER'])) { ?>
                <div class="news-detail-services-header widget-header">
                    <?= Html::tag('div', $arResult['BLOCKS']['SERVICES']['HEADER']['VALUE'], [
                        'class' => [
                            'widget-title',
                            'align-'.$arResult['BLOCKS']['SERVICES']['HEADER']['POSITION']
                        ]
                    ]) ?>
                </div>
            <?php } ?>
            <div class="news-detail-services-content widget-content">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    $arResult['BLOCKS']['SERVICES']['TEMPLATE'],
                    $arResult['BLOCKS']['SERVICES']['PARAMETERS'],
                    $component
                ) ?>
            </div>
        </div>
    </div>
</div>