<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var item = $('[data-role="item"]', data.nodes);
        var button = $('[data-role="button"]', data.nodes);
        var close = $('[data-role="close"]', data.nodes);
        var rollUp = $('[data-role="roll.up"]', data.nodes);
        var volume = $('[data-role="volume"]', data.nodes);
        var quickViewData = button.data('quick-view');
        var video = $('video', item);
        var getCookie = function (name) {
            var matches = document.cookie.match(new RegExp(
                "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ));
            return matches ? decodeURIComponent(matches[1]) : undefined;
        };
        var soundOn = function () {
            if (video.length > 0)
                video.prop('muted', false);

            volume[0].dataset.state = 'on';
        };
        var soundOff = function () {
            if (video.length > 0)
                video.prop('muted', true);

            volume[0].dataset.state = 'off';
        };
        var videoWidgetCookie = getCookie('VIDEO_WIDGET_CLOSE');

        if (!!videoWidgetCookie && videoWidgetCookie === 'Y') {
            item[0].dataset.state = 'hidden';
        } else {
            item[0].dataset.state = 'visible';
        }

        item.on('click', function () {
            var self = $(this)[0];
            if (self.dataset.scaled === 'false') {
                self.dataset.scaled = 'true';
            }
        });

        $(document).on('click', function (event) {
            if (!$(event.target).closest(item).length && item[0].dataset.scaled !== 'false') {
                item[0].dataset.scaled = 'false';

                if (volume[0].dataset.state === 'on')
                    soundOff();
            }
        });

        button.on('click', function () {
            if (button[0].dataset.mode === 'form') {
                var options = <?= JavaScript::toObject([
                    'id' => $arResult['ITEM']['DATA']['FORM']['ID'],
                    'template' => $arResult['ITEM']['DATA']['FORM']['TEMPLATE'],
                    'parameters' => [
                        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'-form',
                        'CONSENT_URL' => $arResult['ITEM']['DATA']['FORM']['CONSENT']
                    ],
                    'settings' => [
                        'title' => $arResult['ITEM']['DATA']['FORM']['TITLE']
                    ]
                ]) ?>;

                app.api.forms.show(options);
                app.metrika.reachGoal('video.widget.forms.open');
                app.metrika.reachGoal(<?= JavaScript::toObject('video.widget.forms.'.$arResult['ITEM']['DATA']['FORM']['ID'].'.open') ?>);
            } else if (button[0].dataset.mode === 'product') {
                app.api.components.show({
                    'component': 'bitrix:catalog.element',
                    'template': quickViewData.TEMPLATE,
                    'parameters': quickViewData.PARAMETERS,
                    'settings': {
                        'parameters': {
                            'className': 'popup-window-quick-view',
                            'width': null
                        }
                    }
                });
                app.metrika.reachGoal('video.widget.product.open');
            }
        });

        close.on('click', function () {
            document.cookie = "VIDEO_WIDGET_CLOSE=Y";
            data.nodes.remove();
        });

        rollUp.on('click', function (event) {
            event.stopPropagation();
            item[0].dataset.scaled = 'false';

            if (volume[0].dataset.state === 'on')
                soundOff();
        });

        volume.on('click', function () {
            if (volume[0].dataset.state === 'off') {
                soundOn();
            } else {
                soundOff();
            }
        });
    }, {
        'name': '[Component] intec.universe:main.video (fixed.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'default'
        }
    });
</script>