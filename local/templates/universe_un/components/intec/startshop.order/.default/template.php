<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 */

global $USER;

$this->setFrameMode(false);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arUser = array();

if ($USER->IsAuthorized()) {
    $arUser = CUser::GetByID($USER->GetID())->Fetch();
}

$arVisual = $arResult['VISUAL'];

?>
<?php $fPropertyDraw = function ($arProperty, $arUser = array()) {

    $sValue = '';

    if (isset($_REQUEST['PROPERTY_'.$arProperty['ID']])) {
        $sValue = $_REQUEST['PROPERTY_'.$arProperty['ID']];
    } else if (!empty($arProperty['USER_FIELD']) && !empty($arUser) && !empty($arUser[$arProperty['USER_FIELD']])) {
        $sValue = $arUser[$arProperty['USER_FIELD']];
    }

    if ($arProperty['TYPE'] == 'S' && empty($arProperty['SUBTYPE'])) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'intec-ui-form-field' => true,
                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
            ], true)
        ]) ?>
            <label class="intec-ui-form-field-title" for="PROPERTY_<?= $arProperty['ID'] ?>">
                <?= $arProperty['LANG'][LANGUAGE_ID]['NAME'] ?>
            </label>
            <div class="intec-ui-form-field-content">
                <input type="text"
                    <?= $arProperty['DATA']['LENGTH'] > 0 ? ' maxlength="'. $arProperty['DATA']['LENGTH'] .'"' : '' ?>
                       class="intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-size-3"
                       name="PROPERTY_<?= $arProperty['ID'] ?>"
                       id="PROPERTY_<?= $arProperty['ID'] ?>"
                       value="<?= htmlspecialcharsbx($sValue) ?>" />
            </div>
            <?php if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])) { ?>
                <div class="intec-ui-form-field-description">
                    <?= $arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'] ?>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arProperty['TYPE'] == 'S' && $arProperty['SUBTYPE'] == 'TEXT') { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'intec-ui-form-field' => true,
                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y',
                'text' => true
            ], true)
        ]) ?>
            <label class="intec-ui-form-field-title">
                <?= $arProperty['LANG'][LANGUAGE_ID]['NAME'] ?>
            </label>
            <div class="intec-ui-form-field-content">
                <textarea name="PROPERTY_<?= $arProperty['ID'] ?>" class="intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-size-3" style="resize: vertical"><?= htmlspecialcharsbx($sValue) ?></textarea>
            </div>
            <?php if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])) { ?>
                <div class="intec-ui-form-field-description">
                    <?= $arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'] ?>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arProperty['TYPE'] == 'B' && empty($arProperty['SUBTYPE'])) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'intec-ui-form-field' => true,
                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y',
                'checkbox' => true
            ], true)
        ]) ?>
            <input type="hidden" value="N" name="PROPERTY_<?= $arProperty['ID'] ?>" />
            <label class="intec-ui-form-field-title">
                <?= $arProperty['LANG'][LANGUAGE_ID]['NAME'] ?>
            </label>
            <div class="intec-ui-form-field-content">
                <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                    <?=Html::input('checbox', 'PROPERTY_'.$arProperty['ID'], '', [
                        'checked' => $sValue == 'Y' ? 'checked' : null
                    ])?>
                    <span class="intec-ui-part-selector"></span>
                </label>
            </div>
            <?php if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])) { ?>
                <div class="intec-ui-form-field-description">
                    <?= $arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'] ?>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arProperty['TYPE'] == 'L' && $arProperty['SUBTYPE'] == 'IBLOCK_ELEMENT') { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'intec-ui-form-field' => true,
                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y',
                'list' => true
            ], true)
        ]) ?>
            <label class="intec-ui-form-field-title">
                <?= $arProperty['LANG'][LANGUAGE_ID]['NAME'] ?>
            </label>
            <div class="intec-ui-form-field-content">
                <select name="PROPERTY_<?= $arProperty['ID'] ?>" class="intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-size-3">
                    <?php foreach ($arProperty['VALUES'] as $iPropertyKey => $arPropertyValue) { ?>
                        <option value="<?= $iPropertyKey ?>"
                            <?= $sValue == $iPropertyKey ? 'selected="selected"' : '' ?>>
                            <?= htmlspecialcharsbx($arPropertyValue['NAME']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php if (!empty($arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'])) { ?>
                <div class="intec-ui-form-field-description">
                    <?= $arProperty['LANG'][LANGUAGE_ID]['DESCRIPTION'] ?>
                </div>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-intec',
        'c-startshop-order',
        'c-startshop-order-default'
    ]
]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if (!empty($arResult['ERRORS'])) { ?>
                <div class="startshop-order-notifications">
                    <?php foreach ($arResult['ERRORS'] as $arError) { ?>
                        <?php if ($arError['CODE'] == 'DELIVERY_EMPTY') { ?>
                            <div class="startshop-order-notifications-item intec-ui intec-ui-control-alert intec-ui-scheme-red">
                                <div class="startshop-order-notification-wrapper">
                                    <?= Loc::getMessage('SO_DEFAULT_ERRORS_DELIVERY_EMPTY') ?>
                                </div>
                            </div>
                        <?php } elseif ($arError['CODE'] == 'PAYMENT_EMPTY') { ?>
                            <div class="startshop-order-notifications-item intec-ui intec-ui-control-alert intec-ui-scheme-red">
                                <div class="startshop-order-notification-wrapper">
                                    <?= Loc::getMessage('SO_DEFAULT_ERRORS_PAYMENT_EMPTY') ?>
                                </div>
                            </div>
                        <?php } elseif ($arError['CODE'] == 'PROPERTIES_EMPTY') { ?>
                            <div class="startshop-order-notifications-item intec-ui intec-ui-control-alert intec-ui-scheme-red">
                                <div class="startshop-order-notification-wrapper">
                                    <?php $arPropertiesEmpty = [];

                                        foreach ($arError['PROPERTIES'] as $arProperty)
                                            $arPropertiesEmpty[] = $arProperty['LANG'][LANGUAGE_ID]['NAME'];

                                    ?>
                                    <?= Loc::getMessage('SO_DEFAULT_ERRORS_PROPERTIES_EMPTY', [
                                            '#FIELDS#' => '<b>"'.implode('"</b>, <b>"', $arPropertiesEmpty).'"</b>'
                                    ]) ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if (!empty($arResult['ITEMS'])) { ?>
                <form method="POST" class="intec-ui-form">
                    <input type="hidden" name="<?= $arParams['REQUEST_VARIABLE_ACTION'] ?>" value="order" />
                    <div class="startshop-order-content intec-grid intec-grid-1200-wrap intec-grid-i-h-15">
                        <div class="startshop-order-sections intec-grid-item intec-grid intec-grid-wrap">
                            <?php if (!empty($arResult['PROPERTIES'])) { ?>
                                <div class="startshop-order-section intec-grid-item-1">
                                    <div class="startshop-order-section-header">
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                                            <div class="intec-grid-item-auto">
                                                <div class="startshop-order-section-number intec-ui-align intec-grid-item-auto intec-cl-background">
                                                    <div class="startshop-order-section-number-content">
                                                        1
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item">
                                                <div class="startshop-order-section-title">
                                                    <?= Loc::getMessage('SO_DEFAULT_SECTIONS_PROPERTIES') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="startshop-order-section-content">
                                        <?php foreach ($arResult['PROPERTIES'] as $arProperty) { ?>
                                            <?php $fPropertyDraw($arProperty, $arUser); ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arResult['DELIVERIES'])) { ?>
                                <div class="startshop-order-section intec-grid-item-1">
                                    <div class="startshop-order-section-header">
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                                            <div class="intec-grid-item-auto">
                                                <div class="startshop-order-section-number intec-ui-align intec-grid-item-auto intec-cl-background">
                                                    <div class="startshop-order-section-number-content">
                                                        2
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item">
                                                <div class="startshop-order-section-title">
                                                    <?= Loc::getMessage('SO_DEFAULT_SECTIONS_DELIVERIES_DELIVERY') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php foreach ($arResult['DELIVERIES'] as $iDeliveryKey => $arDelivery) { ?>
                                        <div class="startshop-order-section-content startshop-order-delivery" data-role="delivery">
                                            <div class="startshop-order-delivery-select">
                                                <label class="startshop-order-radio intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-3">
                                                    <span class="intec-grid intec-grid-a-v-start">
                                                        <span class="intec-grid-item-auto">
                                                            <?= Html::input('radio', 'DELIVERY', $iDeliveryKey, [
                                                                'checked' => isset($_REQUEST['DELIVERY']) && $_REQUEST['DELIVERY'] == $iDeliveryKey
                                                            ]) ?>
                                                            <span class="startshop-order-radio-selector intec-ui-part-selector"></span>
                                                        </span>
                                                        <span class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                            <span class="startshop-order-radio-content">
                                                                <?= Html::encode($arDelivery['LANG'][LANGUAGE_ID]['NAME']) ?>
                                                            </span>
                                                            <span class="startshop-order-radio-additional">
                                                                <?php if ($arDelivery['PRICE']['VALUE'] > 0) { ?>
                                                                    <?= Loc::getMessage('SO_DEFAULT_SECTIONS_DELIVERIES_DELIVERY_PRICE', [
                                                                        '#PRICE#' => $arDelivery['PRICE']['PRINT_VALUE']
                                                                    ]) ?>
                                                                <?php } else { ?>
                                                                    <?= Loc::getMessage('SO_DEFAULT_SECTIONS_DELIVERIES_DELIVERY_FREE') ?>
                                                                <?php } ?>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                            <?php if ($arDelivery['PROPERTIES']) {?>
                                                <div class="startshop-order-delivery-properties" data-role="delivery.properties" data-state="disabled">
                                                    <div class="startshop-order-delivery-properties-content">
                                                        <?php foreach ($arDelivery['PROPERTIES'] as $arDeliveryProperty) { ?>
                                                            <?php $fPropertyDraw($arDeliveryProperty, $arUser) ?>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php }?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arResult['PAYMENTS'])) { ?>
                                <div class="startshop-order-section intec-grid-item-1">
                                    <div class="startshop-order-section-header">
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                                            <div class="intec-grid-item-auto">
                                                <div class="startshop-order-section-number intec-ui-align intec-grid-item-auto intec-cl-background">
                                                    <div class="startshop-order-section-number-content">
                                                        3
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item">
                                                <div class="startshop-order-section-title">
                                                    <?= Loc::getMessage('SO_DEFAULT_SECTIONS_PAYMENTS_PAYMENT') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="startshop-order-section-content">
                                        <div class="intec-grid intec-grid-wrap intec-grid-i-v-12">
                                            <?php foreach ($arResult['PAYMENTS'] as $iPaymentKey => $arPayment) { ?>
                                                <div class="intec-grid-item-1">
                                                    <label class="startshop-order-radio intec-ui intec-ui-control-radiobox intec-ui-scheme-current intec-ui-size-3">
                                                        <span class="intec-grid intec-grid-a-v-start">
                                                            <span class="intec-grid-item-auto">
                                                                <?= Html::input('radio', 'PAYMENT', $iPaymentKey, [
                                                                    'checked' => isset($_REQUEST['PAYMENT']) && $_REQUEST['PAYMENT'] == $iPaymentKey
                                                                ]) ?>
                                                                <span class="startshop-order-radio-selector intec-ui-part-selector"></span>
                                                            </span>
                                                            <span class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                                <span class="startshop-order-radio-content">
                                                                    <?= Html::encode($arPayment['LANG'][LANGUAGE_ID]['NAME']) ?>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="startshop-order-products startshop-order-section intec-grid-item-1">
                                <div class="startshop-order-section-header">
                                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                                        <div class="intec-grid-item-auto">
                                            <div class="startshop-order-section-number intec-ui-align intec-grid-item-auto intec-cl-background">
                                                <div class="startshop-order-section-number-content">
                                                    4
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-grid-item">
                                            <div class="startshop-order-section-title">
                                                <?= Loc::getMessage('SO_DEFAULT_SECTIONS_ITEMS') ?>
                                            </div>
                                        </div>
                                        <div class="intec-grid-item-auto">
                                            <a class="startshop-order-section-link" href="<?= $arParams['URL_BASKET'] ?>">
                                                <?= Loc::getMessage('SO_DEFAULT_BUTTON_BASKET') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="startshop-order-products-content startshop-order-section-content">
                                    <table class="startshop-order-table">
                                        <thead class="startshop-order-table-header">
                                            <tr class="startshop-order-table-row">
                                                <td class="startshop-order-column startshop-order-table-header-name" colspan="2">
                                                    <div class="startshop-order-table-header-cell">
                                                        <?= Loc::getMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_NAME') ?>
                                                    </div>
                                                </td>
                                                <td class="startshop-order-column startshop-order-table-header-offer"></td>
                                                <td class="startshop-order-column startshop-order-table-header-price">
                                                    <div class="startshop-order-table-header-cell">
                                                        <?= Loc::getMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_PRICE') ?>
                                                    </div>
                                                </td>
                                                <td class="startshop-order-column startshop-order-table-header-quantity">
                                                    <div class="startshop-order-table-header-cell">
                                                        <?= Loc::getMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_QUANTITY') ?>
                                                    </div>
                                                </td>
                                                <td class="startshop-order-column startshop-order-table-header-sum">
                                                    <div class="startshop-order-table-header-cell">
                                                        <?= Loc::getMessage('SO_DEFAULT_SECTIONS_ITEMS_COLUMN_TOTAL') ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </thead>
                                        <tbody class="startshop-order-table-body">
                                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                                $sPicture = $arItem['PREVIEW_PICTURE'];

                                                if (empty($sPicture))
                                                    $sPicture = $arItem['DETAIL_PICTURE'];

                                                if (!empty($sPicture)) {
                                                    $sPicture = CFile::ResizeImageGet($sPicture, [
                                                        'width' => 300,
                                                        'height' => 300
                                                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                                    if (!empty($sPicture))
                                                        $sPicture = $sPicture['src'];
                                                }

                                                if (empty($sPicture))
                                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                                $arSection = ArrayHelper::getValue($arItem, ['SECTION_INFO'], []);

                                            ?>
                                                <tr class="startshop-order-table-body-row">
                                                    <td class="startshop-order-table-body-image startshop-order-table-body-column">
                                                        <div class="startshop-order-table-body-column-content">
                                                            <a class="startshop-order-products-image intec-ui-picture" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                                                                <?= Html::img($sPicture, [
                                                                    'class' => 'intec-image-effect',
                                                                    'alt' => $arItem['NAME'],
                                                                    'title' => $arItem['NAME'],
                                                                    'loading' => 'lazy'
                                                                ]) ?>
                                                            </a>
                                                        </div>
                                                    </td>
                                                    <td class="startshop-order-table-body-name startshop-order-table-body-column">
                                                        <div class="startshop-order-table-body-column-content">
                                                            <div class="startshop-order-products-name-content startshop-order-products-block">
                                                                <?php if (!empty($arSection)) {?>
                                                                    <div class="startshop-order-products-section">
                                                                        <?= Html::tag('a', $arSection['NAME'], [
                                                                            'class' => 'intec-cl-text-hover',
                                                                            'href' => $arSection['SECTION_PAGE_URL']
                                                                        ]) ?>
                                                                    </div>
                                                                <?php }?>
                                                                <div class="startshop-order-products-name">
                                                                    <?= Html::tag('a', $arItem['NAME'], [
                                                                        'class' => 'intec-cl-text-hover',
                                                                        'href' => $arItem['DETAIL_PAGE_URL']
                                                                    ]) ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="startshop-order-table-body-offer startshop-order-table-body-column">
                                                        <?php if ($arItem['STARTSHOP']['OFFER']['OFFER']) { ?>
                                                            <div class="startshop-order-table-body-column-content">
                                                                <div class="startshop-order-products-offers">
                                                                    <?php foreach ($arItem['STARTSHOP']['OFFER']['PROPERTIES'] as $arProperty) { ?>
                                                                        <div class="startshop-order-products-offers-item">
                                                                            <div class="startshop-order-products-offers-name">
                                                                                <?= $arProperty['NAME'].':' ?>
                                                                            </div>
                                                                            <?php if ($arProperty['TYPE'] == 'TEXT') { ?>
                                                                                <div class="startshop-order-products-offers-value">
                                                                                    <div class="startshop-order-products-offers-value-text">
                                                                                        <?= $arProperty['VALUE']['TEXT'] ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php } else { ?>
                                                                                <div class="startshop-order-products-offers-value">
                                                                                    <?= Html::tag('div', null, [
                                                                                        'class' => 'startshop-order-products-offers-value-picture',
                                                                                        'title' => $arProperty['VALUE']['TEXT'],
                                                                                        'style' => [
                                                                                            'background-image' => 'url(\''.$arProperty['VALUE']['PICTURE'].'\')'
                                                                                        ]
                                                                                    ]) ?>
                                                                                </div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                    <td class="startshop-order-table-body-price startshop-order-table-body-column">
                                                        <div class="startshop-order-table-body-column-content">
                                                            <div class="startshop-order-products-price">
                                                                <?= $arItem['STARTSHOP']['BASKET']['PRICE']['PRINT_VALUE'] ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="startshop-order-table-body-quantity startshop-order-table-body-column">
                                                        <div class="startshop-order-table-body-column-content">
                                                            <div class="startshop-order-products-quantity">
                                                                <?= $arItem['STARTSHOP']['BASKET']['QUANTITY'] ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="startshop-order-table-body-sum startshop-order-table-body-column">
                                                        <div class="startshop-order-table-body-column-content">
                                                            <div class="startshop-order-products-price">
                                                                <?= CStartShopCurrency::FormatAsString(
                                                                        $arItem['STARTSHOP']['BASKET']['PRICE']['VALUE'] * $arItem['STARTSHOP']['BASKET']['QUANTITY'],
                                                                        $arParams['CURRENCY']
                                                                ) ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="startshop-order-summary intec-grid-item-4 intec-grid-item-1200-1">
                            <div class="startshop-order-summary-content" data-role="total">
                                <div class="startshop-order-summary-block">
                                    <div class="startshop-order-summary-header">
                                        <div class="intec-grid intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-h-4">
                                            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                <div class="startshop-order-summary-header-title">
                                                    <?= Loc::getMessage('SO_DEFAULT_YOUR_ORDER') ?>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item-auto">
                                                <?= Html::tag('a', Loc::getMessage('SO_DEFAULT_BUTTON_BASKET'), [
                                                    'class' => 'startshop-order-summary-header-link',
                                                    'href' => $arParams['URL_BASKET']
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="startshop-order-summary-block">
                                    <div class="startshop-order-summary-block-inner">
                                        <div class="intec-grid intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-h-4">
                                            <div class="intec-grid-item-auto">
                                                <div class="startshop-order-summary-title-default">
                                                    <span>
                                                        <?= Loc::getMessage('SO_DEFAULT_TOTAL_ITEMS') ?>
                                                    </span>
                                                    <span>
                                                        <?= '('.count($arResult['ITEMS']).')' ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                <div class="startshop-order-summary-value-default">
                                                    <?= $arResult['SUM']['PRINT_VALUE'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($arResult['DELIVERIES'])) { ?>
                                        <div class="startshop-order-summary-block-inner">
                                            <div class="intec-grid intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-h-4">
                                                <div class="intec-grid-item-auto">
                                                    <div class="startshop-order-summary-title-default">
                                                        <?= Loc::getMessage('SO_DEFAULT_TOTAL_DELIVERY') ?>
                                                    </div>
                                                </div>
                                                <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <div class="startshop-order-summary-value-default" data-role="total.delivery">
                                                        <?= Loc::getMessage('SO_DEFAULT_TOTAL_DELIVERY_2') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="startshop-order-summary-block">
                                    <div class="intec-grid intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-h-4">
                                        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                            <div class="startshop-order-summary-title-default">
                                                <?= Loc::getMessage('SO_DEFAULT_TOTAL') ?>
                                            </div>
                                        </div>
                                        <div class="intec-grid-item-auto">
                                            <div class="startshop-order-summary-value-default" data-role="total.summary">
                                                <?= $arResult['SUM']['PRINT_VALUE'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="startshop-order-summary-submit">
                                <?= Html::submitButton(Loc::getMessage('SO_DEFAULT_BUTTONS_ORDER'), [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'scheme-current',
                                            'mod-block',
                                            'size-5'
                                        ]
                                    ]
                                ]) ?>
                            </div>
                            <div class="startshop-order-summary-consent startshop-order-consent">
                                <span>
                                    <?= Loc::getMessage('SO_DEFAULT_I_AGREED_TO') ?>
                                </span>
                                <?= Html::tag(!empty($arVisual['CONSENT']) ? 'a' : 'span', Loc::getMessage('SO_DEFAULT_PROCESSING_PERSONAL_DATA'), [
                                    'href' => !empty($arVisual['CONSENT']) ? $arVisual['CONSENT'] : null,
                                    'target' => !empty($arVisual['CONSENT']) ? '_blank' : null
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="startshop-order-checkout">
                        <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-16">
                            <div class="intec-grid-item-auto">
                                <?= Html::submitButton(Loc::getMessage('SO_DEFAULT_BUTTONS_ORDER'), [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'scheme-current',
                                            'mod-round-2',
                                            'size-5'
                                        ]
                                    ]
                                ]) ?>
                            </div>
                            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                <div class="startshop-order-checkout-consent startshop-order-consent">
                                    <span>
                                        <?= Loc::getMessage('SO_DEFAULT_I_AGREED_TO') ?>
                                    </span>
                                    <?= Html::tag(!empty($arVisual['CONSENT']) ? 'a' : 'span', Loc::getMessage('SO_DEFAULT_PROCESSING_PERSONAL_DATA'), [
                                        'href' => !empty($arVisual['CONSENT']) ? $arVisual['CONSENT'] : null,
                                        'target' => !empty($arVisual['CONSENT']) ? '_blank' : null
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php } else { ?>
                <?php if (is_numeric($arResult['ORDER'])) { ?>
                    <div class="intec-ui intec-ui-control-alert intec-ui-scheme-green">
                        <?=GetMessage('SO_DEFAULT_NOTIFIES_ORDER_CREATED', array('#NUMBER#' => $arResult['ORDER']))?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
<?php unset($fPropertyDraw) ?>