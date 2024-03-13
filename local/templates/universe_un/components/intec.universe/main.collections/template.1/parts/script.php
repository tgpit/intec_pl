<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 * @var CBitrixComponentTemplate $this
 */

$arParams['SETTINGS_USE'] = 'N';
$arParams['AJAX_UPDATE'] = 'Y';

$signer = new Signer();

$arParameters = JavaScript::toObject([
    'component' => [
        'path' => $this->getComponent()->getPath().'/ajax.php',
        'template' => $this->GetName(),
        'parameters' => $signer->sign(
            base64_encode(serialize($arParams)),
            'main.collections'
        )
    ],
    'navigation' => [
        'id' => $arResult['NAVIGATION']['ID'],
        'current' => $arResult['NAVIGATION']['PAGE']['CURRENT'],
        'count' => $arResult['NAVIGATION']['PAGE']['COUNT']
    ],
    'container' => [
        'root' => '#'.$sTemplateId,
        'items' => '[data-role="items"]',
        'button' => '[data-role="navigation.button"]'
    ]
]);

unset($signer);

?>
<script type="text/javascript">
    template.load(function () {
        var navigation = new MainCollectionsNavigation();

        navigation.init(<?= $arParameters ?>);
    }, {
        'name': '[Component] intec.universe:main.collections (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>