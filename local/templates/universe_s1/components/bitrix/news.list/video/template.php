<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);

$classFirst = ($arParams['DISPLAY_FIRST_VIDEO'] == 'Y')
    ? 'video-gallery-list-item-first-big'
    : 'video-gallery-list-item';

$arVisual = $arResult['VISUAL'];
$bFirstElementIsDisplayed = false;
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
?>

<div id = "<?=$sTemplateId?>" class="intec-content">
    <div class="intec-content-wrapper">
        <div class="video-gallery-list clearfix">
            <?php foreach($arResult["ITEMS"] as $arItem) { ?>
                <?php
                $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
                $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

                if(isset($arItem['PROPERTIES'][$arParams['IBLOCK_PROPERTY']]) && is_string($arItem['PROPERTIES'][$arParams['IBLOCK_PROPERTY']]['VALUE'])) {

                    $bFirstElementIsDisplayed = true;

                    $url = $arItem['PROPERTIES'][$arParams['IBLOCK_PROPERTY']]['VALUE'];

                    $idVideo = youtube_video($url);

                    $sDuration = '';

                    $sLinkErrorText = '';

                    if (!empty($arVisual['DURATION'])) {
                        $sDuration = ArrayHelper::getValue($arItem['PROPERTIES'][$arVisual['DURATION']], 'VALUE');
                    }
                ?>
                    <?= Html::beginTag('div', [
                        'class' => $classFirst,
                        'id' => $this->GetEditAreaId($arItem['ID']),
                        'data-src' => $url,
                        'data-play' => "true"
                    ]) ?>
                        <div>
                            <?= Html::tag('div', '', [
                                'class' => 'video-gallery-list-item-wrapper',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $idVideo['sddefault'] : null
                                ],
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$idVideo['sddefault'].'\')' : null
                                ]
                            ]) ?>
                            <div class="video-gallery-list-item-wrapper-dark"></div>
                            <div class="video-gallery-list-item-icon">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="video-gallery-list-item-name">
                                <?= $arItem['NAME'] ?>
                            </div>
                        </div>
                        <div itemscope itemtype="http://schema.org/VideoObject" style="display: none">
                            <span itemprop="name">
                                <?= $arItem['NAME'] ?>
                            </span>
                            <span itemprop="description">
                                <?= $arItem['NAME'] ?>
                            </span>
                            <span itemprop="thumbnail">
                                <?= $idVideo['sddefault'] ?>
                            </span>
                            <?php if (!empty($sDuration)) { ?>
                            <span itemprop="duration">
                                <?= $sDuration ?>
                            </span>
                            <?php } ?>
                            <a itemprop="url" href="<?= Core::$app->request->getHostInfo().$APPLICATION->GetCurUri() ?>"></a>
                            <a itemprop="thumbnailUrl" href="<?= $idVideo['sddefault'] ?>"></a>
                            <span itemprop="uploadDate">
                                <?php $qwe = MakeTimeStamp($arItem['DATE_CREATE']) ?>
                                <?= date('Y-m-d', $qwe) ?>
                            </span>
                            <span itemprop="isFamilyFriendly">
                                True
                            </span>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } else {
                    if ($bFirstElementIsDisplayed || !empty($sLinkErrorText))
                        continue;
                    else {
                        $sLinkErrorText = Loc::getMessage('CT_BNL_LINK_PROPERTY_ERROR');
                        print ($sLinkErrorText);
                    }
                } ?>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var gallery = $('.video-gallery-list', root);

        gallery.lightGallery({
            'selector': '[data-play="true"]'
        });
    }, {
        'name': '[Component] bitrix:news.list (video)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>
