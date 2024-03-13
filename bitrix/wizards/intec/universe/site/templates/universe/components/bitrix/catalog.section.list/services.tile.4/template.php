<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$arVisual = $arResult['VISUAL'];

$arPictureSize = [
    'width' => 48,
    'height' => 48
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-services-4'
    ],
    'data-picture-size' => $arVisual['PICTURE']['SIZE']
]) ?>
    <div class="catalog-section-list-items intec-grid intec-grid-wrap intec-grid-a-v-sretch">
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
                    'catalog-section-list-item' => true,
                    'intec-grid-item' => [
                        $arVisual['COLUMNS'] => true,
                        '1024-3' => $arVisual['COLUMNS'] >= 4,
                        '768-2' => $arVisual['COLUMNS'] >= 3,
                        '500-1' => true
                    ]
                ], true),
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'catalog-section-list-item-wrapper' => true,
                    ], true)
                ]) ?>
                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                        <?= Html::beginTag('a', [
                            'class' => 'catalog-section-list-item-picture-wrap',
                            'href' => $arSection['SECTION_PAGE_URL'],
                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                        ]) ?>
                        <?php if ($arPicture['TYPE'] === 'svg') { ?>
                            <?= Html::tag('div', FileHelper::getFileData('@root/'.$arPicture['SOURCE']), [
                                'class' => [
                                    Html::cssClassFromArray([
                                        'catalog-section-list-item-picture' => true,
                                        'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                        'intec-image-effect' => true,
                                    ], true)
                                ]
                            ]) ?>
                        <?php } else { ?>
                            <?= Html::tag('div', null, [
                                'class' => [
                                    'catalog-section-list-item-picture',
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
                    <?php } ?>
                    <div class="catalog-section-list-item-name-wrap">
                        <?= Html::beginTag('a', [
                            'class' => [
                                'catalog-section-list-item-name',
                                'intec-cl-text-hover'
                            ],
                            'href' => $arSection['SECTION_PAGE_URL'],
                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                        ]) ?>
                            <?= $arSection['NAME'] ?>
                        <?= Html::endTag('a') ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>