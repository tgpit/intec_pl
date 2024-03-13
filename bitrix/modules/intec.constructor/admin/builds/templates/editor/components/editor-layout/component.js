(function () {
    return {
        'props': {
            'model': {
                'type': models.Layout,
                'required': true
            }
        },
        'methods': {
            'getZone': function (zone) {
                return this.model.getZone(zone);
            }
        }
    }
})();
