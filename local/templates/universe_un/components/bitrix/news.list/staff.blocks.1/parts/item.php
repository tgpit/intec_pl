<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var bool $bDesktop
 * @var string $sTemplateId
 * @var string $sTag
 * @var CBitrixComponentTemplate $this
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, $sTemplateId, $bDesktop, &$arSvg, &$sTag) {

    $sId = $sTemplateId.'_'.$arItem['ID'];
    $sAreaId = $this->GetEditAreaId($sId);
    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

    $arData = $arItem['DATA'];

    $bAdditional = $arData['PHONE']['SHOW'] || $arData['EMAIL']['SHOW'] || $arData['SOCIAL']['SHOW'] || $arResult['FORM']['ASK']['USE'];

    $sPicture = $arItem['PREVIEW_PICTURE'];

    if (empty($sPicture))
        $sPicture = $arItem['DETAIL_PICTURE'];

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 550,
            'height' => 550
        ], BX_RESIZE_IMAGE_EXACT);

        if (!empty($sPicture['src']))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <?= Html::beginTag('div', [
        'class' => Html::cssClassFromArray([
            'news-list-item' => true,
            'intec-grid-item' => [
                $arVisual['COLUMNS'] => true,
                '1200-3' => $arVisual['COLUMNS'] >= 4,
                '1024-2' => true,
                '550-1' => true
            ]
        ], true)
    ]) ?>
        <?= Html::beginTag('div', [
            'id' => $sAreaId,
            'class' => 'news-list-item-container',
            'data' => [
                'role' => 'item',
                'expanded' => 'false',
                'additional' => $bAdditional ? 'true' : 'false',
                'form-ask' => $arResult['FORM']['ASK']['USE'] ? 'true' : 'false'
            ]
        ]) ?>
            <?= Html::tag($sTag, null, [
                'class' => [
                    'news-list-item-picture',
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
            <div class="news-list-item-text" data-role="item.text">
                <div class="news-list-item-text-content">
                    <div class="news-list-item-text-base" data-role="item.text.base">
                        <?php if ($arData['POSITION']['SHOW']) { ?>
                            <?= Html::tag('div', $arData['POSITION']['VALUE'], [
                                'class' => 'news-list-item-position'
                            ]) ?>
                        <?php } ?>
                        <?= Html::tag($sTag, $arItem['NAME'], [
                            'class' => Html::cssClassFromArray([
                                'news-list-item-name' => true,
                                'intec-cl-text-hover' => $sTag === 'a'
                            ], true),
                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                            'data-role' => 'item.name'
                        ]) ?>
                    </div>
                    <?php if ($bAdditional && $bDesktop) { ?>
                        <div class="news-list-item-text-additional" data-role="item.text.additional">
                            <?php if ($arData['PHONE']['SHOW'] || $arData['EMAIL']['SHOW']) { ?>
                                <div class="news-list-item-text-additional-container">
                                    <?php if ($arData['PHONE']['SHOW']) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'news-list-item-contact',
                                                'intec-grid' => [
                                                    '',
                                                    'nowrap',
                                                    'a-v-center',
                                                    'i-h-4'
                                                ]
                                            ]
                                        ]) ?>
                                            <div class="news-list-item-contact-icon intec-grid-item-auto">
                                                <?= $arSvg['CONTACTS']['PHONE'] ?>
                                            </div>
                                            <div class="news-list-item-contact-value intec-grid-item">
                                                <?= Html::tag('a', $arData['PHONE']['VALUE'], [
                                                    'class' => 'intec-cl-text-hover',
                                                    'href' => 'tel:'.$arData['PHONE']['HTML'],
                                                    'title' => $arData['PHONE']['VALUE']
                                                ]) ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    <?php } ?>
                                    <?php if ($arData['EMAIL']['SHOW']) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'news-list-item-contact',
                                                'intec-grid' => [
                                                    '',
                                                    'nowrap',
                                                    'a-v-center',
                                                    'i-h-4'
                                                ]
                                            ]
                                        ]) ?>
                                            <div class="news-list-item-contact-icon intec-grid-item-auto">
                                                <?= $arSvg['CONTACTS']['EMAIL'] ?>
                                            </div>
                                            <div class="news-list-item-contact-value intec-grid-item">
                                                <?= Html::tag('a', $arData['EMAIL']['VALUE'], [
                                                    'class' => 'intec-cl-text-hover',
                                                    'href' => 'mailto:'.$arData['EMAIL']['VALUE'],
                                                    'title' => $arData['EMAIL']['VALUE']
                                                ]) ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if ($arData['SOCIAL']['SHOW']) { ?>
                                <div class="news-list-item-text-additional-container">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'intec-grid' => [
                                                '',
                                                'wrap',
                                                'a-v-center',
                                                'i-6'
                                            ]
                                        ]
                                    ]) ?>
                                        <?php foreach ($arData['SOCIAL']['VALUES'] as $key => $arSocial) { ?>
                                            <?php if (empty($arSocial)) continue ?>
                                            <div class="intec-grid-item-auto">
                                                <?php if ($key !== 'SKYPE') { ?>
                                                    <?= Html::tag('a', $arSvg['SOCIAL'][$key], [
                                                        'class' => [
                                                            'news-list-item-social',
                                                            'intec-cl-svg-path-fill-hover'
                                                        ],
                                                        'href' => $arSocial,
                                                        'target' => '_blank'
                                                    ]) ?>
                                                <?php } else { ?>
                                                    <?= Html::tag('a', $arSvg['SOCIAL']['SKYPE'], [
                                                        'class' => [
                                                            'news-list-item-social',
                                                            'intec-cl-svg-path-fill-hover'
                                                        ],
                                                        'href' => 'skype:'.$arSocial.'?'.$arVisual['SOCIAL']['SKYPE']['ACTION']
                                                    ]) ?>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    <?= Html::endTag('div') ?>
                                </div>
                            <?php } ?>
                            <?php if ($arResult['FORM']['ASK']['USE']) { ?>
                                <?php if (empty($arResult['FORM']['ASK']['BUTTON']['TEXT']))
                                    $arResult['FORM']['ASK']['BUTTON']['TEXT'] = Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_TEMPLATE_FORM_ASK_BUTTON_TEXT_DEFAULT')
                                ?>
                                <div class="news-list-item-text-additional-button-container">
                                    <?= Html::tag('div', $arResult['FORM']['ASK']['BUTTON']['TEXT'], [
                                        'class' => [
                                            'news-list-item-text-additional-button',
                                            'intec-cl-background',
                                            'intec-cl-background-light-hover'
                                        ],
                                        'data-role' => 'item.button'
                                    ]) ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
<?php } ?>