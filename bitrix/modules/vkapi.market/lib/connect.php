<?php

namespace VKapi\Market;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Exception\AccessException;
use VKapi\Market\Exception\UnknownResponseException;
use VKapi\Market\Exception\ApiResponseException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * 
 * Класс через который происходят обращенияк API вконтакте
 * Class Connect
 * 
 * 
 * @package VKapi\Market
 */
class Connect
{
    const ERROR_RESPONSE = 1;
    const ERROR_ANTICAPTCHA = 2;
    private $oTable = null;
    // private $version = '5.131';
    private $version = '5.140';
    private $appId = null;
    private $appSecret = null;
    private $token = null;
    private $vk_id = 0;
    private $user_id = 0;
    private $id = 0;
    private $iConnectInterval = 500000;
    public function __construct(\Bitrix\Main\HttpRequest $httpRequest = null)
    {
        if (is_null($httpRequest)) {
            $httpRequest = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        }
        $this->iConnectInterval = \VKapi\Market\Manager::getInstance()->getConnectInterval() * 1000;
        $this->httpRequest = $httpRequest;
    }
    /**
     * 
     * Возвращает объект запроса текущей страницы
     * 
     * @return \Bitrix\Main\HttpRequest
     */
    public function getRequest()
    {
        return $this->httpRequest;
    }
    /**
     * 
     * Вернет адрес сайта со схемой, например https://bxmaker.ru
     * 
     * @return string
     */
    public function getSiteUrl()
    {
        return ($this->getRequest()->isHttps() ? 'https://' : 'http://') . $this->getDomain();
    }
    /**
     * 
     * Возвращает домен сайта
     * 
     * @return string
     */
    public function getDomain()
    {
        return preg_replace('/\\:\\d+$/', '', $this->getRequest()->getHttpHost());
    }
    /**
     * 
     * Вернет объект для работы с БД
     * 
     * @return ConnectTable
     */
    public function getTable()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\ConnectTable();
        }
        return $this->oTable;
    }
    /**
     * 
     * Установка используемой версии api
     * 
     * @param $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
    /**
     * 
     * Возвращает используемую версию api
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
    /**
     * 
     * Установка ID приложения
     * 
     * @param $client_id
     */
    public function setAppId($client_id)
    {
        $this->appId = $client_id;
    }
    /**
     * 
     * Возвращает ID приложения
     * 
     * @return string
     */
    public function getAppId()
    {
        if (is_null($this->appId)) {
            $this->appId = \VKapi\Market\Manager::getInstance()->getParam('APP_ID', '', '');
        }
        return $this->appId;
    }
    public function setAppSecret($key)
    {
        $this->appSecret = $key;
    }
    /**
     * 
     * Возвращает защищенный ключ приложения
     * 
     * @return string
     */
    public function getAppSecret()
    {
        if (is_null($this->appSecret)) {
            $this->appSecret = \VKapi\Market\Manager::getInstance()->getParam('APP_SECRET', '', '');
        }
        return $this->appSecret;
    }
    public function setToken($token)
    {
        $this->token = $token;
    }
    public function getToken()
    {
        return $this->token;
    }
    public function setVkId($VK_USER_ID_VK)
    {
        $this->vk_id = $VK_USER_ID_VK;
    }
    public function getVkId()
    {
        return (int) $this->vk_id;
    }
    public function setUserId($vkUserId)
    {
        $this->user_id = $vkUserId;
    }
    public function getUserId()
    {
        return (int) $this->user_id;
    }
    private function setId($rowID)
    {
        $this->id = $rowID;
    }
    public function getId()
    {
        return (int) $this->id;
    }
    /**
     * 
     * Возвращает объект для запросов
     * 
     * @return HttpClient
     */
    public function getHttpClient()
    {
        $oHttpClient = new \VKapi\Market\HttpClient();
        $oManager = \VKapi\Market\Manager::getInstance();
        if ($oManager->isEnabledProxy()) {
            $oHttpClient->setProxy($oManager->getProxyHost(), $oManager->getProxyPort(), $oManager->getProxyUser(), $oManager->getProxyPass());
        }
        return $oHttpClient;
    }
    /**
     * 
     * Возвращает Url адрес окна для предоставления доступа к вк
     * 
     * @return string
     */
    public function getAuthCodeFlowUrl()
    {
        $arParams = [
            'client_id' => $this->getAppId(),
            'redirect_uri' => $this->getAuthCodeFlowRedirectUri(),
            'display' => 'mobile',
            'scope' => implode(',', ['notifications', 'market', 'offline', 'stats', 'user', 'groups', 'photos']),
            'response_type' => 'code',
            //            'response_type' => 'token',
            'v' => $this->getVersion(),
            'state' => 'get_code',
            'revoke' => 1,
        ];
        return 'https://oauth.vk.com/authorize?' . http_build_query($arParams);
    }
    /**
     * 
     * Возвращает довренный Redirect URI для авторизации и получения токенов
     * 
     * @return string
     */
    public function getAuthCodeFlowRedirectUri()
    {
        // $uri = new \Bitrix\Main\Web\Uri($this->getRequest()->getRequestUri());
        // $uri->deleteParams([
        // "get_code",
        // 'code',
        // 'lang',
        // 'state'
        // ]);
        $uri = new \Bitrix\Main\Web\Uri('/bitrix/admin/vkapi.market_list.php');
        $uri->addParams(["get_code" => 'Y']);
        return $this->getSiteUrl() . $uri->getPath();
    }
    /**
     * 
     * Проверка что пользователь нажал кнопку добавить акаунт,
     * попал на страницу подтвердждения в ВКонтакте
     * и далее был переброшен обратно с кодом для получения токенов
     * 
     * @return Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function checkAuthCodeFlow()
    {
        global $USER;
        $result = new \VKapi\Market\Result();
        $state = $this->getRequest()->getQuery("state");
        $code = $this->getRequest()->getQuery("code");
        if ($state == 'get_code') {
            if (!strlen($code)) {
                $result->setError(new \VKapi\Market\Error($this->getRequest()->getQuery("error") . ': ' . $this->getRequest()->getQuery("error_description")));
                return $result;
            }
            $oHttp = $this->getHttpClient();
            $oHttp->get('https://oauth.vk.com/access_token', ['client_id' => $this->getAppId(), 'client_secret' => $this->getAppSecret(), 'redirect_uri' => $this->getAuthCodeFlowRedirectUri(), 'code' => $code]);
            $arData = json_decode($oHttp->getResult(), true);
            if ($oHttp->getStatus() != 200) {
                if ($oHttp->getStatus() == '') {
                    $result->setError(new \VKapi\Market\Error(\VKapi\Market\Manager::getInstance()->getMessage('ERROR_STATUS'), self::ERROR_RESPONSE));
                } else {
                    $result->setError(new \VKapi\Market\Error('Status ' . $oHttp->getStatus() . ' - ' . (isset($arData['error']) ? $arData['error'] . ': ' . $arData['error_description'] : ''), self::ERROR_RESPONSE));
                }
                return $result;
            }
            if (isset($arData['error'])) {
                $result->setError(new \VKapi\Market\Error($arData['error'] . ': ' . $arData['error_description']));
            } else {
                if ($arData['access_token']) {
                    if (intval($arData['expires_in']) == 0) {
                        $arData['expires_in'] = 60 * 60 * 24 * 365 * 2;
                    }
                    $this->setToken($arData['access_token']);
                    $userName = '';
                    $resGetUser = $this->method('users.get', ['user_ids' => $arData['user_id'], 'fields' => 'photo_id, verified, sex, bdate, city, country, home_town, has_photo, photo_50, photo_100, photo_200_orig, photo_200, photo_400_orig, photo_max, photo_max_orig, online, lists, domain, has_mobile, contacts, site, education, universities, schools, status, last_seen, followers_count, common_count, occupation, nickname, relatives, relation, personal, connections, exports, wall_comments, activities, interests, music, movies, tv, books, games, about, quotes, can_post, can_see_all_posts, can_see_audio, can_write_private_message, can_send_friend_request, is_favorite, is_hidden_from_feed, timezone, screen_name, maiden_name, crop_photo, is_friend, friend_status, career, military, blacklisted, blacklisted_by_me']);
                    if ($resGetUser->isSuccess()) {
                        $obGetUser = $resGetUser->getData('response');
                        $userName = trim($obGetUser[0]['first_name'] . ' ' . $obGetUser[0]['last_name']);
                    }
                    $dbrUser = $this->getTable()->getList(['filter' => ['USER_ID_VK' => $arData['user_id']]]);
                    if ($ar = $dbrUser->fetch()) {
                        $this->setToken($arData['access_token']);
                        $resUpdate = $this->getTable()->update($ar['ID'], ['ACCESS_TOKEN' => $arData['access_token'], 'EXPIRES_IN' => $arData['expires_in'], 'NAME' => $userName]);
                        if ($resUpdate->isSuccess()) {
                            $result->setData('MSG', $this->getMessage('USER_IS_UPDATED'));
                            $result->setData('ID', $ar['ID']);
                        } else {
                            $result->setError(new \VKapi\Market\Error($resUpdate->getErrorMessages(), 2));
                        }
                        $this->initAccountId($ar['ID']);
                    } else {
                        $resAdd = $this->getTable()->add(['USER_ID' => $USER->GetID(), 'USER_ID_VK' => $arData['user_id'], 'EXPIRES_IN' => $arData['expires_in'], 'ACCESS_TOKEN' => $arData['access_token'], 'NAME' => $userName]);
                        if ($resAdd->isSuccess()) {
                            $result->setData('MSG', $this->getMessage('USER_IS_ADDED'));
                            $result->setData('ID', $resAdd->getId());
                            $this->initAccountId($resAdd->getId());
                        } else {
                            $result->setError(new \VKapi\Market\Error($resAdd->getErrorMessages(), 1));
                        }
                    }
                } else {
                    $result->setError(new \VKapi\Market\Error('Unknown response'));
                }
            }
            return $result;
        } else {
            $result->setError(new \VKapi\Market\Error(''));
        }
        return $result;
    }
    /**
     * 
     * Вернет языкозависимое сообщение
     * 
     * @param $code
     * @param array $arReplace
     * 
     * @return mixed|string
     */
    public function getMessage($code, $arReplace = [])
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.CONNECT.' . $code, $arReplace);
    }
    /**
     * 
     * Инициализация подклчюения по идентификатору добавленого аккаунта
     * 
     * @param $ID
     * 
     * @return \VKapi\Market\Result
     * 
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function initAccountId($ID)
    {
        $result = new \VKapi\Market\Result();
        $ar = $this->getTable()->getById(intval($ID))->fetch();
        if (!$ar) {
            throw new \VKapi\Market\Exception\AccessException($this->getMessage('ERROR_ACCOUNT_ID', ['#ID#' => $ID]), 'ERROR_ACCOUNT_ID');
        }
        $this->setToken($ar['ACCESS_TOKEN']);
        $this->setVkId($ar['USER_ID_VK']);
        $this->setUserId($ar['USER_ID']);
        $this->setId($ar['ID']);
        return $result;
    }
    /**
     * 
     * Возвращает массив с описанием акаунтов, готовый для передачи SelectBoxFromArray()
     * 
     * @return array ['REFERENCE_ID' => [], 'REFERENCE' => []]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getAccountsSelectList()
    {
        static $arAccounts;
        if (!isset($arAccounts)) {
            $arAccounts = ['REFERENCE_ID' => [''], 'REFERENCE' => [$this->getMessage('NO_SELECT')]];
            $dbr = $this->getTable()->getList(['order' => ['NAME' => 'ASC']]);
            while ($ar = $dbr->Fetch()) {
                $arAccounts['REFERENCE_ID'][] = $ar['ID'];
                $arAccounts['REFERENCE'][] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
            }
        }
        return $arAccounts;
    }
    /**
     * 
     * Возвращает список аккаунтов
     * 
     * @return array {id:name, ...}
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getAccountList()
    {
        static $arAccounts;
        if (!isset($arAccounts)) {
            $arAccounts = [];
            $dbr = $this->getTable()->getList(['order' => ['NAME' => 'ASC']]);
            while ($ar = $dbr->Fetch()) {
                $arAccounts[$ar['ID']] = $ar['NAME'];
            }
        }
        return $arAccounts;
    }
    /**
     * 
     * Запрос к api вконтакте, вернет объект с результатом, если все хорошо или с ошибкой, елси в ответе вернется
     * ошибка
     * 
     * @param $method - метод, например market.getAlbums
     * @param $params - парамтеры для метода, напрмиер array('owner_id' => '-105897', 'offset' => 0)
     * @param int $iteration - итерация, для повторного запроса, когда появляется необходимость ввода капчи, и нужно
     * сделать повторный запрос
     * 
     * @return \VKapi\Market\Result
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    public function method($method, $params = [], $iteration = 0)
    {
        // отслеживаем итерации, чтобы при запросе капчи не делать это слишком много раз
        ++$iteration;
        if (!isset($params['v'])) {
            $params['v'] = $this->getVersion();
        }
        if (!isset($params['access_token'])) {
            $params['access_token'] = $this->getToken();
        }
        $url = 'https://api.vk.com/method/' . $method;
        // ожидаение
        usleep($this->iConnectInterval);
        $oHttp = $this->getHttpClient();
        $oHttp->setVersion($oHttp::HTTP_1_1);
        $oHttp->post($url, \VKapi\Market\Manager::getInstance()->base()->prepareEncoding($params));
        // лог
        $paramsLog = $params;
        $paramsLog['access_token'] = '***';
        $text = '--------' . PHP_EOL . date('d.m.Y H:i:s  ') . $url . PHP_EOL . json_encode($paramsLog, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
        $text .= '- response: status' . $oHttp->getStatus() . PHP_EOL . $oHttp->getResult() . PHP_EOL . PHP_EOL . PHP_EOL;
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/upload/vklog.txt', $text, FILE_APPEND);
        // отловим ошибку с необходимостью ввода капчи, и попробуем ее распознать
        try {
            $result = $this->prepareResponse($oHttp);
        } catch (\VKapi\Market\Exception\ApiResponseException $ex) {
            if (!$ex->is(\VKapi\Market\Api::ERROR_14) || $iteration > 2) {
                throw $ex;
            }
            return $this->resolveCaptcha($oHttp, $method, $params, $iteration);
        }
        return $result;
    }
    /**
     * 
     * Отправка в старой версии, в последующем будет удалено
     * 
     * @param $url
     * @param $arFiles
     * @param array $params
     * 
     * @return Result
     * @throws ApiResponseException
     * @throws UnknownResponseException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     */
    public function sendFiles($url, $arFiles, $params = [])
    {
        // usleep($this->iConnectInterval);
        $oHttp = $this->getHttpClient();
        $oHttp->setVersion($oHttp::HTTP_1_1);
        $oHttp->post($url, $params, $arFiles);
        return $this->prepareResponse($oHttp);
    }
    /**
     * 
     * Подготавливает ответ от API
     * 
     * @param $oHttp
     * 
     * @return Result
     * @throws ApiResponseException
     * @throws UnknownResponseException
     */
    protected function prepareResponse($oHttp)
    {
        $oResult = new \VKapi\Market\Result();
        if ($oHttp->getStatus() != 200) {
            throw new \VKapi\Market\Exception\UnknownResponseException($this->getMessage('ERROR_RESPONSE_STATUS', ['#STATUS#' => $oHttp->getStatus()]), 'ERROR_RESPONSE_STATUS', $oHttp);
        } else {
            $response = json_decode($oHttp->getResult(), true);
            if (is_array($response)) {
                $response = \VKapi\Market\Manager::getInstance()->base()->restoreEncoding($response);
            }
            $response = (array) $response;
            $oResult->setDataArray($response);
            if (isset($response['error'])) {
                if (isset($response['error']['error_code'])) {
                    throw new \VKapi\Market\Exception\ApiResponseException($response['error'], $oHttp);
                }
                throw new \VKapi\Market\Exception\UnknownResponseException($this->getMessage('ERROR_UNKNOWN_FORMAT', ['#ERROR#' => (string) $response['error']]), 'ERROR_UNKNOWN_FORMAT', $oHttp);
            }
        }
        return $oResult;
    }
    /**
     * 
     * Запрашивает данные по captcha
     * 
     * @param $oHttp
     * @param $method
     * @param $params
     * @param $iteration
     * 
     * @return Result|void
     * @throws ApiResponseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function resolveCaptcha($oHttp, $method, $params, $iteration)
    {
        $response = json_decode($oHttp->getResult(), true);
        $oAntiCaptcha = \VKapi\Market\AntiCaptcha::getInstance();
        $resultSendCaptcha = $oAntiCaptcha->sendImageContent(file_get_contents($response['error']['captcha_img']));
        if ($resultSendCaptcha->isSuccess()) {
            $captchaId = $resultSendCaptcha->getData('ID');
            // ждем 25 сек
            sleep(5);
            $word = null;
            $i = 4;
            while ($i > 0 && is_null($word)) {
                $i--;
                $oAntiCaptcha->checkResult();
                $word = $oAntiCaptcha->getWord($captchaId);
                if (is_null($word)) {
                    sleep(5);
                }
            }
            if ($word === false) {
                throw new \VKapi\Market\Exception\ApiResponseException($response['error'], $oHttp);
            } else {
                $params['captcha_sid'] = $response['error']['captcha_sid'];
                $params['captcha_key'] = $word;
                return $this->method($method, $params, $iteration);
            }
        } else {
            if (isset($response['error']['error_code'])) {
                // добавим инфу антикапчи
                $response['error']['error_msg'] .= '| ';
                $response['error']['error_msg'] .= $resultSendCaptcha->getErrorMessages();
                throw new \VKapi\Market\Exception\ApiResponseException($response['error'], $oHttp);
            }
        }
    }
}
?>