(function () {
    return {
        'computed': {
            'container': function () {
                return this.$root.getContainer(this.model.code);
            }
        },
        'props': {
            'model': {
                'type': models.layout.Zone,
                'required': false
            }
        }
    }
})();
