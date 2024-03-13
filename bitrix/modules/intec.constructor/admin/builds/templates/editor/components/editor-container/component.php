<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$this->useComponent('editor-container-grid');

$data = [
    'components' => [
        'area' => $this->useComponent('editor-area')->getId(),
        'block' => $this->useComponent('editor-block')->getId(),
        'component' => $this->useComponent('editor-component')->getId(),
        'variator' => $this->useComponent('editor-variator')->getId(),
        'widget' => $this->useComponent('editor-widget')->getId()
    ]
];

return $data;
