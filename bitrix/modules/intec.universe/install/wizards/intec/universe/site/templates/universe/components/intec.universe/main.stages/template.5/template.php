<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$bHide = count($arResult['ITEMS']) > 3;

if ($arVisual['VIEW'] === '1')
    $bHide = false;

$iCounter = 0;

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-stages',
        'c-stages-template-5'
    ],
    'id' => $sTemplateId,
    'data-view' => $arVisual['VIEW']
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-content'
                ]
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'intec-grid' => [
                            '' => $arVisual['VIEW'] === '1',
                            'wrap' => $arVisual['VIEW'] === '1',
                            'a-v-center' => $arVisual['VIEW'] === '1',
                            'a-h-left' => $arVisual['VIEW'] === '1',
                            'i-20' => $arVisual['VIEW'] === '1'
                        ]
                    ], true),
                    'data' => [
                        'role' => 'items',
                        'hide-items' => $bHide ? 'true' : 'false',
                        'state' => $bHide ? 'collapsed' : null
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $arData = $arItem['DATA'];

                    $iCounter++;

                    ?>
                        <?= Html::beginTag('div', [
                            'id' => $sAreaId,
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1200-3' => $arVisual['COLUMNS'] >= 4,
                                    '768-2' => true,
                                    '500-1' => true
                                ]
                            ], true),
                            'data' => [
                                'role' => 'item',
                                'action' => $bHide && $iCounter > 3 ? 'hide': 'show'
                            ]
                        ]) ?>
                            <div class="widget-item-wrap intec-grid intec-grid-1024-wrap">
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-item-name' => true,
                                        'intec-grid-item' => [
                                            'auto' => $arVisual['VIEW'] === '2',
                                            '1024-1' => $arVisual['VIEW'] === '2',
                                        ]
                                    ], true)
                                ]) ?>
                                    <div class="intec-grid intec-grid-a-v-center">
                                        <div class="intec-grid-item-auto">
                                            <div class="widget-item-name-count intec-cl-border">
                                                <?= $iCounter ?>
                                            </div>
                                        </div>
                                        <div class="intec-grid-item">
                                            <div class="widget-item-name-text" data-size="<?= $arVisual['NAME']['SIZE'] ?>">
                                                <?= Html::decode($arItem['NAME']) ?>
                                            </div>
                                            <?php if (!empty($arData['TIME']['VALUE'])) { ?>
                                                <div class="widget-item-name-time">
                                                    <div class="intec-grid">
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-item-name-time-icon"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                                <?php if ($arVisual['VIEW'] === '2') { ?>
                                    <div class="widget-item-description intec-grid-item intec-grid-item-1024-1">
                                        <?= $arItem[$arVisual['TEXT']['SOURCE']] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
            <?php if ($bHide && $iCounter > 3) { ?>
                <div class="widget-button-wrap">
                    <div class="widget-button intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current intec-ui-size-5 intec-ui-mod-transparent" data-role="button"></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($bHide && $iCounter > 3) include(__DIR__ . '/parts/script.php') ?>
<?= Html::endTag('div') ?>