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

$arVisual = $arResult['VISUAL'];

$vChildrenRender = include(__DIR__.'/parts/children.php');

if ($arVisual['PICTURE']['SIZE'] === 'medium') {
    $arPictureSize = [
        'width' => 64,
        'height' => 64
    ];
} else {
    $arPictureSize = [
        'width' => 48,
        'height' => 48
    ];
}

function declensionWord($number, $word) {
    $array = [2, 0, 1, 1, 1, 2];
    echo $word[ ($number % 100 > 4 && $number % 100 < 20)? 2 : $array[($number % 10 < 5) ? $number % 10 : 5] ];
}

$arWords = [
    Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_LIST_1_DECLENSION_WORD_1'),
    Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_LIST_1_DECLENSION_WORD_2'),
    Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_LIST_1_DECLENSION_WORD_3'),
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section-list',
        'c-catalog-section-list-catalog-list-1'
    ],
    'data-picture-size' => $arVisual['PICTURE']['SIZE']
]) ?>
    <div class="catalog-section-list-items">
        <?php foreach ($arResult['SECTIONS'] as $arSection) {

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
                'class' => [
                    'catalog-section-list-item'
                ],
                'data-role' => 'item',
                'data-expanded' => 'false'
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'catalog-section-list-item-wrapper' => true,
                        'intec-grid' => [
                            '' => true,
                            'a-v-center' => !$arVisual['CHILDREN']['SHOW'] || empty($arSection['SECTIONS']),
                        ]
                    ], true)
                ]) ?>
                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                        <div class="catalog-section-list-item-picture-wrap-1 intec-grid-item-auto">
                            <?= Html::beginTag('a', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-section-list-item-picture-wrap' => true,
                                    'catalog-section-list-item-picture' => true,
                                    'intec-image-effect' => true,
                                    'intec-cl-svg' => $arVisual['SVG']['COLOR'] == 'theme' ? true : false,
                                    'intec-ui-picture' => true
                                ], true),
                                'href' => $arSection['SECTION_PAGE_URL'],
                                'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null,
                            ]) ?>
                            <?php if ($arPicture['TYPE'] === 'svg') { ?>
                                <?= FileHelper::getFileData('@root/'.$arPicture['SOURCE']) ?>
                            <?php } else { ?>
                                <?= Html::img($arPicture['SOURCE'], [
                                    'alt' => $arPicture['ALT'],
                                    'title' => $arPicture['TITLE'],
                                    'loading' => 'lazy',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['SOURCE'] : null
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?= Html::endTag('a') ?>
                        </div>
                    <?php } ?>
                    <div class="intec-grid-item">
                        <div class="catalog-section-list-item-section intec-grid intec-grid-a-v-start">
                            <div class="intec-grid-item intec-grid-item-shrink-1">
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
                                <?php if ($arVisual['ELEMENTS']) { ?>
                                    <div class="catalog-section-list-item-count">
                                        <?= $arSection['ELEMENT_CNT'] ?> <?= declensionWord($arSection['ELEMENT_CNT'], $arWords) ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if ($arVisual['CHILDREN']['SHOW'] && !empty($arSection['SECTIONS'])) { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-section-list-item-button',
                                            'intec-cl-border-hover',
                                            'intec-cl-background-hover',
                                        ],
                                        'data' => [
                                            'role' => 'button',
                                            'expanded' => 'false'
                                        ]
                                    ]) ?>
                                        <div class="catalog-section-list-item-button-text" data-role="button.text">
                                            <i class="far fa-angle-down"></i>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($arVisual['CHILDREN']['SHOW'] && !empty($arSection['SECTIONS'])) { ?>
                            <?= Html::beginTag('div', [
                                'class' => 'catalog-section-list-item-children',
                                'data-role' => 'children'
                            ]) ?>
                                <div class="catalog-section-list-item-children-wrapper">
                                    <div class="intec-grid intec-grid-i-6 intec-grid-wrap">
                                        <?php $vChildrenRender($arSection['SECTIONS']) ?>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
    <?php if ($arVisual['CHILDREN']['SHOW'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>