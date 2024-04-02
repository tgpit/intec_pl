<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arParams['MAP_TYPE'] = 'google';
$arVisual = $arResult['VISUAL'];
$arSvg = [
    'FULL' => [
        'PHONE' => FileHelper::getFileData(__DIR__.'/svg/full.item.phone.svg'),
        'EMAIL' => FileHelper::getFileData(__DIR__.'/svg/full.item.email.svg'),
        'CLOSE' => FileHelper::getFileData(__DIR__.'/svg/full.item.close.svg')
    ]
];

$vQuantity = include(__DIR__.'/parts/quantity.php');

?>
<div class="ns-bitrix c-catalog-store-amount c-catalog-store-amount-map-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['DESCRIPTION_BLOCK']['SHOW']) { ?>
                <!--noindex-->
                    <div class="store-amount-info">
                        <?= $arVisual['DESCRIPTION_BLOCK']['VALUE'] ?>
                    </div>
                <!--/noindex-->
            <?php } ?>
            <div class="store-amount-content">
                <div class="store-amount-list store-amount-part">
                    <?php include(__DIR__.'/parts/list.php') ?>
                    <?php include(__DIR__.'/parts/full.php') ?>
                </div>
                <div class="store-amount-map store-amount-part">
                    <?php include(__DIR__.'/parts/map.php') ?>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var component = new intecCatalogStoreOffersMap(
                <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                <?= JavaScript::toObject($arResult['JS']) ?>
            );

            if (window.offers) {
                var offer = window.offers.getCurrent();

                window.offers.on('change', function (event, offer, values) {
                    component.offerOnChange(offer.id);
                });

                if (offer)
                    component.offerOnChange(offer.id);
            }


            var root = data.nodes;
            var scroll = $('[data-scroll]', root);
            var full = $('[data-role="store.full"]', root);
            var list = $('[data-role="store.list.item"]', root);

            scroll.scrollbar();

            full.items = $('[data-role="store.full.item"]', full);
            full.items.each(function () {
                var self = $(this);
                var id = self.attr('data-store-id');
                var close = $('[data-role="store.full.item.close"]', self);

                close.on('click', function () {
                    self.attr('data-active', 'false');
                    full.attr('data-active', 'false');
                });
            });

            list.eq(0).attr('data-active', 'true');
            list.on('click', function () {
                var self = $(this);
                var id = self.attr('data-store-id');

                component.jump(id);

                if (self.attr('data-active') === 'false') {
                    list.attr('data-active', 'false');
                    self.attr('data-active', 'true');
                }

                full.attr('data-active', 'true');
                full.items.filter('[data-store-id="' + id + '"]').attr('data-active', 'true');
            });
        }, {
            'name': '[Component] bitrix:catalog.store.amount (map.1)',
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
</div>