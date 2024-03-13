<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arSvg
 * @var string $sTemplateId
 */

?>
<?php return function (&$item) use (&$arResult, &$sTemplateId, &$arSvg) {

    if (empty($item['VALUES']))
        return;

    $values = &$item['VALUES'];

    if ($item['DISPLAY_TYPE'] === 'A' || $item['DISPLAY_TYPE'] === 'B')
        if (
            empty($values['MIN']['VALUE']) ||
            empty($values['MAX']['VALUE']) ||
            $values['MIN']['VALUE'] == $values['MAX']['VALUE']
        )
            return;

?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-smart-filter-property',
        'data' => [
            'role' => 'property',
            'expanded' => $item['DISPLAY_EXPANDED'] === 'Y' ? 'true' : 'false'
        ]
    ]) ?>
        <div class="catalog-smart-filter-property-header" data-role="property.header">
            <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                <div class="intec-grid-item">
                    <div class="catalog-smart-filter-property-name">
                        <?= $item['NAME'] ?>
                    </div>
                </div>
                <?php if (!empty($item['FILTER_HINT'])) { ?>
                    <div class="intec-grid-item-auto">
                        <div class="catalog-smart-filter-property-hint" data-role="hint">
                            <?= Html::tag('i', '', [
                                'class' => 'fal fa-question-circle',
                                'data-role' => 'hint.icon'
                            ]) ?>
                            <?= Html::tag('div', $item['FILTER_HINT'], [
                                'class' => 'catalog-smart-filter-property-hint-content',
                                'data' => [
                                    'role' => 'hint.content',
                                    'state' => 'hidden'
                                ]
                            ]) ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="intec-grid-item-auto">
                    <div class="catalog-smart-filter-property-icon intec-ui-picture">
                        <?= $arSvg['PROPERTY']['ARROW'] ?>
                    </div>
                </div>
            </div>
        </div>
        <?= Html::beginTag('div', [
            'class' => 'catalog-smart-filter-property-content-toggle',
            'data-role' => 'property.container',
            'style' => $item['DISPLAY_EXPANDED'] !== 'Y' ? 'display: none' : null
        ]) ?>
            <div class="catalog-smart-filter-property-content">
                <?php if ($item['DISPLAY_TYPE'] === 'A' || $item['DISPLAY_TYPE'] === 'B') { ?>
                    <div class="catalog-smart-filter-range">
                        <div class="intec-grid intec-grid-i-h-4">
                            <div class="intec-grid-item-2">
                                <label class="catalog-smart-filter-range-part">
                                    <span class="catalog-smart-filter-range-sign">
                                        <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_RANGE_SIGN_MIN') ?>
                                    </span>
                                    <?= Html::input('text', $values['MIN']['CONTROL_NAME'], $values['MIN']['HTML_VALUE'], [
                                        'id' => $values['MIN']['CONTROL_ID'],
                                        'class' => [
                                            'intec-ui',
                                            'intec-ui-control-input',
                                            'catalog-smart-filter-range-input',
                                            'catalog-smart-filter-input-text'
                                        ],
                                        'placeholder' => $values['MIN']['VALUE'],
                                        'onkeyup' => 'mobileFilter.keyup(this)'
                                    ]) ?>
                                </label>
                            </div>
                            <div class="intec-grid-item-2">
                                <label class="catalog-smart-filter-range-part">
                                    <span class="catalog-smart-filter-range-sign">
                                        <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_RANGE_SIGN_MAX') ?>
                                    </span>
                                    <?= Html::input('text', $values['MAX']['CONTROL_NAME'], $values['MAX']['HTML_VALUE'], [
                                        'id' => $values['MAX']['CONTROL_ID'],
                                        'class' => [
                                            'intec-ui',
                                            'intec-ui-control-input',
                                            'catalog-smart-filter-range-input',
                                            'catalog-smart-filter-input-text'
                                        ],
                                        'placeholder' => $values['MAX']['VALUE']
                                    ]) ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php if ($item['DISPLAY_TYPE'] === 'A') {

                        $trackId = $sTemplateId.'-track-'.$item['ENCODED_ID'];

                    ?>
                        <div class="catalog-smart-filter-track" id="<?= $trackId ?>">
                            <?= Html::tag('div', null, [
                                'id' => $trackId.'-ai',
                                'class' => [
                                    'catalog-smart-filter-track-line',
                                    'catalog-smart-filter-track-line-inactive'
                                ]
                            ]) ?>
                            <?= Html::tag('div', null, [
                                'id' => $trackId.'-aa',
                                'class' => [
                                    'catalog-smart-filter-track-line',
                                    'catalog-smart-filter-track-line-active',
                                    'intec-cl-background'
                                ]
                            ]) ?>
                            <?= Html::tag('div', null, [
                                'id' => $trackId.'-ua',
                                'class' => [
                                    'catalog-smart-filter-track-line',
                                    'catalog-smart-filter-track-line-unavailable'
                                ]
                            ]) ?>
                            <div class="catalog-smart-filter-track-controls" id="<?= $trackId.'-controls' ?>">
                                <?= Html::tag('div', null, [
                                    'id' => $trackId.'-control-left',
                                    'class' => [
                                        'catalog-smart-filter-track-control',
                                        'catalog-smart-filter-track-control-left',
                                        'intec-cl-background'
                                    ]
                                ]) ?>
                                <?= Html::tag('div', null, [
                                    'id' => $trackId.'-control-right',
                                    'class' => [
                                        'catalog-smart-filter-track-control',
                                        'catalog-smart-filter-track-control-right',
                                        'intec-cl-background'
                                    ]
                                ]) ?>
                            </div>
                        </div>
                        <div class="catalog-smart-filter-bounds">
                            <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-h-8">
                                <div class="intec-grid-item-auto">
                                    <div class="catalog-smart-filter-bounds-value">
                                        <?= $values['MIN']['VALUE'] ?>
                                    </div>
                                </div>
                                <div class="intec-grid-item-auto">
                                    <div class="catalog-smart-filter-bounds-value">
                                        <?= $values['MAX']['VALUE'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            template.load(function () {
                                window[<?= JavaScript::toObject('trackBarMobile'.$item['ENCODED_ID']) ?>] = new BX.Iblock.SmartFilterMobile1(<?= JavaScript::toObject([
                                    'leftSlider' => $trackId.'-control-left',
                                    'rightSlider' => $trackId.'-control-right',
                                    'tracker' => $trackId.'-controls',
                                    'trackerWrap' => $trackId,
                                    'minInputId' => $values['MIN']['CONTROL_ID'],
                                    'maxInputId' => $values['MAX']['CONTROL_ID'],
                                    'minPrice' => $values['MIN']['VALUE'],
                                    'maxPrice' => $values['MAX']['VALUE'],
                                    'curMinPrice' => isset($values['MIN']['HTML_VALUE']) ? $values['MIN']['HTML_VALUE'] : null,
                                    'curMaxPrice' => isset($values['MAX']['HTML_VALUE']) ? $values['MAX']['HTML_VALUE'] : null,
                                    'fltMinPrice' => !empty($values['MIN']['FILTERED_VALUE']) ? $values['MIN']['FILTERED_VALUE'] : $values['MIN']['VALUE'] ,
                                    'fltMaxPrice' => !empty($values['MAX']['FILTERED_VALUE']) ? $values['MAX']['FILTERED_VALUE'] : $values['MAX']['VALUE'],
                                    'precision' => $item['DECIMALS'] ? Type::toInteger($item['DECIMALS']) : 0,
                                    'colorUnavailableActive' => $trackId.'-ua',
                                    'colorAvailableActive' => $trackId.'-aa',
                                    'colorAvailableInactive' => $trackId.'-ai'
                                ]) ?>);
                            });
                        </script>
                    <?php } ?>
                <?php } else if ($item['DISPLAY_TYPE'] === 'F') { ?>
                    <?php foreach ($values as $value) { ?>
                        <div class="catalog-smart-filter-checkbox-default">
                            <?= Html::beginTag('label', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-smart-filter-checkbox-default-content' => true,
                                    'disabled' => $value['DISABLED'],
                                    'intec-ui' => [
                                        '' => true,
                                        'control-checkbox' => true,
                                        'scheme-current' => true,
                                        'size-2' => true
                                    ]
                                ], true),
                                'onclick' => 'mobileFilter.click(this)'
                            ]) ?>
                                <?= Html::input('checkbox', $value['CONTROL_NAME'], $value['HTML_VALUE'], [
                                    'id' => $value['CONTROL_ID'],
                                    'checked' => $value['CHECKED'] ? 'checked' : null,
                                    'disabled' => $value['DISABLED']
                                ]) ?>
                                <span class="intec-ui-part-selector"></span>
                                <span class="intec-ui-part-content">
                                    <?= $value['VALUE'] ?>
                                </span>
                            <?= Html::endTag('label') ?>
                        </div>
                    <?php } ?>
                <?php } else if ($item['DISPLAY_TYPE'] === 'K') {

                    $current = current($values);

                ?>
                    <div class="catalog-smart-filter-radio">
                        <?= Html::beginTag('label', [
                            'class' => [
                                'catalog-smart-filter-radio-content',
                                'intec-ui' => [
                                    '',
                                    'control-radiobox',
                                    'scheme-current',
                                    'size-2'
                                ]
                            ]
                        ]) ?>
                            <?= Html::radio($current['CONTROL_NAME_ALT'], false, [
                                'id' => 'all_'.$current['CONTROL_ID'],
                                'value' => '',
                                'onclick' => 'mobileFilter.click(this)'
                            ]) ?>
                            <span class="intec-ui-part-selector"></span>
                            <span class="intec-ui-part-content">
                                <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_PROPERTY_VALUE_ALL') ?>
                            </span>
                        <?= Html::endTag('label') ?>
                    </div>
                    <?php foreach ($values as $value) { ?>
                        <div class="catalog-smart-filter-radio">
                            <?= Html::beginTag('label', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-smart-filter-radio-content' => true,
                                    'disabled' => $value['DISABLED'],
                                    'intec-ui' => [
                                        '' => true,
                                        'control-radiobox' => true,
                                        'scheme-current' => true,
                                        'size-2' => true
                                    ]
                                ], true)
                            ]) ?>
                                <?= Html::radio($value['CONTROL_NAME_ALT'], $value['CHECKED'], [
                                    'id' => $value['CONTROL_ID'],
                                    'value' => $value['HTML_VALUE_ALT'],
                                    'onclick' => 'mobileFilter.click(this)'
                                ]) ?>
                                <span class="intec-ui-part-selector"></span>
                                <span class="intec-ui-part-content">
                                    <?= $value['VALUE'] ?>
                                </span>
                            <?= Html::endTag('label') ?>
                        </div>
                    <?php } ?>
                    <?php unset($current) ?>
                <?php } else if ($item['DISPLAY_TYPE'] === 'P') {

                    $current = current($values);
                    $selected = false;

                ?>
                    <div class="catalog-smart-filter-select">
                        <label class="catalog-smart-filter-select-content" data-role="property.select">
                            <?= Html::beginTag('select', [
                                'id' => $current['CONTROL_ID'],
                                'name' => $current['CONTROL_NAME_ALT'],
                                'onchange' => 'mobileFilter.click(this)'
                            ]) ?>
                                <option value>
                                    <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_PROPERTY_VALUE_ALL') ?>
                                </option>
                                <?php foreach ($values as $value) { ?>
                                    <?= Html::tag('option', $value['VALUE'], [
                                        'id' => $value['CONTROL_ID'],
                                        'value' => $value['HTML_VALUE_ALT'],
                                        'disabled' => $value['DISABLED'],
                                        'selected' => $value['CHECKED']
                                    ]) ?>
                                    <?php if ($value['CHECKED'])
                                        $selected = $value;
                                    ?>
                                <?php } ?>
                            <?= Html::endTag('select') ?>
                            <span class="catalog-smart-filter-select-indicator">
                                <span class="intec-grid intec-grid-a-v-stretch">
                                    <span class="intec-grid-item">
                                        <span class="catalog-smart-filter-select-indicator-text" data-role="property.select.value">
                                            <?php if (!empty($selected)) { ?>
                                                <?= $selected['VALUE'] ?>
                                            <?php } else { ?>
                                                <?= Loc::getMessage('C_CATALOG_SMART_FILTER_MOBILE_1_TEMPLATE_PROPERTY_VALUE_ALL') ?>
                                            <?php } ?>
                                        </span>
                                    </span>
                                    <span class="intec-grid-item-auto">
                                        <span class="catalog-smart-filter-select-indicator-icon intec-ui-picture">
                                            <?= $arSvg['PROPERTY']['DROPDOWN']['ARROW'] ?>
                                        </span>
                                    </span>
                                </span>
                            </span>
                        </label>
                    </div>
                <?php } else if ($item['DISPLAY_TYPE'] === 'G') { ?>
                    <div class="catalog-smart-filter-checkbox-custom-container">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-6">
                            <?php foreach ($values as $value) {

                                $file = null;

                                if (!empty($value['FILE'])) {
                                    $file = CFile::ResizeImageGet($value['FILE'], [
                                        'width' => 60,
                                        'height' => 60
                                    ], BX_RESIZE_IMAGE_EXACT);

                                    if (!empty($file))
                                        $file = $file['src'];
                                }

                                if (empty($file))
                                    $file = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                            ?>
                                <div class="intec-grid-item-auto">
                                    <div class="catalog-smart-filter-checkbox-custom">
                                        <?= Html::beginTag('label', [
                                            'class' => Html::cssClassFromArray([
                                                'catalog-smart-filter-checkbox-custom-content' => true,
                                                'disabled' => $value['DISABLED'],
                                            ], true),
                                            'data-role' => 'property.checkbox'
                                        ]) ?>
                                            <?= Html::input('checkbox', $value['CONTROL_NAME'], $value['HTML_VALUE'], [
                                                'id' => $value['CONTROL_ID'],
                                                'checked' => $value['CHECKED'] ? 'checked' : null,
                                                'disabled' => $value['DISABLED'],
                                                'onclick' => 'mobileFilter.click(this)'
                                            ]) ?>
                                            <?= Html::beginTag('span', [
                                                'class' => Html::cssClassFromArray([
                                                    'catalog-smart-filter-checkbox-custom-picture' => true,
                                                    'intec-cl-border' => $value['CHECKED']
                                                ], true),
                                                'data-role' => 'property.checkbox.indicator'
                                            ]) ?>
                                                <?= Html::tag('span', null, [
                                                    'class' => 'catalog-smart-filter-checkbox-custom-picture-background',
                                                    'style' => [
                                                        'background-image' => 'url(\''.$file.'\')'
                                                    ]
                                                ]) ?>
                                            <?= Html::endTag('span') ?>
                                        <?= Html::endTag('label') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } else if ($item['DISPLAY_TYPE'] === 'H') { ?>
                    <?php foreach ($values as $value) {

                        $file = null;

                        if (!empty($value['FILE'])) {
                            $file = CFile::ResizeImageGet($value['FILE'], [
                                'width' => 60,
                                'height' => 60
                            ], BX_RESIZE_IMAGE_EXACT);

                            if (!empty($file))
                                $file = $file['src'];
                        }

                        if (empty($file))
                            $file = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="catalog-smart-filter-checkbox-custom">
                            <?= Html::beginTag('label', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-smart-filter-checkbox-custom-content' => true,
                                    'disabled' => $value['DISABLED'],
                                ], true),
                                'data-role' => 'property.checkbox'
                            ]) ?>
                                <?= Html::input('checkbox', $value['CONTROL_NAME'], $value['HTML_VALUE'], [
                                    'id' => $value['CONTROL_ID'],
                                    'checked' => $value['CHECKED'] ? 'checked' : null,
                                    'disabled' => $value['DISABLED'],
                                    'onclick' => 'mobileFilter.click(this)'
                                ]) ?>
                                <span class="intec-grid intec-grid-i-h-8 intec-grid-a-v-center">
                                    <span class="intec-grid-item-auto">
                                        <?= Html::beginTag('span', [
                                            'class' => Html::cssClassFromArray([
                                                'catalog-smart-filter-checkbox-custom-picture' => true,
                                                'intec-cl-border' => $value['CHECKED']
                                            ], true),
                                            'data-role' => 'property.checkbox.indicator'
                                        ]) ?>
                                            <?= Html::tag('span', null, [
                                                'class' => 'catalog-smart-filter-checkbox-custom-picture-background',
                                                'style' => [
                                                    'background-image' => 'url(\''.$file.'\')'
                                                ]
                                            ]) ?>
                                        <?= Html::endTag('span') ?>
                                    </span>
                                    <span class="intec-grid-item">
                                        <span class="catalog-smart-filter-checkbox-custom-text">
                                            <?= $value['VALUE'] ?>
                                        </span>
                                    </span>
                                </span>
                            <?= Html::endTag('label') ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('div') ?>
<?php } ?>