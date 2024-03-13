<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\constructor\models\Build;
use intec\constructor\models\build\Template as BuildTemplate;
use intec\constructor\structure\widget\Template as WidgetTemplate;

global $APPLICATION;

/**
 * @var array $properties
 * @var Build $build
 * @var BuildTemplate $template
 * @var WidgetTemplate $this
 */

?>
<h1>
    <?php $APPLICATION->ShowTitle("header") ?>
</h1>
