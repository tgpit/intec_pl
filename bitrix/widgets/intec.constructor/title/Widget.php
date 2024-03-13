<?php
namespace widgets\intec\constructor\title;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class Widget extends \intec\constructor\structure\Widget
{
    public function getName()
    {
        return $this->getLanguage()->getMessage('name');
    }
}
