(function (api) {
    /**
     * @var Object meta
     */

    if (meta.type === 'properties') {
        return function () {
            return {
                'text': null,
                'font': null
            }
        };
    } else if (meta.type === 'view') {
        return {
            'computed': {
                'font': function () {
                    return this.properties.font;
                },
                'isTextFilled': function () {
                    return this.text !== null && this.text.length > 0;
                },
                'style': function () {
                    var result = {};
                    var font;

                    if (this.font) {
                        font = this.$root.useFont(this.font);

                        if (font !== null)
                            result['font-family'] = '"' + font.family + '", sans-serif';
                    }

                    return result;
                },
                'text': function () {
                    return this.properties.text;
                }
            }
        };
    } else if (meta.type === 'settings') {
        return {
            'mounted': function () {
                var self = this;
                var editor;

                if (this.$refs.text) {
                    editor = CKEDITOR.replace(this.$refs.text);
                    editor.setData(self.properties.text);
                    editor.on('change', function (event) {
                        self.properties.text = event.editor.getData();
                    });
                }
            },
            'computed': {
                'fonts': function () {
                    var result = [];

                    result.push({
                        'text': meta.messages['settings.groups.text.fields.font.values.default'],
                        'value': null
                    });

                    api.each(this.$root.fonts, function (index, font) {
                        result.push({
                            'text': font.name,
                            'value': font.family
                        });
                    });

                    return result;
                }
            }
        };
    }
})(intec);
