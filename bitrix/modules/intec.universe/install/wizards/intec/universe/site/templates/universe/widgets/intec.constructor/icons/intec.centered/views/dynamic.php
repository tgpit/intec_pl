<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<div class="widget widget-icons widget-icons-centered">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::tag('div', '{{ properties.header.value}}', [
                'class' => 'widget-icons-header',
                'v-if' => 'properties.header.show && !$root.$api.isEmpty(properties.header.value)'
            ]) ?>
            <div v-if="properties.items.length > 0" class="widget-icons-items">
                <div class="widget-icons-items-wrapper intec-editor-grid intec-editor-grid-wrap intec-editor-grid-a-v-center">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-icons-item',
                            'widget-icons-item-shrink-1',
                            'intec-editor-grid-item-auto'
                        ],
                        'v-for' => '(item, index) in properties.items',
                        'v-bind:style' => '{
                            "width": width
                        }'
                    ]) ?>
                        <div class="widget-icons-item-content">
                            <div class="widget-icons-item-icon">
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-icons-item-background',
                                    'v-if' => 'properties.background.show',
                                    'v-bind:style' => '{
                                            "background-color": properties.background.color,
                                            "border-radius": backgroundRounding,
                                            "opacity": backgroundOpacity
                                        }'
                                ]) ?>
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-icons-item-picture',
                                    'v-bind:style' => '{
                                            "background-image": "url(" + replacePathMacros(item.image) + ")"
                                        }'
                                ]) ?>
                            </div>
                            <?= Html::tag('div', '{{ item.name }}', [
                                'class' => 'widget-icons-item-name',
                                'v-bind:style' => '{
                                        "font-size": properties.caption.text.size.value + properties.caption.text.size.measure,
                                        "font-weight": properties.caption.style.bold ? "700" : null,
                                        "font-style": properties.caption.style.italic ? "italic" : null,
                                        "text-decoration": properties.caption.style.underline ? "underline" : null,
                                        "text-align": properties.caption.text.align,
                                        "color": properties.caption.text.color,
                                        "opacity": captionOpacity
                                    }'
                            ]) ?>
                        </div>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    </div>
</div>