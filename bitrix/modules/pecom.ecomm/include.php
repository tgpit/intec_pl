<?php
define('PECOM_ECOMM', 'pecom.ecomm'); // Use this if module code needed
define('PECOM_ECOMM_LBL', 'PECOM_ECOMM_');

IncludeModuleLangFile(__FILE__);

// New classes autoloader
spl_autoload_register(function($className){
    if (strpos($className, 'Pecom\Delivery') === 0) {
        $classPath = implode(DIRECTORY_SEPARATOR, explode('\\', substr($className, 15)));

        $filename = implode(DIRECTORY_SEPARATOR, [__DIR__, "classes", "lib", $classPath.".php"]);
        if (is_readable($filename) && file_exists($filename))
            require_once $filename;
    }
});

Bitrix\Main\Loader::registerAutoLoadClasses("pecom.ecomm", array(
    '\Pec\Delivery\Request' => 'lib/Request.php',
    '\Pec\Delivery\Tools' => 'lib/Tools.php',
    '\Pec\Delivery\PecomEcommDb' => 'lib/PecomEcommDb.php',
    '\Pec\Delivery\Handlers' => 'lib/Handlers.php',
));

?>