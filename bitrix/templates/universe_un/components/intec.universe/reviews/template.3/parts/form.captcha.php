<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function (&$arCaptcha) { ?>
    <?php if (empty($arCaptcha))
        return;
    ?>
    <div class="reviews-form-section">
        <?= Html::hiddenInput($arCaptcha['SID']['NAME'], $arCaptcha['SID']['VALUE']) ?>
        <label class="reviews-form-input">
            <span class="reviews-form-input-part">
                <span class="reviews-form-input-caption">
                    <?= Html::tag('span', $arCaptcha['WORD']['CAPTION']) ?>
                    <?php if ($arCaptcha['WORD']['REQUIRED']) { ?>
                        <?= Html::tag('span', '*', [
                            'class' => 'reviews-form-input-caption-required'
                        ]) ?>
                    <?php } ?>
                </span>
            </span>
            <span class="reviews-form-input-part">
                <span class="reviews-form-input-control reviews-form-input-captcha-part">
                    <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arCaptcha['SID']['VALUE']) ?>
                </span>
                <span class="reviews-form-input-control reviews-form-input-captcha-part">
                    <?= Html::input('text', $arCaptcha['WORD']['NAME'], null, [
                        'class' => Html::cssClassFromArray([
                            'reviews-form-input-control-required' => !empty($arCaptcha['WORD']['ERROR']),
                            'intec-ui' => [
                                '' => true,
                                'control-input' => true,
                                'size-4' => true,
                                'mod-block' => true,
                                'mod-round-2' => true
                            ]
                        ], true)
                    ]) ?>
                </span>
            </span>
        </label>
    </div>
<?php } ?>