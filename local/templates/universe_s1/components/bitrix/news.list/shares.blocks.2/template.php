<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'DURATION' => FileHelper::getFileData(__DIR__.'/svg/duration.icon.svg')
];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-list',
        'c-news-list-shares-blocks-2'
    ]
]) ?>
    <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
        <div class="news-list-navigation news-list-navigation-top">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <?php } ?>
    <div class="news-list-content intec-content intec-content-visible">
        <div class="news-list-content-wrapper intec-content-wrapper">
            <?php if ($arVisual['IBLOCK']['DESCRIPTION']['SHOW'] && !empty($arResult['DESCRIPTION'])) { ?>
                <div class="news-list-description">
                    <?= $arResult['DESCRIPTION'] ?>
                </div>
            <?php } ?>
            <div class="news-list-items">
                <div class="intec-grid intec-grid-wrap intec-grid-i-16">
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sImage = $arItem['PREVIEW_PICTURE'];

                        if (empty($sImage))
                            $sImage = $arItem['DETAIL_PICTURE'];

                        if (!empty($sImage)) {
                            $sImage = CFile::ResizeImageGet($sImage, [
                                'width' => 480,
                                'height' => 320
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sImage))
                                $sImage = $sImage['src'];
                        }

                        if (empty($sImage))
                            $sImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="intec-grid-item-3 intec-grid-item-1024-2 intec-grid-item-768-1">
                            <div class="news-list-item" id="<?= $sAreaId ?>">
                                <div class="news-list-item-picture-container">
                                    <?= Html::tag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a', null, [
                                        'class' => 'news-list-item-picture',
                                        'href' => !$arItem['DATA']['HIDE_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                                        ],
                                        'style' => [
                                            'background-image' => 'url(\''.(
                                                $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sImage
                                            ).'\')'
                                        ]
                                    ]) ?>
                                    <?php if ($arItem['DATA']['DISCOUNT']['SHOW'] || $arItem['DATA']['TIMER']['SHOW']) { ?>
                                        <div class="news-list-item-discount-container">
                                            <?php if (
                                                $arItem['DATA']['DISCOUNT']['SHOW'] && (
                                                    !$arItem['DATA']['TIMER']['SHOW'] ||
                                                    !$arItem['DATA']['TIMER']['SALE']['SHOW']
                                                )
                                            ) { ?>
                                                <div class="news-list-item-discount-block">
                                                    <div class="news-list-item-discount intec-grid intec-grid-inline intec-cl-background">
                                                        <div class="news-list-item-discount-part intec-grid-item-auto">
                                                            <?= Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_2_DISCOUNT_PREFIX') ?>
                                                        </div>
                                                        <div class="news-list-item-discount-part intec-grid-item-auto">
                                                            <?= $arItem['DATA']['DISCOUNT']['VALUE'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                                                <div class="news-list-item-discount-block">
                                                    <div data-role="item.timer">
                                                        <div class="news-list-loader-timer" data-role="item.timer.loader">
                                                            <div class="intec-grid intec-grid-wrap intec-grid-i-2">
                                                                <div class="intec-grid-item-auto">
                                                                    <div class="news-list-loader-timer-item news-list-loader"></div>
                                                                </div>
                                                                <div class="intec-grid-item-auto">
                                                                    <div class="news-list-loader-timer-item news-list-loader"></div>
                                                                </div>
                                                                <div class="intec-grid-item-auto">
                                                                    <div class="news-list-loader-timer-item news-list-loader"></div>
                                                                </div>
                                                                <?php if ($arVisual['TIMER']['SECONDS']['SHOW']) { ?>
                                                                    <div class="intec-grid-item-auto">
                                                                        <div class="news-list-loader-timer-item news-list-loader"></div>
                                                                    </div>
                                                                <?php } ?>
                                                                <?php if ($arItem['DATA']['TIMER']['SALE']['SHOW']) { ?>
                                                                    <div class="intec-grid-item-auto">
                                                                        <div class="news-list-loader-timer-item news-list-loader"></div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <script type="text/javascript">
                                                        template.load(function (data) {
                                                            this.api.components.get({
                                                                'component': 'intec.universe:product.timer',
                                                                'template': 'template.2',
                                                                'parameters': <?= JavaScript::toObject(ArrayHelper::merge(
                                                                    $arResult['TIMER'],
                                                                    $arItem['DATA']['TIMER']['VALUES']
                                                                )) ?>
                                                            }).then(function (response) {
                                                                var timer = data.nodes.find('[data-role="item.timer"]');

                                                                timer.find('[data-role="item.timer.loader"]').remove();
                                                                timer.append(response);
                                                            });
                                                        }, {
                                                            'name': '[Component] bitrix:new.list (shares.blocks.2) > Timer',
                                                            'nodes': <?= JavaScript::toObject('#'.$sAreaId) ?>,
                                                            'loader': {
                                                                'name': 'lazy'
                                                            }
                                                        });
                                                    </script>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="news-list-item-name">
                                    <?= Html::tag($arItem['DATA']['HIDE_LINK'] ? 'span' : 'a', $arItem['NAME'], [
                                        'class' => Html::cssClassFromArray([
                                            'intec-cl-text-hover' => !$arItem['DATA']['HIDE_LINK']
                                        ], true),
                                        'href' => !$arItem['DATA']['HIDE_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null
                                    ]) ?>
                                </div>
                                <div class="news-list-item-duration">
                                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                                        <div class="intec-grid-item-auto intec-ui-picture">
                                            <?= $arSvg['DURATION'] ?>
                                        </div>
                                        <div class="intec-grid-item">
                                            <div class="news-list-item-duration-text">
                                                <?= $arItem['DATA']['DURATION']['VALUE'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
        <div class="news-list-navigation news-list-navigation-bottom">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>

