<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<div class="reviews-field reviews-field-captcha">
    <?= Html::hiddenInput($arResult['FORM']['CAPTCHA']['SID']['NAME'], $arResult['FORM']['CAPTCHA']['SID']['VALUE']) ?>
    <label class="reviews-field-label">
        <span class="reviews-field-name">
            <span class="reviews-field-name-value">
                <?= $arResult['FORM']['CAPTCHA']['WORD']['CAPTION'] ?>
            </span>
            <?php if ($arResult['FORM']['CAPTCHA']['WORD']['REQUIRED']) { ?>
                <span class="reviews-field-name-required">
                    *
                </span>
            <?php } ?>
        </span>
        <span class="reviews-field-picture intec-ui-picture">
            <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arResult['FORM']['CAPTCHA']['SID']['VALUE']) ?>
        </span>
        <?= Html::input('text', $arResult['FORM']['CAPTCHA']['WORD']['NAME'], null, [
            'class' => Html::cssClassFromArray([
                'reviews-field-input' => true,
                'reviews-field-input-error' => !empty($arResult['FORM']['CAPTCHA']['WORD']['ERROR']),
                'intec-ui' => [
                    '' => true,
                    'control-input' => true,
                    'size-4' => true,
                    'mod-block' => true,
                    'mod-round-2' => true
                ]
            ], true)
        ]) ?>
    </label>
</div>