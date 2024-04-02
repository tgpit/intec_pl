(function () {
    'use strict';

    if (!!window.intecCatalogStoreOffers)
        return false;

    var $ = template.getLibrary('$');

    window.intecCatalogStoreOffers = function (id, parameters) {
        if (!id || !parameters)
            return;

        this.container = $(id);
        this.measures = parameters.measures;
        this.messages = parameters.messages;
        this.offers = parameters.offers;
        this.parameters = parameters.parameters;
        this.states = parameters.states;
        this.stores = parameters.stores;
    };

    window.intecCatalogStoreOffers.prototype.offerOnChange = function (id) {
        if (!id)
            return;

        var offer = this.offers[id];
        var index, store, status;
        var stores = $('[data-role="store"]', this.container);
        var messageBlock = $('[data-role="message.block"]', this.container);
        var parameters = this.parameters;
        var messages = this.messages;

        messageBlock.show();

        stores.each(function () {
            var store = $(this)[0];
            var quantity = $('[data-role="store.quantity"]', store);
            var text;

            if (parameters.useMinAmount) {
                text = messages[2];
            } else {
                text = '0';
            }

            store.dataset.storeState = 'empty';
            quantity.html(text);
        });

        for (index in offer) {
            if (offer.hasOwnProperty(index)) {
                store = $('[data-store-id="' + index + '"]', this.container);

                status = this.getStatus(offer[index]);

                if (status['state'] != 'empty')
                    messageBlock.hide();

                store.state = $('[data-role="store.state"]', store);
                store.attr('data-store-state', status.state);

                store.quantity = $('[data-role="store.quantity"]', store);
                store.quantity.html(status.text);

                if (!this.parameters.useMinAmount) {
                    store.measure = $('[data-role="store.measure"]', store);
                    store.measure.html(this.measures[id]);
                }
            }
        }
    };

    window.intecCatalogStoreOffers.prototype.getStatus = function (quantity) {
        var text = '0';
        var state = 'empty';

        if (this.parameters.useMinAmount) {
            if (quantity > this.parameters.minAmount) {
                text = this.messages[0];
                state = this.states[0];
            } else if (quantity <= this.parameters.minAmount && quantity > 0) {
                text = this.messages[1];
                state = this.states[1];
            } else {
                text = this.messages[2];
                state = this.states[2];
            }
        } else {
            text = quantity;

            if (quantity > 0)
                state = this.states[0];
            else
                state = this.states[2];
        }

        return {
            'text': text,
            'state': state
        };
    };
})();