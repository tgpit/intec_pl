<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\JavaScript;

?>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');

        BX.Sale.PersonalOrderComponent.PersonalOrderDetail.init(<?= JavaScript::toObject([
            'url' => $this->GetFolder() .'/ajax.php',
            'templateFolder' => $this->GetFolder(),
            'paymentList' => $arPaymentData,
            'templateName' => $this->getName()
        ]) ?>);

        var root = data.nodes;
        var history = root.find('[data-role="history-pages"]');
        var historyItems = history.find('[data-role="history-page"]');
        var historyButtons = history.find('[data-role="buttons"]');
        var historyButton = history.find('[data-role="button"]');
        var historyButtonPrev = history.find('[data-role="prev"]');
        var historyButtonNext = history.find('[data-role="next"]');
        var countButton = parseInt(historyButton.length);
        var blocks = root.find('[data-role="block"]');
        var getParams = (new URL(document.location)).searchParams;
        var blockToScroll = blocks.filter('[data-block="' + getParams.get('data_block') + '"]');

        if (blockToScroll.length !== 0) {
            $('html, body').animate({
                scrollTop: blockToScroll.offset().top - 72
            }, {
                duration: 0,
                easing: 'linear'
            });
        }

        function historyChangeSwitch(btn) {
            historyButton.each(function () {
                $(this)[0].classList.remove('intec-cl-background');
                $(this)[0].dataset.active = 'false';

                if (countButton > 5) {
                    if ($(this)[0].dataset.id != 1 && $(this)[0].dataset.id != countButton && $(this)[0].dataset.id != btn[0].dataset.id) {
                        $(this)[0].dataset.disabled = 'false';
                    }
                }
            });

            btn[0].classList.add('intec-cl-background');
            btn[0].dataset.active = 'true';
            var btnIndex = parseInt(btn[0].dataset.id);

            if (countButton > 5) {
                if (btnIndex == 1 || btnIndex == 2) {
                    historyButton[1].dataset.disabled = 'true';
                    historyButton[2].dataset.disabled = 'true';
                    historyButton[3].dataset.disabled = 'true';
                } else if (btnIndex == countButton || btnIndex == countButton - 1) {
                    historyButton[countButton - 1].dataset.disabled = 'true';
                    historyButton[countButton - 2].dataset.disabled = 'true';
                    historyButton[countButton - 3].dataset.disabled = 'true';
                } else {
                    historyButton[btnIndex - 2].dataset.disabled = 'true';
                    historyButton[btnIndex].dataset.disabled = 'true';
                }
            }

            historyItems.each(function () {
                if ($(this)[0].dataset.id == btn[0].dataset.id) {
                    $(this)[0].dataset.expanded = 'true';
                } else {
                    $(this)[0].dataset.expanded = 'false';
                }
            });

            if (btn[0].dataset.id == countButton) {
                historyButtonNext[0].dataset.active = 'false';
            } else {
                historyButtonNext[0].dataset.active = 'true';
            }

            if (btn[0].dataset.id == 1) {
                historyButtonPrev[0].dataset.active = 'false';
            } else {
                historyButtonPrev[0].dataset.active = 'true';
            }
        }

        historyButtonNext.on('click', function () {
            var curElem = historyButtons[0].querySelectorAll('[data-role="button"][data-active="true"]');
            var curIndex = parseInt(curElem[0].dataset.id) + 1;
            var nextElem = historyButtons[0].querySelectorAll('[data-role="button"][data-id="' + curIndex + '"]');

            historyChangeSwitch(nextElem);

            if (curIndex == countButton) {
                $(this)[0].dataset.active = 'false';
            }
        });

        historyButtonPrev.on('click', function () {
            var curElem = historyButtons[0].querySelectorAll('[data-role="button"][data-active="true"]');
            var curIndex = parseInt(curElem[0].dataset.id) - 1;
            var nextElem = historyButtons[0].querySelectorAll('[data-role="button"][data-id="' + curIndex + '"]');

            historyChangeSwitch(nextElem);

            if (curIndex == 0) {
                $(this)[0].dataset.active = 'false';
            }
        });

        historyButton.on('click', function () {
            historyChangeSwitch($(this));
        });

        blocks.each(function () {
            var content = $(this).find('[data-role="content"]');
            $(this).find('[data-role="collapse"]').on('click', function () {
                if ($(this)[0].dataset.state == 'true') {
                    $(this)[0].dataset.state = 'false';
                } else {
                    $(this)[0].dataset.state = 'true';
                }
                content.slideToggle(400);
            });
        });


    }, {
        'name': '[Component] bitrix:sale.personal.order.detail (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
