<?php

define('EDITOR', true);

$environment = [];

if (isset($_REQUEST['environment']))
    $environment = $_REQUEST['environment'];

if (!is_array($environment))
    $environment = [];

$environment = array_merge([
    'site' => null,
    'directory' => null,
    'template' => null
], $environment);

if (!empty($environment['site']))
    define('SITE_ID', $environment['site']);

if (!empty($environment['directory']))
    define('SITE_DIR', $environment['directory']);

if (!empty($environment['template']))
    define('SITE_TEMPLATE_ID', $environment['template']);

unset($environment);