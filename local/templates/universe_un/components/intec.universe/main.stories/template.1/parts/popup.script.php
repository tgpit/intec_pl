<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = <?= JavaScript::toObject('#'.$sTemplateId) ?>;
        var mainSlider = $('[data-role="main.slider"]', root);
        var subSliderItems = $('[data-role="sub.slider"]', root);
        var mainContainerHeight = $('#UniverseComponent').height();
        var activeItem = $('[data-role="sub.slider"][data-status="active"]', root);
        var activeIndex = activeItem.index();
        var slideWidth;
        var slideMargin = 0;

        resizeHeight('[data-view="popup"]', mainContainerHeight);

        if ($(window).width() <= 550)
            slideWidth = $(window).width();
        else
            slideWidth = (mainContainerHeight / 179) * 100;

        subSliderItems.each(function () {
            $(this).css('min-width', slideWidth);
        });

        positionMainSlider();

        $(window).on('resize', function () {
            mainContainerHeight = $('#UniverseComponent').height();

            resizeHeight('[data-view="popup"]', mainContainerHeight);

            if ($(window).width() <= 550)
                slideWidth = $(window).width();
            else
                slideWidth = (mainContainerHeight / 179) * 100;

            subSliderItems.each(function(){
                $(this).css('min-width', slideWidth);
            });

            slideMargin = 0;
            positionMainSlider();
        });

        $(subSliderItems).on('click', function () {
            if ($(this).attr('data-status') === 'nonactive')
                pausUse = false;
            selectMainSlide(this);
        });

        function selectMainSlide (select) {

            subSliderItems.each(function () {
                changeData(this, 'status', 'nonactive');
            });
            changeData(select, 'status', 'active');


            var oldLoadBarIndex = $('[data-role="load.bar"][data-status="active"]', activeItem).parent().index();
            updateLoadBar(activeItem, oldLoadBarIndex, 'disable');

            if (!pausUse)
                startTimeSlide(activeItem, slideTime, 'clear');

            activeIndex = $(select).index();
            activeItem = $(select);

            if (!pausUse)
                startTimeSlide(activeItem, slideTime);

            var newLoadBarIndex = $('[data-role="load.bar"][data-status="nonactive"]:first', activeItem).parent().index();

            updateLoadBar(activeItem, newLoadBarIndex, 'enable');
            positionMainSlider();
        }

        function positionMainSlider () {

            if (slideMargin <= 0)
                slideMargin = activeItem.outerWidth(true) - activeItem.outerWidth();

            var firstPosition = mainSlider.width() / 2 - slideWidth / 2;

            if ($(window).width() > 425)
                firstPosition = firstPosition - (slideMargin / 2);

            if (activeIndex > 0) {
                mainSlider.css('left', firstPosition - slideWidth * activeIndex);
            } else {
                mainSlider.css('left', firstPosition);
            }
        }

        function resizeHeight (block, height) {
            $(block).css('height', height + 'px');
        }

        /***** sub slider ****/
        var activeControls = $('[data-role="sub.slider.control"]', '[data-role="sub.slider"]');

        var longpress = 250;
        var start;
        var timer;
        var subSliderTimer;
        var preloader = $('[data-role="preloader"]');
        var slideTime = <?= JavaScript::toObject($arVisual['POPUP']['TIME'])?>;
        slideTime = slideTime * 1000;
        var startTime;
        var recalcTime;
        var pausUse = false;

        setTimeout(function () {
            mainSlider.attr('data-status', 'loaded');
            preloader.attr('data-active', 'false');
            updateLoadBar(activeItem, 0);
            startTimeSlide(activeItem, slideTime);
        }, 250);

        activeControls.on('mousedown touchstart', function (e) {
            start = new Date().getTime();

            clearTimeout(subSliderTimer);
            if (pausUse)
                recalcTime = recalcTime - (start - startTime) - 270;
            else
                recalcTime = slideTime - (start - startTime) - 100;

            pausUse = true;

            timer = setTimeout(function () {
                var loadBars = $('[data-role="load.bars"]', activeItem);
                changeData(loadBars, 'pause', 'true');
                }, longpress);

        }).on('mouseleave touchend', function (e) {
            var loadBars = $('[data-role="load.bars"]', activeItem);
            start = 0;

            clearTimeout(timer);
            changeData(loadBars, 'pause', 'false');
        }).on('mouseup touchend', function (e) {
            if ( new Date().getTime() < ( start + longpress )  ) {
                clearTimeout(timer);

                var action = $(this).data('action');
                var container = $(this).closest('[data-role="sub.slider"]');
                var activeStatus = container.attr('data-status');

                if (activeStatus === 'active') {
                    setTimeout(function(){
                        slideUpdate(container, action);
                    },0);
                }

                pausUse = false;
            } else {
                var loadBars = $('[data-role="load.bars"]', activeItem);
                changeData(loadBars, 'pause', 'false');

                startTimeSlide(activeItem, recalcTime);
            }
        });

        function startTimeSlide (container, time, action='none') {

            if (action === 'clear') {
                clearTimeout(subSliderTimer);
            }
            else {
                startTime = new Date().getTime();

                clearTimeout(subSliderTimer);
                subSliderTimer = setTimeout(function(){
                    pausUse = false;
                    slideUpdate(container, 'next');
                }, time);
            }
        }

        function updateLoadBar (mainSlider, newIndex, action = 'update') {
            var loadBars = $('[data-role="load.bar"]', mainSlider);

            if (action === 'update') {
                loadBars.each(function () {
                    var currentIndex = $(this).parent().index();

                    if (currentIndex < newIndex)
                        changeData(this, 'status', 'end');
                    else if (currentIndex === newIndex)
                        changeData(this, 'status', 'active');
                    else if (currentIndex > newIndex)
                        changeData(this, 'status', 'nonactive');
                });
            } else if (action === 'disable') {
                changeData(loadBars.eq(newIndex), 'status', 'nonactive');
            } else if (action === 'enable') {
                changeData(loadBars.eq(newIndex), 'status', 'active');
            }
        }

        function slideUpdate (container, action = 'none') {
            var currentItem = $('[data-role="sub.slider.item"][data-status="enabled"]', container);
            var currentIndex = currentItem.index();
            var items = $('[data-role="sub.slider.item"]', container);
            var maxCount = items.length;

            if (action === "next") {
                if (currentIndex + 1 < maxCount) {
                    $(items).each(function () {
                        changeData(this, 'status', 'disabled');
                    });

                    changeData(items.eq(currentIndex + 1), 'status', 'enabled');
                    updateLoadBar(container, currentIndex + 1);
                    startTimeSlide(container, slideTime);
                } else {
                    if ($(container).index() + 1 >= subSliderItems.length)
                        return;

                    selectMainSlide(subSliderItems.eq($(container).index() + 1));
                    startTimeSlide(subSliderItems.eq($(container).index() + 1), slideTime);
                }
            }

            if (action === "prev") {
                if (currentIndex - 1 >= 0) {
                    $(items).each(function () {
                        changeData(this, 'status', 'disabled');
                    });

                    changeData(items.eq(currentIndex - 1), 'status', 'enabled');
                    updateLoadBar(container, currentIndex - 1);
                } else {
                    if (container.index() - 1 < 0)
                        return;

                    selectMainSlide(subSliderItems.eq(container.index() - 1));
                }
            }
        }

        function changeData (select, data, value) {
            $(select).data('data', value);
            $(select).attr('data-' + data, value);
        }

        document.addEventListener('touchstart', handleTouchStart, false);
        document.addEventListener('touchmove', handleTouchMove, false);

        var xDown = null;

        function getTouches(evt) {
            return evt.touches ||
                evt.originalEvent.touches;
        }

        function handleTouchStart(evt) {
            var firstTouch = getTouches(evt)[0];
            xDown = firstTouch.clientX;
        }

        function handleTouchMove(evt) {
            if (!xDown) {
                return;
            }

            var xUp = evt.touches[0].clientX;
            var xDiff = xDown - xUp;

            if (Math.abs(xDiff) < -50 || Math.abs(xDiff) > 50) {
                var item;
                var container = $('[data-role="sub.slider"][data-status="active"]', root);

                if ( xDiff > 0 ) {
                    item = subSliderItems.eq(container.index() + 1);

                    if (item.length > 0)
                        selectMainSlide(item);

                } else {
                    if (container.index() - 1 >= 0) {
                        item = subSliderItems.eq(container.index() - 1);

                        if (item.length > 0)
                            selectMainSlide(item);
                    }
                }

                xDown = null;
            }
        }

        $('.popup-window-close-icon').on('click', function () {
            document.removeEventListener('touchstart', handleTouchStart, false);
            document.removeEventListener('touchmove', handleTouchMove, false);
        });
    }, {
        'name': '[Component] intec.universe:main.stories (template.1 popup)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>