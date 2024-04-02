<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function (&$arField) { ?>
    <label class="reviews-form-input">
        <span class="reviews-form-input-part">
            <span class="reviews-form-input-caption">
                <?= Html::tag('span', $arField['CAPTION']) ?>
                <?php if ($arField['REQUIRED']) { ?>
                    <?= Html::tag('span', '*', [
                        'class' => 'reviews-form-input-caption-required'
                    ]) ?>
                <?php } ?>
            </span>
        </span>
        <span class="reviews-form-input-part">
            <span class="reviews-form-input-control">
                <?= Html::textarea($arField['NAME'], $arField['VALUE'], [
                    'class' => Html::cssClassFromArray([
                        'reviews-form-input-control-required' => !empty($arField['ERROR']),
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
<?php } ?>