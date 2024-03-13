<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

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
        var globalDays = 0;

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

            globalDays = days;

            if (aboveZero) {
                update(days, hour, min, sec);
                setTimeout(time, timeout);
            } else {
                update('0', '00', '00', '00');
            }
        }

        function updateDaySign(days = null) {

            if (!days && days !== 0)
                return null;

            var sign = null;

            if (days % 10 === 1 && days % 100 !== 11) {
                sign = '<?= Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_UNTIL_THE_END_DAY') ?>';
            } else if (days % 10 >= 2 && days % 10 <= 4 && (days % 100 < 10 || days % 100 >= 20)) {
                sign = '<?= Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_UNTIL_THE_END_DAYS') ?>';
            } else {
                sign = '<?= Loc::getMessage('C_PRODUCT_TIMER_TEMPLATE_3_UNTIL_THE_END_DAYS_MANY') ?>';
            }

            if (!!sign) {
                fieldDay.sign.html(sign);
            }
        }

        function update(day, hour, minute, seconds) {
            if (day >= 0 && hour === 23 && minute === 59) {
                if (!secondsShow)
                    updateDaySign(day);
                else if (seconds === 59) {
                    updateDaySign(day);
                }
            }

            fieldDay.value.html(day);
            fieldHour.value.html(hour);
            fieldMinute.value.html(minute);

            if (secondsShow)
                fieldSecond.value.html(seconds);
        }
    }, {
        'name': '[Component] intec.universe:product.timer (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>