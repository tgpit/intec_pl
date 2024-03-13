<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['STORES']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'PHONE' => FileHelper::getFileData(__DIR__.'/svg/contact.phone.svg'),
    'EMAIL' => FileHelper::getFileData(__DIR__.'/svg/contact.email.svg')
];
$bShowMessageBlock = true;

?>
<div class="ns-bitrix c-catalog-store-amount c-catalog-store-amount-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['DESCRIPTION_BLOCK']['SHOW']) { ?>
                <!--noindex-->
                    <div class="catalog-store-amount-info">
                        <?= $arVisual['DESCRIPTION_BLOCK']['VALUE'] ?>
                    </div>
                <!--/noindex-->
            <?php } ?>
            <div class="catalog-store-amount-items" data-show-empty-store="<?= $arVisual['SHOW_EMPTY_STORE'] ? 'true' : 'false' ?>">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-stretch">
                    <?php foreach ($arResult['STORES'] as $arStore) {
                        if ($arStore['AMOUNT_STATUS'] != 'empty')
                            $bShowMessageBlock = false;

                        include(__DIR__.'/parts/stores.php');
                    } ?>
                </div>
            </div>
            <?php if (($bShowMessageBlock || ($arResult['IS_SKU'] && empty($arParams['OFFER_ID']))) && !$arVisual['SHOW_EMPTY_STORE']) { ?>
                <?= Html::tag('div', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_2_TEMPLATE_EMPTY_EVERYWHERE'), [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-alert',
                            'scheme-red',
                            'm-b-20'
                        ]
                    ],
                    'data' => [
                        'role' => 'message.block'
                    ]
                ]) ?>
            <?php } ?>
        </div>
    </div>
    <?php if ($arResult['IS_SKU'] && empty($arParams['OFFER_ID'])) {
        include(__DIR__.'/parts/script.php');
    } ?>
</div>