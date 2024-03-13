<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$sVoteDisplayValue = null;

if ($arParams['DISPLAY_AS_RATING'] === 'vote_avg') {
	if ($arResult['PROPERTIES']['vote_count']['VALUE'])
        $sVoteDisplayValue = round(
            $arResult['PROPERTIES']['vote_sum']['VALUE'] / $arResult['PROPERTIES']['vote_count']['VALUE'],
            2
        );
	else
        $sVoteDisplayValue = 0;
} else {
    $sVoteDisplayValue = Type::toFloat($arResult['PROPERTIES']['rating']['VALUE']);
}

$sVoteDisplayValue = Type::toFloat($sVoteDisplayValue);

$oFrame = $this->createFrame();

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-iblock-vote' => [
            '',
            'template-3'
        ]
    ],
    'data-id' => $arResult['ID']
]) ?>
    <?php $oFrame->begin() ?>
        <div class="iblock-vote-rating" data-role="container">
            <?php foreach ($arResult['VOTE_NAMES'] as $key => $sName) { ?>
                <?= Html::beginTag('div', [
                    'class' => 'iblock-vote-rating-item',
                    'data-role' => 'container.vote',
                    'data-active' => ($sVoteDisplayValue && round($sVoteDisplayValue) > $key) ? 'true' : 'false',
                    'data-value' => $key,
                    'title' => $sName
                ])?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.99822 12.8067L11.3407 14.5633C11.9916 14.905 12.7516 14.3525 12.6274 13.6283L11.9891 9.90666L14.6941 7.2725C15.2207 6.75916 14.9307 5.865 14.2024 5.75916L10.4657 5.21583L8.79489 1.82833C8.46989 1.16916 7.52905 1.16916 7.20405 1.82833L5.53239 5.21583L1.79572 5.75916C1.06822 5.865 0.777388 6.75916 1.30405 7.2725L4.00905 9.90666L3.37072 13.6283C3.24655 14.3525 4.00655 14.9058 4.65739 14.5633L7.99989 12.8067H7.99822Z" fill="#E8E8E8" stroke="#E8E8E8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arParams['SHOW_RATING'] == 'Y' && $sVoteDisplayValue) { ?>
                <div class="iblock-vote-rating-total">
                    <?= $sVoteDisplayValue ?>
                </div>
            <?php } ?>
        </div>
    <?php $oFrame->beginStub() ?>
        <div class="iblock-vote-rating" data-role="container">
            <?php foreach ($arResult['VOTE_NAMES'] as $key => $sName) { ?>
                <?= Html::beginTag('div', [
                    'class' => 'iblock-vote-rating-item',
                    'data-role' => 'container.vote',
                    'data-active' => 'false',
                    'data-value' => $key,
                    'title' => $sName
                ])?>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.99822 12.8067L11.3407 14.5633C11.9916 14.905 12.7516 14.3525 12.6274 13.6283L11.9891 9.90666L14.6941 7.2725C15.2207 6.75916 14.9307 5.865 14.2024 5.75916L10.4657 5.21583L8.79489 1.82833C8.46989 1.16916 7.52905 1.16916 7.20405 1.82833L5.53239 5.21583L1.79572 5.75916C1.06822 5.865 0.777388 6.75916 1.30405 7.2725L4.00905 9.90666L3.37072 13.6283C3.24655 14.3525 4.00655 14.9058 4.65739 14.5633L7.99989 12.8067H7.99822Z" fill="#E8E8E8" stroke="#E8E8E8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arParams['SHOW_RATING'] == 'Y') { ?>
                <div class="iblock-vote-rating-total">
                    0
                </div>
            <?php } ?>
        </div>
    <?php $oFrame->end() ?>
<?= Html::endTag('div') ?>