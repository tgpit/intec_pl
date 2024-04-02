<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$arVisual = $arResult['VISUAL'];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<?php if (!empty($arResult['STORES'])) { ?>
    <div class="ns-bitrix c-catalog-store-amount c-catalog-store-amount-popup-1" id="<?= $sTemplateId ?>">
        <div class="catalog-store-amount-items">
            <?php foreach ($arResult['STORES'] as $arStore) {
                include(__DIR__.'/parts/stores.php');
            } ?>
        </div>
        <?php unset($arStore) ?>
    </div>
    <?php if ($arResult['IS_SKU']) { ?>
        <script type="text/javascript">
            template.load(function () {
                var storeOffers = new intecCatalogStoreOffers(
                    <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                    <?= JavaScript::toObject($arResult['JS']) ?>
                );

                offers.on('change', function (event, offer, values) {
                    storeOffers.offerOnChange(offer.id);
                });

                storeOffers.offerOnChange(offers.getCurrent().id);
            }, {
                'name': '[Component] bitrix:catalog.store.amount (popup.1)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'lazy',
                    'await': [
                        Promise.await(function () {
                            return !!window.offers;
                        }, 250)
                    ]
                }
            });
        </script>
    <?php } ?>
<?php } ?>
