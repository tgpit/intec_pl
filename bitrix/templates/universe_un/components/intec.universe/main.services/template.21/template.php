<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$vChildrenRender = include(__DIR__.'/parts/children.php');

if ($arVisual['PICTURE']['SIZE'] === 'small') {
    $arPictureSize = [
        'width' => 80,
        'height' => 50
    ];
} else {
    $arPictureSize = [
        'width' => 160,
        'height' => 100
    ];
}
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-services',
        'c-services-template-21'
    ],
    'data' => [
        'picture-size' => $arVisual['PICTURE']['SIZE'],
        'svg-file-use' => $arVisual['SVG']['USE'] ? 'true' : 'false',
        'children-display' => $arVisual['CHILDREN']['DISPLAY']
    ]
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arBlocks['BUTTON']['SHOW']) { ?>
            <div class="widget-header">

                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-10 ">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title-container intec-grid-item">
                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                'class' => [
                                    'widget-title',
                                    'align-'.$arBlocks['HEADER']['POSITION'],
                                    $arBlocks['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['BUTTON']['SHOW']) { ?>
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
                                'href' => $arBlocks['BUTTON']['LINK']
                            ])?>
                                <span><?= $arBlocks['BUTTON']['TEXT'] ?></span>
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
            <div class="widget-content">
                <div class="widget-items intec-grid intec-grid-wrap intec-grid-a-v-sretch">
                    <?php foreach ($arResult['SECTIONS'] as $arSection) {

                        $sId = $sTemplateId.'_'.$arSection['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arSection['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

                        $arPicture = [
                            'TYPE' => 'picture',
                            'SOURCE' => null,
                            'ALT' => null,
                            'TITLE' => null
                        ];

                        if (!empty($arSection['PICTURE'])) {
                            if ($arSection['PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                                $arPicture['TYPE'] = 'svg';
                                $arPicture['SOURCE'] = $arSection['PICTURE']['SRC'];
                            } else {
                                $arPicture['SOURCE'] = CFile::ResizeImageGet($arSection['PICTURE'], $arPictureSize, BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                if (!empty($arPicture['SOURCE'])) {
                                    $arPicture['SOURCE'] = $arPicture['SOURCE']['src'];
                                } else {
                                    $arPicture['SOURCE'] = null;
                                }
                            }
                        }

                        if (empty($arPicture['SOURCE'])) {
                            $arPicture['TYPE'] = 'picture';
                            $arPicture['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        } else {
                            $arPicture['ALT'] = $arSection['PICTURE']['ALT'];
                            $arPicture['TITLE'] = $arSection['PICTURE']['TITLE'];
                        }
                        ?>
                        <?= Html::beginTag('div', [
                            'id' => $sAreaId,
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-2' => $arVisual['COLUMNS'] >= 3,
                                    '500-1' => true
                                ]
                            ], true),
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    Html::cssClassFromArray([
                                        'widget-item-wrapper' => true,
                                        'intec-grid' => [
                                            '' => true,
                                            'a-v-start' => $arVisual['PICTURE']['POSITION']['VERTICAL'] === 'top' ? true : false,
                                            'a-v-center' => $arVisual['PICTURE']['POSITION']['VERTICAL'] === 'center' ? true : false
                                        ]
                                    ], true)
                                ]
                            ])?>
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::beginTag('a', [
                                            'class' => 'widget-item-picture-wrap',
                                            'href' => $arSection['SECTION_PAGE_URL'],
                                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                        ]) ?>
                                            <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                                <?= Html::tag('div', FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                                    'class' => [
                                                        Html::cssClassFromArray([
                                                            'widget-item-picture' => true,
                                                            'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                                            'intec-image-effect' => true,
                                                        ], true)
                                                    ]
                                                ]) ?>
                                            <?php } else { ?>
                                                <?= Html::tag('div', null, [
                                                    'class' => [
                                                        'widget-item-picture',
                                                        'intec-image-effect'
                                                    ],
                                                    'data' => [
                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                                    ],
                                                    'style' => [
                                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arPicture['SOURCE'].'\')' : null
                                                    ]
                                                ]) ?>
                                            <?php } ?>
                                        <?= Html::endTag('a') ?>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item">
                                    <div class="widget-item-content">
                                        <div class="intec-grid-item">
                                            <div class="widget-item-name-wrap">
                                                <?= Html::tag('a', $arSection['NAME'], [
                                                    'class' => [
                                                        'widget-item-name',
                                                        'intec-cl-text-hover'
                                                    ],
                                                    'href' => $arSection['SECTION_PAGE_URL'],
                                                    'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                            </div>
                                            <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arSection['DESCRIPTION'])) { ?>
                                                <div class="widget-item-description">
                                                    <?= $arSection['DESCRIPTION'] ?>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arVisual['CHILDREN']['SHOW'] &&  !empty($arSection['ITEMS'])) { ?>
                                                <div class="widget-item-children">
                                                    <div class="intec-grid intec-grid-i-v-6 intec-grid-i-h-12 intec-grid-wrap">
                                                        <?php $vChildrenRender($arSection['ITEMS']) ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>