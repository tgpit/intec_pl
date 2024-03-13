(function () {
    return {
        'methods': {
            'requestBlockContent': function (id) {
                return this.request('block.getContent', {
                    'id': id
                }, {
                    'environment': true,
                    'responseType': 'text'
                });
            },
            'requestBlockCloning': function (id) {
                return this.request('block.clone', {
                    'id': id
                }).then(function (response) {
                    return new models.elements.Block(response.data);
                });
            },
            'requestBlockConverting': function (id, code, name) {
                return this.request('block.convert', {
                    'id': id,
                    'code': code,
                    'name': name
                }).then(function (response) {
                    return response.data;
                });
            },
            'requestBlockCreating': function (code) {
                return this.request('block.create', {
                    'code': code
                }).then(function (response) {
                    return new models.elements.Block(response.data);
                });
            }
        }
    }
})();
