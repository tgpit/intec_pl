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

$sTemplatePrefix = null;

if ($arResult['VISUAL']['POPUP']['SHOW'])
    $sTemplatePrefix = 'popup';

$sTemplateId = Html::getUniqueId($sTemplatePrefix, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-stories',
        'c-stories-template-1'
    ],
    'id' => $sTemplateId,
    'data-view' => $arVisual['POPUP']['SHOW'] ? 'popup' : $arVisual['LIST']['VIEW']
]) ?>
    <?php if (!$arVisual['POPUP']['SHOW']) { ?>
        <div class="widget-wrapper intec-content intec-content-visible">
            <div class="widget-wrapper-2 intec-content-wrapper">
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['LIST']['BUTTONS']['MORE']['SHOW']) { ?>
                    <div class="widget-header">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="widget-title-container intec-grid-item">
                                    <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                        'class' => [
                                            'widget-title',
                                            'align-'.$arBlocks['HEADER']['POSITION'],
                                            $arVisual['LIST']['BUTTONS']['MORE']['SHOW'] ? 'widget-title-margin' : null
                                        ]
                                    ]) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['LIST']['BUTTONS']['MORE']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-all-container' => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'],
                                        'intec-grid-item' => [
                                            'auto' => $arBlocks['HEADER']['SHOW'],
                                            '1' => !$arBlocks['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-all-button',
                                            'intec-cl-text-light-hover',
                                        ],
                                        'data-role' => 'show.more'
                                    ])?>
                                        <span><?= $arVisual['LIST']['BUTTONS']['MORE']['TEXT'] ?></span>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="widget-description-container intec-grid-item-1">
                                    <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                        <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-content'
                    ]
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-items',
                            'owl-carousel'
                        ],
                        'data' => [
                            'role' => 'slider',
                            'navigation' => $arVisual['LIST']['BUTTONS']['NAVIGATION']['SHOW'] ? 'true' : 'false'
                        ]
                    ])?>
                    <?php foreach ($arResult['SECTIONS'] as $arSection) {
                            $sPicture = $arSection['PICTURE']['SRC'];

                            if (empty($sPicture))
                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        ?>
                        <div class="widget-item intec-image-effect intec-cl-text-hover" data-role="story.item" data-id="<?= $arSection['ID'] ?>">
                            <div class="widget-item-picture" style="background-image: url('<?= $sPicture ?>')"></div>
                            <div class="widget-item-title">
                                <?= $arSection['NAME'] ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?= Html::endTag('div') ?>
                    <?php if ($arVisual['LIST']['BUTTONS']['NAVIGATION']['SHOW']) { ?>
                        <?= Html::tag('div','', [
                            'class'=>'widget-items-navigation',
                            'data'=>[
                                'role'=>'slider.navigation'
                            ]
                        ]) ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    <?php include(__DIR__ . '/parts/script.php') ?>
    <?php } else { ?>
        <?php include(__DIR__ . '/parts/preloader.php') ?>
        <?php include(__DIR__ . '/parts/popup.php') ?>
        <?php include(__DIR__ . '/parts/popup.script.php') ?>
    <?php } ?>
<?= Html::endTag('div') ?>