<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

if (empty($arVisual['SUBMIT']['TEXT']))
    $arVisual['SUBMIT']['TEXT'] = Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_SUBMIT_TEXT_DEFAULT');

$inputText = include(__DIR__.'/parts/input/text.php');
$inputTextArea = include(__DIR__.'/parts/input/textarea.php');
$inputSelect = include(__DIR__.'/parts/input/select.php');
$inputRating = include(__DIR__.'/parts/input/rating.php');

?>
<div class="ns-intec-universe c-reviews c-reviews-popup-1" id="<?= $sTemplateId ?>">
    <?php if ($arResult['FORM']['USE']) { ?>
        <?php if ($arResult['FORM']['ACCESS']) { ?>
            <?php if ($arResult['FORM']['STATUS'] === 'empty') {?>
                <div class="reviews-form">
                    <?= Html::beginForm($APPLICATION->GetCurPageParam(), 'post', [
                        'data-role' => 'form'
                    ]) ?>
                        <?php if (!empty($arResult['FORM']['ERROR'])) { ?>
                            <div class="reviews-form-fragment">
                                <?php include(__DIR__.'/parts/error.php') ?>
                            </div>
                        <?php } ?>
                        <div class="reviews-form-fragment">
                            <?= bitrix_sessid_post() ?>
                            <?php foreach ($arResult['FORM']['FIELDS'] as $arField) {
                                if ($arField['TYPE'] === 'hidden') { ?>
                                    <?= Html::hiddenInput($arField['NAME'], $arField['VALUE']) ?>
                                <?php }
                            } ?>
                            <div class="reviews-form-fragment">
                                <?php foreach ($arResult['FORM']['FIELDS'] as $arField) { ?>
                                        <?php if ($arField['TYPE'] === 'text') { ?>
                                            <div class="reviews-form-section">
                                                <?php $inputText($arField) ?>
                                            </div>
                                        <?php } else if ($arField['TYPE'] === 'textarea') { ?>
                                            <div class="reviews-form-section">
                                                <?php $inputTextArea($arField) ?>
                                            </div>
                                        <?php } else if ($arField['TYPE'] === 'select') { ?>
                                            <div class="reviews-form-section">
                                                <?php if ($arVisual['RATING']['USE'] && $arField['NAME'] === $arVisual['RATING']['CODE'])
                                                    $inputRating($arField);
                                                else
                                                    $inputSelect($arField);
                                                ?>
                                            </div>
                                        <?php } ?>
                                <?php } ?>
                                <?php if ($arVisual['CONSENT']['SHOW']) { ?>
                                    <div class="reviews-form-section">
                                        <?php include(__DIR__.'/parts/input.consent.php') ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($arResult['FORM']['CAPTCHA'])) { ?>
                                    <div class="reviews-form-section">
                                        <?php include(__DIR__.'/parts/input.captcha.php') ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="reviews-form-fragment">
                                <div class="reviews-form-submit">
                                    <?= Html::submitButton($arVisual['SUBMIT']['TEXT'], [
                                        'class' => [
                                            'reviews-form-submit-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'scheme-current',
                                                'mod-round-2'
                                            ],
                                        ],
                                        'disabled' => $arVisual['CONSENT']['SHOW'],
                                        'data-role' => 'form.submit'
                                    ]) ?>
                                    <?php if ($arVisual['CONSENT']['SHOW']) { ?>
                                        <div class="reviews-form-submit-popup">
                                            <span class="reviews-form-submit-popup-content">
                                                <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_SUBMIT_TIP') ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?= Html::endForm() ?>
                </div>
            <?php } else if ($arResult['FORM']['STATUS'] === 'exists') { ?>
                <div class="reviews-message reviews-message-neutral">
                    <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_MESSAGE_EXISTS') ?>
                </div>
            <?php } else if ($arResult['FORM']['STATUS'] === 'added') { ?>
                <div class="reviews-message reviews-message-success">
                    <?php if ($arVisual['MODE'] === 'active') { ?>
                        <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_MESSAGE_ADDED_ACTIVE') ?>
                    <?php } else if ($arVisual['MODE'] === 'disabled') { ?>
                        <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_MESSAGE_ADDED_DISABLED') ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php if ($arVisual['CONSENT']['SHOW'] || $arVisual['RATING']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
    <script>
        //for adaptation window
        window.dispatchEvent(new Event('resize'));
    </script>
</div>