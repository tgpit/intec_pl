(function () {
    'use strict';

    if (!!window.intecCatalogStoreOffersMap)
        return false;

    var $ = template.getLibrary('$');
    var _ = template.getLibrary('_');

    window.intecCatalogStoreOffersMap = function (id, parameters) {
        var self = this;

        if (!id || !parameters)
            return;

        this.container = $(id);
        this.measures = parameters.measures;
        this.messages = parameters.messages;
        this.offers = parameters.offers;
        this.parameters = parameters.parameters;
        this.states = parameters.states;
        this.stores = parameters.stores;
        this.map = parameters.map;
        this.markers = parameters.markers;

        var loader = function () {
            if (window.maps && window.maps.stores) {
                clearInterval(loader.interval);

                var first = null;

                if (self.map === 'google') {
                    window.maps.stores.zoom = 16;

                    for (var marker in self.markers) {
                        if (self.markers.hasOwnProperty(marker)) {
                            if (!first)
                                first = self.markers[marker];

                            new google.maps.Marker({
                                'position': new google.maps.LatLng(
                                    self.markers[marker].lat,
                                    self.markers[marker].lng
                                ),
                                'map': window.maps.stores,
                                'title': self.markers[marker].name
                            });
                        }
                    }

                    if (first)
                        self.googleJump(first.id);
                } else if (self.map === 'yandex') {
                    for (var marker in self.markers) {
                        if (self.markers.hasOwnProperty(marker)) {
                            if (!first)
                                first = self.markers[marker];

                            window.maps.stores.geoObjects.add(new ymaps.Placemark([
                                self.markers[marker].lat,
                                self.markers[marker].lng
                            ], {
                                'hintContent': self.markers[marker].name
                            }));
                        }
                    }

                    if (first)
                        self.yandexJump(first.id);
                }
            }
        };

        loader.interval = setInterval(loader, 100);
    };

    intecCatalogStoreOffersMap.prototype = {
        'offerOnChange': function (id) {
            if (!id)
                return;

            var offer = this.offers[id];
            var index, store, status;

            for (index in offer) {
                if (offer.hasOwnProperty(index)) {
                    store = $('[data-store-id="' + index + '"]', this.container);

                    status = this.getStatus(offer[index]);

                    store.state = $('[data-role="store.state"]', store);
                    store.state.attr('data-store-state', status.state);

                    store.quantity = $('[data-role="store.quantity"]', store);
                    store.quantity.html(status.text);

                    if (!this.parameters.useMinAmount) {
                        store.measure = $('[data-role="store.measure"]', store);
                        store.measure.html(this.measures[id]);
                    }
                }
            }
        },
        'getStatus': function (quantity) {
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
        },
        'jump': function (id) {
            if (this.map === 'google')
                this.googleJump(id);
            else if (this.map === 'yandex')
                this.yandexJump(id);
        },
        'googleJump': function (id) {
            if (!_.isNil(id) && this.markers.hasOwnProperty('store-' + id)) {
                window.maps.stores.panTo(new google.maps.LatLng(
                    this.markers['store-' + id].lat,
                    this.markers['store-' + id].lng
                ));
            }
        },
        'yandexJump': function (id) {
            if (!_.isNil(id) && this.markers.hasOwnProperty('store-' + id)) {
                window.maps.stores.panTo([
                    this.markers['store-' + id].lat,
                    this.markers['store-' + id].lng
                ]);
            }
        }
    };
})();