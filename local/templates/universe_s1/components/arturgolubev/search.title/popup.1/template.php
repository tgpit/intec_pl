<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */
/** @var CBitrixComponent $component */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = &$arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-search-title',
        'c-search-title-popup-1'
    ]
]) ?>
    <div class="search-title-button intec-cl-text-hover" data-action="search.open">
        <div class="search-title-button-wrapper intec-grid intec-grid-nowrap intec-grid-i-h-5 intec-grid-a-v-center">
            <div class="search-title-button-icon-wrap intec-grid-item-auto">
                <div class="search-title-button-icon">
                    <i class="glyph-icon-loop"></i>
                </div>
            </div>
            <div class="search-title-button-text-wrap intec-grid-item-auto">
                <div class="search-title-button-text">
                    <?= Loc::getMessage('C_SEARCH_TITLE_POPUP_1_BUTTON') ?>
                </div>
            </div>
        </div>
    </div>
    <div class="search-title intec-content-wrap" data-role="search">
        <div class="search-title-overlay" data-role="overlay" data-action="search.close"></div>
        <div class="search-title-wrapper" data-role="panel">
            <div class="search-title-wrapper-2 intec-content intec-content-primary intec-content-visible">
                <div class="search-title-wrapper-3 intec-content-wrapper">
                    <div class="search-title-wrapper-4">
                        <?= Html::beginForm($arResult['FORM_ACTION'], 'get', [
                            'class' => 'search-title-form',
                            'data-role' => 'search.form'
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'search-title-form-wrapper',
                                    'intec-grid' => [
                                        '',
                                        'nowrap',
                                        'a-v-center'
                                    ]
                                ]
                            ]) ?>
                                <div class="search-title-form-wrapper-2 intec-grid-item">
                                    <button type="submit" class="intec-ui intec-ui-control-button search-title-form-button" aria-hidden="true" data-action="search.submit">
                                        <div class="intec-ui-part-icon">
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14.2083 12.8333H13.4842L13.2275 12.5858C14.1569 11.5079 14.6677 10.1316 14.6667 8.70834C14.6667 7.52989 14.3172 6.37791 13.6625 5.39806C13.0078 4.41822 12.0772 3.65453 10.9885 3.20355C9.89975 2.75258 8.70173 2.63459 7.54592 2.86449C6.39012 3.09439 5.32845 3.66187 4.49516 4.49516C3.66187 5.32845 3.09439 6.39012 2.86449 7.54592C2.63459 8.70173 2.75258 9.89975 3.20355 10.9885C3.65453 12.0772 4.41822 13.0078 5.39806 13.6625C6.37791 14.3172 7.52989 14.6667 8.70834 14.6667C10.1842 14.6667 11.5408 14.1258 12.5858 13.2275L12.8333 13.4842V14.2083L17.4167 18.7825L18.7825 17.4167L14.2083 12.8333ZM8.70834 12.8333C6.42584 12.8333 4.58334 10.9908 4.58334 8.70834C4.58334 6.42584 6.42584 4.58334 8.70834 4.58334C10.9908 4.58334 12.8333 6.42584 12.8333 8.70834C12.8333 10.9908 10.9908 12.8333 8.70834 12.8333Z" fill="#808080"/>
                                            </svg>
                                        </div>
                                    </button>
                                    <?= Html::textInput('q', null, [
                                        'class' => [
                                            'search-title-form-input'
                                        ],
                                        'id' => $arVisual['INPUT']['ID'],
                                        'maxlength' => 100,
                                        'autocomplete' => 'off',
                                        'placeholder' => Loc::getMessage('C_SEARCH_TITLE_POPUP_1_PLACEHOLDER'),
                                        'data' => [
                                            'role' => 'input'
                                        ]
                                    ]) ?>
                                    <div class="search-title-form-button" data-action="search.clear" aria-hidden="true">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="12" fill="#DCDCDC"/>
                                            <path d="M9.33325 9.33334L14.6666 14.6667" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M14.6666 9.33334L9.33325 14.6667" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="intec-grid-item-auto">
                                    <div class="search-title-form-button" data-action="search.close" aria-hidden="true">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17 14L12 9L7 14" stroke="#B0B0B0" stroke-width="1.75385" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?= Html::endForm() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var root = data.nodes;
            var component;
            var search = $('[data-role="search"]', root);
            var overlay = $('[data-role="overlay"]', search);
            var panel = $('[data-role="panel"]', search);
            var input = $('[data-role="input"]', search);
            var page = $('html');
            var buttons = {};
            var state = false;

            buttons.open = $('[data-action="search.open"]', root);
            buttons.close = $('[data-action="search.close"]', root);
            buttons.clear = $('[data-action="search.clear"]', root);

            search.open = function () {
                if (state) return;

                state = true;
                search.attr('data-expanded', 'true').css({
                    'display': 'block'
                });

                page.css({
                    'overflow': 'hidden',
                    'height': '100%'
                });

                panel.stop().animate({'top': 0}, 350);
                overlay.stop().animate({'opacity': 0.5}, 350);

                input.focus();
            };

            search.close = function () {
                if (!state) return;

                state = false;
                search.attr('data-expanded', 'false');

                if (component)
                    component.RESULT.style.display = 'none';

                panel.stop().animate({'top': -panel.height()}, 350);
                overlay.stop().animate({'opacity': 0}, 350, function () {
                    search.css({'display': 'none'});
                    input.blur();

                    page.css({
                        'overflow': '',
                        'height': ''
                    });
                });
            };

            search.clear = function () {
                input[0].value = '';
            };

            buttons.open.on('click', search.open);
            buttons.close.on('click', search.close);
            buttons.clear.on('click', search.clear);

            <?php if ($arVisual['TIPS']['USE']) { ?>
                component = new JCTitleSearch(<?= JavaScript::toObject([
                    'AJAX_PAGE' => POST_FORM_ACTION_URI,
                    'CONTAINER_ID' => $sTemplateId,
                    'INPUT_ID' => $arVisual['INPUT']['ID'],
                    'MIN_QUERY_LEN' => 2
                ]) ?>);

                component.onFocusLost = function () {};

                component.ShowResult = (function () {
                    var handler = component.ShowResult;

                    return function () {
                        if (state) handler.apply(component, arguments);
                    }
                })();

                $(document).on('click', function (event) {
                    var target = $(event.target);

                    if (
                        target.isOrClosest(overlay) ||
                        !target.isOrClosest([component.CONTAINER, component.RESULT])
                    ) component.RESULT.style.display = 'none';
                });
            <?php } ?>
        }, {
            'name': '[Component] bitrix:search.title (popup.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
        });
    </script>
<?= Html::endTag('div') ?>