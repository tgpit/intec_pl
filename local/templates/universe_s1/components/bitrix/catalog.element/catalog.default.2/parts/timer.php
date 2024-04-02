<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var string $sTemplateId
 */

?>
<div data-role="timer-holder" data-print="false"></div>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        this.api.components.get(<?= JavaScript::toObject(
            $arResult['TIMER']['PROPERTIES']
        ) ?>).then(function (content) {
            $('[data-role="timer-holder"]', root).html(content);
        });
    }, {
        'name': '[Component] bitrix:catalog.element (catalog.default.5)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>