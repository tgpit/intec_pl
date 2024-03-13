(function () {
    'use strict';

    if (!!window.MainNewsComponent)
        return;

	window.SetConstructorCustom = function (parameters) {
		this.id = document.querySelector(parameters.id);
		this.currency = parameters.currency;
		this.price = {
		    'current': null,
            'old': null,
            'difference': null
        };
		this.items = {
		    'main': {},
            'set': [],
            'small': [],
            'total': {},
            'stub': null
        };
		this.buy = null;
		this.ratio = parameters.ratio;
		this.siteId = parameters.siteId;
        this.iblockId = parameters.iblockId;
		this.offersProperties = parameters.offersProperties;
		this.ajaxPath = parameters.ajaxPath;
		this.basketUrl = parameters.basketUrl;
    };

    SetConstructorCustom.prototype = {
        'prepare': function () {
            if (this.id === null)
                return false;

            this.items.main = this.id.querySelector('[data-role="main.item"]');
            this.items.set = this.id.querySelectorAll('[data-role="set.item"]');
            this.items.small = this.id.querySelectorAll('[data-role="small.item"]');
            this.items.total = {
                'current': this.id.querySelector('[data-role="total.price.current"]'),
                'old': this.id.querySelector('[data-role="total.price.old"]'),
                'difference': {
                    'container': this.id.querySelector('[data-role="total.price.difference"]'),
                    'value': this.id.querySelector('[data-role="total.price.difference.value"]')
                }
            };
            this.items.stub = this.id.querySelector('[data-role="small.empty"]');
            this.buy = this.id.querySelector('[data-role="set.buy"]');

            return true;
        },
        'build': function () {
            let set = [];
            let small = [];

            this.items.main = {
                'id': Number(this.items.main.getAttribute('data-id')),
                'element': this.items.main,
                'available': this.items.main.getAttribute('data-available') === 'true',
                'quantity': Number(this.items.main.getAttribute('data-quantity')),
                'price': {
                    'current': Number(this.items.main.getAttribute('data-price-current')),
                    'old': Number(this.items.main.getAttribute('data-price-old')),
                    'difference': Number(this.items.main.getAttribute('data-price-difference'))
                }
            };

            this.items.set.forEach(function (value) {
                set.push({
                    'id': Number(value.getAttribute('data-id')),
                    'element': value,
                    'available': value.getAttribute('data-available') === 'true',
                    'selected': value.getAttribute('data-selected') === 'true',
                    'quantity': Number(value.getAttribute('data-quantity')),
                    'price': {
                        'current': Number(value.getAttribute('data-price-current')),
                        'old': Number(value.getAttribute('data-price-old')),
                        'difference': Number(value.getAttribute('data-price-difference'))
                    }
                });
            });

            this.items.small.forEach(function (value) {
                small.push({
                    'id': Number(value.getAttribute('data-small-id')),
                    'selected': value.getAttribute('data-selected') === 'true',
                    'element': value
                });
            });

            this.items.set = set;
            this.items.small = small;
        },
        'calculate': function () {
            let price = {
                'current': this.items.main.price.current * this.items.main.quantity,
                'old': this.items.main.price.old * this.items.main.quantity,
                'difference': this.items.main.price.difference * this.items.main.quantity
            };

            this.items.set.forEach(function (value) {
                if (value.selected) {
                    price.current = price.current + (value.price.current * value.quantity);
                    price.old = price.old + (value.price.old * value.quantity);
                    price.difference = price.difference + (value.price.difference * value.quantity);
                }
            });

            return price;
        },
        'printPrice': function () {
            let total = this.items.total;
            let price = this.calculate();

            if (total.current !== null) {
                total.current.innerHTML = BX.Currency.currencyFormat(price.current, this.currency, true);
            }

            if (price.difference > 0) {
                if (total.old !== null) {
                    total.old.innerHTML = BX.Currency.currencyFormat(price.old, this.currency, true);
                    total.old.setAttribute('data-show', 'true');
                }

                if (total.difference.container !== null) {
                    total.difference.value.innerHTML = BX.Currency.currencyFormat(price.difference, this.currency, true);
                    total.difference.container.setAttribute('data-show', 'true');
                }
            } else {
                total.old.setAttribute('data-show', 'false');
                total.difference.container.setAttribute('data-show', 'false');
            }
        },
        'actionAdd': function (item) {
            let added = false;

            item = Number(item);

            this.items.set.forEach(function (value, index) {
                if (value.id === item && value.available && !value.selected) {
                    this.items.set[index].selected = true;

                    this.refresh(this.items.set[index].element, true);

                    added = true;
                }
            }, this);

            if (added) {
                this.items.small.forEach(function (value, index) {
                    if (value.id === item && !value.selected) {
                        this.items.small[index].selected = true;

                        this.refresh(this.items.small[index].element, true);
                    }
                }, this);

                this.printPrice();
                this.isEmpty();
            }
        },
        'actionRemove': function (item) {
            let removed = false;

            item = Number(item);

            this.items.set.forEach(function (value, index) {
                if (value.id === item && value.available && value.selected) {
                    this.items.set[index].selected = false;

                    this.refresh(this.items.set[index].element, false);

                    removed = true;
                }
            }, this);

            if (removed) {
                this.items.small.forEach(function (value, index) {
                    if (value.id === item && value.selected) {
                        this.items.small[index].selected = false;

                        this.refresh(this.items.small[index].element, false);
                    }
                }, this);

                this.printPrice();
                this.isEmpty();
            }
        },
        'refresh': function (item, value) {
            let state;

            if (value)
                state = 'true';
            else
                state = 'false';

            item.setAttribute('data-selected', state);
        },
        'isEmpty': function () {
            let empty = true;

            this.items.set.forEach(function (value) {
                if (value.selected)
                    empty = false;
            });

            if (empty) {
                this.items.stub.setAttribute('data-selected', 'true');
                this.buy.setAttribute('data-active', 'false');
            } else {
                this.items.stub.setAttribute('data-selected', 'false');
                this.buy.setAttribute('data-active', 'true');
            }
        },
        'addToBasket': function () {
            let active = this.buy.getAttribute('data-active') === 'true';

            if (!active)
                return false;

            let ids = [];

            ids.push(this.items.main.id);

            this.items.set.forEach(function (value) {
                if (value.available && value.selected)
                    ids.push(value.id);
            });

            BX.showWait(this.id);

            BX.ajax.post(
                this.ajaxPath,
                {
                    sessid: BX.bitrix_sessid(),
                    action: 'catalogSetAdd2Basket',
                    set_ids: ids,
                    lid: this.siteId,
                    iblockId: this.iblockId,
                    setOffersCartProps: this.offersProperties,
                    itemsRatio: this.ratio
                },
                BX.proxy(function(result) {
                    BX.closeWait();
                    document.location.href = this.basketUrl;
                }, this)
            );
        },
        'init': function () {
            if (!this.prepare())
                return;

            this.build();
            this.printPrice();
            this.isEmpty();
        }
    };
})();