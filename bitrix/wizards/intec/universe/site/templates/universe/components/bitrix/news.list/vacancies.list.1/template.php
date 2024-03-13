<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/** @var array $arParams
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

$arVisual = $arResult['VISUAL'];
$arData = $arResult['DATA'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-list',
        'c-news-list-vacancies-list-1'
    ],
    'data-detail-page-use' => $arVisual['DETAIL_PAGE']['USE'] ? 'true' : 'false'
]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['CONTACT_PERSON']['SHOW']) {

                $sPicture = $arResult['CONTACT_PERSON']['PREVIEW_PICTURE'];

                if (empty($sPicture))
                    $sPicture = $arResult['CONTACT_PERSON']['DETAIL_PICTURE'];

                if (!empty($sPicture)) {
                    $sPicture = CFile::ResizeImageGet($sPicture, [
                        'width' => 100,
                        'height' => 100
                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    if (!empty($sPicture['src']))
                        $sPicture = $sPicture['src'];
                }

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                ?>
                <div class="news-list-contact-person">
                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-20 intec-grid-i-v-6 intec-grid-a-h-between intec-grid-wrap">
                        <div class="news-list-contact-person-property intec-grid-item-auto intec-grid-item-600-1">
                            <div class="intec-grid intec-grid-a-v-center">
                                <?= Html::tag('div', null, [
                                    'class' => [
                                        'intec-grid-item-auto',
                                        'news-list-contact-person-image-wrap'
                                    ],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                <div class="intec-grid-item intec-grid-item-shrink-1">
                                    <div class="news-list-contact-person-title">
                                        <?= Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_TEXT") ?>
                                    </div>
                                    <div class="news-list-contact-person-text">
                                        <?= $arResult['CONTACT_PERSON']['NAME'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($arResult['CONTACT_PERSON']['EMAIL'])) { ?>
                            <div class="intec-grid-item-auto">
                                <div class="news-list-contact-person-email">
                                    <div class="news-list-contact-person-title">
                                        <?= Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_EMAIL") ?>
                                    </div>
                                    <?= Html::tag('a', $arResult['CONTACT_PERSON']['EMAIL'], [
                                        'class' => 'news-list-contact-person-text',
                                        'href' => "mailto:".$arResult['CONTACT_PERSON']['EMAIL']
                                    ]) ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (!empty($arResult['CONTACT_PERSON']['EMAIL'])) { ?>
                            <div class="intec-grid-item-auto">
                                <div class="news-list-contact-person-phone">
                                    <div class="news-list-contact-person-title">
                                        <?= Loc::getMessage("C_NEWS_LIST_VACANCIES_LIST_1_CONTACT_PERSON_PHONE") ?>
                                    </div>
                                    <?= Html::tag('a', $arResult['CONTACT_PERSON']['PHONE'], [
                                        'class' => 'news-list-contact-person-text',
                                        'href' => "tel:".$arResult['CONTACT_PERSON']['PHONE']
                                    ]) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="news-list-contact-person-bottom intec-grid intec-grid-i-h-20 intec-grid-i-v-6 intec-grid-a-h-between intec-grid-wrap">
                        <div class="news-list-contact-person-description intec-grid-item">
                            <?= !empty($arVisual['FULL_DESCRIPTION']['SHOW']) ? mb_strimwidth($arResult['CONTACT_PERSON']['PREVIEW_TEXT'] , 0, 150, "...") : $arResult['CONTACT_PERSON']['PREVIEW_TEXT'];?>
                        </div>
                        <?php if ($arVisual['CONTACT_PERSON']['FORM']['SHOW']) { ?>
                            <div class="intec-grid-item-auto intec-grid-item-700-1">
                                <div class="news-list-contact-person-button">
                                    <?= Html::tag('button', Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SEND_SUMMARY'), [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'scheme-current',
                                                'size-4',
                                                'mod-round-2'
                                            ],
                                            'news-list-send-summary'
                                        ],
                                        'onclick' => '(function() {
                                            template.api.forms.show('.JavaScript::toObject([
                                                'id' => $arResult['FORMS']['SUMMARY']['ID'],
                                                'parameters' => [
                                                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM',
                                                    'CONSENT_URL' => $arParams['CONSENT_URL']
                                                ],
                                                'settings' => [
                                                    'title' => $arResult['FORMS']['SUMMARY']['TITLE']
                                                ]
                                            ]).');
                                         })()'
                                    ]) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <div class="news-list-sections" data-role="sections">
                <?php foreach($arResult['SECTIONS'] as $arSection) { ?>
                    <?php if (count($arSection['ITEMS']) <= 0) continue; ?>
                    <div class="news-list-section-title">
                        <?= $arSection['NAME'] ?>
                    </div>
                    <div class="news-list-section" data-role="section">
                        <?php $bItemFirst = true ?>
                        <?php foreach ($arSection['ITEMS'] as $arItem) { ?>
                            <?php
                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sCity =  ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_CITY'], 'VALUE']);
                            $sSkill =  ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_SKILL'], 'VALUE']);
                            $sTypeEmployment =  ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_TYPE_EMPLOYMENT'], 'VALUE']);
                            $sSalary = ArrayHelper::getValue($arItem, ['PROPERTIES', $arParams['PROPERTY_SALARY'], 'VALUE']);

                            $sTag = 'div';

                            if ($arVisual['DETAIL_PAGE']['USE'] && !empty($arItem['DETAIL_PAGE_URL']))
                                $sTag = 'a';

                            ?>
                            <?php if (!$bItemFirst) { ?>
                                <div class="news-list-delimiter"></div>
                            <?php } ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $sTag === 'a' && $arVisual['DETAIL_PAGE']['LINK']['BLANK'] ? '_blank' : null,
                                'class' => Html::cssClassFromArray([
                                    'news-list-item' => true,
                                    'intec-cl-hover-text' => $sTag === 'a'
                                ], true),
                                'data' => [
                                    'role' => 'item'
                                ]
                            ]) ?>
                                <div class="news-list-item-wrapper">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'news-list-item-name',
                                            'intec-grid' => [
                                                '',
                                                'wrap',
                                                'a-h-between',
                                                'a-v-center'
                                            ]
                                        ],
                                        'data' => [
                                            'action' => !$arVisual['DETAIL_PAGE']['USE'] ? 'toggle' : null
                                        ]
                                    ]) ?>
                                        <div class="news-list-item-name-right intec-grid-item">
                                            <div class="news-list-item-name-text">
                                                <?= $arItem['NAME'] ?>
                                            </div>
                                            <?php if (!empty($sCity) || !empty($sSkill) || !empty($sTypeEmployment)) { ?>
                                                <div class="news-list-item-name-stickers intec-grid intec-grid-a-v-center intec-grid-wrap">
                                                    <?php if (!empty($sCity)) { ?>
                                                        <span class="news-list-item-name-sticker intec-grid-item-auto">
                                                            <?= $sCity ?>
                                                            <?php if (!empty($sSkill) || !empty($sTypeEmployment)) { ?>
                                                                <span class="news-list-item-name-sticker-separator">/</span>
                                                            <?php } ?>
                                                        </span>
                                                    <?php } ?>
                                                    <?php if (!empty($sSkill)) { ?>
                                                        <span class="news-list-item-name-sticker intec-grid-item-auto">
                                                            <?= $sSkill ?>
                                                            <?php if (!empty($sTypeEmployment)) { ?>
                                                                <span class="news-list-item-name-sticker-separator">/</span>
                                                            <?php } ?>
                                                        </span>
                                                    <?php } ?>
                                                    <?php if (!empty($sTypeEmployment)) { ?>
                                                        <span class="news-list-item-name-sticker intec-grid-item-auto">
                                                            <?= $sTypeEmployment ?>
                                                        </span>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="intec-grid-item-auto intec-grid-item-500-1">
                                            <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-between">
                                                <?php if ( $arVisual['SALARY']['SHOW'] ) { ?>
                                                    <div class="news-list-item-price">
                                                        <?= $sSalary ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if (!$arVisual['DETAIL_PAGE']['USE']) { ?>
                                                    <div class="news-list-item-name-indicators">
                                                        <i class="fa fa-chevron-up news-list-item-name-indicator news-list-item-name-indicator-active intec-cl-background-hover"></i>
                                                        <i class="fa fa-chevron-down news-list-item-name-indicator news-list-item-name-indicator-inactive intec-cl-background-hover"></i>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?= Html::endTag('div') ?>

                                    <?php if (!$arVisual['DETAIL_PAGE']['USE']) { ?>
                                        <div class="news-list-item-description" data-role="item.description">
                                            <div class="news-list-item-description-wrapper"><?= $arItem['PREVIEW_TEXT'] ?></div>
                                            <?php if ($arVisual['SUMMARY']['FORM']['SHOW']) { ?>
                                                <div class="news-list-item-description-buttons">
                                                    <?= Html::tag('button', Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SEND_SUMMARY'), [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-button',
                                                                'scheme-current',
                                                                'size-4',
                                                                'mod-round-2'
                                                            ],
                                                            'news-list-send-summary'
                                                        ],
                                                        'onclick' => '(function() {
                                                            template.api.forms.show('.JavaScript::toObject([
                                                                'id' => $arResult['FORMS']['SUMMARY']['ID'],
                                                                'parameters' => [
                                                                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM',
                                                                    'CONSENT_URL' => $arParams['CONSENT_URL']
                                                                ],
                                                                'fields' => [
                                                                    $arResult['FORMS']['SUMMARY']['PROPERTIES']['VACANCY'] => $arItem['NAME']
                                                                ],
                                                                'settings' => [
                                                                    'title' => $arResult['FORMS']['SUMMARY']['TITLE']
                                                                ]
                                                            ]).');
                                                        })()'
                                                    ]) ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag($sTag) ?>
                            <?php $bItemFirst = false ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php if (!$arVisual['DETAIL_PAGE']['USE']) { ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?php } ?>
