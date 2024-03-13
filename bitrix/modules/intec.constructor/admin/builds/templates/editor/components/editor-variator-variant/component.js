(function () {
    return {
        'computed': {
            'id': function () {
                return this.model.id;
            },
            'code': {
                'get': function () {
                    return this.model.code;
                },
                'set': function (value) {
                    this.model.code = value;
                }
            },
            'container': {
                'get': function () {
                    return this.model.container;
                },
                'set': function (value) {
                    this.model.container = value;
                }
            },
            'hasContainer': function () {
                return this.model.hasContainer();
            }
        },
        'props': {
            'model': {
                'type': models.elements.Variant,
                'required': true
            }
        }
    }
})();
