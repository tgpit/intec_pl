<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var header = $('[data-global-role="header"]');
        var panel = $('[data-role="top-menu"]', header);
        var fixedTotalUse = <?= $arParams['TOTAL_BLOCK_FIXED_MODE'] === 'Y' ? 'true' : 'false' ?>;
        var root = data.nodes;
        var print = $('[data-role="print"]', root);
        var clear = $('[data-role="clear"]', root);
        var items;
        var quickItemsId = [];
        var quickItems = Object.create(null);
        var confirmRemoveProductUse = <?= $arParams['CONFIRM_REMOVE_PRODUCT_USE'] === 'Y' ? 'true' : 'false' ?>;
        var confirmRemoveProduct = $('[data-role="confirm.remove.product"]', root);
        var confirmRemoveProductTitle = $('[data-role="confirm.remove.product.title"]', confirmRemoveProduct);
        var confirmRemoveProductText = $('[data-role="confirm.remove.product.text"]', confirmRemoveProduct);
        var confirmRemoveProductAgree = $('[data-entity="confirm.remove.product.agree"]', confirmRemoveProduct);
        var confirmRemoveProductCancel = $('[data-entity="confirm.remove.product.cancel"]', confirmRemoveProduct);
        var confirmRemoveProductDelay = $('[data-entity="confirm.remove.product.delay"]', confirmRemoveProduct);
        var basketItemsCount =  $('[data-entity="basket-items-count"]', root);
        var currentHash = window.location.hash;

        var quickViewShow = function (dataItem, quickItemsId) {
            app.api.components.show({
                'component': 'bitrix:catalog.element',
                'template': dataItem.template,
                'parameters': dataItem.parameters,
                'settings': {
                    'parameters': {
                        'className': 'popup-window-quick-view',
                        'width': null
                    }
                }
            });
        };

        var handled = [];

        if (!_.isNil(items))
            handled = items.handled;

        items = $('[data-role="item"]', root);
        items.handled = handled;

        items.each(function () {
            var item = $(this);
            var data = item.data('data');

            if (data) {
                quickItems[data.quickView.parameters.ELEMENT_ID] = data.quickView;
                quickItemsId.push(data.quickView.parameters.ELEMENT_ID);

                if (handled.indexOf(this) > -1)
                    return;

                handled.push(this);

                item.quickView = $('[data-role="quick.view"]', item);

                item.quickView.on('click', function () {
                    quickViewShow(data.quickView, quickItemsId);
                });
            }
        });

        print.on('click', function () {
            var cssPath = [
                <?= JavaScript::toObject(SITE_TEMPLATE_PATH.'/css/interface.css') ?>,
                <?= JavaScript::toObject(SITE_TEMPLATE_PATH.'/css/grid.css') ?>,
                <?= JavaScript::toObject($this->GetFolder().'/style.css') ?>
            ];

            root.printThis({
                'importCSS': false,
                'importStyle': true,
                'loadCSS': cssPath,
                'pageTitle': "",
                'removeInline': false,
                'header': null,
                'formValues': true,
                'base': true
            });
        });

        if (panel.length && fixedTotalUse) (function () {
            var state = false;
            var area = $(window);
            var update;
            var sticky = {
                'nulled': $('[data-sticky="nulled"]', root)
            };

            panel.css('top', '-'+ panel.outerHeight());

            update = function () {
                var bound = 0;

                if (root.is(':visible'))
                    bound += root.offset().top;

                if (area.width() <= 768) {
                    sticky.nulled.css('top', '');
                }

                if (area.scrollTop() > bound)
                    panel.show();
                else
                    panel.hide();
            };

            panel.show = function () {
                if (state) return;

                state = true;

                panel.css('display', 'block')
                    .trigger('show')
                    .stop()
                    .animate({'top': 0}, 500);

                if (area.width() > 768) {
                    if (sticky.nulled.length)
                        sticky.nulled.animate({'top': panel.outerHeight() - 1}, 500);
                }
            };

            panel.hide = function () {
                if (!state) return;

                state = false;

                panel.stop().animate({
                    'top': -panel.height()
                }, 500, function () {
                    panel.trigger('hide');
                    panel.css('display', 'none');
                });

                if (area.width() > 768) {
                    if (sticky.nulled.length) {
                        sticky.nulled.animate({
                            'top': -1
                        }, 500, function () {
                            sticky.nulled.css('top', '');
                        });
                    }
                }
            };

            update();

            area.on('scroll', update)
                .on('resize', update);
        })();

        BX.Sale.BasketComponent.app = app;
        BX.Sale.BasketComponent.quickViewShow = quickViewShow;
        BX.Sale.BasketComponent.quickItemsId = quickItemsId;
        BX.Sale.BasketComponent.quickItems = quickItems;

        clear.on('click', function () {
            if (confirmRemoveProductUse) {
                confirmRemoveProductTitle.html('<?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_CONFIRM_REMOVE_PRODUCTS_TITLE') ?>');
                confirmRemoveProductText.html('<?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_CONFIRM_REMOVE_PRODUCTS_TEXT') ?>');
                confirmRemoveProductDelay[0].dataset.state = 'hidden';
                confirmRemoveProduct[0].dataset.state = 'visible';
                confirmRemoveProductAgree[0].onclick = function () {
                    app.api.basket.clear().run().then(function () {
                        location.reload();
                    });
                };
            } else {
                app.api.basket.clear().run().then(function () {
                    location.reload();
                });
            }

            app.api.basket.update();
        });

        confirmRemoveProductCancel.each(function () {
            $(this).on('click', function () {
                confirmRemoveProduct[0].dataset.state = 'hidden';
                confirmRemoveProductDelay[0].dataset.state = 'visible';
                confirmRemoveProductTitle.html('<?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_CONFIRM_REMOVE_PRODUCT_TITLE') ?>');
                confirmRemoveProductText.html('<?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_CONFIRM_REMOVE_PRODUCT_TEXT') ?>');
            });
        });

        if (currentHash.length) {
            if (currentHash.indexOf('#') == 0)
                currentHash = currentHash.substring(1);
            $(basketItemsCount.filter('[data-filter="'+currentHash+'"]')).trigger('click');
        }

    }, {
        'name': '[Component] bitrix:sale.basket.basket (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>