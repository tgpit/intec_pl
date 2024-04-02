<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<div class="widget widget-icons widget-icons-description">
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
                            <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                <div class="intec-editor-grid-item-auto">
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
                                </div>
                                <div v-if="!$root.$api.isEmpty(item.name)" class="intec-editor-grid-item">
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
                                    <?= Html::tag('div', '{{ item.description }}', [
                                        'class' => 'widget-icons-item-description',
                                        'v-if' => '!$root.$api.isEmpty(item.description)',
                                        'v-bind:style' => '{
                                            "font-size": properties.description.text.size.value + properties.description.text.size.measure,
                                            "font-weight": properties.description.style.bold ? "700" : null,
                                            "font-style": properties.description.style.italic ? "italic" : null,
                                            "text-decoration": properties.description.style.underline ? "underline" : null,
                                            "text-align": properties.description.text.align,
                                            "color": properties.description.text.color,
                                            "opacity": descriptionOpacity
                                        }'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
        </div>
    </div>
</div>