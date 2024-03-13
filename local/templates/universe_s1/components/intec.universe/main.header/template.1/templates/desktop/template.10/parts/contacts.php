<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var string $sTemplateId
 */

?>
<div class="widget-contacts-container intec-grid-item-auto">
    <?= Html::beginTag('div', [
        'class' => 'widget-contacts',
        'data' => [
            'block' => 'phone',
            'multiple' => !empty($arResult['CONTACTS']['VALUES']) ? 'true' : 'false',
            'advanced' => $arResult['CONTACTS']['ADVANCED'] ? 'true' : 'false',
            'expanded' => 'false'
        ]
    ]) ?>
        <div class="widget-contacts-main intec-grid intec-grid-a-v-center" data-block-action="popup.open">
            <div class="widget-phone-content-wrapper intec-grid intec-grid-o-vertical">
                <?php if ($arResult['CONTACTS']['ADVANCED']) { ?>
                    <?php foreach ($arResult['CONTACTS']['SELECTED'] as $arContactItem) { ?>
                        <?php if (!empty($arContactItem['PHONE']['DISPLAY'])) { ?>
                            <a href="tel:<?=$arContactItem['PHONE']['VALUE']?>" class="tel">
                                <span class="value"><?= $arContactItem['PHONE']['DISPLAY'] ?></span>
                            </a>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <?php foreach ($arResult['CONTACTS']['SELECTED'] as $arContactItem) { ?>
                        <a href="tel:<?=$arContactItem['PHONE']['VALUE']?>" class="tel">
                            <span class="value"><?= $arContactItem['PHONE']['DISPLAY'] ?></span>
                        </a>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php if (!empty($arResult['CONTACTS']['VALUES'])) { ?>
                <?= FileHelper::getFileData(__DIR__.'/../svg/contacts.arrow.svg') ?>
            <?php } ?>
        </div>
        <?php if (!empty($arResult['CONTACTS']['VALUES'])) { ?>
            <div class="widget-contacts-advanced" data-block-element="popup">
                <div class="widget-contacts-advanced-items-wrap">
                    <div class="widget-contacts-advanced-items scrollbar-inner">
                        <?php if ($arResult['CONTACTS']['ADVANCED']) { ?>
                            <?php foreach ($arResult['CONTACTS']['VALUES'] as $arContact) { ?>
                                <div class="widget-contacts-advanced-item">
                                    <?php if (!empty($arContact['PHONE']['DISPLAY'])) { ?>
                                        <div class="widget-contacts-advanced-item-phone">
                                            <a href="tel:<?=$arContact['PHONE']['VALUE']?>" class="tel">
                                                <span class="value"><?= $arContact['PHONE']['DISPLAY'] ?></span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arContact['ADDRESS']) { ?>
                                        <div class="widget-contacts-advanced-item-address adr">
                                            <span class="locality"><?= $arContact['ADDRESS'] ?></span>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($arContact['SCHEDULE'])) { ?>
                                        <div class="widget-contacts-advanced-item-schedule">
                                            <?php if (Type::isArray($arContact['SCHEDULE'])) { ?>
                                                <span class="">
                                                    <?php foreach ($arContact['SCHEDULE'] as $sSchedule) { ?>
                                                        <div class="widget-contacts-advanced-item-schedule-item">
                                                            <?= $sSchedule ?>
                                                        </div>
                                                    <?php } ?>
                                                </span>
                                            <?php } else { ?>
                                                <div class="widget-contacts-advanced-item-schedule-item">
                                                    <?= $arContact['SCHEDULE'] ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($arContact['EMAIL'])) { ?>
                                        <div class="widget-contacts-advanced-item-email">
                                            <?= Html::tag('a', $arContact['EMAIL'], [
                                                'class' => [
                                                    'intec-cl-text',
                                                    'intec-cl-text-light-hover',
                                                    'email'
                                                ],
                                                'href' => 'mailto:'.$arContact['EMAIL']
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <?php foreach ($arResult['CONTACTS']['VALUES'] as $arContact) { ?>
                                <div class="widget-contacts-advanced-item">
                                    <div class="widget-contacts-advanced-item-phone">
                                        <a href="tel:<?= $arContact['VALUE'] ?>" class="tel">
                                            <span class="value"><?= $arContact['DISPLAY'] ?></span>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var root = data.nodes;
            var block = $('[data-block="phone"]', root);
            var popup = $('[data-block-element="popup"]', block);
            var scrollContacts = $('.widget-contacts-advanced-items', popup);

            popup.open = $('[data-block-action="popup.open"]', block);
            popup.open.on('mouseenter', function () {
                block.attr('data-expanded', 'true');
            });

            block.on('mouseleave', function () {
                block.attr('data-expanded', 'false');
            });

            scrollContacts.scrollbar();
        }, {
            'name': '[Component] intec.universe:main.header (template.1) > desktop (template.10) > phone.expand',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>