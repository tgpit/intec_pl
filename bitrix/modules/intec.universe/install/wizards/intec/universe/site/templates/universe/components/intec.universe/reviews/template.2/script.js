(function () {
    'use strict';

    if (!!window.IntecReviews2Component)
        return false;

    var _ = template.getLibrary('_');

    window.IntecReviews2Component = function () {
        this.root = null;
        this.component = {
            'path': null,
            'template': null,
            'parameters': null
        };
        this.navigation = {
            'id': null,
            'exists': false,
            'active': false,
            'container': null,
            'root': null,
            'current': 1,
            'count': 1
        };
        this.form = {
            'exists': false,
            'root': null,
            'body': null
        };
        this.items = {
            'exists': false,
            'root': null
        };
        this.settings = {
            'navigationButtonDelete': false
        };
        this.cache = {
            'navigation': {
                'container': null,
                'root': null
            },
            'form': {
                'root': null,
                'body': null
            },
            'items': {
                'root': null
            }
        }
    };

    IntecReviews2Component.prototype = {
        'initialize': function (parameters) {
            this.root = document.querySelector(parameters.root);

            if (this.componentInitialize(parameters.component)) {
                if (this.root !== null) {
                    this.navigationInitialize(parameters.navigation);
                    this.formInitialize(parameters.form);
                    this.itemsInitialize(parameters.items);
                    this.setSettings(parameters.settings);
                }
            }
        },
        'getSettings': function (name) {
            var result = undefined;

            if (!_.isString(name) || _.isEmpty(name))
                result = this.settings;
            else if (this.settings.hasOwnProperty(name))
                result = this.settings[name];

            return _.cloneDeep(result);
        },
        'setSettings': function (settings) {
            if (!_.isObject(settings) || _.isEmpty(settings))
                return;

            var self = this;

            _.each(settings, function (value, key) {
                if (self.settings.hasOwnProperty(key))
                    self.settings[key] = value;
            });
        },
        'componentInitialize': function (component) {
            this.componentSet(component);

            return this.componentValidate();
        },
        'componentSet': function (component) {
            if (_.isNil(component))
                return;

            if (!_.isNil(component.path))
                this.component.path = component.path;

            if (!_.isNil(component.template))
                this.component.template = component.template;

            if (!_.isNil(component.parameters))
                this.component.parameters = component.parameters;
        },
        'componentValidate': function () {
            return !(
                this.component.path === null ||
                this.component.template === null ||
                this.component.parameters === null
            );
        },
        'componentCompleteData': function (data) {
            data.parameters = this.component.parameters;
            data.template = this.component.template;

            return data;
        },
        'navigationInitialize': function (navigation) {
            if (_.isNil(navigation.id))
                return;

            this.navigation.id = navigation.id;

            if (this.cache.navigation.container === null)
                this.navigation.container = this.root.querySelector(navigation.container);
            else
                this.navigation.container = this.root.querySelector(this.cache.navigation.container);

            if (this.navigation.container !== null) {
                if (this.cache.navigation.root === null)
                    this.navigation.root = this.root.querySelector(navigation.root);
                else
                    this.navigation.root = this.root.querySelector(this.cache.navigation.root);
            }

            if (this.navigation.root !== null) {
                if (this.cache.navigation.root === null)
                    this.cache.navigation.root = navigation.root;

                this.navigation.current = parseInt(navigation.current);
                this.navigation.count = parseInt(navigation.count);

                if (isNaN(this.navigation.current) || this.navigation.current < 1)
                    this.navigation.current = 1;

                if (isNaN(this.navigation.count) || this.navigation.count < 1)
                    this.navigation.count = 1;

                this.navigation.exists = true;

                this.navigationCheck();

                if (this.navigation.exists)
                    this.navigationBindAction();
            }
        },
        'navigationCheck': function () {
            if (this.navigation.current > this.navigation.count)
                this.navigation.current = this.navigation.count;

            if (this.navigation.current === this.navigation.count)
                this.navigation.active = false;

            if (this.navigation.current < this.navigation.count)
                this.navigation.active = true;

            this.navigationSetState();
        },
        'navigationSetState': function () {
            if (this.navigation.active) {
                this.navigation.root.setAttribute('data-state', 'active');
            } else {
                if (this.getSettings('navigationButtonDelete')) {
                    this.navigation.exists = false;
                    this.navigation.container.remove();
                } else {
                    this.navigation.root.setAttribute('data-state', 'disabled');
                }
            }
        },
        'navigationBindAction': function () {
            let self = this;
            let data = {};

            data = this.componentCompleteData(data);

            this.navigation.root.addEventListener('click', function () {
                if (!self.navigation.active)
                    return;

                self.setState(self.items.root, 'processing');
                self.setState(self.navigation.root, 'processing');

                BX.ajax({
                    'url': self.component.path + self.navigationSetQuery(),
                    'method': 'POST',
                    'dataType': 'json',
                    'timeout': 60,
                    'cache': true,
                    'data': data,
                    'onsuccess': function (data) {
                        self.itemsUpload(data);
                        self.setState(self.items.root, 'none');
                        self.navigationCheck();
                    },
                    'onfailure': function () {
                        self.navigation.current--;
                        self.setState(self.items.root, 'none');
                        self.setState(self.form.root, 'active');
                    }
                });
            });
        },
        'navigationSetQuery': function () {
            if (this.navigation.active)
                return '?' + this.navigation.id + '=page-' + ++this.navigation.current;
            else
                return '';
        },
        'navigationReset': function () {
            if (this.navigation.exists) {
                this.navigation.current = 1;
                this.navigation.active = true;
                this.navigationCheck();
            }
        },
        'formInitialize': function (form) {
            if (this.cache.form.root === null)
                this.form.root = this.root.querySelector(form.root);
            else
                this.form.root = this.root.querySelector(this.cache.form.root);

            if (this.form.root !== null) {
                if (this.cache.form.root === null)
                    this.cache.form.root = form.root;

                if (this.cache.form.body === null)
                    this.form.body = this.form.root.querySelector(form.body);
                else
                    this.form.body = this.form.root.querySelector(this.cache.form.body);

                if (this.form.body !== null) {
                    if (this.cache.form.body === null)
                        this.cache.form.body = form.body;

                    this.form.exists = true;

                    this.formBindSubmit();
                }
            }
        },
        'formBindSubmit': function () {
            if (!this.form.exists)
                return;

            let self = this;

            this.form.body.addEventListener('submit', function (event) {
                event.preventDefault();

                self.formSubmit();
            });
        },
        'formCollectData': function () {
            let data = {};
            let input = this.form.body.querySelectorAll('input, textarea, select');

            if (input.length)
                input.forEach(function (item) {
                    data[item.name] = item.value;
                });

            return data;
        },
        'formSubmit': function () {
            let self = this;
            let data = this.formCollectData();

            if (_.isNil(data))
                return;

            data = this.componentCompleteData(data);

            this.setState(this.form.root, 'processing');

            BX.ajax({
                'url': this.component.path,
                'method': 'POST',
                'dataType': 'json',
                'timeout': 60,
                'cache': true,
                'data': data,
                'onsuccess': function (data) {
                    if (data.form)
                        self.formRefresh(data.form);

                    if (data.status === 'added') {
                        self.itemsRefresh(data.items);
                        self.navigationReset();
                    }

                    self.setState(self.form.root, 'none');
                    BX.closeWait(self.form.body);
                },
                'onfailure': function () {
                    self.setState(self.form.root, 'none');
                }
            });
        },
        'formRefresh': function (form) {
            if (!this.form.exists)
                return;

            this.form.root.innerHTML = form;
            this.formInitialize();
            this.generateEvent('form.updated');
        },
        'itemsInitialize': function (items) {
            if (this.cache.items.root === null)
                this.items.root = this.root.querySelector(items.root);
            else
                this.items.root = this.root.querySelector(this.cache.items.root);

            if (this.items.root !== null) {
                if (this.cache.items.root === null)
                    this.cache.items.root = items.root;

                this.items.exists = true;
            }
        },
        'itemsRefresh': function (items) {
            if (!this.items.exists)
                return;

            this.items.root.innerHTML = items;
            this.generateEvent('items.updated');
        },
        'itemsUpload': function (data) {
            if (!this.items.exists)
                return;

            if (data.items !== null && data.items !== undefined) {
                this.items.root.insertAdjacentHTML('beforeend', data.items);
                this.generateEvent('items.updated');
            }
        },
        'generateEvent': function (name) {
            let event = new Event('intec.reviews.' + name);

            document.dispatchEvent(event);
        },
        'setState': function (element, processing) {
            element.setAttribute('data-state', processing);
        }
    };
})();