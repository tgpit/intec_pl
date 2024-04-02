function JCSmartFilterMobile1 (ajaxURL, params) {
    this.ajaxURL = ajaxURL;
    this.form = null;
    this.timer = null;
    this.cacheKey = '';
    this.cache = [];
    this.popups = [];
    this.quantity = BX('quantity-filter-mobile');

    if (params && params.SEF_SET_FILTER_URL) {
        this.bindUrlToButton('set-filter-mobile', params.SEF_SET_FILTER_URL);
        this.sef = true;
    }

    if (params && params.SEF_DEL_FILTER_URL)
        this.bindUrlToButton('delete-filter-mobile', params.SEF_DEL_FILTER_URL);
}

JCSmartFilterMobile1.prototype.keyup = function(input) {
    if(!!this.timer) {
        clearTimeout(this.timer);
    }

    this.timer = setTimeout(BX.delegate(function() {
        this.reload(input);
    }, this), 500);
};

JCSmartFilterMobile1.prototype.click = function(checkbox) {
    if(!!this.timer)
        clearTimeout(this.timer);

    this.timer = setTimeout(BX.delegate(function() {
        this.reload(checkbox);
    }, this), 500);
};

JCSmartFilterMobile1.prototype.reload = function(input) {
    if (this.cacheKey !== '') {
        if(!!this.timer)
            clearTimeout(this.timer);

        this.timer = setTimeout(BX.delegate(function() {
            this.reload(input);
        }, this), 1000);

        return;
    }

    this.cacheKey = '|';

    this.position = BX.pos(input, true);
    this.form = BX.findParent(input, {
        'tag': 'form'
    });

    if (this.form) {
        var values = [];

        values[0] = {
            'name': 'ajax',
            'value': 'y'
        };

        this.gatherInputsValues(values, BX.findChildren(this.form, {
            'tag': new RegExp('^(input|select)$', 'i')
        }, true));

        for (var i = 0; i < values.length; i++)
            this.cacheKey += values[i].name + ':' + values[i].value + '|';

        if (this.cache[this.cacheKey]) {
            this.curFilterinput = input;
            this.postHandler(this.cache[this.cacheKey], true);
        } else {
            if (this.sef) {
                var setFilter = BX('set-filter-mobile');

                setFilter.disabled = true;
            }

            this.curFilterinput = input;

            BX.ajax.loadJSON(
                this.ajaxURL,
                this.values2post(values),
                BX.delegate(this.postHandler, this)
            );
        }
    }
};

JCSmartFilterMobile1.prototype.updateItem = function (PID, arItem) {
    if (arItem.PROPERTY_TYPE === 'A' || arItem.PRICE) {
        var trackBar = window['trackBarMobile' + PID];

        if (!trackBar && arItem.ENCODED_ID)
            trackBar = window['trackBarMobile' + arItem.ENCODED_ID];

        if (trackBar && arItem.VALUES) {
            if (arItem.VALUES.MIN) {
                if (arItem.VALUES.MIN.FILTERED_VALUE)
                    trackBar.setMinFilteredValue(arItem.VALUES.MIN.FILTERED_VALUE);
                else
                    trackBar.setMinFilteredValue(arItem.VALUES.MIN.VALUE);
            }

            if (arItem.VALUES.MAX) {
                if (arItem.VALUES.MAX.FILTERED_VALUE)
                    trackBar.setMaxFilteredValue(arItem.VALUES.MAX.FILTERED_VALUE);
                else
                    trackBar.setMaxFilteredValue(arItem.VALUES.MAX.VALUE);
            }
        }
    } else if (arItem.VALUES) {
        for (var i in arItem.VALUES) {
            if (arItem.VALUES.hasOwnProperty(i)) {
                var value = arItem.VALUES[i];
                var control = BX(value.CONTROL_ID + '_mobile');

                if (!!control) {
                    if (control.tagName === 'SELECT') {
                        if (control.length > 0) {
                            var option = control.namedItem(value.CONTROL_ID + '_mobile');

                            if (option !== null) {
                                if (value.DISABLED) {
                                    if (option.value === control.value && option.selected) {
                                        option.selected = false;

                                        control.dispatchEvent(new Event('change'));
                                    }

                                    option.disabled = true;
                                } else {
                                    option.disabled = false;
                                }
                            }
                        }
                    } else {
                        var label = document.querySelector('[data-property="' + value.CONTROL_ID + '"]');

                        if (value.DISABLED) {
                            if (label)
                                BX.addClass(label, 'disabled');
                            else
                                BX.addClass(control.parentNode, 'disabled');

                            control.checked = false;
                            control.disabled = true;
                        } else {
                            if (label)
                                BX.removeClass(label, 'disabled');
                            else
                                BX.removeClass(control.parentNode, 'disabled');

                            control.disabled = false;
                        }
                    }
                }
            }
        }
    }
};

JCSmartFilterMobile1.prototype.postHandler = function (result, fromCache) {
    var url;

    if (!!result && !!result.ITEMS) {
        for (var popupId in this.popups) {
            if (this.popups.hasOwnProperty(popupId))
                this.popups[popupId].destroy();
        }

        this.popups = [];

        for (var PID in result.ITEMS) {
            if (result.ITEMS.hasOwnProperty(PID))
                this.updateItem(PID, result.ITEMS[PID]);
        }

        if (this.quantity)
            this.quantity.innerHTML = '(' + result.ELEMENT_COUNT + ')';

        if (result.INSTANT_RELOAD && result.COMPONENT_CONTAINER_ID) {
            url = BX.util.htmlspecialcharsback(result.FILTER_AJAX_URL);
            BX.ajax.insertToNode(url, result.COMPONENT_CONTAINER_ID);
        } else {
            if (result.SEF_SET_FILTER_URL)
                this.bindUrlToButton('set-filter-mobile', result.SEF_SET_FILTER_URL);
        }
    }

    if (this.sef) {
        var setFilter = BX('set-filter-mobile');

        setFilter.disabled = false;
    }

    if (!fromCache && this.cacheKey !== '')
        this.cache[this.cacheKey] = result;

    this.cacheKey = '';
};

JCSmartFilterMobile1.prototype.bindUrlToButton = function (buttonId, url) {
    var button = BX(buttonId);

    if (button) {
        var proxy = function (j, func) {
            return function () {
                return func(j);
            }
        };

        if (button.type === 'submit')
            button.type = 'button';

        BX.bind(button, 'click', proxy(url, function (url) {
            window.location.href = url;

            return false;
        }));
    }
};

JCSmartFilterMobile1.prototype.gatherInputsValues = function (values, elements) {
    if (elements) {
        for (var i = 0; i < elements.length; i++) {
            var el = elements[i];

            if (el.disabled || !el.type)
                continue;

            switch (el.type.toLowerCase()) {
                case 'text':
                case 'textarea':
                case 'password':
                case 'hidden':
                case 'select-one':
                    if (el.value.length) {
                        values[values.length] = {
                            'name': el.name,
                            'value': el.value
                        };
                    }

                    break;
                case 'radio':
                case 'checkbox':
                    if (el.checked) {
                        values[values.length] = {
                            'name': el.name,
                            'value': el.value
                        };
                    }

                    break;
                case 'select-multiple':
                    for (var j = 0; j < el.options.length; j++) {
                        if (el.options[j].selected) {
                            values[values.length] = {
                                'name': el.name,
                                'value': el.options[j].value
                            };
                        }
                    }

                    break;
                default:
                    break;
            }
        }
    }
};

JCSmartFilterMobile1.prototype.values2post = function (values) {
    var post = [];
    var current = post;
    var i = 0;

    while (i < values.length) {
        var p = values[i].name.indexOf('[');

        if (p == -1) {
            current[values[i].name] = values[i].value;
            current = post;
            i++;
        } else {
            var name = values[i].name.substring(0, p);
            var rest = values[i].name.substring(p + 1);

            if (!current[name])
                current[name] = [];

            var pp = rest.indexOf(']');

            if (pp == -1) {
                current = post;
                i++;
            } else if (pp == 0) {
                current = current[name];
                values[i].name = '' + current.length;
            } else {
                current = current[name];
                values[i].name = rest.substring(0, pp) + rest.substring(pp+1);
            }
        }
    }

    return post;
};

BX.namespace('BX.Iblock.SmartFilterMobile1');

BX.Iblock.SmartFilterMobile1 = (function() {
    var SmartFilter = function(arParams) {
        if (typeof arParams === 'object') {
            this.leftSlider = BX(arParams.leftSlider);
            this.rightSlider = BX(arParams.rightSlider);
            this.tracker = BX(arParams.tracker);
            this.trackerWrap = BX(arParams.trackerWrap);
            this.minInput = BX(arParams.minInputId);
            this.maxInput = BX(arParams.maxInputId);
            this.minPrice = parseFloat(arParams.minPrice);
            this.maxPrice = parseFloat(arParams.maxPrice);
            this.curMinPrice = parseFloat(arParams.curMinPrice);
            this.curMaxPrice = parseFloat(arParams.curMaxPrice);
            this.fltMinPrice = arParams.fltMinPrice ? parseFloat(arParams.fltMinPrice) : parseFloat(arParams.curMinPrice);
            this.fltMaxPrice = arParams.fltMaxPrice ? parseFloat(arParams.fltMaxPrice) : parseFloat(arParams.curMaxPrice);
            this.precision = arParams.precision || 0;
            this.priceDiff = this.maxPrice - this.minPrice;
            this.leftPercent = 0;
            this.rightPercent = 0;
            this.fltMinPercent = 0;
            this.fltMaxPercent = 0;
            this.colorUnavailableActive = BX(arParams.colorUnavailableActive);
            this.colorAvailableActive = BX(arParams.colorAvailableActive);
            this.colorAvailableInactive = BX(arParams.colorAvailableInactive);

            this.isTouch = false;

            this.init();

            if ('ontouchstart' in document.documentElement) {
                this.isTouch = true;

                BX.bind(this.leftSlider, 'touchstart', BX.proxy(function(event){
                    this.onMoveLeftSlider(event)
                }, this));

                BX.bind(this.rightSlider, 'touchstart', BX.proxy(function(event){
                    this.onMoveRightSlider(event)
                }, this));
            } else {
                BX.bind(this.leftSlider, 'mousedown', BX.proxy(function(event){
                    this.onMoveLeftSlider(event)
                }, this));

                BX.bind(this.rightSlider, 'mousedown', BX.proxy(function(event){
                    this.onMoveRightSlider(event)
                }, this));
            }

            BX.bind(this.minInput, 'keyup', BX.proxy(function(event){
                this.onInputChange();
            }, this));

            BX.bind(this.maxInput, 'keyup', BX.proxy(function(event){
                this.onInputChange();
            }, this));
        }
    };

    SmartFilter.prototype.init = function() {
        var priceDiff;

        if (this.curMinPrice > this.minPrice) {
            priceDiff = this.curMinPrice - this.minPrice;

            this.leftPercent = (priceDiff * 100) / this.priceDiff;
            this.leftSlider.style.left = this.leftPercent + '%';
            this.colorUnavailableActive.style.left = this.leftPercent + '%';
        }

        this.setMinFilteredValue(this.fltMinPrice);

        if (this.curMaxPrice < this.maxPrice) {
            priceDiff = this.maxPrice - this.curMaxPrice;

            this.rightPercent = (priceDiff * 100) / this.priceDiff;
            this.rightSlider.style.right = this.rightPercent + '%';
            this.colorUnavailableActive.style.right = this.rightPercent + '%';
        }

        this.setMaxFilteredValue(this.fltMaxPrice);
    };

    SmartFilter.prototype.setMinFilteredValue = function (fltMinPrice) {
        this.fltMinPrice = parseFloat(fltMinPrice);

        if (this.fltMinPrice >= this.minPrice) {
            var priceDiff = this.fltMinPrice - this.minPrice;

            this.fltMinPercent = (priceDiff * 100) / this.priceDiff;

            if (this.leftPercent > this.fltMinPercent)
                this.colorAvailableActive.style.left = this.leftPercent + '%';
            else
                this.colorAvailableActive.style.left = this.fltMinPercent + '%';

            this.colorAvailableInactive.style.left = this.fltMinPercent + '%';
        } else {
            this.colorAvailableActive.style.left = '0%';
            this.colorAvailableInactive.style.left = '0%';
        }
    };

    SmartFilter.prototype.setMaxFilteredValue = function (fltMaxPrice) {
        this.fltMaxPrice = parseFloat(fltMaxPrice);

        if (this.fltMaxPrice <= this.maxPrice) {
            var priceDiff = this.maxPrice - this.fltMaxPrice;

            this.fltMaxPercent = (priceDiff * 100) / this.priceDiff;

            if (this.rightPercent > this.fltMaxPercent)
                this.colorAvailableActive.style.right = this.rightPercent + '%';
            else
                this.colorAvailableActive.style.right = this.fltMaxPercent + '%';

            this.colorAvailableInactive.style.right = this.fltMaxPercent + '%';
        } else {
            this.colorAvailableActive.style.right = '0%';
            this.colorAvailableInactive.style.right = '0%';
        }
    };

    SmartFilter.prototype.getXCoord = function(elem) {
        var box = elem.getBoundingClientRect();
        var body = document.body;
        var docElem = document.documentElement;

        var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft;
        var clientLeft = docElem.clientLeft || body.clientLeft || 0;
        var left = box.left + scrollLeft - clientLeft;

        return Math.round(left);
    };

    SmartFilter.prototype.getPageX = function(e) {
        var pageX = null;

        e = e || window.event;

        if (this.isTouch && event.targetTouches[0] != null) {
            pageX = e.targetTouches[0].pageX;
        } else if (e.pageX != null) {
            pageX = e.pageX;
        } else if (e.clientX != null) {
            var html = document.documentElement;
            var body = document.body;

            pageX = e.clientX + (html.scrollLeft || body && body.scrollLeft || 0);
            pageX -= html.clientLeft || 0;
        }

        return pageX;
    };

    SmartFilter.prototype.recountMinPrice = function() {
        var newMinPrice = (this.priceDiff*this.leftPercent) / 100;

        newMinPrice = (this.minPrice + newMinPrice).toFixed(this.precision);

        if (newMinPrice != this.minPrice)
            this.minInput.value = newMinPrice;
        else
            this.minInput.value = '';

        /** @global JCSmartFilterMobile1 smartFilter */
        mobileFilter.keyup(this.minInput);
    };

    SmartFilter.prototype.recountMaxPrice = function() {
        var newMaxPrice = (this.priceDiff*this.rightPercent) / 100;

        newMaxPrice = (this.maxPrice - newMaxPrice).toFixed(this.precision);

        if (newMaxPrice != this.maxPrice)
            this.maxInput.value = newMaxPrice;
        else
            this.maxInput.value = '';

        /** @global JCSmartFilterMobile1 smartFilter */
        mobileFilter.keyup(this.maxInput);
    };

    SmartFilter.prototype.onInputChange = function () {
        var priceDiff;

        if (this.minInput.value) {
            var leftInputValue = this.minInput.value;

            if (leftInputValue < this.minPrice)
                leftInputValue = this.minPrice;

            if (leftInputValue > this.maxPrice)
                leftInputValue = this.maxPrice;

            priceDiff = leftInputValue - this.minPrice;

            this.leftPercent = (priceDiff * 100) / this.priceDiff;

            this.makeLeftSliderMove(false);
        }

        if (this.maxInput.value) {
            var rightInputValue = this.maxInput.value;

            if (rightInputValue < this.minPrice)
                rightInputValue = this.minPrice;

            if (rightInputValue > this.maxPrice)
                rightInputValue = this.maxPrice;

            priceDiff = this.maxPrice - rightInputValue;

            this.rightPercent = (priceDiff * 100) / this.priceDiff;

            this.makeRightSliderMove(false);
        }
    };

    SmartFilter.prototype.makeLeftSliderMove = function(recountPrice) {
        var areBothSlidersMoving = false;

        recountPrice = (recountPrice !== false);

        this.leftSlider.style.left = this.leftPercent + '%';
        this.colorUnavailableActive.style.left = this.leftPercent + '%';

        if (this.leftPercent + this.rightPercent >= 100) {
            areBothSlidersMoving = true;

            this.rightPercent = 100 - this.leftPercent;
            this.rightSlider.style.right = this.rightPercent + '%';
            this.colorUnavailableActive.style.right = this.rightPercent + '%';
        }

        if (this.leftPercent >= this.fltMinPercent && this.leftPercent <= (100 - this.fltMaxPercent)) {
            this.colorAvailableActive.style.left = this.leftPercent + '%';

            if (areBothSlidersMoving)
                this.colorAvailableActive.style.right = 100 - this.leftPercent + '%';
        } else if (this.leftPercent <= this.fltMinPercent) {
            this.colorAvailableActive.style.left = this.fltMinPercent + '%';

            if (areBothSlidersMoving)
                this.colorAvailableActive.style.right = 100 - this.fltMinPercent + '%';
        } else if (this.leftPercent >= this.fltMaxPercent) {
            this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + '%';

            if (areBothSlidersMoving)
                this.colorAvailableActive.style.right = this.fltMaxPercent + '%';
        }

        if (recountPrice) {
            this.recountMinPrice();

            if (areBothSlidersMoving)
                this.recountMaxPrice();
        }
    };

    SmartFilter.prototype.countNewLeft = function(event) {
        var pageX = this.getPageX(event);
        var trackerXCoord = this.getXCoord(this.trackerWrap);
        var rightEdge = this.trackerWrap.offsetWidth;
        var newLeft = pageX - trackerXCoord;

        if (newLeft < 0)
            newLeft = 0;
        else if (newLeft > rightEdge)
            newLeft = rightEdge;

        return newLeft;
    };

    SmartFilter.prototype.onMoveLeftSlider = function(e) {
        if (!this.isTouch) {
            this.leftSlider.ondragstart = function() {
                return false;
            };
        }

        if (!this.isTouch) {
            document.onmousemove = BX.proxy(function(event) {
                this.leftPercent = ((this.countNewLeft(event) * 100) / this.trackerWrap.offsetWidth);
                this.makeLeftSliderMove();
            }, this);

            document.onmouseup = function() {
                document.onmousemove = document.onmouseup = null;
            };
        } else {
            document.ontouchmove = BX.proxy(function(event) {
                this.leftPercent = ((this.countNewLeft(event) * 100) / this.trackerWrap.offsetWidth);
                this.makeLeftSliderMove();
            }, this);

            document.ontouchend = function() {
                document.ontouchmove = document.touchend = null;
            };
        }

        return false;
    };

    SmartFilter.prototype.makeRightSliderMove = function(recountPrice) {
        var areBothSlidersMoving = false;

        recountPrice = (recountPrice !== false);

        this.rightSlider.style.right = this.rightPercent + '%';
        this.colorUnavailableActive.style.right = this.rightPercent + '%';

        if (this.leftPercent + this.rightPercent >= 100) {
            areBothSlidersMoving = true;

            this.leftPercent = 100 - this.rightPercent;
            this.leftSlider.style.left = this.leftPercent + '%';
            this.colorUnavailableActive.style.left = this.leftPercent + '%';
        }

        if ((100 - this.rightPercent) >= this.fltMinPercent && this.rightPercent >= this.fltMaxPercent) {
            this.colorAvailableActive.style.right = this.rightPercent + '%';

            if (areBothSlidersMoving)
                this.colorAvailableActive.style.left = 100 - this.rightPercent + '%';
        } else if (this.rightPercent <= this.fltMaxPercent) {
            this.colorAvailableActive.style.right = this.fltMaxPercent + '%';

            if (areBothSlidersMoving)
                this.colorAvailableActive.style.left = 100 - this.fltMaxPercent + '%';
        } else if ((100 - this.rightPercent) <= this.fltMinPercent) {
            this.colorAvailableActive.style.right = 100 - this.fltMinPercent + '%';

            if (areBothSlidersMoving)
                this.colorAvailableActive.style.left = this.fltMinPercent + '%';
        }

        if (recountPrice) {
            this.recountMaxPrice();

            if (areBothSlidersMoving)
                this.recountMinPrice();
        }
    };

    SmartFilter.prototype.onMoveRightSlider = function(e) {
        if (!this.isTouch) {
            this.rightSlider.ondragstart = function() {
                return false;
            };
        }

        if (!this.isTouch) {
            document.onmousemove = BX.proxy(function(event) {
                this.rightPercent = 100 - (((this.countNewLeft(event)) * 100) / (this.trackerWrap.offsetWidth));
                this.makeRightSliderMove();
            }, this);

            document.onmouseup = function() {
                document.onmousemove = document.onmouseup = null;
            };
        } else {
            document.ontouchmove = BX.proxy(function(event) {
                this.rightPercent = 100 - (((this.countNewLeft(event)) * 100) / (this.trackerWrap.offsetWidth));
                this.makeRightSliderMove();
            }, this);

            document.ontouchend = function() {
                document.ontouchmove = document.ontouchend = null;
            };
        }

        return false;
    };

    return SmartFilter;
})();