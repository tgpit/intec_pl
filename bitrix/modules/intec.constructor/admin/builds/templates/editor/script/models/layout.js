(function () {
    var prototype;
    var model;

    models.Layout = function (data) {
        var self = this;

        self.uid = uid++;
        self.code = null;
        self.name = null;
        self.picture = null;
        self.zones = [];

        api.object.configure(self, data, function (event) {
            var path = event.pathString;

            if (path === 'zones') {
                event.value = api.array.rebuild(event.value, function (index, data) {
                    return new models.layout.Zone(data);
                });
            }
        });
    };

    model = models.Layout;
    prototype = model.prototype;

    /**
     * Определяет, является ли компановка виртуальной
     * @returns {boolean}
     */
    prototype.isVirtual = function () {
        return this.code === null;
    };

    /**
     * Возвращает зону по коду или объекту.
     * @param code
     * @returns {Object}
     */
    prototype.getZone = function (code) {
        return api.array.find(code, this.zones, function (index, item, code) {
            return item.code === code;
        });
    }
})();