<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<?php return function (&$arCaptcha) { ?>
    <div class="reviews-form-captcha">
        <?= Html::hiddenInput($arCaptcha['SID']['NAME'], $arCaptcha['SID']['VALUE']) ?>
        <div class="reviews-form-captcha-content intec-ui-form-field intec-ui-form-field-required">
            <span class="intec-ui-form-field-title">
                <?= $arCaptcha['WORD']['CAPTION'] ?>
            </span>
            <label class="reviews-form-captcha-field intec-ui-form-field-content">
                <?= Html::beginTag('span', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'nowrap',
                            'a-v-center',
                            'i-h-4',
                            'i-v-2',
                            '500-wrap'
                        ]
                    ]
                ]) ?>
                    <span class="intec-grid-item-auto">
                        <span class="reviews-form-captcha-input">
                            <?= Html::input('text', $arCaptcha['WORD']['NAME'], null, [
                                'class' => [
                                    'intec-ui' => [
                                        '',
                                        'control-input',
                                        'size-2',
                                        'mod-block',
                                        'mod-round-2'
                                    ]
                                ],
                                'data-error' => !empty($arCaptcha['WORD']['ERROR']) ? 'true' : 'false'
                            ]) ?>
                        </span>
                    </span>
                    <span class="intec-grid-item-auto">
                        <span class="reviews-form-captcha-picture intec-ui-picture">
                            <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arCaptcha['SID']['VALUE'], [
                                'alt' => '',
                                'title' => '',
                                'loading' => 'lazy'
                            ]) ?>
                        </span>
                    </span>
                <?= Html::endTag('span') ?>
            </label>
        </div>
    </div>
<?php } ?>