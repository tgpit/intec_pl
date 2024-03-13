<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arLazyLoad
 * @var CBitrixComponent $component
 * @var CAllMain $APPLICATION
 */

?>
<div class="news-detail-content-tabs">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <ul class="news-detail-content-tabs-list intec-ui intec-ui-control-tabs intec-ui-scheme-current intec-ui-view-2" data-ui-control="tabs" data-role="tabs">
                <?php if (!empty($arResult['OBJECTIVE'])) { ?>
                    <li class="news-detail-content-tabs-list-item intec-ui-part-tab" data-active="true" data-role="tabs.item">
                        <?= Html::tag('a', Loc::getMessage('N_PROJECTS_N_D_DEFAULT_SECTION_OBJECTIVE'), [
                            'href' => '#'.$sTemplateId.'-objective',
                            'data-type' => 'tab'
                        ]) ?>
                    </li>
                <?php } ?>
                <?php if ($arResult['SOLUTION']['SHOW']) { ?>
                    <li class="news-detail-content-tabs-list-item intec-ui-part-tab" data-active="false" data-role="tabs.item">
                        <?= Html::tag('a', Loc::getMessage('N_PROJECTS_N_D_DEFAULT_SECTION_SOLUTION'), [
                            'href' => '#'.$sTemplateId.'-solution',
                            'data-type' => 'tab'
                        ]) ?>
                    </li>
                <?php } ?>
                <?php if ($arResult['REVIEWS']) { ?>
                    <li class="news-detail-content-tabs-list-item intec-ui-part-tab" data-active="false" data-role="tabs.item">
                        <?= Html::tag('a', Loc::getMessage('N_PROJECTS_N_D_DEFAULT_SECTION_REVIEWS'), [
                            'href' => '#'.$sTemplateId.'-reviews',
                            'data-type' => 'tab'
                        ]) ?>
                    </li>
                <?php } ?>
                <?php if (!empty($arResult['ORDER_PROJECT'])) { ?>
                    <li class="news-detail-content-tabs-list-item intec-ui-part-tab" data-active="false" data-role="tabs.item">
                        <?= Html::tag('a', Loc::getMessage('N_PROJECTS_N_D_DEFAULT_ORDER_PROJECT'), [
                            'href' => '#'.$sTemplateId.'-order-project',
                            'data-type' => 'tab'
                        ]) ?>
                    </li>
                <?php } ?>
            </ul>
            <div class="news-detail-content-tabs-content intec-ui intec-ui-control-tabs-content" data-role="tabs-content">
                <?php if (!empty($arResult['OBJECTIVE'])) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'intec-ui-part-tab',
                        'id' => $sTemplateId.'-objective',
                        'data-active' => 'true'
                    ]) ?>
                        <div class="news-detail-content-tabs-content-item">
                            <div class="news-detail-content-tabs-content-item-text">
                                <?= $arResult['OBJECTIVE'] ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php if ($arResult['SOLUTION']['SHOW']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'intec-ui-part-tab',
                        'id' => $sTemplateId.'-solution',
                        'data-active' => 'false'
                    ]) ?>
                        <div class="news-detail-content-tabs-content-item">
                            <?php if (!empty($arResult['SOLUTION']['FULL'])) { ?>
                                <div class="news-detail-content-tabs-content-item-text">
                                    <?= $arResult['SOLUTION']['FULL'] ?>
                                </div>
                                <?php if (!empty($arResult['SOLUTION']['IMAGE'])) { ?>
                                    <?= Html::img($arLazyLoad['USE'] ? $arLazyLoad['STUB'] : $arResult['SOLUTION']['IMAGE']['SRC'], [
                                        'alt' => $arItem['NAME'],
                                        'title' => $arItem['NAME'],
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                                            'original' => $arLazyLoad['USE'] ? $arResult['SOLUTION']['IMAGE']['SRC'] : null
                                        ],
                                        'class' => Html::cssClassFromArray([
                                            'news-detail-content-tabs-content-item-solution-full-image' => true,
                                            'image-border' => $arResult['SOLUTION']['IMAGE_BORDER']
                                        ], true)
                                    ]) ?>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="news-detail-content-tabs-content-item-text">
                                    <?= $arResult['SOLUTION']['BEGIN'] ?>
                                </div>
                                <?php if (!empty($arResult['SOLUTION']['IMAGE'])) { ?>
                                    <?= Html::img($arLazyLoad['USE'] ? $arLazyLoad['STUB'] : $arResult['SOLUTION']['IMAGE']['SRC'], [
                                        'alt' => $arItem['NAME'],
                                        'title' => $arItem['NAME'],
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                                            'original' => $arLazyLoad['USE'] ? $arResult['SOLUTION']['IMAGE']['SRC'] : null
                                        ],
                                        'class' => Html::cssClassFromArray([
                                            'news-detail-content-tabs-content-item-solution-image' => true,
                                            'image-border' => $arResult['SOLUTION']['IMAGE_BORDER']
                                        ], true)
                                    ]) ?>
                                <?php } ?>
                                <div class="news-detail-content-tabs-content-item-text">
                                    <?= $arResult['SOLUTION']['END'] ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>

                <?php include(__DIR__.'/reviews.php') ?>

                <?php if (!empty($arResult['ORDER_PROJECT'])) { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'intec-ui-part-tab',
                        'id' => $sTemplateId.'-order-project',
                        'data-active' => 'false'
                    ]) ?>
                        <div class="news-detail-content-tabs-content-item">
                            <div class="news-detail-content-tabs-content-item-text">
                                <?= $arResult['ORDER_PROJECT'] ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
