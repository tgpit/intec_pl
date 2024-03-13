(function () {
    return {
        'computed': {
            'isActive': function () {
                return this.active;
            },
            'isInteractive': function () {
                return this.interactive;
            },
            'icon': function () {
                return this.tab.icon;
            }
        },
        'props': {
            'active': {
                'type': Boolean,
                'required': false,
                'default': false
            },
            'interactive': {
                'type': Boolean,
                'required': false,
                'default': false
            },
            'link': {
                'type': String,
                'required': false
            },
            'name': {
                'type': String,
                'required': false
            }
        }
    }
})();
