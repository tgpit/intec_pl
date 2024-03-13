<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\component\InnerTemplate;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arData
 * @var InnerTemplate $this
 */

$sTemplateId = $arData['id'];
$sPrefix = 'BANNER_';
$arParameters = [];

foreach ($arParams as $sKey => $sValue)
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut($sKey, StringHelper::length($sPrefix));
        $arParameters[$sKey] = $sValue;
    }

$arParameters['SELECTOR'] = '#'.$sTemplateId;
$arParameters['ATTRIBUTE'] = 'data-color';

?>
<div class="widget-banner-1">
    <?php $APPLICATION->IncludeComponent(
        'intec.universe:main.slider',
        'template.1',
        $arParameters,
        $this->getComponent()
    ) ?>
</div>
<?php if ($arResult['VISUAL']['TRANSPARENCY'] && !defined('EDITOR')) { ?>
    <script type="text/javascript">
        (function () {
            var _ = template.getLibrary('_');
            var $ = template.getLibrary('$');

            var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            var header = $('.widget-view.widget-view-desktop', root);
            var banner = $('.widget-banner', root);
            var slides = $('.widget-item-content', banner);
            var handle;
            var handleThrottle;

            handle = function () {
                var height;

                if (header.css('display') === 'none') {
                    height = 0;
                } else {
                    height = header.height();
                }

                banner.css({'margin-top': -height + 'px'});
                slides.css({'padding-top': height + 'px'});
            };

            handleThrottle = _.throttle(handle, 250, {
                'leading': false
            });

            template.load(function () {
                handle();

                $(window).on('resize', handleThrottle);
            }, {
                'name': '[Component] intec.universe:main.header (template.1) > banners (template.1)'
            });

            setTimeout(handle, 0);
        })();
    </script>
<?php } ?>