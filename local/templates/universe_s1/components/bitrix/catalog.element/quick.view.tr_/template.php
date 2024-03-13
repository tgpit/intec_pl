<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 */

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

/**
 * @var array $arData
 */

include(__DIR__.'/parts/data.php');

$arVisual = $arResult['VISUAL'];
$arPrice = null;
?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-quick-view-1',
        'catalog-element-scroll'
    ],
    'data' => [
        'data' => Json::encode($arData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'properties' => Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'available' => $arData['available'] ? 'true' : 'false',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false',
    ],
    'style' => [
        'opacity' => 0,
    ]
]) ?>
    <div class="c-catalog-element">
        <div class="intec-content-wrapper" align=center>
           	<div class="itec-grid-item-1 catalog-element-name">
               	<?= $arResult['NAME'] ?>
           	</div>
       		<div class="catalog-element-gallery-block">
      	        <? include(__DIR__.'/parts/gallery.php') ?>
  	        </div>
	    </div>
    </div>
<?= Html::endTag('div') ?>

<?php include(__DIR__.'/parts/script.php') ?>