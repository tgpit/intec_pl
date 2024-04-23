<?php
namespace intec\core;

use CUpdateClientPartner;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;
use CEventType;
use CEvent;
use COption;
use CSite;
use CEventMessage;
use intec\core\collections\Arrays;

class AdminNotify
{
    private $use = false;
    private $isAdmin = false;
    private $errors = [];
    private $modules = [];
    private $expired = [];
    private $monthLib = [];
    private $sendingInfo = [];
    private $cookieKey = 'INTEC_CORE_BANNER_STATE';

    /**
     * max-state: 14, 7, 0
     * */
    private $generalExpired = [
        'HAS' => false,
        'COUNT' => 0,
        'MAX_STATE' => null
    ];

    public function __construct ()
    {
        global $USER;

        $this->isAdmin = $USER->CanDoOperation('edit_other_settings') && $USER->CanDoOperation('view_other_settings');

        if (!$this->isAdmin) {
            $this->addError('<span style="color: red">Access denied</span>');
        } else {
            $this->setUse();
            $this->setModules();
            $this->setGeneralExpired();
            $this->setSendingInfo();
        }
    }

    private function setUse ()
    {
        $this->use = Core::$app->getParameters()->getNotificationUse();
    }

    public function getUse ()
    {
        return $this->use;
    }

    private function addError ($text)
    {
        $this->errors[] = $text;
    }

    public function getErrors ()
    {
        if (!empty($this->errors))
            return implode('<br>', $this->errors);
        else
            return false;
    }

    private  function setModules ()
    {
        $requestedModules = CUpdateClientPartner::GetRequestedModules("");
        $updateList = CUpdateClientPartner::GetUpdatesList($errorMessage, LANG, 'Y', $requestedModules, Array("fullmoduleinfo" => "Y"));
        $modules = Array();

        if(!empty($updateList["MODULE"])) {
            foreach($updateList["MODULE"] as $k => $v) {
                if (StringHelper::startsWith($v["@"]["ID"], 'intec')) {
                    $modules[$v["@"]["ID"]] = Array(
                        "NAME" => $v["@"]["NAME"],
                        "VERSION" => (isset($v["#"]["VERSION"]) ? $v["#"]["VERSION"][count($v["#"]["VERSION"]) - 1]["@"]["ID"] : ""),
                        "FREE_MODULE" => $v["@"]["FREE_MODULE"],
                        "DATE_FROM" => $v["@"]["DATE_FROM"],
                        "DATE_TO" => $v["@"]["DATE_TO"],
                        "UPDATE_END" => $v["@"]["UPDATE_END"],
                    );
                }
            }
        }

        if (!empty($updateList["CLIENT"])) {
            $modules['bitrix'] = $updateList["CLIENT"]['0']['@'];
            $modules['bitrix']['NAME'] = Loc::getMessage('intec.core.name.bitrix');
        }

        if (!empty($updateList["ERROR"])) {
            foreach ($updateList["ERROR"] as $error) {
                $this->addError($error['#']);
            }
        }

        foreach ($modules as $key => &$module) {
            if ($module['FREE_MODULE'] === 'Y')
                continue;

            $delta = strtotime($module["DATE_TO"]) - time();
            $daysToExpire = ($delta < 0? 0 : ceil($delta/86400));
            $module['DAYS_LEFT'] = $daysToExpire;

            $this->setExpired($key, $daysToExpire, $module);
        }

        $this->modules = $modules;
    }

    private function setExpired ($code, $days, $data)
    {
        if (!empty($code) && (!empty($days) || $days === 0) && !array_key_exists($code, $this->expired) && $days <= 14) {
             $information = [
                'DAYS' => $days,
                '14_DAYS' => false,
                '7_DAYS' => false,
                'END' => false
            ];

            if ($days <= 0) {
                $information['END'] = true;
            } elseif ($days <= 7) {
                $information['7_DAYS'] = true;
            } elseif ($days <= 14) {
                $information['14_DAYS'] = true;
            }

            $this->expired[$code] = $information;
            $this->expired[$code]['DATA'] = $data;
        }
    }

    public function getExpired ()
    {
        return $this->expired;
    }

    public function getModules()
    {
        if (!$this->isAdmin)
            return $this->getErrors();
        elseif (empty($this->modules))
            return $this->getErrors();
        else
            return $this->modules;
    }

    public function getModuleByCode($code)
    {
        if (empty(trim($code)))
            return false;

        $modules = $this->getModules();

        if (is_array($modules)) {
            if (ArrayHelper::keyExists($code, $modules)) {
                return $modules[$code];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function setGeneralExpired ()
    {
        if (count($this->expired) > 0) {
            foreach ($this->expired as $expired) {
                if ($this->generalExpired['MAX_STATE'] === null) {
                    $this->generalExpired['HAS'] = true;

                    $maxState = null;

                    if ($expired['14_DAYS'])
                        $maxState = 14;
                    elseif ($expired['7_DAYS'])
                        $maxState = 7;
                    elseif ($expired['END'])
                        $maxState = 0;

                    $this->generalExpired['MAX_STATE'] = $maxState;
                } else {
                    $maxState = $this->generalExpired['MAX_STATE'];

                    if ($expired['14_DAYS'] && $maxState > 14)
                        $maxState = 14;
                    elseif ($expired['7_DAYS'] && $maxState > 7)
                        $maxState = 7;
                    elseif ($expired['END'] && $maxState > 0)
                        $maxState = 0;

                    $this->generalExpired['MAX_STATE'] = $maxState;
                }

                $this->generalExpired['COUNT']++;
            }
        }
    }

    public function getGeneralExpired ()
    {
        return $this->generalExpired;
    }

    public function setDateFormat () {
        $this->monthLib = [
            Loc::getMessage('intec.core.month.jan'),
            Loc::getMessage('intec.core.month.feb'),
            Loc::getMessage('intec.core.month.mar'),
            Loc::getMessage('intec.core.month.apr'),
            Loc::getMessage('intec.core.month.may'),
            Loc::getMessage('intec.core.month.jun'),
            Loc::getMessage('intec.core.month.jul'),
            Loc::getMessage('intec.core.month.aug'),
            Loc::getMessage('intec.core.month.sep'),
            Loc::getMessage('intec.core.month.oct'),
            Loc::getMessage('intec.core.month.nov'),
            Loc::getMessage('intec.core.month.dec'),
        ];

        if (!empty($this->modules) && !empty($this->monthLib)) {
            foreach ($this->modules as &$module) {
                if (!empty($module['DATE_FROM'])) {
                    $date = strtotime($module['DATE_FROM']);
                    $month = $this->monthLib[date('n', $date) - 1];

                    $module['DATE_FROM'] = date('d', $date) . ' ' . $month . ' ' . date('Y', $date);
                }

                if (!empty($module['DATE_TO'])) {
                    $date = strtotime($module['DATE_TO']);
                    $month = $this->monthLib[date('n', $date) - 1];

                    $module['DATE_TO'] = date('d', $date) . ' ' . $month . ' ' . date('Y', $date);
                }
            }
        }
    }

    public function getDeclination ($num, $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);

        return $num . ' ' . $titles[($num % 100 > 4 && $num % 100 < 20) ? 2 : $cases[min($num % 10, 5)]];
    }

    public function sortByActivity ()
    {
        if (!empty($this->modules)) {
            uasort($this->modules, function ($a, $b) {
                if ($a['UPDATE_END'] === 'Y' && $b['UPDATE_END'] === 'Y' || $a['UPDATE_END'] !== 'Y' && $b['UPDATE_END'] !== 'Y') {
                    return 0;
                } elseif ($a['UPDATE_END'] === 'Y' && $b['UPDATE_END'] !== 'Y') {
                    return -1;
                } else {
                    return 1;
                }
            });
            uasort($this->modules, function ($a, $b) {
                if ($a['FREE_MODULE'] === 'Y' && $b['FREE_MODULE'] === 'Y' || $a['FREE_MODULE'] !== 'Y' && $b['FREE_MODULE'] !== 'Y') {
                    return 0;
                } elseif ($a['FREE_MODULE'] === 'Y' && $b['FREE_MODULE'] !== 'Y') {
                    return 1;
                } else {
                    return 0;
                }
            });
        }
    }

    public static function sendNotify (): string
    {
        $notifier = new AdminNotify();
        $notifier->setUse();

        if ($notifier->use) {
            $notifier->setModules();
            $notifier->setSendingInfo();
            $notifier->updateSendingInfo();

            $solutions = $notifier->getExpired();
            $hasSend = false;

            foreach ($solutions as $key => $solution) {

                $event = '';
                $fields = [
                    'DAYS' => $solution['DATA']['DAYS_LEFT'],
                    'DATE_END' => $solution['DATA']['DATE_TO'],
                    'SOLUTION' => $solution['DATA']['NAME'],
                    'SOLUTION_LINK' => 'https://marketplace.1c-bitrix.ru/solutions/' . $key . '/'
                ];

                if ($solution['END'] && !$notifier->sendingInfo[$key]['END']) {
                    if ($key === 'bitrix') {
                        $event = 'INTEC_LICENCE_BITRIX_EXPIRED_NOTIFY';
                    } else {
                        $event = 'INTEC_LICENCE_SOLUTION_EXPIRED_NOTIFY';
                    }
                } elseif ($solution['7_DAYS'] && !$notifier->sendingInfo[$key]['7_DAYS']) {
                    if ($key === 'bitrix') {
                        $event = 'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY';
                    } else {
                        $event = 'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY';
                    }
                } elseif ($solution['14_DAYS'] && !$notifier->sendingInfo[$key]['14_DAYS']) {
                    if ($key === 'bitrix') {
                        $event = 'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY';
                    } else {
                        $event = 'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY';
                    }
                }

                if (!empty($event)) {
                    $sendResult = $notifier->sendMail($event, $fields);

                    if ($sendResult === 'Y') {
                        $hasSend = true;
                        $notifier->sendingInfo[$key]['DATE_TO'] = $solution['DATA']['DATE_TO'];
                        $notifier->sendingInfo[$key]['14_DAYS'] = $solution['14_DAYS'];
                        $notifier->sendingInfo[$key]['7_DAYS'] = $solution['7_DAYS'];
                        $notifier->sendingInfo[$key]['END'] = $solution['END'];
                    }
                }
            }

            if ($hasSend)
                $notifier->saveSendingInfo();
        }

        return '\intec\core\AdminNotify::sendNotify();';
    }

    public function sendMail ($event, $fields)
    {
        $this->checkMailTemplate($event);

        $siteId = Arrays::fromDBResult(CSite::GetList())->asArray();
        $siteId = ArrayHelper::getFirstValue($siteId);
        $siteId = $siteId['LID'];

        $mail = new CEventMessage;
        $mailId = $mail::GetList('id', 'asc', [
            'TYPE_ID' => [
                $event
            ]
        ]);

        $mailId = Arrays::fromDBResult($mailId)->asArray();
        $mailId = ArrayHelper::getFirstValue($mailId);
        $mailId = $mailId['ID'];

        return CEvent::SendImmediate (
            $event,
            $siteId,
            $fields,
            'N',
            $mailId,
        );
    }

    private function setSendingInfo ()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'].'/upload/intec.core/';
        $data = [];

        if (FileHelper::isDirectory($filePath)) {
            if (FileHelper::isFile($filePath . 'sandingMail.json')) {
                $data = FileHelper::getFileData($filePath . 'sandingMail.json');
                $data = Json::decode($data);
            } else {
                FileHelper::setFileData($filePath.'sandingMail.json', '{}');
            }
        } else {
            FileHelper::createDirectory($filePath);
            FileHelper::setFileData($filePath.'sandingMail.json', '{}');
        }

        $this->sendingInfo = $data;
    }

    public function updateSendingInfo ()
    {
        foreach ($this->modules as $key => $module) {
            if (ArrayHelper::keyExists($key, $this->sendingInfo)) {
                if ($this->sendingInfo[$key]['DATE_TO'] !== $module['DATE_TO']) {
                    if (strtotime($module['DATE_TO']) > strtotime($this->sendingInfo[$key]['DATE_TO'])) {
                        unset($this->sendingInfo[$key]);
                    }
                }
            }
        }
    }

    private function saveSendingInfo ()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'].'/upload/intec.core/sandingMail.json';

        if (!FileHelper::isFile($filePath)) {
            if (FileHelper::isFile($filePath)) {
                FileHelper::createDirectory($_SERVER['DOCUMENT_ROOT'].'/upload/intec.core/');
            }

            FileHelper::setFileData($filePath, '{}');
        }

        $json = $this->sendingInfo;
        $json = Json::encode($json);
        $data = FileHelper::getFileData($filePath);
        $data = Json::decode($data);

        if ($json !== $data) {
            return FileHelper::setFileData($filePath, $json);
        } else {
            return false;
        }
    }

    private function checkMailTemplate ($event)
    {
        $siteId = Arrays::fromDBResult(CSite::GetList())->asArray();
        $siteId = ArrayHelper::getFirstValue($siteId);
        $siteId = $siteId['LID'];

        $createMail = false;
        $createEvent = false;
        $mailEvents = Arrays::fromDBResult(CEventType::GetList(['LID' => $siteId, 'TYPE_ID' => $event]))->asArray();

        if (!empty($mailEvents)) {
            $mail = Arrays::fromDBResult(CEventMessage::GetList('id', 'desc', ['LID' => $siteId, 'TYPE_ID' => $event]))->asArray();

            if (empty($mail)) {
                $createMail = true;
            }
        } else {
            $createEvent = true;
            $createMail = true;
        }

        if ($createMail) {
           $this->createMailTemplate($event, $siteId, $createEvent);
        }
    }

    private function createMailTemplate ($event, $siteId, $createMailEvent = false)
    {
        $mailPath = $_SERVER['DOCUMENT_ROOT'].'/bitrix/resources/intec.core/mail/';

        if ($createMailEvent) {
            $mailEvent = new CEventType;
            $mailEventSettings = [
                'LID' => $siteId,
                'EVENT_NAME' => $event,
                'EVENT_TYPE' => 'email',
                'NAME' => '',
                'DESCRIPTION' => Loc::getMessage('intec.core.email.event.description'),
            ];

            if ($event === 'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY') {
                $mailEventSettings['NAME'] = Loc::getMessage('intec.core.email.event.solution.expire.name');
            } elseif ($event === 'INTEC_LICENCE_SOLUTION_EXPIRED_NOTIFY') {
                $mailEventSettings['NAME'] = Loc::getMessage('intec.core.email.event.solution.expired.name');
            } elseif ($event === 'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY') {
                $mailEventSettings['NAME'] = Loc::getMessage('intec.core.email.event.bitrix.expire.name');
            } elseif ($event === 'INTEC_LICENCE_BITRIX_EXPIRED_NOTIFY') {
                $mailEventSettings['NAME'] = Loc::getMessage('intec.core.email.event.bitrix.expired.name');
            }

            $mailEvent::Add($mailEventSettings);
        }

        $mail = new CEventMessage;

        $mailSetting = [
            'ACTIVE' => 'Y',
            'EVENT_NAME' => $event,
            'LID' => $siteId,
            'EMAIL_FROM' => COption::GetOptionString("main", "email_from"),
            'EMAIL_TO' => COption::GetOptionString("main", "email_from"),
            'SUBJECT' => '',
            'BODY_TYPE' => 'html',
            'MESSAGE' => ''
        ];

        if ($event === 'INTEC_LICENCE_SOLUTION_EXPIRE_NOTIFY') {
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.email.subject.solution.expire');

            if (FileHelper::isFile($mailPath . 'mail_solution_expire.html'))
                $mailSetting['MESSAGE'] = file_get_contents($mailPath . 'mail_solution_expire.html');

        } elseif ($event === 'INTEC_LICENCE_SOLUTION_EXPIRED_NOTIFY') {
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.email.subject.solution.expired');

            if (FileHelper::isFile($mailPath . 'mail_solution_expired.html'))
                $mailSetting['MESSAGE'] = file_get_contents($mailPath . 'mail_solution_expired.html');

        } elseif ($event === 'INTEC_LICENCE_BITRIX_EXPIRE_NOTIFY') {
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.email.subject.bitrix.expire');

            if (FileHelper::isFile($mailPath . 'mail_bitrix_expire.html'))
                $mailSetting['MESSAGE'] = file_get_contents($mailPath . 'mail_bitrix_expire.html');

        } elseif ($event === 'INTEC_LICENCE_BITRIX_EXPIRED_NOTIFY') {
            $mailSetting['SUBJECT'] = Loc::getMessage('intec.core.email.subject.bitrix.expired');

            if (FileHelper::isFile($mailPath . 'mail_bitrix_expired.html'))
                $mailSetting['MESSAGE'] = file_get_contents($mailPath . 'mail_bitrix_expired.html');
        }

        $mail->Add($mailSetting);
    }

    public function getCookieKey ()
    {
        return $this->cookieKey;
    }

    public function setBannerClose ()
    {
        $key = &$this->cookieKey;

        if (!ArrayHelper::keyExists($key, $_COOKIE)) {
            setcookie($key, time());
        }
    }

    public function isBannerClose ()
    {
        $key = &$this->cookieKey;

        if (ArrayHelper::keyExists($key, $_COOKIE) || empty($_COOKIE[$key])) {
            if ((time() - $_COOKIE[$key]) <= 86400) {
                return true;
            }

            setcookie($key, null);
        }

        return false;
    }
}
