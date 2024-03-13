<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$data = [
    'components' => [
        'panel' => $this->useComponent('interface-menu-tab-panel')->getId(),
        'popup' => $this->useComponent('interface-menu-tab-popup')->getId()
    ]
];

return $data;
