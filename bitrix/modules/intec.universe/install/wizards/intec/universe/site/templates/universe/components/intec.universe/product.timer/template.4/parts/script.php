<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

$bShowSeconds = $arVisual['BLOCKS']['SECONDS'];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        var secondsShow = $(<?= JavaScript::toObject($bShowSeconds) ?>)[0];
        var fieldDay = {
            'value':  $('[data-role="days"]', root),
            'sign': $('[data-role="days.sign"]', root)
        };
        var fieldHour = {
            'value':  $('[data-role="hours"]', root),
            'sign': $('[data-role="hours.sign"]', root)
        };
        var fieldMinute = {
            'value':  $('[data-role="minutes"]', root),
            'sign': $('[data-role="minutes.sign"]', root)
        };
        var fieldSecond = {
            'value':  $('[data-role="seconds"]', root),
            'sign': $('[data-role="seconds.sign"]', root)
        };
        var aboveZero = true;
        var arDate = $(<?= JavaScript::toObject($arResult['DATE']['VALUE']) ?>);

        if (!secondsShow)
            arDate[4]++;

        var finalDate = new Date(arDate[0], arDate[1] - 1, arDate[2], arDate[3], arDate[4], arDate[5]);
        var timeout = 60000;

        if (secondsShow)
            timeout = 1000;

        time();

        function time() {
            var currentDate = new Date();
            var differentDate = finalDate - currentDate;
            var days = Math.floor(differentDate / 1000);

            if (days <= 0)
                aboveZero = false;

            if (!secondsShow)
                timeout = ((days % 60) * 1000) + 1000;

            var sec = days % 60; if (sec < 10) sec = '0' + sec; days = Math.floor(days / 60);
            var min = days % 60; if (min < 10) min = '0' + min; days = Math.floor(days / 60);
            var hour = days % 24; if (hour < 10) hour = '0' + hour; days = Math.floor(days / 24);

            if (days < 10) {
                days = '0' + days;
            }

            if (aboveZero) {
                update(days, hour, min, sec);
                setTimeout(time, timeout);
            } else {
                update('00', '00', '00', '00');
            }
        }

        function update(day, hour, minute, seconds) {
            fieldDay.value.html(day);
            fieldHour.value.html(hour);
            fieldMinute.value.html(minute);

            if (secondsShow)
                fieldSecond.value.html(seconds);
        }
    }, {
        'name': '[Component] intec.universe:product.timer (template.4)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>