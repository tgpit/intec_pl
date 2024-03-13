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
            'template-1'
        ]
    ],
    'data-id' => $arResult['ID']
]) ?>
    <?php $oFrame->begin() ?>
        <div class="iblock-vote-rating" data-role="container">
            <?php foreach ($arResult['VOTE_NAMES'] as $key => $sName) { ?>
                <?= Html::Tag('i', '', [
                    'class' => 'iblock-vote-rating-item intec-ui-icon intec-ui-icon-star-1',
                    'data-role' => 'container.vote',
                    'data-active' => ($sVoteDisplayValue && round($sVoteDisplayValue) > $key) ? 'true' : 'false',
                    'data-value' => $key,
                    'title' => $sName
                ])?>
            <?php } ?>
            <?php if ($arParams['SHOW_RATING'] == 'Y' && $sVoteDisplayValue) { ?>
                <div class="iblock-vote-rating-total">
                    <?= $sVoteDisplayValue ?>
                </div>
            <?php } ?>
        </div>
        <?php include(__DIR__.'/parts/script.php') ?>
    <?php $oFrame->beginStub() ?>
        <div class="iblock-vote-rating" data-role="container">
            <?php foreach ($arResult['VOTE_NAMES'] as $key => $sName) { ?>
                <?= Html::Tag('i', '', [
                    'class' => 'iblock-vote-rating-item intec-ui-icon intec-ui-icon-star-1',
                    'data-role' => 'container.vote',
                    'data-active' => 'false',
                    'data-value' => $key,
                    'title' => $sName
                ])?>
            <?php } ?>
            <?php if ($arParams['SHOW_RATING'] == 'Y') { ?>
                <div class="iblock-vote-rating-total">
                    0
                </div>
            <?php } ?>
        </div>
    <?php $oFrame->end() ?>
<?= Html::endTag('div') ?>