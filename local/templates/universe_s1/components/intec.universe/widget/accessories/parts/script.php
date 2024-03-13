<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

/**
 * @var string $sTemplateId
 */

$bShowSeconds = $arVisual['TIMER']['SECONDS']['SHOW'];

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var closeIcons = $('.widget-panel-form-close-icon', root);
        var overlay = $('[data-role="form-overlay"]', root);
        var labels = $('.intec-ui-control-radiobox', overlay);
        var viewForm = $('.widget-panel-view-form', overlay);
        var sortForm = $('.widget-panel-sort-form', overlay);
        var filter = $('[data-role="widget.filter"]', root);
        var currentWidth = $(window).width();
        var counter = 0;

        var sectionList = {
            'container' : $('[data-role="section.list"]', root),
            'items' : $('[data-role="section.list.item"]', root),
            'control' : $('[data-role="section.list.control"]', root),
            'mobileControl': null,
            'controlText' : $('[data-role="section.list.control.text"]', root),
            'openHeight' : 0,
            'closeHeight' : 0,
            'closeHeightMobile' : 0,
            'active' : false,
            'activeMode': null
        };

        sectionList.activeMode = sectionList.container.attr('data-active');

        if (sectionList.activeMode !== 'none') {


            sectionList.items.each(function(){
                counter++;
                sectionList.openHeight += $(this).outerHeight(true);

                if (counter === 1)
                    sectionList.closeHeightMobile = sectionList.openHeight;

                if (counter === 7)
                    sectionList.closeHeight = sectionList.openHeight;

                if (counter > 7 && !sectionList.active)
                    sectionList.active = true;

                if ($(this).hasClass('active')) {
                    sectionList.mobileControl = $(this);
                    sectionList.closeHeightMobile += $(this).outerHeight(true);
                }
            });

            if (sectionList.closeHeight === 0)
                sectionList.closeHeight = sectionList.openHeight;



            var mobile = currentWidth <= 900;

            if (sectionList.control.attr('data-status') === 'open') {
                switcher('open', mobile);
            }
            else {
                switcher('close', mobile);
            }

            delete(mobile);



            if (sectionList.active) {
                sectionList.control.on('click', function () {
                    var status = $(this).attr('data-status');

                    switcher(status, false, true);
                });
            }

            sectionList.mobileControl.on('click', function () {
                if (currentWidth <= 900) {
                    if (sectionList.container.attr('data-status') === 'open')
                        switcher('close', true);
                    else
                        switcher('open', true)
                }
            });

            $(window).on('resize', function () {
                currentWidth = $(this).width();

                if (sectionList.activeMode !== 'none') {
                    var mobile = false;

                    if (currentWidth < 900)
                        mobile = true;

                    if (sectionList.control.attr('data-status') === 'open') {
                        switcher('open', mobile);
                    }
                    else {
                        switcher('close', mobile);
                    }
                }
            });
        }

        function switcher (status = 'close', mobile = false, reversStatus = false) {

            if (reversStatus) {
                if (status === 'close')
                    status = 'open';
                else if (status === 'open')
                    status = 'close';
            }

            if (status === 'close') {
                if (sectionList.active) {
                    sectionList.control.attr('data-status', 'close');
                    sectionList.controlText.html( '<?= Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_LIST_SHOW_ALL') ?>');
                }

                sectionList.container.attr('data-status', 'close');

                if (mobile)
                    sectionList.container.css('height', sectionList.closeHeightMobile);
                else
                    sectionList.container.css('height', sectionList.closeHeight);

            }

            if (status === 'open') {
                if (sectionList.active) {
                    sectionList.control.attr('data-status', 'open');
                    sectionList.controlText.html('<?= Loc::getMessage('C_WIDGET_ACCESSORIES_TEMPLATE_LIST_HIDE') ?>');
                }

                sectionList.container.attr('data-status', 'open');
                sectionList.container.css('height', sectionList.openHeight);
            }
        }


        viewForm.button = $('.widget-panel-views-mobile', root);
        sortForm.button = $('.widget-panel-sorting', root);

        var sortDesktopForm = $('.widget-panel-sort-form-desktop', sortForm.button);

        sortDesktopForm.state = false;
        sortForm.button.on('click', function () {
            if (document.documentElement.clientWidth <= 850) {
                overlay.fadeIn();
                sortForm.fadeIn().animate({opacity: 1.0, marginBottom: "0", display: "block"}, {duration: 600, queue: false});
            } else {
                if (sortDesktopForm.state) {
                    sortDesktopForm.hide();
                } else {
                    sortDesktopForm.show();
                }
                sortDesktopForm.state = !sortDesktopForm.state;
            }
        });

        viewForm.button.on('click', function () {
            overlay.fadeIn();
            viewForm.fadeIn().animate({opacity: 1.0, marginBottom: "0", display: "block"}, {duration: 600, queue: false});
        });

        labels.on('click', function () {
            this.querySelector('input[type="radio"]').checked = true;
            this.querySelector('a').click();
        });

        closeIcons.on('click', function (event) {
            event.stopPropagation();
            var form = this.closest('.widget-panel-form');
            $(overlay).fadeOut(1000);
            $(form).fadeOut().animate({opacity: 0, marginBottom: "-300px", display: "none"}, {duration: 600, queue: false});
        });

        overlay.on('click', function (event) {
            if (event.target === this) {
                $('.widget-panel-form-close-icon', this).click();
            }
        });

        filter.state = false;
        filter.button = $('[data-role="widget.filter.button"]', root);
        filter.button.on('click', function () {
            if (filter.state) {
                filter.hide();
            } else {
                filter.show();
            }

            filter.state = !filter.state;
        });
    }, {
        'name': '[Component] intec.universe:main.widget (widget.accessories.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>