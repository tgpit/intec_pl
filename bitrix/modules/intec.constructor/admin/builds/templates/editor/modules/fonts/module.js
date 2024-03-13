(function () {
    var registered = {};

    return {
        'data': {
            'fonts': []
        },
        'methods': {
            'getFont': function (family) {
                return api.array.find(family, this.fonts, function (index, font, family) {
                    return font.family === family;
                });
            },
            'isFontRegistered': function (font) {
                font = this.getFont(font);

                if (font === null)
                    return false;

                return api.isDeclared(registered[font.family]);
            },
            'refreshFonts': function () {
                var self = this;

                return this.requestFonts().then(function (response) {
                    self.fonts = response;

                    return response;
                });
            },
            'registerFont': function (font) {
                font = this.getFont(font);

                if (font === null || this.isFontRegistered(font))
                    return;

                registered[font.family] = this.$resources.loadStyle(font.style);
            },
            'requestFonts': function () {
                return this.request('font.getList').then(function (response) {
                    return api.array.rebuild(response.data, function (index, data) {
                        return new models.Font(data);
                    });
                });
            },
            'useFont': function (font) {
                font = this.getFont(font);

                if (font !== null) {
                    if (!this.isFontRegistered(font))
                        this.registerFont(font);

                    return font;
                }

                return null;
            }
        }
    }
})();
