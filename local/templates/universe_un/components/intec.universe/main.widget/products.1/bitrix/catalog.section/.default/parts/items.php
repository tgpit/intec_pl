<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var Closure $dData
 * @var Closure $vButtons
 * @var Closure $vImage
 */

$iItemsCount = null;
$iItemsCurrent = 0;

if ($arVisual['LINES'] !== null)
    $iItemsCount = $arVisual['COLUMNS']['DESKTOP'] * $arVisual['LINES'];

?>

<?= Html::beginTag('div', [
    'class' => Html::cssClassFromArray([
        'widget-items' => true,
        'owl-carousel' => $arVisual['SLIDER']['USE'],
        'intec-grid' => $arVisual['SLIDER']['USE'] ? false : [
            '' => true,
            'wrap' => true,
            'a-v-stretch' => true,
            'a-h-start' => true
        ]
    ], true),
    'data-role' => 'items'
]) ?>
    <?php foreach ($arItems as $arItem) {

        if ($iItemsCount !== null)
            if ($iItemsCurrent >= $iItemsCount)
              break;

        $sId = $sTemplateId.'_'.$arItem['ID'];
        $sAreaId = $this->GetEditAreaId($sId);
        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

        $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
        $sLink = Html::decode($arItem['DETAIL_PAGE_URL']);
        $arPrice = null;

        if (!empty($arItem['ITEM_PRICES']))
            $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

        $arSkuProps = [];

        if (!empty($arResult['SKU_PROPS']))
            $arSkuProps = $arResult['SKU_PROPS'];
        else if (!empty($arItem['SKU_PROPS']))
            $arSkuProps = $arItem['SKU_PROPS'];

        ?>
        <?= Html::beginTag('div', [
            'id' => $sAreaId,
            'class' => Html::cssClassFromArray([
                'widget-item' => true,
                'intec-grid-item' => $arVisual['SLIDER']['USE'] ? null : [
                    $arVisual['COLUMNS']['DESKTOP'] => true,
                    '500-1' => ($arVisual['COLUMNS']['DESKTOP'] <= 4) && $arVisual['COLUMNS']['MOBILE'] == 1,
                    '500-2' => ($arVisual['COLUMNS']['DESKTOP'] <= 4) && $arVisual['COLUMNS']['MOBILE'] == 2,
                    '800-2' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                    '1000-3' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3,
                    '700-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                    '720-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                    '950-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                    '1200-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3
                ]
            ],  true),
            'data' => [
                'id' => $arItem['ID'],
                'role' => 'item',
                'data' => $sData,
                'expanded' => 'false',
                'available' => $arItem['CAN_BUY'] ? 'true' : 'false'
            ]
        ]) ?>
            <div class="widget-item-wrapper intec-grid intec-grid-o-vertical intec-grid-a-h-between" data-borders-style="<?= $arVisual['BORDERS']['STYLE'] ?>">
                <div class="widget-item-substrate"></div>
                <div class="widget-item-base">
                    <div class="widget-item-image-container">
                        <?php $vImage($arItem) ?>
                        <div class="widget-item-action-container-wrap">
                            <div class="widget-item-action-container">
                                <?php if ($arItem['ACTION'] !== 'none') { ?>
                                    <div class="widget-item-purchase-container">
                                        <div class="widget-item-purchase">
                                            <?php $vPurchase($arItem) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['QUICK_VIEW']['USE'] && !$arResult['QUICK_VIEW']['DETAIL']) { ?>
                                    <div class="widget-item-quick-view">
                                        <div class="widget-item-quick-view-button" data-role="quick.view">
                                            <div class="widget-item-quick-view-button-icon">
                                                <i class="intec-ui-icon intec-ui-icon-eye-1"></i>
                                            </div>
                                            <div class="widget-item-quick-view-button-text">
                                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_1_QUICK_VIEW') ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($arItem['VISUAL']['MARKS']['SHOW']) { ?>
                            <!--noindex-->
                            <div class="widget-item-marks">
                                <?php $APPLICATION->includeComponent(
                                    'intec.universe:main.markers',
                                    'template.1', [
                                        'HIT' => $arItem['VISUAL']['MARKS']['VALUES']['HIT'] ? 'Y' : 'N',
                                        'NEW' => $arItem['VISUAL']['MARKS']['VALUES']['NEW'] ? 'Y' : 'N',
                                        'RECOMMEND' => $arItem['VISUAL']['MARKS']['VALUES']['RECOMMEND'] ? 'Y' : 'N',
                                        'SHARE' => $arItem['VISUAL']['MARKS']['VALUES']['SHARE'] ? 'Y' : 'N',
                                        'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                                    ],
                                    $component,
                                    ['HIDE_ICONS' => 'Y']
                                ) ?>
                            </div>
                            <!--/noindex-->
                        <?php } ?>
                        <?php if ($arItem['VISUAL']['DELAY']['USE'] || $arItem['VISUAL']['COMPARE']['USE']) { ?>
                            <!--noindex-->
                            <?php $vButtons($arItem) ?>
                            <!--/noindex-->
                        <?php } ?>
                    </div>
                    <?php if ($arVisual['VOTE']['SHOW']) { ?>
                        <!--noindex-->
                        <div class="widget-item-vote" data-align="<?= $arVisual['VOTE']['ALIGN'] ?>">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:iblock.vote',
                                'template.1', [
                                    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                    'ELEMENT_ID' => $arItem['ID'],
                                    'ELEMENT_CODE' => $arItem['CODE'],
                                    'MAX_VOTE' => '5',
                                    'VOTE_NAMES' => array(
                                        0 => '1',
                                        1 => '2',
                                        2 => '3',
                                        3 => '4',
                                        4 => '5',
                                    ),
                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                    'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                    'SHOW_RATING' => 'N'
                                ],
                                $component,
                                ['HIDE_ICONS' => 'Y']
                            ) ?>
                        </div>
                        <!--/noindex-->
                    <?php } ?>
                    <?php if ($arItem['VISUAL']['QUANTITY']['SHOW']) { ?>
                        <!--noindex-->
                        <div class="widget-item-quantity-wrap">
                            <?php $vQuantity($arItem) ?>
                        </div>
                        <!--/noindex-->
                    <?php } ?>
                    <div class="widget-item-name" data-align="<?= $arVisual['NAME']['ALIGN'] ?>">
                        <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                            'class' => 'intec-cl-text-hover',
                            'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
                            'title' => $arItem['NAME'],
                            'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                        ]) ?>
                    </div>
                    <?php if ($arVisual['SECTION']['SHOW'] && !empty($arItem['SECTION'])) { ?>
                        <div class="widget-item-section" data-align="<?= $arVisual['SECTION']['ALIGN'] ?>">
                            <?= Html::tag('a', $arItem['SECTION']['NAME'], [
                                'class' => 'intec-cl-text-hover',
                                'href' => $arItem['SECTION']['SECTION_PAGE_URL'],
                                'title' => $arItem['SECTION']['NAME']
                            ]) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arItem['VISUAL']['PRICE']['SHOW'])
                        $vPrice($arPrice);
                    ?>
                    <?php if ($arItem['VISUAL']['TIMER']['SHOW']) { ?>
                        <div class="widget-item-section-timer">
                            <?php include(__DIR__ . '/timer.php'); ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if (
                    (
                        $arVisual['OFFERS']['USE'] &&
                        $arItem['VISUAL']['OFFER'] &&
                        !empty($arSkuProps)
                    ) ||
                    $arItem['VISUAL']['ACTION'] !== 'none'
                ) { ?>
                    <!--noindex-->
                    <div class="widget-item-advanced">
                        <?php if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'] && !empty($arSkuProps))
                            $vSku($arSkuProps);
                        ?>
                        <?php if ($arItem['VISUAL']['ACTION'] !== 'none') { ?>
                            <div class="widget-item-purchase-container">
                                <div class="widget-item-purchase-adaptive">
                                    <?php $vPurchase($arItem) ?>
                                </div>
                                <div class="widget-item-purchase-mobile">
                                    <?php $vPurchase($arItem, true) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <!--/noindex-->
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
        <?php $iItemsCurrent++; ?>
    <?php } ?>
<?= Html::endTag('div') ?>
<?php if ($arVisual['SLIDER']['USE']) { ?>
    <div class="widget-navigation-counter">
        <span data-role="navigation.container">
            <span data-role="navigation.counter">
                1
            </span>
            <span>
                /
            </span>
            <span data-role="navigation.max.count">
                <?= ceil(count($arItems) / $arVisual['COLUMNS']['DESKTOP']) ?>
            </span>
        </span>
    </div>
    <script type="text/javascript">
        template.load(function () {
            var _ = this.getLibrary('_');
            var $ = this.getLibrary('$');

            var root = arguments[0].nodes;
            var area = root;
            var maxContainer = $('[data-role="navigation.max.count"]', root);
            var countContainer = $('[data-role="navigation.counter"]', root);
            var elementColumn = <?= JavaScript::toObject($arVisual['COLUMNS']['DESKTOP'])?>;
            var controlButton = {
                'next': $('[data-role="navigation.next"]', root),
                'prev': $('[data-role="navigation.prev"]', root)
            };
            var data = <?= JavaScript::toObject([
                'columns' => [
                    'desktop' => $arVisual['COLUMNS']['DESKTOP'],
                    'mobile' => $arVisual['COLUMNS']['MOBILE']
                ],
                'navigation' => $arVisual['SLIDER']['NAVIGATION'],
                'navClass' => [
                    'owl-prev intec-cl-background-hover intec-cl-border-hover',
                    'owl-next intec-cl-background-hover intec-cl-border-hover'
                ],
                'navText' => [
                    FileHelper::getFileData(__DIR__.'/../svg/navigation.left.svg'),
                    FileHelper::getFileData(__DIR__.'/../svg/navigation.right.svg')
                ],
                'dots' => $arVisual['SLIDER']['DOTS']
            ]) ?>;

            <?php if (!empty($arCategory)) { ?>
            area = $(<?= JavaScript::toObject('#'.$sTemplateId.'-tab-'.$iCounter) ?>, root);
            <?php } ?>

            function setElementColumn (obj) {
                if (_.isNil(obj))
                    return false;

                var elements = obj.children();
                var parent = obj.parent();
                var lasElement = elements[elements.length - 1];
                var column = $(parent).outerWidth(true) / $(lasElement).outerWidth(true);

                column = column.toFixed();

                if (isNaN(column))
                    return false;

                if (column <= 0)
                    column = 1;

                elementColumn = column;

                return true;
            }

            function setMaxCount (obj, maxContainer) {
                if (_.isNil(obj) || _.isNil(maxContainer))
                    return false;

                var elements = obj.children();
                var maxCount = Math.ceil(elements.length / elementColumn);

                maxContainer.html(maxCount);
            }

            function setCurrentCount (obj, countContainer) {
                if (_.isNil(obj) || _.isNil(maxContainer))
                    return false;

                var elements = obj.children();
                var maxCount = Math.ceil(elements.length / elementColumn);
                var elementsActive = elements.filter('.active');
                var lastActive = elementsActive[elementsActive.length - 1];
                var currentNumber = $(lastActive).index() + 1;

                if (currentNumber === elements.length && currentNumber !== maxCount)
                    currentNumber++;

                var current = Math.floor(currentNumber / elementColumn);
                countContainer.html(current);
            }

            function updateNavPosition (obj) {
                var parent = obj.parent();
                var navigation = $('.owl-nav', parent);
                var counter = $('[data-role="navigation.container"]', parent);

                if (navigation.hasClass('disabled')) {
                    counter.parent().addClass('disabled');
                } else if (counter.parent().hasClass('disabled')) {
                    counter.parent().removeClass('disabled');
                }

                if ($(window).width() <= 768)
                    navigation.css('width', counter.outerWidth(true) + 96);
                else
                    navigation.css('width', '');
            }

            handler = function () {
                var items = area.find('.owl-stage:first');

                items.children('.owl-item').css('visibility', 'collapse');
                items.children('.owl-item.active').css('visibility', '');

                if (setElementColumn(this.$stage)) {
                    setMaxCount(this.$stage, maxContainer);
                    setCurrentCount(this.$stage, countContainer);
                    updateNavPosition(this.$element);
                }
            };


            var slider = $('.widget-items', area);
            var responsive = {
                0: {
                    'items': <?= $arVisual['COLUMNS']['MOBILE'] ?>,
                    'dots': false
                },
                769: {
                    'dots': data.dots
                }
            };

            if (data.columns.desktop > 2)
                responsive[500] = {
                    'items': 2,
                    'dots': false
                };

            if (data.columns.desktop > 3)
                responsive[820] = {
                    'items': 3
                };

            if (data.columns.desktop > 4)
                responsive[1100] = {
                    'items': 4
                };

            responsive[1200] = {
                'items': data.columns.desktop
            };
            slider.owlCarousel({
                'center': false,
                'loop': false,
                'nav': data.navigation,
                'navText': data.navText,
                'navClass': data.navClass,
                'stagePadding': 5,
                'responsive': responsive,
                'onResized': handler,
                'onRefreshed': handler,
                'onInitialized': handler,
                'onTranslated': handler
            });
        }, {
            'name': '[Component] intec.universe:main.widget (products.1) > bitrix:catalog.section (.default) > items',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
<?php } ?>