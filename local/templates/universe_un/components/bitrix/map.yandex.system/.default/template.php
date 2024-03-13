<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

if (!CModule::IncludeModule('intec.core'))
    return;

$this->setFrameMode(true);

$sId = Type::toString($arParams['MAP_ID']);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];

$arData = [
    'options' => [
        'zoom' => Type::toFloat($arParams['INIT_MAP_SCALE']),
        'center' => [
            Type::toFloat($arParams['INIT_MAP_LAT']),
            Type::toFloat($arParams['INIT_MAP_LON'])
        ],
        'type' => 'yandex#'.$arResult['ALL_MAP_TYPES'][$arParams['INIT_MAP_TYPE']]
    ],
    'behaviors' => [],
    'controls' => []
];

$arBehaviors = [
    'ALL' => $arResult['ALL_MAP_OPTIONS'],
    'SET' => $arParams['OPTIONS']
];

foreach ($arBehaviors['ALL'] as $sKey => $sBehavior) {
    $bSet = ArrayHelper::isIn($sKey, $arBehaviors['SET']);
    $arData['behaviors'][$sBehavior] = $bSet;
}

unset($arBehaviors);

$arControls = [
    'ALL' => $arResult['ALL_MAP_CONTROLS'],
    'SET' => $arParams['CONTROLS']
];

foreach ($arControls['ALL'] as $sKey => $sControl) {
    $bSet = ArrayHelper::isIn($sKey, $arControls['SET']);
    $arData['controls'][$sControl] = $bSet;
}

unset($arControls);

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-map-yandex-system',
        'c-map-yandex-system-default'
    ],
    'style' => [
        'width' => !empty($arParams['MAP_WIDTH']) ? $arParams['MAP_WIDTH'] : null,
        'height' => !empty($arParams['MAP_HEIGHT']) ? $arParams['MAP_HEIGHT'] : null
    ]
]) ?>
    <div class="map-yandex-system-control" data-role="control">
        <?= Loc::getMessage('C_MAP_YANDEX_SYSTEM_DEFAULT_LOADING') ?>
    </div>
    <?php if ($arVisual['OVERLAY']) { ?>
        <div class="map-yandex-system-overlay" data-role="overlay"></div>
    <?php } ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var _ = this.getLibrary('_');
            var control = $('[data-role="control"]', data.nodes);
            var overlay = $('[data-role="overlay"]', data.nodes);
            var params = <?= JavaScript::toObject($arData) ?>;
            var initialize;
            var loader;

            initialize = function () {
                var options = params.options;
                var behaviors = params.behaviors;
                var controls = params.controls;
                var root = data.nodes;
                var map;
                var pan = {
                    'interval': null,
                    'parameters': []
                };

                if (!window.ymaps)
                    return;

                control.html(null);
                map = new window.ymaps.Map(control.get(0), options);

                _.each(behaviors, function (state, behavior) {
                    if (state) {
                        map.behaviors.enable(behavior);
                    } else if (map.behaviors.isEnabled(behavior)) {
                        map.behaviors.disable(behavior);
                    }
                });

                _.each(controls, function (state, control) {
                    if (state) map.controls.add(control);
                });

                <?php if (!empty($arParams['ONMAPREADY'])) { ?>
                    if (window.<?= $arParams['ONMAPREADY']?>) {
                        <?php if ($arParams['ONMAPREADY_PROPERTY']) { ?>
                            <?= $arParams['ONMAPREADY_PROPERTY']?> = map;
                            window.<?= $arParams['ONMAPREADY']?>();
                        <?php } else { ?>
                            window.<?= $arParams['ONMAPREADY']?>(map);
                        <?php } ?>
                    }
                <?php } ?>

                if (!_.isObject(window.maps))
                    window.maps = {};

                window.maps[<?= JavaScript::toObject($sId) ?>] = map;

                <?php if ($arVisual['OVERLAY']) { ?>
                    overlay.show();

                    root.on('mousedown', function (event) {
                        overlay.hide();
                    }).on('mouseleave', function (event) {
                        overlay.show();
                    });
                <?php } ?>

                map.panTo = (function (method) {
                    return function () {
                        var size = map.container.getSize();

                        pan.parameters = arguments;

                        if (size[0] === 0 || size[1] === 0) {
                            if (!pan.interval)
                                pan.interval = setInterval(function () {
                                    size = map.container.getSize();

                                    if (size[0] !== 0 && size[1] !== 0) {
                                        clearInterval(pan.interval);

                                        pan.interval = null;
                                        method.apply(map, pan.parameters);
                                        pan.parameters = null;
                                    }
                                }, 100);
                        } else {
                            if (pan.interval) {
                                clearInterval(pan.interval);

                                pan.interval = null;
                            }

                            method.apply(map, pan.parameters);
                            pan.parameters = null;
                        }
                    };
                })(map.panTo);
            };

            loader = function () {
                if (window.ymaps) {
                    ymaps.ready(initialize);
                } else {
                    setTimeout(loader, 100);
                }
            };

            <?php if ($arParams['DEV_MODE'] === 'Y') { ?>
                if (window.ymaps)
                    window.bYandexMapScriptsLoaded = true;

                if (window.bYandexMapScriptsLoading !== true && window.bYandexMapScriptsLoaded !== true) {
                    window.bYandexMapScriptsLoading = true;

                    BX.loadScript('<?= $arResult['MAPS_SCRIPT_URL'] ?>', function () {
                        window.bYandexMapScriptsLoaded = true;
                        loader();
                    });
                } else {
                    loader();
                }
            <?php } else { ?>
                loader();
            <?php } ?>
        }, {
            'name': '[Component] bitrix:map.yandex.system (.default)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?= Html::endTag('div') ?>