<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="widget c-widget c-widget-web-form ask-question-container" id="<?= $sTemplateId ?>" data-print="false">
    <div class="web-form-container">
        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-v-15 intec-grid-i-h-25">
            <div class="web-form-name intec-grid-item-auto intec-grid-item-shrink-1 intec-grid-item-1024-1">
                <div class="web-form-name-container intec-grid intec-grid-a-v-center intec-grid-i-h-25">
                    <div class="intec-grid-item intec-grid-item-1024-1">
                        <div class="web-form-name-text">
                            <?= $arResult['WEB_FORM']['NAME'] ?>
                        </div>
                    </div>
                    <div class="web-form-name-decoration intec-grid-item-auto">
                        <div class="web-form-name-decoration-item intec-cl-background"></div>
                    </div>
                </div>
            </div>
            <div class="web-form-description intec-grid-item intec-grid-item-shrink-1 intec-grid-item-1024-1">
                <div class="web-form-description-text">
                    <?= $arResult['WEB_FORM']['DESCRIPTION'] ?>
                </div>
            </div>
            <div class="web-form-buttons intec-grid-item-auto intec-grid-item-shrink-1 intec-grid-item-1024-1">
                <?= Html::tag('button', $arResult['WEB_FORM']['BUTTON'], [
                    'id' => 'ask_question_button_'.$arResult['COMPONENT_HASH'],
                    'class' => [
                        'web-form-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'size-3',
                            'scheme-current',
                            'mod-round-3'
                        ]
                    ],
                    'data-role' => 'form'
                ]) ?>
            </div>
        </div>
    </div>
    <?php include (__DIR__.'/parts/script.php') ?>
</div>