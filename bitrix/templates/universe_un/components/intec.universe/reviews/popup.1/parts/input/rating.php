<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

?>
<?php return function (&$field) {

    $svg = FileHelper::getFileData(__DIR__.'/../../svg/rating.svg');
    $counter = 0;

?>
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
            <span class="reviews-rating reviews-field-input" data-role="form.rating">
                <?= Html::hiddenInput($field['NAME'], $field['VALUE'], [
                    'data-role' => 'rating.input'
                ]) ?>
                <span class="intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                    <span class="intec-grid-item-auto">
                        <span class="intec-grid intec-grid-i-h-2">
                            <?php foreach ($field['OPTIONS'] as $id => $option) { ?>
                                <span class="intec-grid-item-auto">
                                    <?= Html::tag('span', $svg, [
                                        'class' => 'reviews-rating-item',
                                        'title' => $option,
                                        'data' => [
                                            'role' => 'rating.item',
                                            'index' => $counter++,
                                            'hover' => 'false',
                                            'active' => 'false',
                                            'value' => $id
                                        ]
                                    ]) ?>
                                </span>
                            <?php } ?>
                        </span>
                    </span>
                    <span class="intec-grid-item-auto" data-role="rating.information">
                        <span class="intec-grid intec-grid-i-h-4 intec-grid-a-v-center">
                            <span class="intec-grid-item-auto">
                                <span class="reviews-rating-caption-separator"></span>
                            </span>
                            <span class="intec-grid-item-auto">
                                <span class="reviews-rating-caption" data-role="rating.caption"></span>
                            </span>
                        </span>
                    </span>
                </span>
            </span>
        </label>
    </div>
<?php } ?>