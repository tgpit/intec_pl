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

        var root = data.nodes;

        var items;
        var quickViewShow;
        var quickItemsId = [];
        var quickItems = Object.create(null);
        var print = $('[data-role="print.button"]', root);
        var filterInput = $(' #basket-filter-input', root);
        var filterValue;
        var table = $('[data-role="table"]', root);
        var clearButton = $('[data-role="clear.button"]', root);
        var deleteButtons = $('[data-role="delete.button"]', root);
        var deleteBasketItem;
        var content = $('[data-role="content.wrapper"]', root);
        var alertFormUse = <?= JavaScript::toObject($arParams['SHOW_ALERT_FORM'] === 'Y') ?>;
        var itemCounters = $('[data-role="item.counter"]', root);

        if (alertFormUse) {
            var alertBasketForm = $('[data-role="alert.basket.form"]', root);
            var alertBasketFormCloseIcon = $('[data-role="alert.close.icon"]', alertBasketForm);
            var alertBasketFormYes = $('[data-role="alert.button.yes"]', alertBasketForm);
            var alertBasketFormNo = $('[data-role="alert.button.no"]', alertBasketForm);
            var alertBasketFormTitle = $('[data-role="alert.form.title"]', alertBasketForm);
            var alertBasketFormText = $('[data-role="alert.form.text"]', alertBasketForm);
            var showFormButtons = $('[data-role="alert.form.show"]', root);
        }

        print.on('click', function() {
            var cssString = '<?= $this->GetFolder() ?>/style.css';

            root.printThis({
                importCSS: false,
                importStyle: true,
                loadCSS: cssString,
                pageTitle: "",
                removeInline: false,
                header: null,
                formValues: true,
                base: true
            });
        });

        <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
            quickViewShow = function (dataItem, quickItemsId) {
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
                }).then(function (popup) {
                    <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
                        var container = $(popup.contentContainer);

                        var indexItem = quickItemsId.indexOf(dataItem.parameters.ELEMENT_ID);
                        var prevItemId = quickItemsId[indexItem - 1];
                        var nextItemId = quickItemsId[indexItem + 1];

                        if (prevItemId === undefined)
                            prevItemId = 0;

                        if (nextItemId === undefined)
                            nextItemId = 0;

                        var load = container.find('.popup-load-container');

                        load.css('display', 'none');

                        container.append('<div class="popup-load-container"><div class="popup-load-whirlpool"></div></div>');

                        if (prevItemId !== 0 || nextItemId !== 0) {
                            container.append('<div class="popup-button btn-prev intec-cl-background-hover" data-role="quickView.button" data-id="' + prevItemId + '">' +
                                '<i class="far fa-angle-left"></i>' +
                                '</div>');
                            container.append('<div class="popup-button btn-next intec-cl-background-hover" data-role="quickView.button" data-id="' + nextItemId + '">' +
                                '<i class="far fa-angle-right"></i>' +
                                '</div>');
                        }
                    <?php } ?>
                });
            };

            <?php if ($arResult['QUICK_VIEW']['SLIDE']['USE']) { ?>
                $('body').on('click', '[data-role="quickView.button"]', function () {
                    var item = $(this);
                    var id = item.data('id');

                    item.parent().find('.popup-load-container').css('display', 'block');

                    quickViewShow(quickItems[id], quickItemsId);
                });
            <?php } ?>
        <?php } ?>

        var handled = [];

        if (!_.isNil(items))
            handled = items.handled;

        items = $('[data-role="item"]', root);
        items.handled = handled;

        items.each(function () {
            var item = $(this);

            item.counter = $('[data-role="item.counter"]', item);
            item.increase = $('[data-action="increase"]', item.counter);
            item.decrease = $('[data-action="decrease"]', item.counter);
            item.numeric = $('[data-role="item.numeric"]', item.counter);
            item.priceTotal = $('[data-role="item.price.total"]', item);

            var quantity = item.counter.data('quantity');
            var QuantityObj = new Startshop.Controls.NumericUpDown(quantity);

            QuantityObj.Settings.Events.OnValueChange = function ($oNumeric) {
                item.numeric.val($oNumeric.GetValue());

                app.api.basket.setQuantity({
                    'id': item.data('id'),
                    'quantity': $oNumeric.GetValue()
                }).run().then(function () {
                    reload();
                });
            };

            item.increase.on('click', function () {
                QuantityObj.Increase();
            });

            item.decrease.on('click', function () {
                QuantityObj.Decrease();
            });

            item.numeric.on('change', function () {
                QuantityObj.SetValue($(this).val());
            });

            function reload() {
                var buttons = [];
                var counters = [];
                var clear;
                var form;
                var clearStub = $(clearButton).clone();

                $(clearButton).before(clearStub);
                clear = $(clearButton).detach();

                itemCounters.each(function () {
                    var counter = $(this);
                    var counterStub = counter.clone();

                    counter.before(counterStub);
                    counters.push(counter.detach());
                });

                if (alertFormUse) {
                    alertBasketForm.fadeOut();

                    showFormButtons.each(function () {
                        var button = $(this);
                        var buttonStub = button.clone();

                        button.before(buttonStub);
                        buttons.push(button.detach());
                    });

                    form = alertBasketForm.detach();
                } else {
                    deleteButtons.each(function () {
                        var button = $(this);
                        var buttonStub = button.clone();

                        button.before(buttonStub);
                        buttons.push(button.detach());
                    });
                }

                $.ajax({
                    'data': {
                        'basket': {
                            'ajax': 'Y'
                        }
                    },
                    'cache': false,
                    'type': 'GET',
                    'beforeSend': function () {
                        $('[data-role="preloader"]', root).attr('data-active', 'true');
                    },
                    'success': function (response) {
                        $('[data-role="preloader"]', root).attr('data-active', 'false');

                        content.html(response);

                        content.find('[data-role="clear.button"]').replaceWith(clear);

                        $(counters).each(function () {
                            var counter = $(this);

                            content.find('[data-role="item.counter"][data-id=\"' + counter.data('id') + '\"]').replaceWith(counter);
                        });

                        if (alertFormUse) {
                            content.find('[data-role="alert.basket.form"]').replaceWith(form);

                            $(buttons).each(function () {
                                var button = $(this);

                                content.find('[data-role="alert.form.show"][data-id=\"' + button.data('id') + '\"]').replaceWith(button);
                            });
                        } else {
                            $(buttons).each(function () {
                                var button = $(this);

                                content.find('[data-role="delete.button"][data-id=\"' + button.data('id') + '\"]').replaceWith(button);
                            });
                        }
                    }
                });
            }
        });

        deleteBasketItem = function (id, alertFormUse) {
            app.api.basket.getItems().run().then(function (result) {
                var item = result.filter(function (i) {
                    return i.id === parseInt(id, 10);
                })[0];

                if (item) {
                    app.api.basket.remove({
                        'id': item.id,
                        'quantity': item.quantity,
                        'price': item.price
                    }).run().then(function () {
                        var buttons = [];
                        var counters = [];
                        var clear;
                        var form;
                        var clearStub = $(clearButton).clone();

                        $(clearButton).before(clearStub);
                        clear = $(clearButton).detach();

                        itemCounters.each(function () {
                            var counter = $(this);
                            var counterStub = counter.clone();

                            counter.before(counterStub);
                            counters.push(counter.detach());
                        });

                        if (alertFormUse) {
                            alertBasketForm.fadeOut();

                            showFormButtons.each(function () {
                                var button = $(this);
                                var buttonStub = button.clone();

                                button.before(buttonStub);
                                buttons.push(button.detach());
                            });

                            form = alertBasketForm.detach();
                        } else {
                            deleteButtons.each(function () {
                                var button = $(this);
                                var buttonStub = button.clone();

                                button.before(buttonStub);
                                buttons.push(button.detach());
                            });
                        }

                        $.ajax({
                            'data': {
                                'basket': {
                                    'ajax': 'Y'
                                }
                            },
                            'cache': false,
                            'type': 'GET',
                            'beforeSend': function () {
                                $('[data-role="preloader"]', root).attr('data-active', 'true');
                            },
                            'success': function (response) {
                                $('[data-role="preloader"]', root).attr('data-active', 'false');

                                content.html(response);

                                content.find('[data-role="clear.button"]').replaceWith(clear);

                                $(counters).each(function () {
                                    var counter = $(this);

                                    content.find('[data-role="item.counter"][data-id=\"' + counter.data('id') + '\"]').replaceWith(counter);
                                });

                                if (alertFormUse) {
                                    content.find('[data-role="alert.basket.form"]').replaceWith(form);

                                    $(buttons).each(function () {
                                        var button = $(this);

                                        content.find('[data-role="alert.form.show"][data-id=\"' + button.data('id') + '\"]').replaceWith(button);
                                    });
                                } else {
                                    $(buttons).each(function () {
                                        var button = $(this);

                                        content.find('[data-role="delete.button"][data-id=\"' + button.data('id') + '\"]').replaceWith(button);
                                    });
                                }
                            }
                        });
                    });
                }
            });
        };

        deleteButtons.on('click', function () {
            var self = $(this);
            self.id = self.data('id');

            deleteBasketItem(self.id, alertFormUse);
        });

        clearButton.on('click', function () {
            if (alertFormUse) {
                alertBasketFormTitle.text('<?= Loc::getMessage('SBB_DEFAULT_ALERT_TITLE_BASKET') ?>');
                alertBasketFormText.text('<?= Loc::getMessage('SBB_DEFAULT_ALERT_TEXT_BASKET') ?>');

                alertBasketForm.fadeIn();

                alertBasketFormNo.on('click', function () {
                    alertBasketForm.fadeOut();
                });

                alertBasketFormCloseIcon.on('click', function () {
                    alertBasketForm.fadeOut();
                });

                alertBasketFormYes.on('click', function () {
                    app.api.basket.clear().run().then(function () {
                        $.ajax({
                            'data': {
                                'basket': {
                                    'ajax': 'Y'
                                }
                            },
                            'cache': false,
                            'type': 'GET',
                            'beforeSend': function () {
                                $('[data-role="preloader"]', root).attr('data-active', 'true');
                            },
                            'success': function (response) {
                                $('[data-role="preloader"]', root).attr('data-active', 'false');
                                content.html(response);
                            }
                        });
                    });
                });
            } else {
                app.api.basket.clear().run().then(function () {
                    $.ajax({
                        'data': {
                            'basket': {
                                'ajax': 'Y'
                            }
                        },
                        'cache': false,
                        'type': 'GET',
                        'beforeSend': function () {
                            $('[data-role="preloader"]', root).attr('data-active', 'true');
                        },
                        'success': function (response) {
                            $('[data-role="preloader"]', root).attr('data-active', 'false');
                            content.html(response);
                        }
                    });
                });
            }
        });

        var highlight = function (collection, str, className) {
            var regex = new RegExp(str, "gi");

            collection.each(function () {
                $(this).contents().filter(function() {
                    return this.nodeType === 3 && regex.test(this.nodeValue);
                }).replaceWith(function() {
                    return (this.nodeValue || '').replace(regex, function(match) {
                        return "<span class=\"" + className + "\">" + match + "</span>";
                    });
                });
            });
        };

        filterInput.on('keyup', function () {
            filterValue = $(this).val().toUpperCase().trim();

            if (filterValue.length > 0) {
                items.addClass('table-row-display-none');
                table.addClass('table-filter-not-found');

                items.each(function (index, value) {
                    var item = $(this);
                    var nameBlock = item.find('[data-role="item.name.link"]');
                    var name = nameBlock.text();

                    nameBlock.html(name);

                    var nameUpCase = name.toUpperCase();

                    if (nameUpCase.includes(filterValue)) {

                        table.removeClass('table-filter-not-found');

                        highlight(nameBlock, filterValue, 'text-highlight');

                        item.removeClass('table-row-display-none');
                    }
                });
            } else {
                table.removeClass('table-filter-not-found');

                items.each(function (index, value) {
                    var item = $(this);
                    var nameBlock = item.find('[data-role="item.name.link"]');
                    var name = nameBlock.text();

                    nameBlock.html(name);

                    item.removeClass('table-row-display-none');
                });
            }
        });

        <?php if ($arResult['QUICK_VIEW']['USE']) { ?>
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
        <?php } ?>

        if (alertFormUse) {
            showFormButtons.on('click', function () {
                var self = this;
                self.id = $(self).data('id');

                alertBasketFormTitle.text('<?= Loc::getMessage('SBB_DEFAULT_ALERT_TITLE_PRODUCT') ?>');
                alertBasketFormText.text('<?= Loc::getMessage('SBB_DEFAULT_ALERT_TEXT_PRODUCT') ?>');

                alertBasketForm.fadeIn();

                alertBasketFormNo.on('click', function () {
                    alertBasketForm.fadeOut();
                });

                alertBasketFormCloseIcon.on('click', function () {
                    alertBasketForm.fadeOut();
                });

                alertBasketFormYes.on('click', function () {
                    deleteBasketItem(self.id, alertFormUse);
                });
            });
        }
    }, {
        'name': '[Component] intec:startshop.basket.basket (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>