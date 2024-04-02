<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arData = $arResult['DATA'];
$arSvg = [
    'CONTACT' => [
        'PHONE' => FileHelper::getFileData(__DIR__.'/svg/contact.phone.svg'),
        'EMAIL' => FileHelper::getFileData(__DIR__.'/svg/contact.email.svg')
    ],
    'SOCIAL' => [
        'VK' => FileHelper::getFileData(__DIR__.'/svg/social.vk.svg'),
        'FB' => FileHelper::getFileData(__DIR__.'/svg/social.fb.svg'),
        'INST' => FileHelper::getFileData(__DIR__.'/svg/social.inst.svg'),
        'TW' => FileHelper::getFileData(__DIR__.'/svg/social.tw.svg'),
        'SKYPE' => FileHelper::getFileData(__DIR__.'/svg/social.skype.svg')
    ],
    'FOOTER' => [
        'ARROW' => FileHelper::getFileData(__DIR__.'/svg/footer.arrow.svg')
    ]
];

if ($arVisual['PICTURE']['SHOW']) {
    $sPicture = $arResult['DETAIL_PICTURE'];

    if (empty($sPicture))
        $sPicture = $arResult['PREVIEW_PICTURE'];

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 300,
            'height' => 300
        ], BX_RESIZE_IMAGE_EXACT);

        if (!empty($sPicture))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';
}

$bInformationShow = false;

if (
    $arData['POSITION']['SHOW'] ||
    $arVisual['NAME']['SHOW'] ||
    $arData['PHONE']['SHOW'] ||
    $arData['EMAIL']['SHOW'] ||
    $arData['SOCIAL']['SHOW'] ||
    $arResult['FORM']['ASK']['USE']
)
    $bInformationShow = true;

$bInformationAndPictureShow = $arVisual['PICTURE']['SHOW'] || $bInformationShow;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'news-detail',
    'data-picture' => $arVisual['PICTURE']['SHOW'] ? 'true' : 'false'
]) ?>
    <?php if ($bInformationAndPictureShow) { ?>
        <div class="news-detail-information-container intec-grid intec-grid-650-wrap">
            <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                <div class="intec-grid-item-auto intec-grid-item-650-1">
                    <div class="news-detail-picture intec-image-effect">
                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                            'alt' => $arResult['NAME'],
                            'title' => $arResult['NAME'],
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                            ]
                        ]) ?>
                    </div>
                </div>
            <?php } ?>
            <?php if ($bInformationShow) { ?>
                <div class="intec-grid-item intec-grid-item-650-1">
                    <div class="news-detail-information">
                        <?php if ($arData['POSITION']['SHOW'] || $arVisual['NAME']['SHOW']) { ?>
                            <div class="news-detail-base-container">
                                <?php if ($arData['POSITION']['SHOW']) { ?>
                                    <div class="news-detail-position">
                                        <?= $arData['POSITION']['VALUE'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['NAME']['SHOW']) { ?>
                                    <div class="news-detail-name">
                                        <?= $arResult['NAME'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($arData['PHONE']['SHOW'] || $arData['EMAIL']['SHOW'] || $arData['SOCIAL']['SHOW']) { ?>
                            <div class="news-detail-contact-container">
                                <?php if ($arData['PHONE']['SHOW'] || $arData['EMAIL']['SHOW']) {
                                    include(__DIR__.'/parts/contacts.standard.php');
                                } ?>
                                <?php if ($arData['SOCIAL']['SHOW']) {
                                    include(__DIR__.'/parts/contacs.social.php');
                                } ?>
                            </div>
                        <?php } ?>
                        <?php if ($arResult['FORM']['ASK']['USE']) { ?>
                            <?php if (empty($arResult['FORM']['ASK']['BUTTON']['TEXT']))
                                $arResult['FORM']['ASK']['BUTTON']['TEXT'] = Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_TEMPLATE_FORM_ASK_BUTTON_TEXT_DEFAULT');
                            ?>
                            <div class="news-detail-button-container">
                                <?= Html::tag('div', $arResult['FORM']['ASK']['BUTTON']['TEXT'], [
                                    'class' => [
                                        'news-detail-button',
                                        'intec-cl-text',
                                        'intec-cl-border',
                                        'intec-cl-background-hover'
                                    ],
                                    'data-role' => 'button.ask'
                                ]) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arResult['DETAIL_TEXT'])) { ?>
        <div class="news-detail-description">
            <?php if ($arData['DESCRIPTION']['HEADER']['SHOW']) { ?>
                <div class="news-detail-description-header">
                    <?= $arData['DESCRIPTION']['HEADER']['VALUE'] ?>
                </div>
            <?php } ?>
            <div class="news-detail-description-text">
                <?= $arResult['DETAIL_TEXT'] ?>
            </div>
        </div>
    <?php } ?>
    <?php if ($arData['PROJECTS']['SHOW']) { ?>
        <?php include(__DIR__.'/parts/projects.php'); ?>
    <?php } ?>
    <?php if ($arData['REVIEWS']['SHOW']) { ?>
        <?php include(__DIR__.'/parts/reviews.php'); ?>
    <?php } ?>
    <?php if ($arVisual['BUTTON']['BACK']['SHOW']) { ?>
        <?php if (empty($arVisual['BUTTON']['BACK']['TEXT']))
            $arVisual['BUTTON']['BACK']['TEXT'] = Loc::getMessage('C_NEWS_DETAIL_STAFF_DEFAULT_1_TEMPLATE_BUTTON_BACK_TEXT_DEFAULT');
        ?>
        <div class="news-detail-footer">
            <a class="news-detail-footer-back intec-cl-text-hover intec-cl-svg-path-stroke-hover" href="<?= $arResult['LIST_PAGE_URL'] ?>">
                <span class="news-detail-footer-back-icon">
                    <?= $arSvg['FOOTER']['ARROW'] ?>
                </span>
                <span class="news-detail-footer-back-text">
                    <?= $arVisual['BUTTON']['BACK']['TEXT'] ?>
                </span>
            </a>
        </div>
    <?php } ?>
    <?php if ($arResult['FORM']['ASK']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
<?= Html::endTag('div') ?>