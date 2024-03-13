(function () {
    return {
        'functional': true,
        'computed': {
            'compiledTemplate': function () {
                if (this.template)
                    return Vue.compile(this.template);

                return null;
            }
        },
        'props': {
            'context': {
                'required': false,
                'default': function () {
                    return null;
                }
            },
            'template': {
                'required': true,
                'default': function () {
                    return null;
                }
            }
        },
        'render': function (createElement) {
            var context = this.context;

            if (!context)
                context = this;

            if (this.compiledTemplate)
                return this.compiledTemplate.render.call(context, createElement);

            return null;
        }
    }
})();
