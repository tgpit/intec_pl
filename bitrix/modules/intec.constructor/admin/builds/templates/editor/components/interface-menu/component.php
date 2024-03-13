<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$data = [
    'components' => [
        'item' => $this->useComponent('interface-menu-item')->getId(),
        'tab' => $this->useComponent('interface-menu-tab')->getId()
    ]
];

return $data;
