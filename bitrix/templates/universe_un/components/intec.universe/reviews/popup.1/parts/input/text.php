<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function (&$field) { ?>
    <div class="reviews-field">
        <label class="reviews-field-label">
            <span class="reviews-field-name">
                <span class="reviews-field-name-value">
                    <?= $field['CAPTION'] ?>
                </span>
                <?php if ($field['REQUIRED']) { ?>
                    <span class="reviews-field-name-required">
                        *
                    </span>
                <?php } ?>
            </span>
            <?= Html::input('text', $field['NAME'], $field['VALUE'], [
                'class' => Html::cssClassFromArray([
                    'reviews-field-input' => true,
                    'reviews-field-input-error' => !empty($field['ERROR']),
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
<?php } ?>