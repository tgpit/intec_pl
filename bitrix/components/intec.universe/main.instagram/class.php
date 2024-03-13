<?

use intec\core\base\InvalidParamException;
use intec\core\helpers\Json;
use intec\core\net\Url;
use intec\core\net\http\Request;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Encoding;
use intec\core\helpers\FileHelper;
use intec\core\helpers\StringHelper;
use intec\core\io\Path;
use Bitrix\Main\Localization\Loc;

class IntecInstagramComponent extends CBitrixComponent
{

    private $token;
    private $path;
    private $globalCachePath;

    public function setToken($value)
    {
        $this->token = $value;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setFileCachePath($value)
    {
        if (!empty($value) && !empty($this->token)) {
            $value = StringHelper::replaceMacros($value, ['SITE_DIR' => SITE_DIR]);
            $value = Path::from('@root/'.$value);
            $value = $value->add('instagram.'.$this->token.'.json')->value;
            $this->path = $value;
        }
    }

    public function getFileCachePath()
    {
        return $this->path;
    }

    public function setGlobalFileCachePath($value)
    {
        if (!empty($value) && !empty($this->token)) {
            $value = StringHelper::replaceMacros($value, ['SITE_DIR' => SITE_DIR]);
            $value = Path::from('@root/bitrix/cache/'.$value);
            $value = $value->add('instagram.'.$this->token.'.json')->value;
            $this->globalCachePath = $value;
        }
    }

    public function getGlobalFileCachePath()
    {
        return $this->globalCachePath;
    }

    public function isCacheFileExists($cacheTime = 0)
    {
        if ($_REQUEST['clear_cache'] === 'Y')
            return false;

        if (FileHelper::isFile($this->path)) {

            if (!$this->isGlobalCacheExist()) {
                $this->createGlobalCache();
                return false;
            }

            $json = FileHelper::getFileData($this->path);

            try {
                $data = Json::decode($json);
            } catch (InvalidParamException $exception) {
                $data = null;
                return false;
            }

            $dateCurrent = new DateTime();
            $dateExpired = new DateTime($data['date']);
            $dateExpired->add(new DateInterval('PT'.$cacheTime.'S'));

            if ($dateCurrent > $dateExpired)
                return false;
            else
                return true;
        } else {
            return false;
        }
    }

    public function storeCache($data)
    {
        if (!empty($data) && !empty($this->path)) {
            $json = Json::encode($data);
            return FileHelper::setFileData($this->path, $json);
        } else {
            return false;
        }
    }

    public function createGlobalCache()
    {
        $data = [
            'date' => date('Y-m-d H:i:s'),
            'status' => true
        ];

        if (!empty($data) && !empty($this->globalCachePath)) {
            $json = Json::encode($data);
            FileHelper::setFileData($this->globalCachePath, $json);
            return true;
        } else {
            return false;
        }
    }

    public function isGlobalCacheExist()
    {
        if (FileHelper::isFile($this->globalCachePath)) {
            return true;
        } else {
            return false;
        }
    }

    public function readCache()
    {
        $json = null;
        $data = null;

        $json = FileHelper::getFileData($this->path);

        try {
            $data = Json::decode($json);
        } catch (InvalidParamException $exception) {
            $data = null;
        }

        return $data;
    }

    public function getData()
    {
        if (empty($this->token))
            return null;

        $url = new Url();
        $url->setScheme('https')
            ->setHost('graph.instagram.com')
            ->setPathString('/me/media')
            ->getQuery()
                ->set('access_token', $this->token)
                ->set('fields','id,media_url,permalink,caption,timestamp,thumbnail_url');

        $request = new Request();
        $request->setJumps(100);
        $response = $request->send($url->build());
        $json = $response->getContent();

        try {
            $data = Json::decode($json);
            $data['date'] = date('Y-m-d H:i:s');
        } catch (InvalidParamException $exception) {
            $data = null;
        }

        return $data;
    }

    public function refreshToken()
    {
        if (empty($this->token))
            return null;

        $url = new Url();
        $url->setScheme('https')
            ->setHost('graph.instagram.com')
            ->setPathString('/refresh_access_token')
            ->getQuery()
                ->set('access_token', $this->token)
                ->set('grant_type','ig_refresh_token');

        $request = new Request();
        $request->setJumps(100);
        $response = $request->send($url->build());
        $json = $response->getContent();

        try {
            $data = Json::decode($json);
            $data['date'] = date('Y-m-d H:i:s');
        } catch (InvalidParamException $exception) {
            $data = null;
        }

        return $data;
    }

    public function executeComponent(){

        global $APPLICATION;

        $arParams = $this->arParams;
        $this->setToken(ArrayHelper::getValue($arParams, 'ACCESS_TOKEN'));
        $countItems = ArrayHelper::getValue($arParams, 'COUNT_ITEMS', '10');

        if (empty(trim($countItems)))
            $countItems = 0;

        $path = ArrayHelper::getValue(
            $arParams,
            'CACHE_PATH',
            'upload/intec.universe/instagram/cache/#SITE_DIR#'
        );

        $json = null;
        $data = null;

        $globalPath = SITE_ID . $this->getRelativePath();
        $this->setGlobalFileCachePath($globalPath);
        $this->setFileCachePath($path);
        $cacheExist = $this->isCacheFileExists($arParams['CACHE_TIME']);

        if (!$cacheExist) {
            $this->clearResultCache();
            $data = $this->getData();
            $this->refreshToken();
            $this->storeCache($data);
        } else {
            $data = $this->readCache();
        }

        $this->arResult = [
            'ITEMS' => []
        ];

        if ($data !== null) {
            $counter = 1;

            foreach($data['data'] as $arItem){

                if ($countItems > 0 && $counter > $countItems)
                    break;

                $this->arResult['ITEMS'][] = [
                    'ID' => $arItem['id'],
                    'IMAGES' => $arItem['media_url'],
                    'DESCRIPTION' => $arItem['caption'],
                    'LINK' => $arItem['permalink'],
                    'DATE' => $arItem['timestamp'],
                    'VIDEO' => [
                        'IS' => ArrayHelper::keyExists('thumbnail_url',$arItem),
                        'IMAGES' => ArrayHelper::keyExists('thumbnail_url',$arItem) ? $arItem['thumbnail_url'] : null
                    ],
                ];
                $counter++;
            }

            $this->arResult['ITEMS'] = ArrayHelper::convertEncoding(
                $this->arResult['ITEMS'],
                Encoding::getDefault(),
                Encoding::UTF8
            );

            $this->includeComponentTemplate();
        }

        $backUrl = $APPLICATION->GetCurPage();
        $urlUpdate = new Url();
        $urlUpdate->setPathString($this->getPath()."/update.php")
            ->getQuery()->set('BACK_URL', $backUrl)
            ->set('ACCESS_TOKEN', $this->token)
            ->set('CACHE_FILE', $path)
            ->set('refresh', 'Y');

        $this->AddIncludeAreaIcons(
            array(
                array(
                    'URL' => $urlUpdate->build(),
                    'SRC' => '',
                    'TITLE' => Loc::GetMessage('REFRESH_BUTTON')
                )
            )
        );

        return null;
    }
}