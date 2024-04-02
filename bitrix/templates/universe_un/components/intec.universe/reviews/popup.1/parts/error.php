<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<?= Html::beginTag('div', [
    'class' => [
        'reviews-error',
        'intec-ui' => [
            '',
            'control-alert',
            'scheme-red'
        ]
    ]
]) ?>
    <?php foreach ($arResult['FORM']['ERROR'] as $error) { ?>
        <div class="reviews-error-item">
            <?php if ($error === 'required') { ?>
                <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_ERROR_REQUIRED') ?>
            <?php } else if ($error === 'captcha') { ?>
                <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_ERROR_CAPTCHA') ?>
            <?php }  else if ($error === 'add-failure') { ?>
                <?= Loc::getMessage('C_REVIEWS_POPUP_1_TEMPLATE_ERROR_ADD_FAILURE') ?>
            <?php } ?>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>