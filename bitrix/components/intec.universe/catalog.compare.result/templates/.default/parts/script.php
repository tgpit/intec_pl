<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var _ = this.getLibrary('_');
        var root = data.nodes;
        var area = $(window);
        var component = new BX.Iblock.Catalog.CompareClass(
            <?= JavaScript::toObject($sTemplateId) ?>,
            <?= JavaScript::toObject($arResult['~COMPARE_URL_TEMPLATE']) ?>
        );

        var difference = $('[data-role="difference"]', root);
        var countItems = area.width() <= 768 ? 1 : 5;
        var compareSlider = $('[data-role="slider"]', root);
        var scrollStep = compareSlider.width() / countItems;
        var tabs = $('[data-role="compare.tab.section"]', root);
        var scroll = $('[data-role="scroll"]', root);

        if (area.width() <= 768) {
            scroll.scrollbar();
        }

        _.each(tabs, function (tab) {
            tab.header = $('[data-role="header"]', tab);
            tab.sliders = $('[data-role="slider"]', tab);

            tab.sliders.each(function () {
                var slider = $(this);

                slider.compareContent = $('[data-type="compare.content"][data-position="' + slider[0].dataset.position + '"]', tab);
                slider.slides = $('[data-role="slide"]', slider);
                slider.dragged = $('[data-type="compare.content"][data-position="' + slider[0].dataset.position + '"] [data-role="slide"]', tab);
                slider.buttonsFixed = $('[data-role="item.fixed"]', slider);
                slider.arIndexes = [];
                slider.sliderDirection = 'next';
                slider.translateWidth = scrollStep * slider.slides.length;
                slider.navigation = {
                    length: 0
                };
                slider.startSwipePosition = 0;
                slider.startSwipeTransform = 0;
                slider.headerIndex = $('[data-position="' + slider[0].dataset.position + '"][data-role="header.index"]', tab);
                slider.itemsIndex = $('[data-position="' + slider[0].dataset.position + '"][data-role="items.index"]', tab);
                slider.itemsDots = $('[data-position="' + slider[0].dataset.position + '"][data-role="items.dots"] [data-role="items.dot"]', tab);

                if (slider[0].dataset.position === 'left') {
                    slider.navigation = $('[data-role="navigation"]', tab);
                    slider.itemsDots[0].classList.add('intec-cl-background');

                    if (slider.slides.length <= countItems) {
                        slider.navigation[0].dataset.state = 'false';
                    }
                } else {
                    if (slider.slides.length > 1) {
                        slider.itemsDots[1].classList.add('intec-cl-background');
                        slider.headerIndex[0].innerHTML = 2;
                        slider.itemsIndex[0].innerHTML = 2;

                        _.each(slider.compareContent, function (content) {
                            content.style.transform = "translateX(" + (- scrollStep) + "px)";
                        });
                    } else {
                        slider.itemsDots[0].classList.add('intec-cl-background');
                    }
                }

                area.on('resize scroll', function () {
                    if (area.scrollTop() > tabs.filter('[data-active="true"]').offset().top) {
                        if (tab.header[0].dataset.active === 'false') {
                            tab.header[0].dataset.active = true;
                            tab.header.slideToggle(500);
                        }

                        if (slider.navigation.length !== 0 && slider.navigation[0].dataset.fixed === 'false') {
                            slider.navigation[0].dataset.fixed = 'true';
                        }
                    } else {
                        if (tab.header[0].dataset.active === 'true') {
                            tab.header[0].dataset.active = false;
                            tab.header.slideToggle(500);
                        }

                        if (slider.navigation.length !== 0 && slider.navigation[0].dataset.fixed === 'true') {
                            slider.navigation[0].dataset.fixed = 'false';
                        }
                    }
                });

                slider.getEvent = function () {
                    return (event.type.search('touch') !== -1) ? event.touches[0] : event;
                };

                slider.swipeStart = function () {
                    var evt = slider.getEvent();
                    var sliderMatrix = new WebKitCSSMatrix(slider[0].style.webkitTransform);

                    slider.startSwipePosition = evt.clientX;
                    slider.startSwipeTransform = sliderMatrix.m41;

                    this.addEventListener('mousemove', slider.swipeMove);
                    this.addEventListener('touchmove', slider.swipeMove);
                    this.addEventListener('mouseup', slider.swipeEnd);
                    this.addEventListener('touchend', slider.swipeEnd);
                };

                slider.swipeMove = function () {
                    var evt = slider.getEvent();
                    var swipeAmount = evt.clientX - slider.startSwipePosition;
                    var firstRatio = 1;
                    var offsetSlide = 0;
                    var currentIndex = parseInt(this.dataset.index) + 1;

                    this.ondragstart = function () {
                        return false;
                    };

                    if (Math.abs(swipeAmount) > scrollStep / 4) {
                        if (swipeAmount < 0) {
                            firstRatio = -1;
                        }

                        offsetSlide = slider.startSwipeTransform + firstRatio * scrollStep;

                        if (parseInt(this.dataset.index) !== 0 && swipeAmount > 0 || parseInt(this.dataset.index) < slider.slides.length - 1 && swipeAmount < 0) {
                            _.each(slider.compareContent, function (content) {
                                content.style.transform = "translateX(" + offsetSlide + "px)";
                            });

                            currentIndex = currentIndex - firstRatio;

                            slider.headerIndex[0].innerHTML = currentIndex;
                            slider.itemsIndex[0].innerHTML = currentIndex;

                            _.each(slider.itemsDots, function (dot) {

                                dot.classList.remove('intec-cl-background');

                                if (dot.dataset.index == currentIndex) {
                                    dot.classList.add('intec-cl-background');
                                }
                            });
                        }

                        this.removeEventListener('mousemove', slider.swipeMove);
                        this.removeEventListener('touchmove', slider.swipeMove);
                    } else {
                        this.onmouseleave = function () {
                            this.removeEventListener('mousemove', slider.swipeMove);
                            this.removeEventListener('touchmove', slider.swipeMove);
                        }
                    }

                };

                slider.swipeEnd = function () {
                    this.removeEventListener('mousemove', slider.swipeMove);
                    this.removeEventListener('touchmove', slider.swipeMove);
                };

                _.each(slider.slides, function (slide) {
                    slider.arIndexes.push(slide.dataset.index);
                });

                _.each(slider.dragged, function (slide) {
                    if (area.width() <= 768) {
                        slide.ondragstart = function () {
                            return false;
                        };

                        slide.addEventListener('touchstart', slider.swipeStart);
                        slide.addEventListener('mousedown', slider.swipeStart);
                    }

                });

                slider.fixedElement = function (step, fixedElement, eventType) {
                    var fixedElementMatrix = new WebKitCSSMatrix(fixedElement[0].style.webkitTransform);
                    var slideTranslate = 0;
                    var currentIndex = _.indexOf(slider.arIndexes, fixedElement[0].dataset.index);
                    var newIndex = 0;
                    var firstRatio = 1;
                    var secondRatio = 1;

                    if (eventType === 'next') {
                        newIndex = currentIndex + 1;
                        firstRatio = -1;
                    } else if (eventType === 'prev') {
                        newIndex = currentIndex - 1;
                        secondRatio = -1;
                    }

                    slider.arIndexes[currentIndex] = slider.arIndexes[newIndex];
                    slider.arIndexes[newIndex] = fixedElement[0].dataset.index;

                    var nextElement = slider.slides.filter('[data-index="' + slider.arIndexes[currentIndex] + '"]');
                    var nextElementMatrix = new WebKitCSSMatrix(nextElement[0].style.webkitTransform);

                    nextElement[0].style.transform = "translateX(" + (_.round(nextElementMatrix.m41, 1) + firstRatio * step) + "px)";
                    slideTranslate = _.round(fixedElementMatrix.m41, 1) + secondRatio * step;
                    fixedElement[0].style.transform = "translateX(" + slideTranslate + "px)";

                    _.each(slider.compareContent, function (content) {
                        $('[data-index="' + fixedElement[0].dataset.index + '"]', content)[0].style.transform = "translateX(" + slideTranslate + "px)";
                        $('[data-index="' + nextElement[0].dataset.index + '"]', content)[0].style.transform = "translateX(" + (nextElementMatrix.m41 + firstRatio * step) + "px)";

                    });
                };

                slider.on('click', function (event) {
                    var target = $(event.target);
                    var button = target.closest('[data-role="item.remove"]', slider);
                    var buttonFixed = target.closest('[data-role="item.fixed"]', slider);

                    if (buttonFixed.length !== 0) {
                        var currentSlide = target.closest('[data-role="slide"]', slider)[0];

                        if (currentSlide.dataset.fixed === 'true') {
                            buttonFixed.removeClass('intec-cl-svg-path-stroke');
                            currentSlide.dataset.fixed = 'false';
                        } else {
                            if (slider.slides.filter('[data-fixed="true"]').length !== 0) {
                                slider.buttonsFixed.filter('.intec-cl-svg-path-stroke').removeClass('intec-cl-svg-path-stroke');
                                slider.slides.filter('[data-fixed="true"]')[0].dataset.fixed = 'false';
                            }

                            buttonFixed.addClass('intec-cl-svg-path-stroke');
                            currentSlide.dataset.fixed = 'true';
                        }

                        _.each(slider.compareContent, function (content) {
                            $('[data-index="' + currentSlide.dataset.index + '"]', content)[0].dataset.fixed = currentSlide.dataset.fixed;
                        });
                    }

                    if (button.length !== 0) {
                        component.MakeAjaxAction(button.attr('data-action'));
                    }
                });

                if (slider.navigation.length !== 0) {
                    slider.navigation.getButtons = function () {
                        return $('[data-role="navigation.button"]', slider.navigation);
                    };

                    slider.navigation.getButtons().on('click', function () {
                        var button = $(this);
                        var action = button.attr('data-action');
                        var directionSlide = 1;
                        var fixedElement = slider.slides.filter('[data-fixed="true"]');
                        var contentMatrix = new WebKitCSSMatrix(slider[0].style.webkitTransform);

                        if (action === 'next') {
                            slider.sliderDirection = 'next';
                            directionSlide = -1;
                            slider.navigation.getButtons().filter('[data-action="prev"]')[0].dataset.state = 'enabled';
                        } else if (action === 'prev') {
                            slider.sliderDirection = 'prev';
                            slider.navigation.getButtons().filter('[data-action="next"]')[0].dataset.state = 'enabled';
                        }

                        $(this)[0].dataset.state = 'disabled';
                        var offsetSlide = _.round(contentMatrix.m41, 1) + directionSlide * scrollStep;

                        if (offsetSlide <= 0 && offsetSlide >= (slider.slides.length - countItems) * (-1) * scrollStep) {
                            if (fixedElement.length !== 0) {
                                slider.fixedElement(scrollStep, fixedElement, slider.sliderDirection);
                            }

                            _.each(slider.compareContent, function (content) {
                                content.style.transform = "translateX(" + offsetSlide + "px)";
                            });
                        }

                        if (action === 'next' && offsetSlide >= (slider.slides.length - countItems - 1) * (-1) * scrollStep) {
                            $(this)[0].dataset.state = 'enabled';
                        } else if (action === 'prev' && offsetSlide < 0) {
                            $(this)[0].dataset.state = 'enabled';
                        }
                    });
                }
            });
        });

        if (difference.length !== 0) {
            difference.locked = false;
            difference.input = difference.find('input');
            difference.on('click', function () {
                if (difference.locked)
                    return;

                difference.locked = true;
                difference.input.prop('checked', !difference.input.prop('checked'));
                document.location.href = difference.attr('data-action');
            });
        }
    }, {
        'name': '[Component] bitrix:catalog.compare.result (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
