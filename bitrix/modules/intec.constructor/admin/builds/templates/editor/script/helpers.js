helpers = (function () {
    var helpers = {};

    helpers.DOM = {};

    /**
     * helpers.DOM.evaluateScripts
     * @param scripts
     */
    helpers.DOM.evaluateScripts = (function (scripts) {
        var handler;

        handler = function (scripts) {
            api.each(scripts, function (index, script) {
                var parent = script.parentNode;
                var element = document.createElement('script');

                index = api.toInteger(index);
                element.async = false;
                element.defer = false;

                api.each(script.attributes, function (index, attribute) {
                    element.setAttribute(attribute.name, attribute.value);
                });

                element.textContent = script.textContent;
                parent.insertBefore(element, script);
                parent.removeChild(script);

                if (element.src) {
                    var stack = Array.prototype.slice.call(scripts, index + 1);
                    var elementLoad = element.onload;
                    var elementError = element.onerror;

                    element.onload = function () {
                        try {
                            if (api.isFunction(elementLoad))
                                elementLoad.apply(this, arguments);
                        } catch (e) {
                            console.error(e);
                        } finally {
                            handler(stack);
                        }
                    };

                    element.onerror = function () {
                        try {
                            if (api.isFunction(elementError))
                                elementError.apply(this, arguments);
                        } catch (e) {
                            console.error(e);
                        } finally {
                            handler(stack);
                        }
                    };

                    return false;
                }
            });
        };

        return function (scripts) {
            handler(scripts);
        };
    })();

    return helpers;
})();