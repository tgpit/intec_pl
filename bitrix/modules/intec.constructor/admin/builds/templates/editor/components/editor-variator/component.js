(function () {
    return {
        'computed': {
            'id': function () {
                return this.model.id;
            },
            'variant': {
                'get': function () {
                    return this.model.getVariant();
                },
                'set' : function (value) {
                    return this.model.setVariant(value)
                }
            },
            'hasVariant': function () {
                return this.model.hasVariant();
            },
            'variants': function () {
                return this.model.variants;
            }
        },
        'props': {
            'model': {
                'type': models.elements.Variator,
                'required': true
            }
        }
    }
})();
