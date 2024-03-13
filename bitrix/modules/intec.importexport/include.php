<?php

use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;

require_once('classes/Loader.php');

Loc::loadMessages(__FILE__);

class IntecImportexport
{
    protected static $MODULE_ID = 'intec.importexport';
    protected static $MODULE_STATE = 0;

    public static function Initialize()
    {
        static::$MODULE_STATE = CModule::IncludeModuleEx(static::$MODULE_ID);

        $dateNow = new DateTime();
        $dateFile = Application::getDocumentRoot().'/bitrix/modules/'.static::$MODULE_ID.'/temp.dat';
        $dateFileExists = is_file($dateFile);
        $dateLast = new DateTime();

        if ($dateFileExists)
            $dateLast->setTimestamp(intval(file_get_contents($dateFile)));

        $dateDifference = $dateNow->diff($dateLast);

        if ($dateDifference->days > 1 || !$dateFileExists) {
            $key = Application::getDocumentRoot().'/bitrix/license_key.php';

            if (is_file($key)) {
                include($key);

                $key = null;

                if (isset($LICENSE_KEY))
                    $key = $LICENSE_KEY;
            } else {
                $key = null;
            }

            $address = 'http://license.intecwork1.ru/licenses/verify';
            $parameters = [
                'solution' => static::$MODULE_ID,
                'key' => $key,
                'hash' => !empty($key) ? md5('BITRIX'.$key.'LICENCE') : null,
                'domain' => $_SERVER['SERVER_NAME'],
                'email' => null,
                'firstName' => null,
                'lastName' => null,
                'secondName' => null,
                'mark' => null
            ];

            $user = CUser::GetByID(1)->Fetch();

            if (!empty($user)) {
                $parameters['email'] = $user['EMAIL'];
                $parameters['firstName'] = $user['NAME'];
                $parameters['lastName'] = $user['LAST_NAME'];
                $parameters['secondName'] = $user['SECOND_NAME'];
            }

            if (is_file(__DIR__.'/options.php')) {
                $content = @file_get_contents(__DIR__.'/options.php');

                if (strpos($content, '#HASH#') !== false) {
                    $parameters['mark'] = bin2hex(random_bytes(16));
                    $content = str_replace('#HASH#', $parameters['mark'], $content);
                    @file_put_contents(__DIR__.'/options.php', $content);
                } else {
                    $matches = [];

                    if (preg_match('/@comment\\s*(\\S*)/i', $content, $matches))
                        $parameters['mark'] = $matches[1];

                    unset($matches);
                }

                unset($content);
            }

            $content = '';

            foreach ($parameters as $key => $value) {
                if (!empty($content))
                    $content .= '&';

                $content .= rawurlencode($key).'='.rawurlencode($value);
            }

            $result = @file_get_contents($address, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => 'Content-Type: application/x-www-form-urlencoded'."\r\n",
                    'content' => $content
                ]
            ]));

            if ($result == 'blocked') {
                static::$MODULE_STATE = 0;
            } else if ($result == 'removed') {
                DeleteDirFilesEx('/bitrix/modules/'.static::$MODULE_ID);
                static::$MODULE_STATE = 0;
            } else {
                file_put_contents($dateFile, $dateNow->getTimestamp());
            }
        }

        static::Validate();
    }

    protected static function Validate()
    {
        if (static::$MODULE_STATE != 1 && static::$MODULE_STATE != 2)
            die(Loc::getMessage('intec.importexport.demo', ['#MODULE_ID#' => static::$MODULE_ID]));
    }
}

?>

