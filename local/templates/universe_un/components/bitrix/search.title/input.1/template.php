<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = &$arResult['VISUAL'];

?>
<div id="<?= $sTemplateId ?>" class="ns-bitrix c-search-title c-search-title-input-1">
    <div class="search-title">
        <?= Html::beginForm($arResult['FORM_ACTION'], 'get', [
            'class' => 'search-title-form'
        ]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'search-title-form-wrapper',
                    'intec-grid' => [
                        '',
                        'i-h-5',
                        'nowrap',
                        'a-v-center'
                    ]
                ]
            ]) ?>
                <div class="intec-grid-item">
                    <?= Html::textInput('q', null, [
                        'class' => [
                            'search-title-input'
                        ],
                        'id' => $arVisual['INPUT']['ID'],
                        'maxlength' => 100,
                        'autocomplete' => 'off',
                        'placeholder' => Loc::getMessage('C_SEARCH_TITLE_INPUT_1_PLACEHOLDER')
                    ]) ?>
                </div>
                <div class="intec-grid-item-auto">
                    <button type="submit" class="search-title-button intec-cl-text" aria-hidden="true">
                        <i class="glyph-icon-loop"></i>
                    </button>
                </div>
            <?= Html::endTag('div') ?>
        <?= Html::endForm() ?>
    </div>
    <?php if ($arVisual['TIPS']['USE']) { ?>
        <script type="text/javascript">
            template.load(function () {
                var $ = this.getLibrary('$');
                var component = new JCTitleSearch(<?= JavaScript::toObject([
                    'AJAX_PAGE' => POST_FORM_ACTION_URI,
                    'CONTAINER_ID' => $sTemplateId,
                    'INPUT_ID' => $arVisual['INPUT']['ID'],
                    'MIN_QUERY_LEN' => 2
                ]) ?>);

                component.onFocusLost = function () {};

                component.adjustResultNode = function () {
                    var self = component;

                    if(!(BX.type.isElementNode(self.RESULT) && BX.type.isElementNode(self.CONTAINER)))
                        return { top: 0, right: 0, bottom: 0, left: 0, width: 0, height: 0 };

                    self.RESULT.style.position = 'absolute';
                    self.RESULT.style.left = '';
                    self.RESULT.style.top = '';
                    self.RESULT.style.width = '';

                    var position = BX.pos(self.CONTAINER);
                    var width = self.RESULT.clientWidth;

                    if (position.width > width) {
                        self.RESULT.style.left = (position.left + ((position.width - width) / 2)) + 'px';
                    } else {
                        if ((width + position.left) > document.documentElement.clientWidth) {
                            self.RESULT.style.left = (document.documentElement.clientWidth - width) / 2 + 'px';
                        } else {
                            self.RESULT.style.left = position.left + 'px';
                        }
                    }

                    self.RESULT.style.top = (position.bottom + 14) + 'px';
                    self.RESULT.style.width = self.RESULT.clientWidth + 'px';

                    return position;
                };

                $(document).on('click', function (event) {
                    var target = $(event.target);

                    if (!target.isOrClosest([component.CONTAINER, component.RESULT]))
                        component.RESULT.style.display = 'none';
                });
            }, {
                'name': '[Component] bitrix:search.title (input.1)'
            });
        </script>
    <?php } ?>
</div>