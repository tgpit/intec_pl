<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div'

?>
<div class="widget c-shares c-shares-template-6" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if (
                $arResult['HEADER_BLOCK']['SHOW'] ||
                $arResult['DESCRIPTION_BLOCK']['SHOW'] ||
                $arResult['LIST_BLOCK']['SHOW']
            ) { ?>
                <div class="widget-header" data-link-all="<?= $arResult['LIST_BLOCK']['SHOW'] ? 'true' : 'false' ?>">
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
                                        'align-' . $arResult['DESCRIPTION_BLOCK']['POSITION']
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                        <?php if ($arResult['LIST_BLOCK']['SHOW']) { ?>
                            <div class="intec-grid-item-auto">
                                <a class="widget-list" href="<?= $arResult['LIST_BLOCK']['URL'] ?>">
                                    <span class="widget-list-desktop intec-cl-text-light-hover">
                                        <?php if (empty($arResult['LIST_BLOCK']['TEXT'])) { ?>
                                            <?= Loc::getMessage('C_SHARES_TEMPLATE_6_TEMPLATE_LIST_BLOCK_TEXT_DEFAULT') ?>
                                        <?php } else { ?>
                                            <?= $arResult['LIST_BLOCK']['TEXT'] ?>
                                        <?php } ?>
                                    </span>
                                    <span class="widget-list-mobile intec-cl-svg-path-stroke-hover">
                                        <?= FileHelper::getFileData(__DIR__.'/svg/header.list.mobile.svg') ?>
                                    </span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="intec-grid intec-grid-wrap intec-grid-i-12 intec-grid-a-v-stretch" data-role="items">
                    <!--items-->
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = null;

                        if (!empty($arItem['DATA']['PICTURE']['VALUE'])) {
                            $sPicture = CFile::ResizeImageGet($arItem['DATA']['PICTURE']['VALUE'], [
                                'width' => 250,
                                'height' => 250
                            ], BX_RESIZE_IMAGE_EXACT);

                            if (!empty($sPicture['src']))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="intec-grid-item-2 intec-grid-item-1024-1">
                            <div class="widget-item" id="<?= $sAreaId ?>">
                                <div class="widget-item-content intec-grid intec-grid-a-v-center intec-grid-i-12 intec-grid-500-wrap">
                                    <div class="widget-item-content-information intec-grid-item intec-grid-item-500-1">
                                        <?php if ($arItem['DATA']['HEADER']['SHOW']) { ?>
                                            <div class="widget-item-header">
                                                <?= $arItem['DATA']['HEADER']['VALUE'] ?>
                                            </div>
                                        <?php } ?>
                                        <div class="widget-item-name">
                                            <?= Html::tag($sTag, $arItem['NAME'], [
                                                'class' => Html::cssClassFromArray([
                                                    'intec-cl-text-light-hover' => $arVisual['LINK']['USE'],
                                                ], true),
                                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                            ]) ?>
                                        </div>
                                        <?php if ($arItem['DATA']['PREVIEW']['SHOW']) { ?>
                                            <div class="widget-item-description">
                                                <?= $arItem['DATA']['PREVIEW']['VALUE'] ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                                            <div class="widget-item-timer" data-role="timer">
                                                <div class="widget-loader-timer" data-role="timer.loader">
                                                    <?php if ($arVisual['TIMER']['HEADER']['SHOW']) { ?>
                                                        <div class="widget-loader-timer-name widget-loader"></div>
                                                    <?php } ?>
                                                    <div class="widget-loader-timer-blocks">
                                                        <div class="intec-grid intec-grid-wrap intec-grid-i-2">
                                                            <div class="intec-grid-item-auto">
                                                                <div class="widget-loader-timer-block widget-loader"></div>
                                                            </div>
                                                            <div class="intec-grid-item-auto">
                                                                <div class="widget-loader-timer-block widget-loader"></div>
                                                            </div>
                                                            <div class="intec-grid-item-auto">
                                                                <div class="widget-loader-timer-block widget-loader"></div>
                                                            </div>
                                                            <?php if ($arVisual['TIMER']['SECONDS']['SHOW']) { ?>
                                                                <div class="intec-grid-item-auto">
                                                                    <div class="widget-loader-timer-block widget-loader"></div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (
                                                                $arVisual['TIMER']['SALE']['SHOW'] &&
                                                                !empty($arItem['DATA']['TIMER']['VALUES']['SALE_VALUE'])
                                                            ) { ?>
                                                                <div class="intec-grid-item-auto">
                                                                    <div class="widget-loader-timer-block widget-loader"></div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!defined('EDITOR')) { ?>
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
                                                            var timer = data.nodes.find('[data-role="timer"]');

                                                            timer.find('[data-role="timer.loader"]').html(response);
                                                        });
                                                    }, {
                                                        'name': '[Component] intec.universe:main.shares (template.6) > Timer',
                                                        'nodes': <?= JavaScript::toObject('#'.$sAreaId) ?>,
                                                        'loader': {
                                                            'name': 'lazy'
                                                        }
                                                    });
                                                </script>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                    <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                        <div class="widget-item-content-picture intec-grid-item-auto intec-grid-item-500-1">
                                            <?= Html::tag($sTag, null, [
                                                'class' => [
                                                    'widget-item-picture',
                                                    'intec-image-effect'
                                                ],
                                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                ],
                                                'style' => [
                                                    'background-image' => 'url(\''.(
                                                        $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arItem['DATA']['PICTURE']['VALUE']['SRC']
                                                    ).'\')'
                                                ]
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!--items-->
                </div>
            </div>
            <?php if ($arResult['NAVIGATION']['USE']) { ?>
                <div class="widget-pagination" data-role="navigation">
                    <!--navigation-->
                    <?= $arResult['NAVIGATION']['PRINT'] ?>
                    <!--navigation-->
                </div>
            <?php } ?>
        </div>
    </div>
    <?php if ($arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax' && !defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
</div>
