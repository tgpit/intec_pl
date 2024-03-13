<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\Core;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arData = $arResult['DATA'];

$sCity =  $arResult['PROPERTIES'][$arData['CITY']]['VALUE'];
$sSkill =  $arResult['PROPERTIES'][$arData['SKILL']]['VALUE'];
$sTypeEmployment =  $arResult['PROPERTIES'][$arData['TYPE_EMPLOYMENT']]['VALUE'];
$sSalary = $arResult['PROPERTIES'][$arData['SALARY']]['VALUE'];

$arPicture = [];
$arPictures = $arResult['DETAIL_PICTURE'];

if (empty($arPictures))
    $arPictures = $arResult['PREVIEW_PICTURE'];

if (!empty($arPictures))
    $arPicture = CFile::ResizeImageGet( $arPictures,
        ['width' => 1700, 'height' => 400],
        BX_RESIZE_IMAGE_EXACT
    );

?>

<div class="ns-bitrix c-news-detail c-news-detail-vacancies-detail-1">
    <div class="news-detail-wrapper intec-content">
        <div class="news-detail-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arPicture)) { ?>
                <div class="news-detail-image-wrap">
                    <?= Html::tag('div', null, [
                        'class' => 'news-detail-image',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['src'] : null
                        ],
                        'style' => [
                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arPicture['src'].'\')' : null
                        ]
                    ]) ?>
                </div>
            <?php } ?>
            <div class="news-detail-info intec-grid intec-grid-a-h-between intec-grid-wrap">
                <div class="news-detail-info-element intec-grid intec-grid-o-vertical">
                    <span class="news-detail-info-element-name">
                        <?= Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_CITY') ?>
                    </span>
                    <span class="news-detail-info-element-value">
                        <?= $sCity ?>
                    </span>
                </div>
                <div class="news-detail-info-element intec-grid intec-grid-o-vertical">
                    <span class="news-detail-info-element-name">
                        <?= Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_SALARY') ?>
                    </span>
                    <span class="news-detail-info-element-value">
                        <?= $sSalary ?>
                    </span>
                </div>
                <div class="news-detail-info-element intec-grid intec-grid-o-vertical">
                    <span class="news-detail-info-element-name">
                        <?= Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_EXPERIENCE') ?>
                    </span>
                    <span class="news-detail-info-element-value">
                        <?= $sSkill ?>
                    </span>
                </div>
                <div class="news-detail-info-element intec-grid intec-grid-o-vertical">
                    <span class="news-detail-info-element-name">
                        <?= Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_TYPE_EMPLOYMENT') ?>
                    </span>
                    <span class="news-detail-info-element-value">
                        <?= $sTypeEmployment ?>
                    </span>
                </div>
            </div>
            <div class="news-detail-description">
                <?= $arResult['DETAIL_TEXT'] ?>
            </div>
            <?php if ($arResult['FORM']['SUMMARY']['SHOW']) { ?>
                <div class="news-detail-send-summary-wrap">
                    <?= Html::tag('button', Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_SEND_SUMMARY'), [
                        'class' => [
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current',
                                'size-4',
                                'mod-round-2'
                            ],
                            'news-detail-send-summary'
                        ],
                        'onclick' => '(function() {
                            template.api.forms.show('.JavaScript::toObject([
                                'id' => $arResult['FORM']['SUMMARY']['ID'],
                                'parameters' => [
                                    'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_FORM',
                                    'CONSENT_URL' => $arParams['CONSENT_URL']
                                ],
                                'fields' => [
                                    $arResult['FORM']['SUMMARY']['PROPERTIES']['VACANCY'] => $arResult['NAME']
                                ],
                                'settings' => [
                                    'title' => $arResult['FORM']['SUMMARY']['TITLE']
                                ]
                            ]).');
                        })()'
                    ]) ?>
                </div>
            <?php } ?>
            <hr class="news-detail-hr">
            <div class="news-detail-back intec-cl-text intec-grid intec-grid-a-v-center">
                <span class="news-detail-back-icon">
                     <svg width="4" height="8" viewBox="0 0 4 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.33329 1.33325L0.666626 3.99992L3.33329 6.66659" stroke="#808080" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <a href="<?=$arResult['LIST_PAGE_URL']?>" class="news-detail-back-text">
                    <?= Loc::getMessage('C_NEWS_DETAIL_VACANCIES_DETAIL_1_BACK') ?>
                </a>
            </div>
        </div>
    </div>
</div>