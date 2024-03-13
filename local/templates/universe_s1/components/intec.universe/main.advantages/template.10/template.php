<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

?>
<div class="widget c-advantages c-advantages-template-10" id="<?= $sTemplateId ?>">
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
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items',
                        'intec-grid',
                        'intec-grid-wrap',
                        'intec-grid-a-h-center',
                        'intec-grid-i-h-15',
                        'intec-grid-i-v-25'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $arData = $arItem['DATA'];
                        $arPicture = [
                            'TYPE' => 'picture',
                            'SOURCE' => null
                        ];

                        if (!empty($arData['PICTURE'])) {
                            if ($arData['PICTURE']['CONTENT_TYPE'] === 'image/svg+xml') {
                                $arPicture['TYPE'] = 'svg';
                                $arPicture['SOURCE'] = $arData['PICTURE']['SRC'];
                            } else {
                                $arPicture['SOURCE'] = CFile::ResizeImageGet($arData['PICTURE'], [
                                    'width' => 98,
                                    'height' => 98
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

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
                        }
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-3' => $arVisual['COLUMNS'] >= 4,
                                    '500-2' => $arVisual['COLUMNS'] >= 3,
                                    '400-1' => $arVisual['COLUMNS'] >= 2
                                ]
                            ], true),
                            'id' => $sAreaId
                        ]) ?>
                            <div class="widget-item-wrapper">
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <div class="widget-item-picture-wrap">
                                        <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                            <?= Html::tag('div', FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                                'class' => [
                                                    'widget-item-picture',
                                                    'intec-cl-svg',
                                                    'intec-image-effect',
                                                    'intec-ui-picture'
                                                ]
                                            ]) ?>
                                        <?php } else { ?>
                                            <?= Html::tag('div', '', [
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
                                    </div>
                                <?php } ?>
                                <div class="widget-item-text">
                                    <div class="widget-item-name intec-cl-text-hover">
                                        <?= $arItem['NAME'] ?>
                                    </div>
                                    <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                        <div class="widget-item-description">
                                            <?= $arItem['PREVIEW_TEXT'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>