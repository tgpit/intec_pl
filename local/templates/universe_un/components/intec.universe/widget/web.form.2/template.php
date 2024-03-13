<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['WEB_FORM']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="widget-web-form-2 intec-cl-background" id="<?= $sTemplateId ?>" data-print="false">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="widget-web-form-2-wrapper">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a' => [
                                'h-start',
                                'v-center'
                            ],
                            'i' => [
                                'h-25',
                                'v-20'
                            ]
                        ]
                    ]
                ]) ?>
                    <div class="intec-grid-item-5 intec-grid-item-1024-3 intec-grid-item-768-1">
                        <div class="widget-web-form-2-image"></div>
                        <div class="widget-web-form-2-title">
                            <?= $arResult['TEXT']['TITLE'] ?>
                        </div>
                    </div>
                    <div class="intec-grid-item intec-grid-item-768-1">
                        <div class="widget-web-form-2-description">
                            <?= $arResult['TEXT']['DESCRIPTION'] ?>
                        </div>
                    </div>
                    <div class="intec-grid-item-auto intec-grid-item-768-1">
                        <?= Html::tag('div', $arResult['TEXT']['BUTTON'], [
                            'class' => [
                                'widget-web-form-2-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'size-4',
                                    'mod-transparent',
                                    'mod-round-2'
                                ]
                            ],
                            'data-role' => 'form'
                        ]) ?>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
