(function () {
    'use strict';

    if (!!window.MainNewsNavigation)
        return false;

    var _ = template.getLibrary('_');
    
    window.MainNewsNavigation = function () {
        this.active = false;
        this.component = {
            'path': null,
            'template': null,
            'parameters': {}
        };
        this.navigation = {
            'id': null,
            'current': 1,
            'count': 1
        };
        this.container = {
            'root': null,
            'items': null,
            'navigationContainer': null,
            'navigationButton': null
        };
        this.settings = {
            'buttonDelete': false
        };
    };

    MainNewsNavigation.prototype = {
        'init': function (parameters) {
            this.setParameters(parameters);

            if (!this.checkParameters())
                return false;

            if (!this.setContainers())
                return false;

            this.configure();
            this.bindAction();
        },
        'setParameters': function (parameters) {
            if (_.isNil(parameters))
                return false;

            if (_.isNil(parameters.component))
                return false;

            if (_.isNil(parameters.navigation))
                return false;

            if (_.isNil(parameters.container))
                return false;

            if (!_.isNil(parameters.component.path))
                this.component.path = parameters.component.path;

            if (!_.isNil(parameters.component.template))
                this.component.template = parameters.component.template;

            if (!_.isNil(parameters.component.parameters))
                this.component.parameters = parameters.component.parameters;

            if (!_.isNil(parameters.navigation.id))
                this.navigation.id = parameters.navigation.id;

            if (!_.isNil(parameters.navigation.current))
                this.navigation.current = parameters.navigation.current;

            if (!_.isNil(parameters.navigation.count))
                this.navigation.count = parameters.navigation.count;

            if (!_.isNil(parameters.container.root))
                this.container.root = parameters.container.root;

            if (!_.isNil(parameters.container.items))
                this.container.items = parameters.container.items;

            if (!_.isNil(parameters.container.navigationContainer))
                this.container.navigationContainer = parameters.container.navigationContainer;

            if (!_.isNil(parameters.container.navigationButton))
                this.container.navigationButton = parameters.container.navigationButton;

            if (!_.isEmpty(parameters.settings) && _.isObject(parameters.settings))
                this.setSettings(parameters.settings);
        },
        'checkParameters': function () {
            for (var key in this.component) {
                if (_.isNil(this.component[key]))
                    return false;
            }

            for (var key in this.container) {
                if (_.isNil(this.container[key]))
                    return false;
            }

            if (_.isNil(this.navigation.id))
                return;

            return this.navigation.current < this.navigation.count;
        },
        'setContainers': function () {
            this.container.root = document.querySelector(this.container.root);

            if (this.container.root === null)
                return false;

            this.container.items = this.container.root.querySelector(this.container.items);

            if (this.container.items === null)
                return false;

            this.container.navigationContainer = this.container.root.querySelector(this.container.navigationContainer);

            if (this.container.navigationContainer !== null)
                this.container.navigationButton = this.container.navigationContainer.querySelector(this.container.navigationButton);

            return this.container.navigationButton !== null;
        },
        'getSettings': function (name) {
            var result = undefined;

            if (!_.isNil(name) && _.isString(name)) {
                if (this.settings.hasOwnProperty(name))
                    result = this.settings[name];
            } else {
                result = this.settings
            }

            return _.cloneDeep(result);
        },
        'setSettings': function (settings) {
            if (_.isEmpty(settings) || !_.isObject(settings))
                return;

            var self = this;

            _.each(settings, function (value, key) {
                if (self.settings.hasOwnProperty(key))
                    self.settings[key] = value;
            });
        },
        'isActive': function () {
            this.active = this.navigation.current < this.navigation.count;

            return this.active;
        },
        'buttonSetState': function (state) {
            this.container.navigationButton.setAttribute('data-state', state);
        },
        'buttonIsActive': function () {
            return this.container.navigationButton.getAttribute('data-state') === 'active';
        },
        'itemsSetState': function (state) {
            this.container.items.setAttribute('data-state', state);
        },
        'configure': function () {
            if (this.isActive())
                this.buttonSetState('active');
            else {
                if (this.getSettings('buttonDelete') === true)
                    this.container.navigationContainer.remove();
                else
                    this.buttonSetState('disabled');
            }

            this.itemsSetState('none');
        },
        'bindAction': function () {
            this.container.navigationButton.addEventListener('click', BX.delegate(function () {
                this.actionClick();
            }, this));
        },
        'setUrl': function () {
            var number;

            number = this.navigation.current + 1;

            return this.component.path + '?' + this.navigation.id + '=page-' + number;
        },
        'actionClick': function () {
            if (!this.active || !this.buttonIsActive())
                return false;

            this.buttonSetState('processing');
            this.itemsSetState('processing');

            BX.ajax({
                'url': this.setUrl(),
                'method': 'POST',
                'dataType': 'json',
                'timeout': 60,
                'cache': true,
                'data': {
                    'action': 'navigation',
                    'template': this.component.template,
                    'parameters': this.component.parameters
                },
                'onsuccess': BX.delegate(function (data) {
                    this.actionSuccess(data);
                    this.configure();
                }, this),
                'onfailure': BX.delegate(function () {
                    this.configure();
                }, this)
            });
        },
        'actionSuccess': function (data) {
            if (data.items !== null) {
                this.container.items.insertAdjacentHTML('beforeend', data.items);
                this.navigation.current++;
            }
        }
    }
})();