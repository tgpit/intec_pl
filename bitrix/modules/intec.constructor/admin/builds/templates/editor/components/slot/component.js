(function () {
    return {
        'functional': true,
        'props': {
            'slot': {
                'required': true
            }
        },
        'render': function (createElement, context) {
            return context.props.slot;
        }
    }
})();
