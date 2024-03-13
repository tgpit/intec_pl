<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 * @var string $sTag
 * @var CBitrixComponentTemplate $this
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, $sTemplateId, &$arSvg, &$sTag) {

    $sId = $sTemplateId.'_'.$arItem['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

    $arData = $arItem['DATA'];

    $bContactShow = $arResult['FORM']['ASK']['USE'] || $arData['EMAIL']['SHOW'] || $arData['PHONE']['SHOW'];

    if ($arVisual['PICTURE']['SHOW']) {
        $sPicture = $arItem['PREVIEW_PICTURE'];

        if (empty($sPicture))
            $sPicture = $arItem['DETAIL_PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet($sPicture, [
                'width' => 150,
                'height' => 150
            ], BX_RESIZE_IMAGE_EXACT);

            if (!empty($sPicture['src']))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';
    }

?>
    <?= Html::beginTag('div', [
        'id' => $sAreaId,
        'class' => 'news-list-item',
        'data' => [
            'role' => 'item'
        ]
    ]) ?>
        <div class="intec-grid intec-grid-450-wrap">
            <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                <div class="intec-grid-item-auto intec-grid-item-450-1">
                    <?= Html::beginTag($sTag, [
                        'class' => [
                            'news-list-item-picture',
                            'intec-image-effect'
                        ],
                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                    ]) ?>
                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                            'alt' => $arItem['NAME'],
                            'title' => $arItem['NAME'],
                            'loading' => 'lazy',
                            'data' => [
                                'view' => $arVisual['PICTURE']['VIEW'],
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            ],
                            'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                        ]) ?>
                    <?= Html::endTag($sTag) ?>
                </div>
            <?php } ?>
            <div class="intec-grid-item">
                <div class="news-list-item-content">
                    <div class="news-list-item-content-base">
                        <?php if ($arData['POSITION']['SHOW']) { ?>
                            <div class="news-list-item-position">
                                <?= $arData['POSITION']['VALUE'] ?>
                            </div>
                        <?php } ?>
                        <?= Html::tag($sTag, $arItem['NAME'], [
                            'class' => Html::cssClassFromArray([
                                'news-list-item-name' => true,
                                'intec-cl-text-hover' => $sTag === 'a'
                            ] , true),
                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                            'data-role' => 'item.name'
                        ]) ?>
                    </div>
                    <?php if ($bContactShow || $arData['SOCIAL']['SHOW'] || $arData['PREVIEW']['SHOW']) { ?>
                        <div class="news-list-item-content-additional">
                            <?php if ($bContactShow) { ?>
                                <div class="news-list-item-contact">
                                    <div class="news-list-item-contact-container">
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'intec-grid' => [
                                                    '',
                                                    'wrap',
                                                    'a-v-center',
                                                    'i-h-12',
                                                    'i-v-8',
                                                    'a-h-450-center'
                                                ]
                                            ]
                                        ]) ?>
                                            <?php if ($arResult['FORM']['ASK']['USE']) { ?>
                                                <?php if (empty($arResult['FORM']['ASK']['BUTTON']['TEXT']))
                                                    $arResult['FORM']['ASK']['BUTTON']['TEXT'] = Loc::getMessage('C_NEWS_LIST_STAFF_LIST_1_TEMPLATE_FORM_ASK_BUTTON_TEXT_DEFAULT');
                                                ?>
                                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <?= Html::tag('div', $arResult['FORM']['ASK']['BUTTON']['TEXT'], [
                                                        'class' => [
                                                            'news-list-item-contact-button',
                                                            'intec-cl-border',
                                                            'intec-cl-text',
                                                            'intec-cl-background-hover'
                                                        ],
                                                        'data-role' => 'item.button'
                                                    ]) ?>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arData['EMAIL']['SHOW']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <?= Html::beginTag('a', [
                                                        'class' => 'news-list-item-contact-link',
                                                        'href' => 'mailto:'.$arData['EMAIL']['VALUE']
                                                    ]) ?>
                                                        <span class="news-list-item-contact-link-icon">
                                                            <?= $arSvg['CONTACTS']['EMAIL'] ?>
                                                        </span>
                                                        <span class="news-list-item-contact-link-value intec-cl-text-hover">
                                                            <?= $arData['EMAIL']['VALUE'] ?>
                                                        </span>
                                                    <?= Html::endTag('a') ?>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arData['PHONE']['SHOW']) { ?>
                                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <?= Html::beginTag('a', [
                                                        'class' => 'news-list-item-contact-link',
                                                        'href' => 'tel:'.$arData['PHONE']['HTML']
                                                    ]) ?>
                                                    <span class="news-list-item-contact-link-icon">
                                                            <?= $arSvg['CONTACTS']['PHONE'] ?>
                                                        </span>
                                                    <span class="news-list-item-contact-link-value intec-cl-text-hover">
                                                            <?= $arData['PHONE']['VALUE'] ?>
                                                        </span>
                                                    <?= Html::endTag('a') ?>
                                                </div>
                                            <?php } ?>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arData['SOCIAL']['SHOW']) { ?>
                                <div class="news-list-item-social">
                                    <div class="news-list-item-social-item-container">
                                        <div class="intec-grid intec-grid-wrap intec-grid-i-6 intec-grid-a-h-450-center">
                                            <?php foreach ($arData['SOCIAL']['VALUES'] as $key => $sValue) { ?>
                                                <?php if (empty($sValue)) continue ?>
                                                <div class="intec-grid-item-auto">
                                                    <?php if ($key !== 'SKYPE') { ?>
                                                        <?= Html::tag('a', $arSvg['SOCIAL'][$key], [
                                                            'class' => [
                                                                'news-list-item-social-item',
                                                                'intec-cl-svg-path-fill-hover'
                                                            ],
                                                            'href' => $sValue,
                                                            'target' => '_blank'
                                                        ]) ?>
                                                    <?php } else { ?>
                                                        <?= Html::tag('a', $arSvg['SOCIAL'][$key], [
                                                            'class' => [
                                                                'news-list-item-social-item',
                                                                'intec-cl-svg-path-fill-hover'
                                                            ],
                                                            'href' => 'skype:'.$sValue.'?'.$arVisual['SOCIAL']['SKYPE']['ACTION']
                                                        ]) ?>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                            <?php unset($key, $sValue) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($arData['PREVIEW']['SHOW']) { ?>
                                <div class="news-list-item-preview">
                                    <?= $arData['PREVIEW']['VALUE'] ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>