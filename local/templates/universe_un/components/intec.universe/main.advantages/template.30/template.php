<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
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

$iCounter = 0;
?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'widget c-advantages c-advantages-template-30',
    'data' => [
        'theme' => $arVisual['THEME'],
        'title-position' => $arVisual['TITLE']['POSITION'],
        'name-align' => $arVisual['NAME']['ALIGN'],
        'picture-align' => $arVisual['PICTURE']['ALIGN'],
        'picture-position' => $arVisual['PICTURE']['POSITION'],
        'preview-align' => $arVisual['PREVIEW']['ALIGN']
    ],
    'style' => $arVisual['BACKGROUND']['SHOW'] ? 'background: '.$arVisual['BACKGROUND']['COLOR'] : null
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    Html::cssClassFromArray([
                        'widget-wrapper' => true,
                        'intec-grid' => $arVisual['TITLE']['POSITION'] == 'left' ? true : false,
                        'intec-grid-wrap' => $arVisual['TITLE']['POSITION'] == 'left' ? true : false
                    ], true)
                ]
            ]) ?>
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            Html::cssClassFromArray([
                                'widget-header' => true,
                                'intec-grid-item-4' => $arVisual['TITLE']['POSITION'] == 'left' ? true : false,
                                'intec-grid-item-900-1' => $arVisual['TITLE']['POSITION'] == 'left' ? true : false
                            ], true)
                        ]
                    ]) ?>
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
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        Html::cssClassFromArray([
                            'widget-content' => true,
                            'intec-grid-item' => $arVisual['TITLE']['POSITION'] == 'left' ? true : false
                        ], true)
                    ]
                ]) ?>
                    <div class="widget-items-wrap intec-content intec-content-visible">
                        <div class="widget-items-wrap-2 intec-content-wrapper">
                            <div class="widget-items intec-grid intec-grid-wrap intec-grid-a-h-center">
                                <?php foreach ($arResult['ITEMS'] as $arItem) {
                                    $sId = $sTemplateId.'_'.$arItem['ID'];
                                    $sAreaId = $this->GetEditAreaId($sId);
                                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                    $sTag = (!empty($arItem['DATA']['LINK']) && $arItem['DATA']['LINK'] !== '/') ? 'a' : 'div';
                                    $arImage = [
                                        'TYPE' => 'picture',
                                        'SOURCE' => null
                                    ];

                                    if (!empty($arItem['DATA']['IMAGE'])) {
                                        if ($arItem['DATA']['IMAGE']['CONTENT_TYPE'] === 'image/svg+xml') {
                                            $arImage['TYPE'] = 'svg';
                                            $arImage['SOURCE'] = $arItem['DATA']['IMAGE']['SRC'];
                                        } else {
                                            $arImage['SOURCE'] = CFile::ResizeImageGet($arItem['DATA']['IMAGE'], [
                                                'width' => 40,
                                                'height' => 40
                                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                            if (!empty($arImage['SOURCE'])) {
                                                $arImage['SOURCE'] = $arImage['SOURCE']['src'];
                                            } else {
                                                $arImage['SOURCE'] = null;
                                            }
                                        }
                                    }

                                    if (empty($arImage['SOURCE'])) {
                                        $arImage['TYPE'] = 'picture';
                                        $arImage['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                    }
                                ?>
                                    <?= Html::beginTag('div',[
                                        'id' => $sAreaId,
                                        'class' => [
                                            Html::cssClassFromArray([
                                                'widget-item' => true,
                                                'intec-grid-item' => [
                                                    $arVisual['COLUMNS'] => true,
                                                    '400-1' => $arVisual['MOBILE']['COLUMNS'] == 1,
                                                    '768-2' => $arVisual['COLUMNS'] >= 2,
                                                    '900-3' => $arVisual['COLUMNS'] >= 3,
                                                    '1000-4' => $arVisual['COLUMNS'] >= 4
                                                ],
                                            ], true)
                                        ],
                                        'data-picture-show' => $arVisual['PICTURE']['SHOW'] ? 'true' : 'false'
                                    ]) ?>
                                        <div class="widget-item-wrapper">
                                            <?php if ($arVisual['PICTURE']['POSITION'] == 'left') { ?>
                                                <div class="intec-grid">
                                                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                                        <div class="intec-grid-item intec-grid-item-auto">
                                                            <?php if ($arImage['TYPE'] === 'svg') { ?>
                                                                <?= Html::tag($sTag, FileHelper::getFileData('@root/'.$arImage['SOURCE']), [
                                                                    'class' => [
                                                                        'widget-item-picture',
                                                                        'intec-ui-picture',
                                                                        'intec-cl-svg'
                                                                    ],
                                                                    'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                                ]) ?>
                                                            <?php } else { ?>
                                                                <?= Html::tag($sTag, null, [
                                                                    'class' => [
                                                                        'widget-item-picture',
                                                                        'intec-image-effect'
                                                                    ],
                                                                    'data' => [
                                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : null
                                                                    ],
                                                                    'style' => [
                                                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arImage['SOURCE'].'\')' : null
                                                                    ],
                                                                    'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                                ]) ?>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="intec-grid-item">
                                                        <?php if (!empty($arItem['NAME']) && $arVisual['NAME']['SHOW']) { ?>
                                                            <?= Html::tag($sTag, $arItem['NAME'], [
                                                                'class' => Html::cssClassFromArray([
                                                                    'widget-item-name' => true,
                                                                    'intec-cl-text-hover' => $sTag === 'a'
                                                                ], true),
                                                                'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                            ]) ?>
                                                        <?php } ?>
                                                        <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                                            <div class="widget-item-description">
                                                                <?= $arItem['PREVIEW_TEXT'] ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                                    <?php if ($arImage['TYPE'] === 'svg') { ?>
                                                        <?= Html::tag($sTag, FileHelper::getFileData('@root/'.$arImage['SOURCE']), [
                                                            'class' => [
                                                                'widget-item-picture',
                                                                'intec-ui-picture',
                                                                'intec-cl-svg'
                                                            ],
                                                            'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                        ]) ?>
                                                    <?php } else { ?>
                                                        <?= Html::tag($sTag, null, [
                                                            'class' => [
                                                                'widget-item-picture',
                                                                'intec-image-effect'
                                                            ],
                                                            'data' => [
                                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : null
                                                            ],
                                                            'style' => [
                                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arImage['SOURCE'].'\')' : null
                                                            ],
                                                            'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                        ]) ?>
                                                    <?php } ?>
                                                <?php } ?>
                                                <?php if (!empty($arItem['NAME']) && $arVisual['NAME']['SHOW']) { ?>
                                                    <?= Html::tag($sTag, $arItem['NAME'], [
                                                        'class' => Html::cssClassFromArray([
                                                            'widget-item-name' => true,
                                                            'intec-cl-text-hover' => $sTag === 'a'
                                                        ], true),
                                                        'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                    ]) ?>
                                                <?php } ?>
                                                <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                                    <div class="widget-item-description">
                                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>