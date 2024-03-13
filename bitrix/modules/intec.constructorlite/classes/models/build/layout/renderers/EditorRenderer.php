<?php
namespace intec\constructor\models\build\layout\renderers;

use intec\core\helpers\Html;
use intec\constructor\models\build\layout\Renderer;
use intec\constructor\models\build\layout\Zone;

class EditorRenderer extends Renderer
{
    /**
     * @inheritdoc
     */
    public function getIsRenderAllowed()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function renderZone($zone)
    {
        if (!($zone instanceof Zone))
            return;

        echo Html::tag('component', null, [
            'is' => 'v-editor-layout-zone',
            'v-bind:model' => 'layout.getZone(\''.$zone->getCode().'\')'
        ]);
    }
}