(function () {
    'use strict';

    if (!!window.MainCollectionsNavigation)
        return false;

    var _ = template.getLibrary('_');

    window.MainCollectionsNavigation = function () {
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
            'button': null
        };
    };

    MainCollectionsNavigation.prototype = {
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
            if (_.isEmpty(parameters))
                return false;

            if (_.isEmpty(parameters.component))
                return false;

            if (_.isEmpty(parameters.navigation))
                return false;

            if (_.isEmpty(parameters.container))
                return false;

            if (!_.isEmpty(parameters.component.path))
                this.component.path = parameters.component.path;

            if (!_.isEmpty(parameters.component.template))
                this.component.template = parameters.component.template;

            if (!_.isEmpty(parameters.component.parameters))
                this.component.parameters = parameters.component.parameters;

            if (!_.isEmpty(parameters.navigation.id))
                this.navigation.id = parameters.navigation.id;

            if (_.isInteger(parameters.navigation.current) && parameters.navigation.current > 0)
                this.navigation.current = parameters.navigation.current;
            else
                this.navigation.current = 1;

            if (_.isInteger(parameters.navigation.count) && parameters.navigation.count > 1)
                this.navigation.count = parameters.navigation.count;
            else
                this.navigation.count = 1;

            if (!_.isEmpty(parameters.container.root))
                this.container.root = parameters.container.root;

            if (!_.isEmpty(parameters.container.items))
                this.container.items = parameters.container.items;

            if (!_.isEmpty(parameters.container.button))
                this.container.button = parameters.container.button;
        },
        'checkParameters': function () {
            for (var key in this.component) {
                if (_.isEmpty(this.component[key]))
                    return false;
            }

            for (var key in this.container) {
                if (_.isEmpty(this.container[key]))
                    return false;
            }

            if (_.isEmpty(this.navigation.id))
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

            this.container.button = this.container.root.querySelector(this.container.button);

            return this.container.button !== null;
        },
        'isActive': function () {
            this.active = this.navigation.current < this.navigation.count;

            return this.active;
        },
        'buttonSetState': function (state) {
            this.container.button.setAttribute('data-state', state);
        },
        'buttonIsActive': function () {
            return this.container.button.getAttribute('data-state') === 'active';
        },
        'itemsSetState': function (state) {
            this.container.items.setAttribute('data-state', state);
        },
        'configure': function () {
            if (this.isActive())
                this.buttonSetState('active');
            else
                this.buttonSetState('disabled');

            this.itemsSetState('none');
        },
        'bindAction': function () {
            this.container.button.addEventListener('click', BX.delegate(function () {
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