<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Context;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$isAjax = Context::getCurrent()->getRequest()->isAjaxRequest();
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$arNavigation = $arResult['NAVIGATION'];
$sTag = $arVisual['LINK']['USE'] ? 'a' : 'span';

?>
<div class="widget c-shares c-shares-template-1" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arResult['HEADER_BLOCK']['SHOW'] || $arResult['DESCRIPTION_BLOCK']['SHOW'] || $arResult['LINK_ALL_BLOCK']['SHOW']) { ?>
                <div class="widget-header" data-link-all="<?= $arResult['LINK_ALL_BLOCK']['SHOW'] ? 'true' : 'false' ?>">
                    <div class="intec-grid intec-grid-a-h-end intec-grid-a-v-center">
                        <div class="intec-grid-item">
                            <?php if ($arResult['HEADER_BLOCK']['SHOW']) { ?>
                                <?= Html::tag('div', $arResult['HEADER_BLOCK']['TEXT'], [
                                    'class' => [
                                        'widget-title',
                                        'align-' . $arResult['HEADER_BLOCK']['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?php if ($arResult['DESCRIPTION_BLOCK']['SHOW']) { ?>
                                <?= Html::tag('div', $arResult['DESCRIPTION_BLOCK']['TEXT'], [
                                    'class' => [
                                        'widget-description',
                                        'align-'.$arResult['DESCRIPTION_BLOCK']['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                        <?php if ($arResult['LINK_ALL_BLOCK']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('a', [
                                    'class' => 'widget-all',
                                    'href' => $arResult['LINK_ALL_BLOCK']['LIST_PAGE']
                                ]) ?>
                                    <span class="widget-all-desktop intec-cl-text-hover">
                                        <?php if (!empty($arResult['LINK_ALL_BLOCK']['TEXT'])) { ?>
                                            <?= $arResult['LINK_ALL_BLOCK']['TEXT'] ?>
                                        <?php } else { ?>
                                            <?= Loc::getMessage('C_SHARES_TEMP1_TEMPLATE_LINK_ALL_BLOCK_TEXT_DEFAULT') ?>
                                        <?php } ?>
                                    </span>
                                    <span class="widget-all-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                        <?= FileHelper::getFileData(__DIR__ . '/svg/list.arrow.svg') ?>
                                    </span>
                                <?= Html::endTag('a') ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-stretch',
                            'a-h-start',
                            'i-16'
                        ]
                    ]),
                    'data-role' => 'items'
                ]) ?>
                    <!--items-->
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = null;

                        if (!empty($arItem['PREVIEW_PICTURE']))
                            $arPicture = $arItem['PREVIEW_PICTURE'];
                        else if (!empty($arItem['DETAIL_PICTURE']))
                            $arPicture = $arItem['DETAIL_PICTURE'];
                        else
                            $arPicture = null;

                        if (!empty($arPicture)) {
                            $arPicture = CFile::ResizeImageGet($arPicture, [
                                'width' => 600,
                                'height' => 600
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($arPicture))
                                $sPicture = $arPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'],
                                    '1024-2',
                                    '500-1'
                                ]
                            ]
                        ]) ?>
                            <div class="widget-item" id="<?= $sAreaId ?>">
                                <?= Html::tag($sTag, null, [
                                    'class' => [
                                        'widget-item-picture',
                                        'intec-image-effect'
                                    ],
                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                    'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                <div class="widget-item-text">
                                    <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                                        <div class="widget-item-timer" data-role="timer">
                                            <div class="widget-loader-timer">
                                                <div class="intec-grid intec-grid-wrap intec-grid-i-2">
                                                    <div class="intec-grid-item-auto">
                                                        <div class="widget-loader-timer-item widget-loader-effect"></div>
                                                    </div>
                                                    <div class="intec-grid-item-auto">
                                                        <div class="widget-loader-timer-item widget-loader-effect"></div>
                                                    </div>
                                                    <div class="intec-grid-item-auto">
                                                        <div class="widget-loader-timer-item widget-loader-effect"></div>
                                                    </div>
                                                    <?php if ($arVisual['TIMER']['SECONDS']['SHOW']) { ?>
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-loader-timer-item widget-loader-effect"></div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (
                                                        $arVisual['TIMER']['SALE']['SHOW'] &&
                                                        !empty($arItem['DATA']['TIMER']['VALUES']['SALE_VALUE'])
                                                    ) { ?>
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-loader-timer-item widget-loader-effect"></div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (!defined('EDITOR')) { ?>
                                            <script type="text/javascript">
                                                template.load(function (data) {
                                                    this.api.components.get(<?= JavaScript::toObject([
                                                        'component' => $arResult['TIMER']['COMPONENT'],
                                                        'template' => $arResult['TIMER']['TEMPLATE'],
                                                        'parameters' => ArrayHelper::merge(
                                                            $arResult['TIMER']['PARAMETERS'],
                                                            $arItem['DATA']['TIMER']['VALUES']
                                                        )
                                                    ]) ?>).then(function (response) {
                                                        data.nodes.find('[data-role="timer"]').html(response);
                                                    });
                                                }, {
                                                    'name': '[Component] intec.universe:main.shares (template.1) > Timer',
                                                    'nodes': <?= JavaScript::toObject('#'.$sAreaId) ?>,
                                                    'loader': {
                                                        'name': 'lazy'
                                                    }
                                                });
                                            </script>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php if ($arItem['DATA']['HEADER']['SHOW']) { ?>
                                        <div class="widget-item-period">
                                            <?= $arItem['DATA']['HEADER']['VALUE'] ?>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-name">
                                        <?= Html::tag($sTag, $arItem['NAME'], [
                                            'class' => Html::cssClassFromArray([
                                                'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                            ], true),
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                            'title' => $arItem['NAME'],
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <!--items-->
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arNavigation['USE']) { ?>
                <div class="widget-pagination" data-role="navigation">
                    <!--navigation-->
                    <?= $arNavigation['PRINT'] ?>
                    <!--navigation-->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if (!defined('EDITOR') && $arNavigation['USE'] && $arNavigation['MODE'] === 'ajax')
        include(__DIR__.'/parts/script.php');
    ?>
</div>