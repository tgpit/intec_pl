<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\JavaScript;

/**
 * @var string $sTemplateId
 */

?>
<script type="text/javascript">
    template.load(function (data) {
        var app = this;
        var $ = this.getLibrary('$');
        var root = data.nodes;

        app.api.components.get(<?= JavaScript::toObject(
            $arVisual['TIMER']['PROPERTIES']
        ) ?>).then(function (content) {
            $('[data-role="timer"]', root).html(content);
        });
    }, {
        'name': '[Component] bitrix:news.detail (shares.default.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>