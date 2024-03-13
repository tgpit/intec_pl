(function () {
    var requestQueries = [];

    return {
        'computed': {
            'environment': function () {
                return {
                    'site': this.site ? this.site.id : null,
                    'directory': this.site ? this.site.directory : null,
                    'template': this.code
                };
            }
        },
        'data': {
            'settings': {
                'requestQueriesCount': 0,
                'requestQueriesInterval': 0
            }
        },
        'methods': {
            'request': function (action, data, options) {
                var self = this;
                var query;
                var promise;
                var promiseResolve;
                var promiseReject;
                var send;

                if (!api.isDeclared(action))
                    return;

                promise = new Promise(function (resolve, reject) {
                    promiseResolve = resolve;
                    promiseReject = reject;
                }).then(function (data) {
                    requestQueries.splice(requestQueries.indexOf(promise), 1);

                    return data;
                }, function (reason) {
                    requestQueries.splice(requestQueries.indexOf(promise), 1);

                    return reason;
                });

                options = api.extend({
                    'responseType': 'json',
                    'environment': false
                }, options, {
                    'method': 'post',
                    'url': location.pathname,
                    'data': null,
                    'headers': {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    'transformResponse': [function (data) {
                        if (options.responseType === 'json') {
                            data = api.extend({
                                'status': 'error'
                            }, data);

                            if (data.status === 'error') {
                                if (!api.isDeclared(data.code))
                                    data.code = 'badRequest';

                                if (!api.isDeclared(data.message))
                                    data.message = 'Bad request';
                            }

                            if (!api.isDeclared(data.data))
                                data.data = null;
                        }

                        return data;
                    }]
                });

                if (location.search.length > 0) {
                    query = Qs.parse(location.search.substr(1));
                } else {
                    query = {};
                }

                if (!FormData || !(data instanceof FormData)) {
                    data = api.extend({}, data, {
                        'action': action
                    });

                    if (options.environment)
                        data['environment'] = this.environment;

                    options.data = Qs.stringify(data);
                } else {
                    data.append('action', action);

                    api.each(this.environment, function (key, value) {
                        data.append('environment[' + key + ']', value);
                    });

                    options.data = data;
                }

                query = Qs.stringify(query);

                if (query.length > 0)
                    options.url += '?' + query;

                send = function () {
                    if (self.settings.requestQueriesCount === 0 || requestQueries.length < self.settings.requestQueriesCount) {
                        requestQueries.push(promise);

                        axios(options).then(function (response) {
                            var data = response.data;

                            if (options.responseType === 'json') {
                                if (data.status === 'success') {
                                    promiseResolve(data);
                                } else {
                                    if (api.isDeclared(data.code)) {
                                        console.error('Error occurred during request witch code "' + data.code + '"');
                                    } else {
                                        console.error('Error occurred during request');
                                    }

                                    if (api.isDeclared(data.message))
                                        console.error(data.message);

                                    promiseReject(data);
                                }
                            } else {
                                promiseResolve(data);
                            }
                        }).catch(function (error) {
                            console.error('Error occurred during request');
                            console.log(error);

                            promiseReject({
                                'status': 'error',
                                'code': 'networkError',
                                'message': 'Network error',
                                'data': error
                            });
                        });
                    } else {
                        Promise.race(requestQueries).then(function (data) {
                            setTimeout(function () {
                                send();
                            }, self.settings.requestQueriesInterval);

                            return data;
                        }, function (reason) {
                            setTimeout(function () {
                                send();
                            }, self.settings.requestQueriesInterval);

                            return reason;
                        });
                    }
                };

                send();

                return promise;
            }
        }
    }
})();
