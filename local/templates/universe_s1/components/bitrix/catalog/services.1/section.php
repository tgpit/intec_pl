<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\helpers\JavaScript;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$bSeo = Loader::includeModule('intec.seo');
$oRequest = Core::$app->request;
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$bIsAjax = false;

if ($oRequest->getIsAjax()) {
    $bIsAjax = $oRequest->get('catalog');
    $bIsAjax = ArrayHelper::getValue($bIsAjax, 'ajax') === 'Y';
}

$arParams = ArrayHelper::merge([
    'SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW' => 'Y',
    'SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION' => 'top',
    'SECTIONS_CHILDREN_CANONICAL_URL_USE' => 'N',
    'SECTIONS_CHILDREN_CANONICAL_URL_TEMPLATE' => null,
    'SECTIONS_CHILDREN_MENU_SHOW' => 'N',
    'FILTER_AJAX' => 'N',
    'SECTIONS_LAYOUT' => '1'
], $arParams);

$arIBlock = $arResult['IBLOCK'];
$arSection = $arResult['SECTION'];
$arCanonicalUrl = [
    'USE' => $arParams['SECTIONS_CHILDREN_CANONICAL_URL_USE'] === 'Y',
    'TEMPLATE' => $arParams['SECTIONS_CHILDREN_CANONICAL_URL_TEMPLATE']
];

if (empty($arCanonicalUrl['TEMPLATE']) || empty($arSection))
    $arCanonicalUrl['USE'] = false;

$arDescription = [
    'SHOW' => $arParams['SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'top',
        'bottom'
    ], $arParams['SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION']),
    'VALUE' => !empty($arSection) ? $arSection['DESCRIPTION'] : null
];

if (empty($arDescription['VALUE']))
    $arDescription['SHOW'] = false;

$sLevel = 'CHILDREN';

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/filter.php');
include(__DIR__.'/parts/sections.php');
include(__DIR__.'/parts/elements.php');

$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['SECTIONS_CHILDREN_MENU_SHOW'] === 'Y';

$arColumns = [
    'SHOW' => $arMenu['SHOW'] || ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical')
];

if ($arColumns['SHOW']) {
    $arSections['PARAMETERS']['WIDE'] = 'N';
    $arElements['PARAMETERS']['WIDE'] = 'N';
}

if ($arCanonicalUrl['USE'])
    $APPLICATION->SetPageProperty('canonical', CIBlock::ReplaceSectionUrl(
        $arCanonicalUrl['TEMPLATE'],
        $arSection,
        true,
        'S'
    ));

?>
<?php if ($arResult['CONTENT']['SECTIONS']['BEGIN']['SHOW']) { ?>
    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.include',
        '.default',
        [
            'AREA_FILE_SHOW' => 'file',
            'PATH' => $arResult['CONTENT']['SECTIONS']['BEGIN']['PATH'],
            'EDIT_TEMPLATE' => ''
        ],
        $component
    ) ?>
<?php } ?>
<div id="<?= $sTemplateId ?>" class="ns-bitrix c-catalog c-catalog-services-1 p-section">
    <div class="catalog-wrapper intec-content intec-content-visible">
        <div class="catalog-wrapper-2 intec-content-wrapper">
            <?php if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'horizontal') { ?>
                <div class="catalog-smart-filter">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.smart.filter',
                        $arFilter['TEMPLATE'],
                        $arFilter['PARAMETERS'],
                        $component
                    ) ?>
                </div>
            <?php } ?>
            <div class="catalog-content">
                <?php if ($arColumns['SHOW']) { ?>
                    <div class="catalog-content-left intec-content-left">
                        <?php if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical') { ?>
                            <div class="catalog-smart-filter">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:catalog.smart.filter',
                                    $arFilter['TEMPLATE'],
                                    $arFilter['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arMenu['SHOW']) { ?>
                            <div class="catalog-menu">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    $arMenu['TEMPLATE'],
                                    $arMenu['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="catalog-content-right intec-content-right">
                        <?php include(__DIR__.'/parts/panel.php') ?>
                        <div class="catalog-content-right-wrapper intec-content-right-wrapper" data-role="catalog.content">
                <?php } ?>
                <?php if ($arDescription['SHOW'] && $arDescription['POSITION'] === 'top') { ?>
                    <div class="<?= Html::cssClassFromArray([
                        'catalog-description',
                        'catalog-description-'.$arDescription['POSITION'],
                        'intec-ui-markup-text'
                    ]) ?>"><?= $arDescription['VALUE'] ?></div>
                <?php } ?>
                <?php if ($bIsAjax) $APPLICATION->RestartBuffer() ?>
                <?php include(__DIR__.'/parts/preloader.php') ?>
                <?php if ($arElements['SHOW'] && $arElements['POSITION'] === 'top') { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section',
                        $arElements['TEMPLATE'],
                        $arElements['PARAMETERS'],
                        $component
                    ); ?>
                <?php } ?>
                <?php if ($arSections['SHOW']) { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section.list',
                        $arSections['TEMPLATE'],
                        $arSections['PARAMETERS'],
                        $component
                    ); ?>
                <?php } ?>
                <?php if ($arElements['SHOW'] && $arElements['POSITION'] === 'bottom' || !$arElements['SHOW'] && !empty($arElements['TEMPLATE'])) { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section',
                        $arElements['TEMPLATE'],
                        $arElements['PARAMETERS'],
                        $component
                    ); ?>
                <?php } ?>
                <?php if ($bSeo) { ?>
                    <?php $APPLICATION->IncludeComponent('intec.seo:iblocks.metadata.loader', '', [
                        'IBLOCK_ID' => $arIBlock['ID'],
                        'SECTION_ID' => $arSection['ID'],
                        'TYPE' => 'section',
                        'MODE' => 'single',
                        'METADATA_SET' => 'Y',
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME']
                    ], $component) ?>
                <?php } ?>
                <?php if ($arDescription['SHOW'] && $arDescription['POSITION'] === 'bottom') { ?>
                    <div class="<?= Html::cssClassFromArray([
                        'catalog-description',
                        'catalog-description-'.$arDescription['POSITION'],
                        'intec-ui-markup-text'
                    ]) ?>"><?= $arDescription['VALUE'] ?></div>
                <?php } ?>
                <?php if ($bIsAjax) exit() ?>
                <?php if ($arColumns['SHOW']) { ?>
                        </div>
                    </div>
                    <div class="intec-ui-clearfix"></div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var app = this;
            var $ = this.getLibrary('$');

            var root = data.nodes;
            var filter = $('[data-role="catalog.filter"]', root);
            var content = $('[data-role="catalog.content"]', root);

            filter.state = false;
            filter.button = $('[data-role="catalog.filter.button"]', root);
            filter.button.on('click', function () {
                if (filter.state) {
                    filter.hide();
                } else {
                    filter.show();
                }

                filter.state = !filter.state;
            });

            content.refresh = function (url) {
                var panel = $('[data-role="catalog.panel"]', content);
                var preloader = $('[data-role="catalog.preloader"]', content);

                if (url == null)
                    url = null;

                $.ajax({
                    'url': url,
                    'data': {
                        'catalog': {
                            'ajax': 'Y'
                        }
                    },
                    'cache': false,
                    'beforeSend': function() {
                        preloader.attr('data-active', 'true');
                    },
                    'success': function (response) {
                        preloader.attr('data-active', 'false');
                        panel.detach();
                        preloader.detach();
                        content.html(response);
                        content.find('.catalog-content-preloader-layer').replaceWith(preloader);
                        content.find('[data-role="catalog.panel"]').replaceWith(panel);
                        content.find('[data-role="catalog.filter"]').replaceWith(filter);
                        app.api.basket.update();
                    }
                });
            };

            <?php if ($arFilter['SHOW'] && $arFilter['AJAX']) { ?>
            var updater = function (url) {
                if (window.history.enabled || window.history.pushState != null) {
                    window.history.pushState(null, document.title, url);
                } else {
                    window.location.href = url;
                }
                content.refresh(url);
            };

            if (smartFilter && smartFilter.on)
                smartFilter.on('refresh', updater);

            <?php } ?>
        }, {
            'name': '[Component] bitrix:catalog (services.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>
<?php if ($arResult['CONTENT']['SECTIONS']['END']['SHOW']) { ?>
    <?php $APPLICATION->IncludeComponent(
        'bitrix:main.include',
        '.default',
        [
            'AREA_FILE_SHOW' => 'file',
            'PATH' => $arResult['CONTENT']['SECTIONS']['END']['PATH'],
            'EDIT_TEMPLATE' => ''
        ],
        $component
    ) ?>
<?php } ?>