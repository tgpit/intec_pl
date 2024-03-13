<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arLazyLoad = $arResult['LAZYLOAD'];

?>
<div class="projects" id="<?= $sTemplateId ?>">
    <?php if ($arResult['VIEW_PARAMETERS']['TABS'] === 'scroll') { ?>
        <div class="scrollbar-outer" data-role="scrollbar">
    <?php } ?>
    <?= Html::beginTag('ul', [
        'class' => [
            'news-list-tabs',
            'tabs-view-' . $arResult['VIEW_PARAMETERS']['TABS'],
            'intec-ui' => [
                '',
                'control-tabs',
                'mod-block',
                'scheme-current',
            ]
        ],
        'data' => [
            'ui-control' => 'tabs'
        ]
    ]) ?>
        <?php $bActive = true ?>
        <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
            <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
            <?= Html::beginTag('li', [
                'class' => [
                    'intec-ui-part-tab',
                    'tabs-view-' . $arResult['VIEW_PARAMETERS']['TABS']
                ],
                'data' => [
                    'active' => $bActive ? 'true' : 'false'
                ]
            ]) ?>
                <a href="#projects-<?= $sTemplateId ?>-section-<?= $arSection['ID'] ?>" data-type="tab">
                    <?= $arSection['NAME'] ?>
                </a>
            <?= Html::endTag('li') ?>
            <?php $bActive = false ?>
        <?php } ?>
    <?= Html::endTag('ul') ?>
    <?php if ($arResult['VIEW_PARAMETERS']['TABS'] === 'scroll') { ?>
        </div>
    <?php } ?>
    <div class="intec-ui intec-ui-control-tabs-content intec-ui-clearfix">
        <?php $bActive = true ?>
        <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
            <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
            <?= Html::beginTag('div', [
                'id' => 'projects-'.$sTemplateId.'-section-'.$arSection['ID'],
                'class' => 'intec-ui-part-tab',
                'data' => [
                    'active' => $bActive ? 'true' : 'false'
                ]
            ]) ?>
                <div class="projects-items">
                    <div class="projects-items-wrapper">
                        <?php foreach($arSection['ITEMS'] as $arItem) { ?>
                        <?php
                            $sId = $sTemplateId.'_desktop_default_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);
                            $sImage = null;

                            if (!empty($arItem['PREVIEW_PICTURE'])) {
                                $sImage = $arItem['PREVIEW_PICTURE'];
                            } else if (!empty($arItem['DETAIL_PICTURE'])) {
                                $sImage = $arItem['DETAIL_PICTURE'];
                            }

                            $sImage = CFile::ResizeImageGet($sImage, array(
                                'width' => 380,
                                'height' => 248
                            ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sImage)) {
                                $sImage = $sImage['src'];
                            } else {
                                $sImage = null;
                            }
                        ?>
                            <div class="projects-item">
                                <div class="projects-item-wrapper" id="<?= $sAreaId ?>">
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'projects-item-wrapper-2'
                                        ],
                                        'href' => $arItem['DETAIL_PAGE_URL'],
                                        'data' => [
                                            'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                                            'original' => $arLazyLoad['USE'] ? $sImage : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arLazyLoad['USE'] ? 'url(\''.$sImage.'\')' : null
                                        ]
                                    ]) ?>
                                        <div class="projects-item-name">
                                            <div class="projects-item-name-wrapper">
                                                <?= $arItem['NAME'] ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('a') ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
            <?php $bActive = false ?>
        <?php } ?>
    </div>
    <?php if ($arResult['VIEW_PARAMETERS']['TABS'] === 'scroll') { ?>
        <script type="text/javascript">
            template.load(function (data) {
                var app = this;
                var $ = app.getLibrary('$');
                var root = data.nodes;
                var scrollbar = $('[data-role="scrollbar"]', root);

                scrollbar.scrollbar();
            }, {
                'name': '[Component] bitrix:news.list (projects.list)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'lazy'
                }
            });
        </script>
    <?php } ?>
</div>
<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
    <br /><?=$arResult["NAV_STRING"]?>
<?endif;?>
