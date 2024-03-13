<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\constructor\models\Build;
use intec\constructor\models\build\Template as BuildTemplate;
use intec\constructor\structure\widget\Template as WidgetTemplate;

/**
 * @var array $properties
 * @var Build $build
 * @var BuildTemplate $template
 * @var array $data
 * @var WidgetTemplate $this
 */

?>
<div class="intec-editor-element-stub">
    <div class="intec-editor-element-stub-wrapper">
        <?= $this->getLanguage()->getMessage('message') ?>
    </div>
</div>
