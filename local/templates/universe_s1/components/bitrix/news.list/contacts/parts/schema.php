<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

if (empty($arResult['CONTACT']))
    return;

$sSiteUrl = Core::$app->request->getHostInfo().SITE_DIR;
$arPicture = !empty($arResult['CONTACT']['PREVIEW_PICTURE']) ? $arResult['CONTACT']['PREVIEW_PICTURE'] : $arResult['CONTACT']['DETAIL_PICTURE'];

if (!empty($arPicture)) {
    $sPicture = $arPicture['SRC'];
} else {
    $sPicture = $sSiteUrl.'include/logotype.png';
}

?>
<div itemscope itemtype="http://schema.org/LocalBusiness" style="display:none;">
    <?php if (!empty($arResult['CONTACT']['NAME'])) { ?>
        <span itemprop="name">
            <?= $arResult['CONTACT']['NAME'] ?>
        </span>
    <?php } ?>
    <img itemprop="image" src="<?= $sPicture ?>" alt="<?= $arResult['CONTACT']['NAME'] ?>" title="<?= $arResult['CONTACT']['NAME'] ?>" />
    <?php if (!empty($arResult['CONTACT']['DATA']['ADDRESS'])) { ?>
        <div itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
            <span itemprop="streetAddress">
                <?= $arResult['CONTACT']['DATA']['ADDRESS'] ?>
            </span>
        </div>
    <?php } ?>
    <?php if (!empty($arResult['CONTACT']['DATA']['PHONE'])) { ?>
        <span itemprop="telephone">
            <?= $arResult['CONTACT']['DATA']['PHONE']['DISPLAY'] ?>
        </span>
    <?php } ?>
    <?php if (!empty($arResult['CONTACT']['DATA']['EMAIL'])) { ?>
        <span itemprop="email">
            <?= $arResult['CONTACT']['DATA']['EMAIL'] ?>
        </span>
    <?php } ?>
    <?php if (!empty($arResult['CONTACT']['DATA']['OPENING_HOURS'])) { ?>
        <time itemprop="openingHours" datetime="<?= $arResult['CONTACT']['DATA']['OPENING_HOURS'] ?>"></time>
    <?php } ?>
</div>