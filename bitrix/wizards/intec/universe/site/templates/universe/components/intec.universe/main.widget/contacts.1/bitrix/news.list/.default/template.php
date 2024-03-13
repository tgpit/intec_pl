<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="widget c-widget c-widget-contacts-1" id="<?= $sTemplateId ?>">
    <div class="widget-content">
        <div class="widget-items">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <div class="widget-items-container">
                        <div class="intec-grid intec-grid-768-wrap">
                            <?php if ($arResult['DATA']['FEEDBACK']['SHOW']) { ?>
                                <div class="widget-feedback-section intec-grid-item-auto intec-grid-item-a-center intec-grid-item-768-1">
                                    <?php include(__DIR__.'/parts/feedback.php') ?>
                                </div>
                            <?php } ?>
                            <div class="widget-items-section intec-grid-item intec-grid-item-a-stretch intec-grid-item-768-1">
                                <?php include(__DIR__.'/parts/items.php') ?>
                            </div>
                        </div>
                        <div class="widget-navigation" data-role="contacts.slider.navigation"></div>
                        <div class="widget-dots">
                            <div class="widget-dots-content" data-role="contacts.slider.dots"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?= Html::beginTag('div', [
            'class' => 'widget-map',
            'data' => [
                'role' => 'maps',
                'gray' => $arVisual['MAP']['GRAY'] ? 'true' : 'false'
            ]
        ]) ?>
            <?php include(__DIR__.'/parts/map.php') ?>
        <?= Html::endTag('div') ?>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
