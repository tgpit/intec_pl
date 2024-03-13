<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

include(__DIR__.'/result_modifier.php');

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;


/**
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 * @var Arrays $arSections
 */

if (!Loader::includeModule('intec.core'))
    return;

global $APPLICATION;

if (defined('EDITOR'))
    $arResult = [[
        'TITLE' => Loc::getMessage('BREADCRUMB_PAGE_TITLE'),
        'LINK' => ''
    ]];

if (empty($arResult))
    return '';

$sTemplateId = 'i-c-breadcrumb';
$arRender = [];
$arResult = ArrayHelper::merge(
    [[
        'TITLE' => Loc::getMessage('BREADCRUMB_MAIN_TITLE'),
        'LINK' => SITE_DIR
    ]],
    $arResult
);

$arSeparator = [
    'MOBILE' => null,
    'USUAL' => null
];

$iCount = count($arResult);
$iIndex = 0;

$arRenderSlider = [];

foreach ($arResult as $arItem) {
    $sTitle = Html::encode($arItem['TITLE']);
    $sLink = $arItem['LINK'];

    if ($arItem['LINK'] != '') {
        $arSectionCurrent = null;

        foreach ($arSections as $arSection)
            if ($arSection['SECTION_PAGE_URL'] == $arItem['LINK']) {
                $arSectionCurrent = $arSection;
                break;
            }

        $arRenderMenu = [];

        if (!empty($arSectionCurrent) && $arParams['BREADCRUMB_DROPDOWN_USE'] === 'Y') {
            $arSectionsCurrent = $arSections->where(function ($sKey, $arSection) use (&$arSectionCurrent) {
                return $arSection['IBLOCK_SECTION_ID'] == $arSectionCurrent['IBLOCK_SECTION_ID'];
            })->asArray();

            if (!empty($arSectionsCurrent)) {
                $arRenderMenu[] = Html::beginTag('div', [
                    'class' => 'breadcrumb-menu',
                    'data-control' => 'menu'
                ]);
                $arRenderMenu[] = Html::beginTag('div', [
                    'class' => 'breadcrumb-menu-wrapper'
                ]);

                foreach ($arSectionsCurrent as $arSection) {
                    $arRenderMenu[] = Html::tag('a', $arSection['NAME'], [
                        'class' => 'breadcrumb-menu-item intec-cl-text-hover',
                        'href' => $arSection['SECTION_PAGE_URL']
                    ]);
                }

                $arRenderMenu[] = Html::endTag('div');
                $arRenderMenu[] = Html::endTag('div');
            }
        }

        $arRender[] =
            '<div class="breadcrumb-item" data-control="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                <a href="'.$sLink.'" title="'.$sTitle.'" data-control="link" class="breadcrumb-link intec-cl-text-hover" itemprop="item">
                    <span itemprop="name">'.$sTitle.'</span>
                    <meta itemprop="position" content="'.($iIndex + 1).'">'.
            (!empty($arRenderMenu) ? '<i class="far fa-angle-down"></i>' : null).
            '</a>'.
            implode('', $arRenderMenu).
            '</div>';

        $arRenderSlider[] =
            '<div class="breadcrumb-item" data-control="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                <a href="'.$sLink.'" title="'.$sTitle.'" data-control="link" class="breadcrumb-link intec-cl-text-hover" itemprop="item">
                    <span itemprop="name">'.$sTitle.'</span>
                    <meta itemprop="position" content="'.($iIndex + 1).'">'.
            '</a>'.
            '</div>';
    } else {
        $arRender[] =
            '<div class="breadcrumb-item intec-cl-text" data-control="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                <div itemprop="item">
                    <span itemprop="name">'.$sTitle.'</span>
                    <meta itemprop="position" content="'.($iIndex + 1).'">
                </div>
            </div>';

        $arRenderSlider[] =
            '<div class="breadcrumb-item intec-cl-text" data-control="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                <div itemprop="item">
                    <span itemprop="name">'.$sTitle.'</span>
                    <meta itemprop="position" content="'.($iIndex + 1).'">
                </div>
            </div>';
    }

    $iIndex++;
}

$arPreviews = $arResult[count($arResult)-2];

$sMobileData = $arVisual['MOBILE']['USE'] ? 'true' : 'false';

$sDesktopRender = implode('<span class="breadcrumb-separator"> / </span>', $arRender);
$sMobileSliderRender =
    '<div class="scroll-mod-hiding scrollbar-inner">'.
        '<div class="scroll-mod-hiding scrollbar-inner scroll-content scroll-scrolly_visible" data-role="scroll">'.
            implode('<span class="breadcrumb-separator"> / </span>', $arRenderSlider).
        '</div>'.
    '</div>';
$sMobileWithoutSliderRender =
    '<div class="breadcrumb-item" data-control="item" itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
        <a href="'.$arPreviews['LINK'].'" title="'.$arPreviews['TITLE'].'" data-control="link" class="breadcrumb-link intec-cl-text-hover" itemprop="item">
            <i class="far fa-angle-left intec-cl-text"></i>'.
            $arPreviews['TITLE'].
        '</a>'.
    '</div>';

$sReturns =
    Html::beginTag('div', [
        'id' => $sTemplateId,
        'class' => [
            'ns-bitrix',
            'c-breadcrumb' => [
                '',
                'default'
            ]
        ],
        'data' => [
            'mobile' => $arVisual['MOBILE']['USE'] && !$arVisual['MOBILE']['SLIDER']['USE'] ? null : $sMobileData,
            'mobile-use' => $arVisual['MOBILE']['USE'] ? 'true' : 'false',
            'mobile-slider-use' => $arVisual['MOBILE']['USE'] && $arVisual['MOBILE']['SLIDER']['USE'] ? 'true' : 'false',
            'print' => 'false'
        ]
    ]).
    '<div class="breadcrumb-wrapper intec-content intec-content-visible">'.
        '<div class="breadcrumb-wrapper-2 intec-content-wrapper" itemscope="" itemtype="http://schema.org/BreadcrumbList" data-role="content">'.
            implode('<span class="breadcrumb-separator"> / </span>', $arRender).
        '</div>'.
    '</div>'
            .Html::script('template.load(function (data) {
                var $ = this.getLibrary(\'$\');
                var root = data.nodes;
                var content = $(\'[data-role="content"]\', root);
                var mobileUse = root.data(\'mobile-use\');
                var mobileSliderUse = root.data(\'mobile-slider-use\');
                
                var setMenuListener = function () {
                    $(\'[data-control="item"]\', root).each(function () {
                        var item = $(this);
                        var link = item.find(\'[data-control="link"]\');
                        var menu = item.find(\'[data-control="menu"]\');
                    
                        item.hover(function () {
                            link.addClass(\'intec-cl-text\');
                            
                            menu.css({\'display\': \'block\'}).stop().animate({
                                \'opacity\': 1
                            }, 300);
                        }, function () {
                            link.removeClass(\'intec-cl-text\');
                            
                            menu.stop().animate({
                                \'opacity\': 0
                            }, 300, function () {
                                menu.css({\'display\': \'none\'});
                            });
                        });
                    });
                };
                
                if (document.documentElement.offsetWidth > 770) {
                    if (!root.hasClass(\'c-breadcrumb-usual\')) {
                        root.addClass(\'c-breadcrumb-usual\');
                        root.removeClass(\'c-breadcrumb-mobile\');
                        root.removeClass(\'c-breadcrumb-mobile-slider\');
                        content.html('.JavaScript::toObject($sDesktopRender).');
                        setMenuListener();
                    }
                } else {
                    if (mobileUse && mobileSliderUse) {
                        if (!root.hasClass(\'c-breadcrumb-mobile-slider\')) {
                            root.addClass(\'c-breadcrumb-mobile-slider\');
                            root.removeClass(\'c-breadcrumb-usual\');
                            root.removeClass(\'c-breadcrumb-mobile\');
                            content.html('.JavaScript::toObject($sMobileSliderRender).');
                            
                            var scroll = $(\'[data-role="scroll"]\', root);
                            scroll.scrollbar();
                        }
                    } else if (mobileUse && !mobileSliderUse) {
                        if (!root.hasClass(\'c-breadcrumb-mobile\')) {
                            root.addClass(\'c-breadcrumb-mobile\');
                            root.removeClass(\'c-breadcrumb-usual\');
                            root.removeClass(\'c-breadcrumb-mobile-slider\');
                            content.html('.JavaScript::toObject($sMobileWithoutSliderRender).');
                        }
                    }
                }
                
                $(window).on(\'resize\', function () {
                    if (document.documentElement.offsetWidth > 770) {
                        if (!root.hasClass(\'c-breadcrumb-usual\')) {
                            root.addClass(\'c-breadcrumb-usual\');
                            root.removeClass(\'c-breadcrumb-mobile\');
                            root.removeClass(\'c-breadcrumb-mobile-slider\');
                            content.html('.JavaScript::toObject($sDesktopRender).');
                            setMenuListener();
                        }
                    } else {
                        if (mobileUse && mobileSliderUse) {
                            if (!root.hasClass(\'c-breadcrumb-mobile-slider\')) {
                                root.addClass(\'c-breadcrumb-mobile-slider\');
                                root.removeClass(\'c-breadcrumb-usual\');
                                root.removeClass(\'c-breadcrumb-mobile\');
                                content.html('.JavaScript::toObject($sMobileSliderRender).');
                                
                                var scroll = $(\'[data-role="scroll"]\', root);
                                scroll.scrollbar();
                            }
                        } else if (mobileUse && !mobileSliderUse) {
                            if (!root.hasClass(\'c-breadcrumb-mobile\')) {
                                root.addClass(\'c-breadcrumb-mobile\');
                                root.removeClass(\'c-breadcrumb-usual\');
                                root.removeClass(\'c-breadcrumb-mobile-slider\');
                                content.html('.JavaScript::toObject($sMobileWithoutSliderRender).');
                            }
                        }
                    }
                });
        }, {
        \'name\': \'[Component] bitrix:breadcrumb (.default)\',
        \'nodes\': '.JavaScript::toObject('#'.$sTemplateId).',
        \'loader\': {
            \'name\': \'lazy\'
        }
    })', [
        'type' => 'text/javascript'
    ]).
    '</div>';

return $sReturns;