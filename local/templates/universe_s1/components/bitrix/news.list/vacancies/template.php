<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$bShowSummaryForm = $arParams['FORM_SUMMARY_DISPLAY'] == 'Y' && !empty($arParams['FORM_SUMMARY_ID']);

?>
<div class="vacancies" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('ul', [
                'class' => [
                    'intec-ui' => [
                        '',
                        'control-tabs',
                        'scheme-current',
                        'view-1',
                        'mod-block',
                        'mod-position-left'
                    ]
                ],
                'data' => [
                    'ui-control' => 'tabs'
                ]
            ]) ?>
                <?php $bActive = true ?>
                <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                <?php
                    if (count($arSection['ITEMS']) <= 0)
                        continue;
                ?>
                    <?= Html::beginTag('li', [
                        'class' => 'intec-ui-part-tab',
                        'data' => [
                            'active' => $bActive ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?= Html::tag('a', $arSection['NAME'], [
                            'href' => '#'.$sTemplateId.'-section-'.$arSection['ID'],
                            'data' => [
                                'type' => 'tab'
                            ]
                        ]) ?>
                    <?= Html::endTag('li') ?>
                    <?php $bActive = false ?>
                <?php } ?>
            <?= Html::endTag('ul') ?>
            <div class="intec-ui intec-ui-control-tabs-content">
                <?php $bActive = true ?>
                <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                    <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
                    <?= Html::beginTag('div', [
                        'id' => $sTemplateId.'-section-'.$arSection['ID'],
                        'class' => 'intec-ui-part-tab',
                        'data' => [
                            'active' => $bActive ? 'true' : 'false'
                        ]
                    ]) ?>
                        <div class="vacancies-section">
                            <?php $bItemFirst = true ?>
                            <?php foreach ($arSection['ITEMS'] as $arItem) { ?>
                            <?php
                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $sSalary = ArrayHelper::getValue($arItem, ['SYSTEM_PROPERTIES', 'SALARY', 'VALUE']);

                            ?>
                                <?php if (!$bItemFirst) { ?>
                                    <div class="vacancies-delimiter"></div>
                                <?php } ?>
                                <div class="vacancies-item" id="<?= $sAreaId ?>">
                                    <div class="vacancies-item-wrapper">
                                        <div class="vacancies-item-name" data-action="toggle">
                                            <div class="vacancies-item-name-text"><?= $arItem['NAME'] ?></div>
                                            <?php if (!empty($sSalary)) { ?>
                                            <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-center vacancies-item-name-salary">
                                                <div class="vacancies-item-name-salary-wrapper"><?= $sSalary
                                                    /*number_format($sSalary, 0, '.', ' ').
                                                    (!empty($arParams['CURRENCY']) ? ' '.$arParams['CURRENCY'] : '')*/
                                                ?></div>
                                            </div>
                                            <?php } ?>
                                            <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-center vacancies-item-name-indicators">
                                                <i class="fa fa-chevron-up vacancies-item-name-indicator vacancies-item-name-indicator-active"></i>
                                                <i class="fa fa-chevron-down vacancies-item-name-indicator vacancies-item-name-indicator-inactive"></i>
                                            </div>
                                        </div>
                                        <div class="vacancies-item-description">
                                            <div class="vacancies-item-description-wrapper"><?= $arItem['PREVIEW_TEXT'] ?></div>
                                            <?php if ($bShowSummaryForm) { ?>
                                                <div class="vacancies-item-description-buttons">
                                                    <div class="intec-ui intec-ui-control-button intec-ui-mod-round-3 intec-ui-scheme-current intec-ui-size-1" data-name="<?= Html::encode($arItem['NAME']) ?>" data-action="form"><?= GetMessage('N_L_VACANCIES_FORM_SUMMARY') ?></div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php $bItemFirst = false ?>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                    <script type="text/javascript">
                        template.load(function (data) {
                            var app = this;
                            var $ = app.getLibrary('$');

                            var root = data.nodes;
                            var items = root.find('.vacancies-item');
                            var active = null;
                            var duration = 300;

                            items.each(function () {
                                var self = this;
                                var item = $(this);
                                var toggle = item.find('[data-action=toggle]');

                                toggle.on('click', function () {
                                    if (active === self) {
                                        close(self);
                                        active = null;
                                    } else {
                                        open(self);
                                    }
                                });
                            });

                            var open = function (item) {
                                if (active === item)
                                    return;

                                var block;
                                var height;

                                close(active);
                                active = item;

                                item = $(item);
                                item.addClass('active');
                                block = item.find('.vacancies-item-description');
                                height = block.css({
                                    'display': 'block',
                                    'height': 'auto'
                                }).height();
                                block.css({'height': 0}).stop().animate({'height': height + 'px'}, duration, function () {
                                    block.css('height', 'auto');
                                });
                            };

                            var close = function (item) {
                                var block;

                                item = $(item);
                                item.removeClass('active');
                                block = item.find('.vacancies-item-description');
                                block.stop().animate({'height': 0}, duration, function () {
                                    block.css({
                                        'display': 'none',
                                        'height': 'auto'
                                    });
                                });
                            };

                            <?php if ($bShowSummaryForm) { ?>
                                root.find('[data-action=form]').click(function () {
                                    var item = $(this);
                                    var name = item.data('name');
                                    var fields = {};

                                    <?php if (!empty($arParams['PROPERTY_FORM_SUMMARY_VACANCY'])) { ?>
                                        fields[<?= JavaScript::toObject($arParams['PROPERTY_FORM_SUMMARY_VACANCY']) ?>] = name;
                                    <?php } ?>

                                    app.api.forms.show({
                                        'id': <?= JavaScript::toObject($arParams['FORM_SUMMARY_ID']) ?>,
                                        'template': '.default',
                                        'parameters': {
                                            'AJAX_OPTION_ADDITIONAL': <?= JavaScript::toObject($sTemplateId.'_FORM') ?>,
                                            'CONSENT_URL': <?= JavaScript::toObject($arParams['CONSENT_URL']) ?>
                                        },
                                        'settings': {
                                            'title': <?= JavaScript::toObject(GetMessage('N_L_VACANCIES_FORM_SUMMARY')) ?>
                                        },
                                        'fields': fields
                                    });

                                    app.metrika.reachGoal('forms.open');
                                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arParams['FORM_SUMMARY_ID'].'.open') ?>);
                                });
                            <?php } ?>
                        }, {
                            'name': '[Component] bitrix:news.list (vacancies)',
                            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                            'loader': {
                                'name': 'lazy'
                            }
                        });
                    </script>
                    <?php $bActive = false ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
