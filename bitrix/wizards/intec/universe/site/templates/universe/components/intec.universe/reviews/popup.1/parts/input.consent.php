<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<div class="reviews-field reviews-field-consent">
    <label class="reviews-field-label">
        <span class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
            <span class="intec-grid-item-auto">
                <?= Html::beginTag('span', [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-switch',
                            'scheme-current',
                            'size-4'
                        ]
                    ]
                ]) ?>
                    <?= Html::checkbox('CONSENT', !empty($arResult['FORM']['CONSENT']) ? $arResult['FORM']['CONSENT'] : false, [
                        'data-role' => 'form.consent'
                    ]) ?>
                    <span class="intec-ui-part-selector"></span>
                <?= Html::endTag('span') ?>
            </span>
            <span class="intec-grid-item">
                <span class="reviews-field-name">
                    <span class="reviews-field-name-value">
                        <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_FORM_CONSENT_URL', [
                            '#URL#' => $arVisual['CONSENT']['URL']
                        ]) ?>
                    </span>
                </span>
            </span>
        </span>
    </label>
</div>