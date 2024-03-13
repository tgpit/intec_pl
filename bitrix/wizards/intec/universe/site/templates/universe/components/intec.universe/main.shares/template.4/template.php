<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arHeader = ArrayHelper::getValue($arResult, 'HEADER_BLOCK');
$arDescription = ArrayHelper::getValue($arResult, 'DESCRIPTION_BLOCK');
$arFooter = ArrayHelper::getValue($arResult, 'FOOTER_BLOCK');
$arVisual = ArrayHelper::getValue($arResult, 'VISUAL');

$arNavigation = $arResult['NAVIGATION'];

$iElementCount = 1;
$arClasses = [];

$sElementTag = $arVisual['LINK_USE']?'a':'div';
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-shares',
        'c-shares-template-4'
    ],
    'data' => [
        'column' => $arVisual['COLUMNS']
    ]
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arHeader['SHOW'] || $arDescription['SHOW'] || ($arFooter['SHOW'] && $arFooter['PLACE'] === 'top')) { ?>
                <div class="widget-header" data-link-all="<?= $arFooter['SHOW'] && $arFooter['PLACE'] === 'top' ? 'true' : 'false' ?>">
                    <div class="intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                        <div class="intec-grid-item">
                            <?php if ($arHeader['SHOW']) { ?>
                                <?= Html::tag('div', Html::encode($arHeader['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-' . $arHeader['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?php if ($arDescription['SHOW']) { ?>
                                <?= Html::tag('div', Html::encode($arDescription['TEXT']), [
                                    'class' => [
                                        'widget-description',
                                        'align-' . $arDescription['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                        <?php if ($arFooter['SHOW'] && $arFooter['PLACE'] === 'top') { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'widget-all-button',
                                        'intec-cl-text-light-hover'
                                    ],
                                    'href' => $arFooter['LIST_PAGE']
                                ])?>
                                <span><?= $arFooter['TEXT'] ?></span>
                                <i class="fal fa-angle-right"></i>
                                <?= Html::endTag('a')?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-content',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-v-start',
                        'a-h-start',
                        'i-7'
                    ]
                ]),
                'data-role' => 'items'
            ]) ?>
                <!--items-->
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $sDetailPageUrl = ArrayHelper::getValue($arItem, 'DETAIL_PAGE_URL');

                    $arData = $arItem['DATA'];

                    $sName = ArrayHelper::getValue($arItem, 'NAME');

                    if (!empty($arData['TITLE']))
                        $sName = $arData['TITLE'];

                    if (!empty($arData['PICTURE'])) {
                        $sPicture = $arData['PICTURE'];
                    } elseif (!empty($arItem['PREVIEW_PICTURE'])) {
                        $sPicture = $arItem['PREVIEW_PICTURE'];
                    } else {
                        $sPicture = $arItem['DETAIL_PICTURE'];
                    }

                    if (!empty($sPicture)) {
                        $sPicture = CFile::ResizeImageGet($sPicture, [
                            'width' => 600,
                            'height' => 600
                        ], BX_RESIZE_IMAGE_PROPORTIONAL);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                    }

                    if (empty($sPicture))
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-item-wrap',
                            'intec-grid-item' => [
                                $arVisual['COLUMNS'],
                                '1100-2',
                                '720-1'
                            ]
                        ])
                    ]) ?>
                        <?= Html::beginTag($sElementTag,[
                            'class' => [
                                'widget-item',
                                'intec-cl-background-light'
                            ],
                            'id' => $sAreaId,
                            'href' => $sElementTag === 'a' ? $sDetailPageUrl : null,
                            'style' => [
                                'background-color' => !empty($arData['BACKGROUND']) ? $arData['BACKGROUND'].'!important' : null,
                            ]
                        ])?>
                            <?= Html::tag('div', '', [
                                'class' => [
                                    'widget-item-picture'
                                ],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ],
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                ]
                            ]) ?>
                            <div class="widget-item-text-wrap">
                                <div class="widget-item-text">
                                    <?if ($arData['STICK']['SHOW']) {?>
                                        <?if (!is_array($arData['STICK']['TEXT'])) {?>
                                            <?= Html::tag('div', $arData['STICK']['TEXT'],[
                                                'class' => [
                                                    'widget-item-stick',
                                                    'intec-cl-background-dark'
                                                ],
                                                'style' => [
                                                    'background-color' => !empty($arData['STICK']['BACKGROUND']) ? $arData['STICK']['BACKGROUND'].'!important' : null,
                                                ]
                                            ])?>
                                        <?} else {
                                            foreach ($arData['STICK']['TEXT'] as $key => $value) {
                                               echo Html::tag('div', $value, [
                                                    'class' => [
                                                        'widget-item-stick',
                                                        'intec-cl-background-dark'
                                                    ],
                                                    'style' => [
                                                        'background-color' => !empty($arData['STICK']['BACKGROUND']) ? $arData['STICK']['BACKGROUND'].'!important' : null,
                                                    ]
                                                ]);
                                                echo "<br>";
                                            }
                                        }?>
                                    <?}?>
                                    <div class="widget-item-name">
                                        <?= $sName ?>
                                    </div>
                                </div>
                            </div>
                         <?= Html::endTag($sElementTag) ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <!--items-->
            <?= Html::endTag('div') ?>
            <?php if ($arNavigation['USE']) { ?>
                <div class="widget-pagination" data-role="navigation">
                    <!--navigation-->
                    <?= $arNavigation['PRINT'] ?>
                    <!--navigation-->
                </div>
            <?php } ?>
            <?php if ($arFooter['SHOW'] && $arFooter['PLACE'] === 'bottom') { ?>
                <div class="widget-footer align-<?= $arFooter['POSITION'] ?>">
                    <?= Html::tag('a', $arFooter['TEXT'], [
                        'class' => [
                            'widget-footer-all',
                            'intec-cl-border',
                            'intec-cl-background-hover'
                        ],
                        'href' => $arFooter['LIST_PAGE']
                    ]) ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($arNavigation['USE'] && $arNavigation['MODE'] === 'ajax' && !defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>