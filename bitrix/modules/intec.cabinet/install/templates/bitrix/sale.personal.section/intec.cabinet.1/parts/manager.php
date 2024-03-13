<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;

/**
 * @var array $arManager
 * @var array $arSvg
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>

<div class="sale-personal-section-manager">
    <div class="sale-personal-section-manager-header">
        <div class="sale-personal-section-manager-title">
            <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_MANAGER_TITLE') ?>
        </div>
    </div>
    <div class="sale-personal-section-manager-wrap">
        <div class="intec-grid intec-grid-wrap intec-grid-i-12 intec-grid-a-h-between intec-grid-a-v-start">
            <div class="intec-grid-item-2 intec-grid-item-550-1 intec-grid intec-grid-nowrap intec-grid-i-8 intec-grid-a-h-start intec-grid-a-v-center">
                <div class="intec-grid-item-auto">
                    <div class="sale-personal-section-manager-picture intec-ui-picture">
                        <img src="<?= $arManager['PICTURE'] ?>" alt="<?= $arManager['NAME'] ?>" />
                    </div>
                </div>
                <div class="intec-grid-item">
                    <div class="sale-personal-section-manager-name">
                        <?= $arManager['NAME'] ?>
                    </div>
                    <?php if (!empty($arManager['MANAGER_PROPERTY']['POSITION'])) { ?>
                        <div class="sale-personal-section-manager-position">
                            <?= $arManager['MANAGER_PROPERTY']['POSITION'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="intec-grid-item-2 intec-grid-item-550-1">
                <?php if (!empty($arManager['MANAGER_PROPERTY']['PHONE'])) { ?>
                    <?= Html::beginTag('a', [
                        'class' => [
                            'sale-personal-section-manager-contact',
                            'intec-cl-text',
                            'intec-grid' => [
                                '',
                                'nowrap',
                                'i-h-4',
                                'a-h-start',
                                'a-v-center'
                            ]
                        ],
                        'href' => 'tel:'.StringHelper::replace($arManager['MANAGER_PROPERTY']['PHONE'], ['(' => '', ')' => '', ' ' => '', '-' => ''])
                    ]) ?>
                        <div class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-fill">
                            <?= $arSvg['PHONE'] ?>
                        </div>
                        <div class="intec-grid-item">
                            <?= $arManager['MANAGER_PROPERTY']['PHONE'] ?>
                        </div>
                    <?= Html::endTag('a') ?>
                <?php } ?>
                <?php if (!empty($arManager['MANAGER_PROPERTY']['EMAIL'])) { ?>
                    <?= Html::beginTag('a', [
                        'class' => [
                            'sale-personal-section-manager-contact',
                            'intec-cl-text',
                            'intec-grid' => [
                                '',
                                'nowrap',
                                'i-h-4',
                                'a-h-start',
                                'a-v-center'
                            ]
                        ],
                        'href' => 'mailto:'.$arManager['MANAGER_PROPERTY']['EMAIL']
                    ]) ?>
                    <div class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-fill">
                        <?= $arSvg['EMAIL'] ?>
                    </div>
                    <div class="intec-grid-item">
                        <?= $arManager['MANAGER_PROPERTY']['EMAIL'] ?>
                    </div>
                    <?= Html::endTag('a') ?>
                <?php } ?>
                <div class="sale-personal-section-manager-contact-icons intec-grid intec-grid-wrap intec-grid-i-4 intec-grid-a-h-start intec-grid-a-v-center">
                    <?php foreach ($arManager['MANAGER_PROPERTY'] as $propKey => $propValue) { ?>
                        <?php if (StringHelper::startsWith($propKey, 'SOCIAL') && !empty($arManager['MANAGER_PROPERTY'][$propKey])) { ?>
                            <a href="<?= $propValue ?>" target="_blank" class="intec-grid-item-auto intec-ui-picture intec-cl-svg-path-fill-hover">
                                <?= $arSvg[$propKey] ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>