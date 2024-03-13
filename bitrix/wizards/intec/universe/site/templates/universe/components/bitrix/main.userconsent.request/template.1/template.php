<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\bitrix\Component;
use Bitrix\Main\UserConsent\Agreement;

$userConsentArray = Agreement::getActiveList();
$userConsent = $userConsentArray[$arResult['CONFIG']['id']];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

?>
<label data-bx-user-consent="<?= Html::encode(Json::htmlEncode($arResult['CONFIG'])) ?>" class="main-user-consent-request intec-ui intec-ui-control-switch intec-ui-scheme-current intec-ui-size-5">
    <?= Html::checkbox($arParams['INPUT_NAME'], $arParams['IS_CHECKED'], [
        'value' => 'Y'
    ]) ?>
    <span class="intec-ui-part-selector"></span>
    <span class="intec-ui-part-content">
        <?= Html::decode($arResult['INPUT_LABEL']) ?>
    </span>
</label>
<script type="text/html" data-bx-template="main-user-consent-request-loader">
	<div id="<?=$sTemplateId?>" class="main-user-consent-request-popup">
		<div class="main-user-consent-request-popup-cont">
            <svg data-bx-close="" class="main-user-consent-close-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33301 1.33337L14.6663 14.6667" stroke="#404040" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M14.6663 1.33337L1.33301 14.6667" stroke="#404040" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
			<div data-bx-head="" class="main-user-consent-request-popup-header"><?=$userConsent?></div>
			<div class="main-user-consent-request-popup-body">
				<div data-bx-loader="" class="main-user-consent-request-loader">
					<svg class="main-user-consent-request-circular" viewBox="25 25 50 50">
						<circle class="main-user-consent-request-path" cx="50" cy="50" r="20" fill="none" stroke-width="1" stroke-miterlimit="10"></circle>
					</svg>
				</div>
				<div data-bx-content="" class="main-user-consent-request-popup-content">
					<div class="main-user-consent-request-popup-textarea-block">
						<textarea data-bx-textarea="" class="main-user-consent-request-popup-text" disabled></textarea>
					</div>
					<div class="intec-grid intec-grid-nowrap intec-grid-425-wrap intec-grid-a-h-start main-user-consent-request-popup-buttons">
						<div data-bx-btn-accept="" id="main-user-consent-request-popup-button-acc" class="intec-grid-item-400-1 main-user-consent-request-popup-button main-user-consent-request-popup-button-acc intec-ui intec-ui-control-button intec-ui-mod-round-2 intec-ui-scheme-current">Y</div>
						<div data-bx-btn-reject="" id="main-user-consent-request-popup-button-rej" class="intec-grid-item-400-1 main-user-consent-request-popup-button main-user-consent-request-popup-button-rej intec-ui intec-ui-control-button intec-ui-mod-round-4 intec-ui-mod-transparent intec-ui-scheme-current">N</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</script>