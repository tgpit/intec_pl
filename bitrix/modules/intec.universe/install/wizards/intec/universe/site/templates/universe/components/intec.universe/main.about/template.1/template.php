<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEM']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$sId = $sTemplateId.'_'.$arResult['ITEM']['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['ITEM']['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['ITEM']['DELETE_LINK']);

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'VIDEO' => FileHelper::getFileData(__DIR__.'/svg/video.play.svg')
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'widget c-about-ref c-about-ref-template-1',
    'data-background' => $arVisual['BACKGROUND']['SHOW'] ? 'true' : null
]) ?>
    <?= Html::beginTag('div', [
        'class' => 'widget-content',
        'data' => [
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] && $arVisual['BACKGROUND']['SHOW'] ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] && $arVisual['BACKGROUND']['SHOW'] ? $arResult['BACKGROUND']['SRC'] : null
        ],
        'style' => [
            'background-image' => $arVisual['BACKGROUND']['SHOW'] ? 'url(\''.(
                $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arResult['BACKGROUND']['SRC']
            ).'\')' : null
        ]
    ]) ?>
        <div class="intec-content intec-content-primary intec-content-visible">
            <div class="intec-content-wrapper">
                <div class="widget-content-text" id="<?= $sAreaId ?>">
                    <?php if ($arVisual['TITLE']['SHOW']) { ?>
                        <div class="widget-title">
                            <?= $arResult['TITLE'] ?>
                        </div>
                    <?php } ?>
                    <div class="widget-name">
                        <?= $arResult['ITEM']['NAME'] ?>
                    </div>
                    <?php if ($arVisual['PREVIEW']['SHOW']) { ?>
                        <div class="widget-preview">
                            <?= $arResult['ITEM']['PREVIEW_TEXT'] ?>
                        </div>
                    <?php } ?>
                    <?php if ($arVisual['BUTTON']['SHOW']) {

                        if (empty($arVisual['BUTTON']['TEXT']))
                            $arVisual['BUTTON']['TEXT'] = Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_TEMPLATE_BUTTON_TEXT_DEFAULT');

                    ?>
                        <div class="widget-buttons">
                            <?= Html::tag('a', $arVisual['BUTTON']['TEXT'], [
                                'class' => [
                                    'widget-button',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'size-2',
                                        'mod-round-2',
                                        'scheme-current'
                                    ]
                                ],
                                'href' => $arResult['LINK'],
                                'target' => $arVisual['BUTTON']['BLANK'] ? '_blank' : null
                            ]) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if ($arVisual['PICTURE']['SHOW'] || $arVisual['VIDEO']['SHOW']) { ?>
            <div class="widget-content-additional" data-role="video">
                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                    <?= Html::tag('div', null, [
                        'class' => 'widget-picture',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true': 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arResult['PICTURE']['SRC'] : null
                        ],
                        'style' => [
                            'background-size' => $arVisual['PICTURE']['SIZE'],
                            'background-position' => $arVisual['PICTURE']['POSITION']['VERTICAL'].' '.$arVisual['PICTURE']['POSITION']['HORIZONTAL'],
                            'background-image' => 'url(\''.(
                                $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arResult['PICTURE']['SRC']
                            ).'\')'
                        ]
                    ]) ?>
                <?php } ?>
                <?php if ($arVisual['VIDEO']['SHOW']) { ?>
                    <?= Html::tag('div', $arSvg['VIDEO'], [
                        'class' => 'widget-video',
                        'title' => Loc::getMessage('C_MAIN_ABOUT_TEMPLATE_1_TEMPLATE_VIDEO_TITLE'),
                        'data' => [
                            'play' => '',
                            'src' => $arResult['VIDEO']
                        ]
                    ]) ?>
                <?php } ?>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
    <?php if ($arVisual['VIDEO']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>