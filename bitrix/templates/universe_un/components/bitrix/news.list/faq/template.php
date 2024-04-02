<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="ns-bitrix c-news-list c-news-list-faq" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if (!$arResult['VISUAL']['LIST']['COMMON']['USE']) { ?>
                <?= Html::beginTag('ul', [
                    'class' => [
                        'scrollbar-inner',
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
                        'ui-control' => 'tabs',
                        'role' => 'scrollbar'
                    ]
                ]) ?>
                    <?php $bActive = true ?>
                    <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                    <?php
                        if (empty($arSection['ITEMS']))
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
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'faq-section-common-list' =>  $arResult['VISUAL']['LIST']['COMMON']['USE'],
                    'intec-ui' => [
                        '' => true,
                        'control-tabs-content' => true
                    ]
                ], true)
            ]) ?>
                <?php $bActive = true ?>
                <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                <?php
                    if (empty($arSection['ITEMS']))
                        continue;
                ?>
                    <?= Html::beginTag('div', [
                        'id' => $sTemplateId.'-section-'.$arSection['ID'],
                        'class' => 'intec-ui-part-tab',
                        'data' => [
                            'active' => $bActive ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?php if ($arResult['VISUAL']['LIST']['COMMON']['USE']) { ?>
                            <?= Html::tag('div', $arSection['NAME'], [
                                'class' => 'faq-section-name'
                            ]) ?>
                        <?php } ?>
                        <div class="faq-section">
                            <?php foreach ($arSection['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            ?>
                                <div class="faq-item" id="<?= $sAreaId ?>" itemscope="" itemtype="http://schema.org/Question">
                                    <div class="faq-item-wrapper">
                                        <div class="faq-item-name" data-action="toggle">
                                            <div class="faq-item-name-text" itemprop="name"><?= $arItem['NAME'] ?></div>
                                            <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-center faq-item-name-indicators">
                                                <i class="fa fa-chevron-up faq-item-name-indicator faq-item-name-indicator-active"></i>
                                                <i class="fa fa-chevron-down faq-item-name-indicator faq-item-name-indicator-inactive"></i>
                                            </div>
                                        </div>
                                        <div class="faq-item-description" itemprop="acceptedAnswer" itemscope="" itemtype="http://schema.org/Answer">
                                            <div class="faq-item-description-wrapper" itemprop="text"><?= $arItem['PREVIEW_TEXT'] ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php if (!$arResult['VISUAL']['LIST']['COMMON']['USE']) { ?>
                        <?php $bActive = false ?>
                    <?php } ?>
                    <?= Html::endTag('div') ?>
                <? } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');

            var root = data.nodes;
            var items = root.find('.faq-item');
            var active = null;
            var duration = 300;
            var scrollbar = $('[data-role="scrollbar"]', root);

            scrollbar.scrollbar();

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
                block = item.find('.faq-item-description');
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
                block = item.find('.faq-item-description');
                block.stop().animate({'height': 0}, duration, function () {
                    block.css({
                        'display': 'none',
                        'height': 'auto'
                    });
                });
            };
        }, {
            'name': '[Component] bitrix:news.list (faq)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>
