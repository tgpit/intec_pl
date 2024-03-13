<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
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

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-list',
        'c-news-list-shares-blocks-1'
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
            <div class="news-list-items intec-grid intec-grid-wrap intec-grid-i-10">
                <?php foreach($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $sImage = $arItem['PREVIEW_PICTURE'];

                    if (empty($sImage))
                        $sImage = $arItem['DETAIL_PICTURE'];

                    if (!empty($sImage)) {
                        $sImage = CFile::ResizeImageGet($sImage, [
                            'width' => 940,
                            'height' => 940
                        ], BX_RESIZE_IMAGE_PROPORTIONAL);

                        if (!empty($sImage))
                            $sImage = $sImage['src'];
                    }

                    if (empty($sImage))
                        $sImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                ?>
                    <div class="intec-grid-item-2 intec-grid-item-768-1">
                        <div class="news-list-item" id="<?= $sAreaId ?>">
                            <?= Html::beginTag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a', [
                                'class' => 'news-list-item-content',
                                'href' => !$arItem['DATA']['HIDE_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null
                            ]) ?>
                                <?= Html::tag('span', null, [
                                    'class' => 'news-list-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sImage.'\')' : null
                                    ]
                                ]) ?>
                                <span class="news-list-item-information">
                                    <span class="news-list-item-name">
                                        <?= $arItem['NAME'] ?>
                                    </span>
                                    <?php if ($arItem['DATA']['DURATION']['SHOW']) { ?>
                                        <span class="news-list-item-duration">
                                            <?= $arItem['DATA']['DURATION']['VALUE'] ?>
                                        </span>
                                    <?php } ?>
                                </span>
                            <?= Html::endTag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a') ?>
                            <?php if ($arItem['DATA']['DISCOUNT']['SHOW'] || $arItem['DATA']['TIMER']['SHOW']) { ?>
                                <div class="news-list-item-additional">
                                    <?php if (
                                        $arItem['DATA']['DISCOUNT']['SHOW'] && (
                                            !$arItem['DATA']['TIMER']['SHOW'] ||
                                            !$arItem['DATA']['TIMER']['SALE']['SHOW']
                                        )
                                    ) { ?>
                                        <div class="news-list-item-additional-block">
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'news-list-item-discount',
                                                    'intec-cl-background',
                                                    'intec-grid' => [
                                                        '',
                                                        'inline'
                                                    ]
                                                ]
                                            ]) ?>
                                                <div class="news-list-item-discount-part intec-grid-item-auto">
                                                    <?= Loc::getMessage('C_NEWS_LIST_SHARES_BLOCKS_1_DISCOUNT_PREFIX') ?>
                                                </div>
                                                <div class="news-list-item-discount-part intec-grid-item-auto">
                                                    <?= $arItem['DATA']['DISCOUNT']['VALUE'] ?>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                                        <div class="news-list-item-additional-block">
                                            <div class="" data-role="item.timer">
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
                                                    'name': '[Component] bitrix:new.list (shares.blocks.1) > Timer',
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
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
        <div class="news-list-navigation news-list-navigation-bottom">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>