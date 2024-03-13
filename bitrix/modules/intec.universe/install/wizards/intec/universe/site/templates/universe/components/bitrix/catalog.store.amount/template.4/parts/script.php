<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

?>

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
        'name': '[Component] bitrix:catalog.store.amount (template.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'options': {
                'await': [
                    Promise.await(function () {
                        return !!window.offers;
                    }, 250)
                ]
            }
        }
    })
</script>