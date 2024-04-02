<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Security\Sign\Signer;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arNavigation
 * @var string $sTemplateId
 * @var CBitrixComponentTemplate $this
 */

$arParams['AJAX_UPDATE'] = 'Y';

$signer = new Signer();

$arParameters = JavaScript::toObject([
    'component' => [
        'path' => $this->getComponent()->getPath().'/ajax.php',
        'template' => $this->GetName(),
        'parameters' => $signer->sign(
            base64_encode(serialize($arParams)),
            'main.news'
        )
    ],
    'navigation' => [
        'id' => $arNavigation['ID'],
        'current' => $arNavigation['PAGE']['CURRENT'],
        'count' => $arNavigation['PAGE']['COUNT']
    ],
    'container' => [
        'root' => '#'.$sTemplateId,
        'items' => '[data-role="items"]',
        'navigationContainer' => '[data-role="navigation"]',
        'navigationButton' => '[data-role="navigation.button"]'
    ],
    'settings' => [
        'buttonDelete' => true
    ]
]);

?>
<script type="text/javascript">
    template.load(function (data) {
        var navigation = new MainNewsNavigation();

        navigation.init(<?= $arParameters ?>);
    }, {
        'name': '[Component] intec.universe:main.news (template.3)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>