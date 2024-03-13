<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
        'c-news-list-shares-blocks-3'
    ]
]) ?>
    <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
        <div class="news-list-navigation news-list-navigation-top">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <?php } ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['IBLOCK']['DESCRIPTION']['SHOW'] && !empty($arResult['DESCRIPTION'])) { ?>
                <div class="news-list-description">
                    <?= $arResult['DESCRIPTION'] ?>
                </div>
            <?php } ?>
            <div class="news-list-items">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'wrap',
                            'i-16'
                        ]
                    ],
                    'data-link' => $arVisual['ELEMENT']['AS_LINK'] ? 'true' : 'false'
                ]) ?>
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
                                'width' => 560,
                                'height' => 560
                            ], BX_RESIZE_IMAGE_EXACT);

                            if (!empty($sImage))
                                $sImage = $sImage['src'];
                        }

                        if (empty($sImage))
                            $sImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="intec-grid-item-4 intec-grid-item-1024-3 intec-grid-item-768-2 intec-grid-item-500-1">
                            <div class="news-list-item" id="<?= $sAreaId ?>">
                                <?= Html::beginTag($arVisual['ELEMENT']['AS_LINK'] ? 'a' : 'div', [
                                    'class' => 'news-list-item-content',
                                    'href' => $arVisual['ELEMENT']['AS_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null
                                ]) ?>
                                    <?= Html::tag('span', null, [
                                        'class' => 'news-list-item-picture',
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
                                    <span class="news-list-item-information">
                                        <?php if ($arItem['DATA']['DURATION']['SHOW']) { ?>
                                            <span class="news-list-item-duration">
                                                <?= $arItem['DATA']['DURATION']['VALUE'] ?>
                                            </span>
                                        <?php } ?>
                                        <span class="news-list-item-name">
                                            <?= Html::tag($arVisual['ELEMENT']['AS_LINK'] ? 'span' : 'a', $arItem['NAME'], [
                                                'href' => !$arVisual['ELEMENT']['AS_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null
                                            ]) ?>
                                        </span>
                                    </span>
                                <?= Html::endTag($arVisual['ELEMENT']['AS_LINK'] ? 'a' : 'div') ?>
                                <?php if ($arItem['DATA']['TIMER']['SHOW'] || $arItem['DATA']['DISCOUNT']['SHOW']) { ?>
                                    <div class="news-list-item-additional">
                                        <div class="intec-grid intec-grid-wrap intec-grid-i-4">
                                            <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
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
                                            <?php if ($arItem['DATA']['DISCOUNT']['SHOW']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <div class="news-list-item-discount">
                                                        <?= $arItem['DATA']['DISCOUNT']['VALUE'] ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
        <div class="news-list-navigation news-list-navigation-bottom">
            <?= $arResult['NAV_STRING'] ?>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>

