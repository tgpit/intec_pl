<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\Core;
use intec\core\base\Collection;
use intec\core\helpers\JavaScript;

/**
 * @var Collection $properties
 */

Core::$app->web->js->addString('
    template.load(function () {
        var app = this;
        var _ = app.getLibrary(\'_\');

        app.metrika.on(\'reachGoal\', function (name) {
            app.ecommerce.sendData({\'event\': name});
        });
    
        app.api.basket.on(\'add\', function (data, item) {
            app.metrika.reachGoal(\'basket.add\');
            
            if (!_.isNil(item))
                app.ecommerce.sendData({
                    \'event\': \'addToCart\',
                    \'ecommerce\': {
                        \'add\': {
                            \'products\': [{
                                \'name\': item.name,
                                \'id\': item.id,
                                \'price\': item.price,
                                \'category\': !_.isNil(item.section) ? item.section.name : null,
                                \'quantity\': item.quantity
                            }]
                        }
                    }
                });
        });
    
        app.api.basket.on(\'remove\', function (data, item) {
            app.metrika.reachGoal(\'basket.remove\');
            
            if (!_.isNil(item))
                app.ecommerce.sendData({
                    \'event\': \'removeFromCart\',
                    \'ecommerce\': {
                        \'remove\': {
                            \'products\': [{
                                \'name\': item.name,
                                \'id\': item.id,
                                \'price\': item.price,
                                \'category\': !_.isNil(item.section) ? item.section.name : null,
                                \'quantity\': item.quantity
                            }]
                        }
                    }
                });
        });
    
        app.api.basket.on(\'clear\', function (items) {
            var data;
        
            app.metrika.reachGoal(\'basket.clear\');
            
            if (!_.isNil(items)) {
                data = {
                    \'event\': \'removeFromCart\',
                    \'ecommerce\': {
                        \'remove\': {
                            \'products\': []
                        }
                    }
                };
            
                _.each(items, function (item) {
                    data.ecommerce.remove.products.push({
                        \'name\': item.name,
                        \'id\': item.id,
                        \'price\': item.price,
                        \'category\': !_.isNil(item.section) ? item.section.name : null,
                        \'quantity\': item.quantity
                    });
                });
                
                app.ecommerce.sendData(data);
            }
        });
    }, {
        \'name\': \'[Metrika] Events\'
    });
');

if ($properties->get('yandex-metrika-use')) {
    $id = $properties->get('yandex-metrika-id');

    if (!empty($id)) {
        $settings = [
            'id' => $id,
            'accurateTrackBounce' => true,
            'clickmap' => $properties->get('yandex-metrika-click-map'),
            'trackHash' => $properties->get('yandex-metrika-track-hash'),
            'trackLinks' => $properties->get('yandex-metrika-track-links'),
            'webvisor' => $properties->get('yandex-metrika-track-webvisor')
        ];

        if ($properties->get('yandex-metrika-ecommerce'))
            $settings['ecommerce'] = 'dataLayer';

        Core::$app->web->js->addFile('https://mc.yandex.ru/metrika/tag.js');
        Core::$app->web->js->addString('
            (function () {
                window.yandex = {};
                window.yandex.metrika = new Ya.Metrika2('.JavaScript::toObject($settings).');
                
                template.load(function () {
                    var app = this;

                    app.metrika.on(\'reachGoal\', function (name) {
                        window.yandex.metrika.reachGoal(name);
                    });
                }, {
                    \'name\': \'[Metrika] Yandex Metrika\'
                });
            })()
        ');

        unset($settings);
    }

    unset($id);
}

if ($properties->get('google-tag-use')) {
    $id = $properties->get('google-tag-id');

    if (!empty($id)) {
        Core::$app->web->js->addString('
            (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':
                new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],
                j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src=
                \'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,\'script\',\'dataLayer\','.JavaScript::toObject($id).');
        ');
    }

    unset($id);
}
