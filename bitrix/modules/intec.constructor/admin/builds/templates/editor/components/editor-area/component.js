(function () {
    return {
        'computed': {
            'id': function () {
                return this.model.id;
            },
            'name': function () {
                return this.model.name;
            },
            'container': function () {
                return this.model.container;
            },
            'hasContainer': function () {
                return this.model.hasContainer();
            }
        },
        'props': {
            'model': {
                'type': models.elements.Area,
                'required': true
            }
        }
    };
})();
