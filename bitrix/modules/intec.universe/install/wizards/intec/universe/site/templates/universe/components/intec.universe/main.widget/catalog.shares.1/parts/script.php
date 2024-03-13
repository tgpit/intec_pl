<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die(); ?>
<?php
use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

/**
 * @var string $sTemplateId
 */

$bShowSeconds = $arVisual['TIMER']['SECONDS']['SHOW'];

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        var secondsShow = $(<?= JavaScript::toObject($bShowSeconds) ?>);
        var fieldDay = $('[data-role="days"]', root);
        var fieldHour = $('[data-role="hours"]', root);
        var fieldMinute = $('[data-role="minutes"]', root);
        var fieldSeconds = $('[data-role="seconds"]', root);
        var scrollbar = $('[data-role="scrollbar"]', root);
        var aboveZero = true;
        var arDate = $(<?= JavaScript::toObject($arResult['DATA']['TIMER']['DATE']) ?>);

        if (!secondsShow)
            arDate[4]++;

        var finalDate = new Date(arDate[0], arDate[1] - 1, arDate[2], arDate[3], arDate[4], arDate[5]);
        var timeout = 60000;

        if (secondsShow)
            timeout = 1000;

        scrollbar.scrollbar();
        update('0', '00', '00', '00');
        time();

        function time() {
            var currentDate = new Date();
            var differentDate = finalDate - currentDate;
            var days = Math.floor(differentDate / 1000); /*миллисекунды в секунды*/

            if (days <= 0)
                aboveZero = false;

            if (!secondsShow) {
                timeout = ((days % 60) * 1000) + 1000;
            }

            var sec = days % 60; if (sec < 10) sec = '0' + sec; days = Math.floor(days / 60); /*секунды в минуты*/
            var min = days % 60; if (min < 10) min = '0' + min; days = Math.floor(days / 60); /*минуты в часы*/
            var hour = days % 24; if (hour < 10) hour = '0' + hour; days = Math.floor(days / 24); /*часы в дни*/

            if (aboveZero){
                update(days, hour, min, sec);
                setTimeout(time, timeout);
            } else {
                update('0', '00', '00', '00');
            }
        }

        function update(day, hour, minute, seconds) {
            fieldDay.html(day);
            fieldHour.html(hour);
            fieldMinute.html(minute);

            if (secondsShow)
                fieldSeconds.html(seconds);
        }
    }, {
        'name': '[Component] intec.universe:main.widget (catalog.shares.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>