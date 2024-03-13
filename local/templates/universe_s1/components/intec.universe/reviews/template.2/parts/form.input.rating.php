<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arFormSvg
 */

?>
<?php return function (&$arField) use (&$arFormSvg) {

    $iCounter = 0;

?>
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
            <span class="reviews-form-input-rating" data-role="form.grade">
                <span class="reviews-form-input-rating-action reviews-form-input-rating-part">
                    <?= Html::hiddenInput($arField['NAME'], $arField['VALUE'], [
                        'data-role' => 'form.grade.input'
                    ]) ?>
                    <?php foreach ($arField['OPTIONS'] as $id => $sOption) { ?>
                        <?= Html::tag('span', $arFormSvg['RATING'], [
                            'class' => 'reviews-form-input-rating-item',
                            'title' => $sOption,
                            'data' => [
                                'role' => 'form.grade.item',
                                'index' => $iCounter++,
                                'hover' => 'false',
                                'active' => 'false',
                                'value' => $id
                            ]
                        ]) ?>
                    <?php } ?>
                </span>
                <?= Html::beginTag('span', [
                    'class' => [
                        'reviews-form-input-rating-information',
                        'reviews-form-input-rating-part'
                    ],
                    'data' => [
                        'role' => 'form.grade.information',
                        'active' => 'false'
                    ]
                ]) ?>
                    <?= Html::tag('span', Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_GRADE_SEPARATOR'), [
                        'class' => 'reviews-form-input-rating-information-separator'
                    ]) ?>
                    <?= Html::tag('span', null, [
                        'class' => 'reviews-form-input-rating-information-title',
                        'data-role' => 'form.grade.information.title'
                    ]) ?>
                <?= Html::endTag('span') ?>
            </span>
        </span>
    </label>
<?php } ?>