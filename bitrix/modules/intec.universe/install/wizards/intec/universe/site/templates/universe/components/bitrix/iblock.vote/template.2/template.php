<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this).$arParams['MESSAGE_404']);

$sVoteDisplayValue = null;

if ($arParams['DISPLAY_AS_RATING'] === 'vote_avg') {
    if ($arResult['PROPERTIES']['vote_count']['VALUE'])
        $sVoteDisplayValue = round($arResult['PROPERTIES']['vote_sum']['VALUE'] / $arResult['PROPERTIES']['vote_count']['VALUE'], 2);
    else
        $sVoteDisplayValue = 0;
} else {
    $sVoteDisplayValue = Type::toFloat($arResult['PROPERTIES']['rating']['VALUE']);
}

$sVoteDisplayValue = Type::toFloat($sVoteDisplayValue);

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-iblock-vote',
        'c-iblock-vote-template-2'
    ],
    'data-id' => $arResult['ID']
]) ?>
    <div class="iblock-vote-body" data-role="container">
        <div class="iblock-vote-container">
            <?php foreach ($arResult['VOTE_NAMES'] as $key => $sName) { ?>
                <?= Html::tag('div', FileHelper::getFileData(__DIR__.'/svg/star.svg'), [
                    'class' => 'iblock-vote-item',
                    'title' => Loc::getMessage('C_IBLOCK_VOTE_TEMPLATE_2_TEMPLATE_TITLE', [
                        '#VOTED#' => $arResult['VOTED'] ? Loc::getMessage('C_IBLOCK_VOTE_TEMPLATE_2_TEMPLATE_TITLE_VOTED').' ' : null,
                        '#RATING#' => $sVoteDisplayValue ? $sVoteDisplayValue : 0,
                        '#COUNT#' => $arResult['PROPERTIES']['vote_count']['VALUE'] ? $arResult['PROPERTIES']['vote_count']['VALUE'] : 0
                    ]),
                    'data' => [
                        'role' => 'container.vote',
                        'active' => ($sVoteDisplayValue && round($sVoteDisplayValue) > $key) ? 'true' : 'false',
                        'value' => $key
                    ]
                ]) ?>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
