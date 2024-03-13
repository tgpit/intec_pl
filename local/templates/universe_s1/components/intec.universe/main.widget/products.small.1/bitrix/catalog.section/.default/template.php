<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;

if ($bBase)
    CJSCore::Init(array('currency'));

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

if (empty($arResult['ITEMS']))
    return;

/**
 * @var Closure $vCounter
 * @var Closure $dData
 * @var Closure $vButtons
 * @var Closure $vImage
 * @var Closure $vPrice
 * @var Closure $vPurchase
 * @var Closure $vQuantity
 * @var Closure $vSku
 * @var Closure $vSkuExtended
 * @var Closure $vQuickView
 */
include(__DIR__.'/parts/counter.php');
include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/buttons.php');
include(__DIR__.'/parts/image.php');
include(__DIR__.'/parts/price.php');
include(__DIR__.'/parts/purchase.php');
include(__DIR__.'/parts/quantity.php');

$bSlideUse = count($arResult['ITEMS']) > 1;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-widget',
        'c-widget-products-small-1'
    ],
    'data' => [
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'button' => $arResult['ACTION'] !== 'none' ? 'true' : 'false',
        'products-small-slider-use' => $bSlideUse ? 'true' : 'false'
    ]
]) ?>
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['PRODUCT_DAY_TIMER']['SHOW']) { ?>
        <div class="widget-header intec-grid intec-grid-wrap intec-grid-a-v-center">
            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                <div class="<?= Html::cssClassFromArray([
                    'widget-header-text',
                    'align-'.$arBlocks['HEADER']['ALIGN'],
                    'intec-grid-item'
                ]) ?>">
                    <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                </div>
            <?php } ?>
            <?php if ($arBlocks['PRODUCT_DAY_TIMER']['SHOW']) { ?>
                <div class="<?= Html::cssClassFromArray([
                    'widget-header-timer',
                    'intec-grid-item-auto'
                ]) ?>" data-role="product.day.timer.block"></div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="widget-content intec-grid-item" data-role="products.slider.content">

        <?php include(__DIR__.'/parts/items.php') ?>
        <?php include(__DIR__.'/parts/navigation.php') ?>

        <?php if (!defined('EDITOR')) include(__DIR__.'/parts/script.php') ?>
    </div>
<?= Html::endTag('div') ?>
