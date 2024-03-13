<?php

namespace Pecom\Ecomm;

use Error;

abstract class AbstractHelper
{
    private function __construct()
    {
        throw new Error('Нельзя создавать объекты данного класса');
    }
}