(function () {
    var prototype;
    var constructor;

    classes.Resources = function (document) {
        var self = this;

        Object.defineProperty(self, 'document', {
            'configurable': false,
            'enumerable': true,
            'get': function () {
                return document;
            }
        });
    };

    constructor = classes.Resources;
    prototype = constructor.prototype;

    prototype.createScript = function (value, external, properties, attributes) {
        var node = this.document.createElement('script');

        if (external) {
            node.async = false;

            api.each(attributes, function (name, value) {
                node.setAttribute(name, value);
            });

            api.extend(node, properties);

            node.type = 'text/javascript';
            node.src = value;
        } else {
            api.each(attributes, function (name, value) {
                node.setAttribute(name, value);
            });

            api.extend(node, properties);

            node.type = 'text/javascript';
            node.textContent = value;
            node.removeAttribute('src');
        }

        return node;
    };

    prototype.loadScript = function (value, external, properties, attributes, parent) {
        var self = this;

        return new Promise(function (resolve, reject) {
            var node = self.createScript(link, external, properties, attributes);
            var nodeOnError;
            var nodeOnLoad;

            if (!parent)
                parent = self.document.head;

            if (node.src && !node.async) {
                nodeOnError = script.onerror;
                nodeOnLoad = script.onload;

                node.onload = function () {
                    try {
                        if (api.isFunction(nodeOnLoad))
                            nodeOnLoad.apply(this, arguments);
                    } catch (e) {
                        console.error(e);
                    } finally {
                        resolve(node);
                    }
                };

                node.onerror = function () {
                    try {
                        if (api.isFunction(scriptOnError))
                            nodeOnError.apply(this, arguments);
                    } catch (e) {
                        console.error(e);
                    } finally {
                        reject();
                    }
                };

                parent.appendChild(script);
            } else {
                parent.appendChild(node);
                resolve(node);
            }
        });
    };

    prototype.createStyle = function (value, external, properties, attributes) {
        var node;

        if (external) {
            node = this.document.createElement('link');

            api.each(attributes, function (name, value) {
                node.setAttribute(name, value);
            });

            api.extend(node, properties);

            node.rel = 'stylesheet';
            node.href = value;
        } else {
            node = this.document.createElement('style');

            api.each(attributes, function (name, value) {
                node.setAttribute(name, value);
            });

            api.extend(node, properties);

            node.type = 'text/css';
            node.textContent = value;
        }

        return node;
    };

    prototype.getNodesFromText = function (value) {
        var container = document.createElement('div');

        container.innerHTML = value;

        return container.querySelectorAll('script, style, link');
    };

    prototype.getObjectsFromNodes = function (nodes) {
        var objects = [];

        api.each(nodes, function (index, node) {
            if (!api.isObject(node))
                return;

            var attributes = {};
            var object;

            if (node.tagName !== 'SCRIPT' && node.tagName !== 'LINK' && node.tagName !== 'STYLE')
                return;

            api.each(node.attributes, function (index, attribute) {
                attributes[attribute.name] = attribute.value;
            });

            if (node.tagName === 'SCRIPT') {
                object = {
                    'type': 'script',
                    'value': null,
                    'external': false,
                    'properties': {},
                    'attributes': attributes
                };

                if (node.src) {
                    object.value = node.src;
                    object.external = true;
                } else {
                    object.value = node.textContent;
                }
            } else {
                object = {
                    'type': 'style',
                    'value': null,
                    'external': false,
                    'properties': {},
                    'attributes': attributes
                };

                if (node.tagName === 'LINK') {
                    object.value = node.href;
                    object.external = true;
                } else {
                    object.value = node.textContent;
                }
            }

            objects.push(object);
        });

        return objects;
    };

    prototype.getObjectsFromText = function (value) {
        return this.getObjectsFromNodes(this.getNodesFromText(value));
    };

    prototype.loadStyle = function (value, external, properties, attributes, parent) {
        var self = this;

        return new Promise(function (resolve) {
            var node = self.createStyle(value, external, properties, attributes);

            if (!parent)
                parent = self.document.head;

            parent.appendChild(node);
            resolve(node);
        });
    };

    prototype.loadFromObjects = function (objects, parent, rejectOnError) {
        var self = this;
        var handler;
        var promise;
        var promiseResolve;
        var promiseReject;
        var scripts = [];
        var result = [];

        if (!parent)
            parent = self.document.head;

        handler = function (scripts) {
            var await = false;

            api.each(scripts, function (index, script) {
                var scriptsStack;
                var scriptOnError;
                var scriptOnLoad;

                if (script.src && !script.async) {
                    await = true;
                    scriptsStack = Array.prototype.slice.call(scripts, index + 1);
                    scriptOnError = script.onerror;
                    scriptOnLoad = script.onload;

                    script.onload = function () {
                        try {
                            if (api.isFunction(scriptOnLoad))
                                scriptOnLoad.apply(this, arguments);
                        } catch (e) {
                            console.error(e);
                        } finally {
                            handler(scriptsStack);
                        }
                    };

                    script.onerror = function () {
                        try {
                            if (api.isFunction(scriptOnError))
                                scriptOnError.apply(this, arguments);
                        } catch (e) {
                            console.error(e);
                        } finally {
                            if (rejectOnError) {
                                promiseReject();
                            } else {
                                handler(scriptsStack);
                            }
                        }
                    };

                    parent.appendChild(script);
                    return false;
                } else {
                    parent.appendChild(script);
                }
            });

            if (!await)
                promiseResolve(result);
        };

        promise = new Promise(function (resolve, reject) {
            promiseResolve = resolve;
            promiseReject = reject;
        });

        api.each(objects, function (index, object) {
            if (!api.isObject(object) || !api.isDeclared(object.type) || !api.isDeclared(object.value))
                return;

            var node = null;

            if (object.type === 'script') {
                node = self.createScript(object.value, object.external, object.properties, object.attributes);
                scripts.push(node);
            } else if (object.type === 'style') {
                node = self.createStyle(object.value, object.external, object.properties, object.attributes);
                parent.appendChild(node);
            }

            if (node !== null)
                result.push(node);
        });

        handler(scripts);

        return promise;
    };

    prototype.loadFromNodes = function (nodes, parent, rejectOnError) {
        return this.loadFromObjects(this.getObjectsFromNodes(nodes), parent, rejectOnError);
    };

    prototype.loadFromNode = function (node, parent, rejectOnError) {
        return this.loadFromNodes([node], parent, rejectOnError);
    };

    prototype.loadFromText = function (value, parent, rejectOnError) {
        return this.loadFromNodes(this.getNodesFromText(value), parent, rejectOnError);
    }
})();
