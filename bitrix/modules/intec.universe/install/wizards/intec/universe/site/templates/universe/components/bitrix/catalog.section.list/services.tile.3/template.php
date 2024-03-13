<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-services-tile-3'
    ],
    'data-picture-size' => $arVisual['PICTURE']['SIZE']
]) ?>
    <div class="catalog-section-list-items intec-grid intec-grid-wrap intec-grid-i-16 intec-grid-a-v-sretch">
        <?php foreach ($arResult['SECTIONS'] as $arSection) {

            $sId = $sTemplateId.'_'.$arSection['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arSection['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);


            $arPicture = $arSection['PICTURE'];

            if (!empty($arPicture)) {
                $arPicture = CFile::ResizeImageGet($arPicture, [
                    'width' => 600,
                    'height' => 600
                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                if (!empty($arPicture))
                    $arPicture = [
                        'ALT' => $arSection['PICTURE']['ALT'],
                        'SRC' => $arPicture['src'],
                        'TITLE' => $arSection['PICTURE']['TITLE']
                    ];
            }

            if (empty($arPicture)) {
                $arPicture = [
                    'ALT' => null,
                    'SRC' => SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                    'TITLE' => null
                ];
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
                        'catalog-section-list-item-wrapper' => true
                    ], true)
                ]) ?>
                    <?= Html::beginTag('a', [
                        'class' => 'catalog-section-list-item-picture-wrap',
                        'href' => $arSection['SECTION_PAGE_URL'],
                        'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                    ]) ?>
                        <?= Html::tag('div', null, [
                            'class' => [
                                'catalog-section-list-item-picture',
                                'intec-image-effect'
                            ],
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SRC'] : null
                            ],
                            'style' => [
                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arPicture['SRC'].'\')' : null
                            ]
                        ]) ?>
                    <?= Html::endTag('a') ?>
                    <div class="catalog-section-list-item-text-wrap">
                        <div class="catalog-section-list-item-text">
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
                            <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arSection['DESCRIPTION'])) { ?>
                                <div class="catalog-section-list-item-description-wrap">
                                    <div class="catalog-section-list-item-description">
                                        <?= Html::stripTags($arSection['DESCRIPTION']) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>
