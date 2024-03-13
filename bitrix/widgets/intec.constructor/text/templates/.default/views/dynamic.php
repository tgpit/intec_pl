<?php

use intec\constructor\models\Build;
use intec\constructor\models\build\Template as BuildTemplate;
use intec\constructor\structure\widget\Template as WidgetTemplate;

/**
 * @var array $properties
 * @var Build $build
 * @var BuildTemplate $template
 * @var array
 * @var WidgetTemplate $this
 */

?>
<div class="intec-constructor-widget" data-widget="text" v-if="isTextFilled" v-html="text" v-bind:style="style"></div>
<div class="intec-editor-element-stub" v-else>
    <div class="intec-editor-element-stub-wrapper">
        <?= $this->getLanguage()->getMessage('message') ?>
    </div>
</div>
