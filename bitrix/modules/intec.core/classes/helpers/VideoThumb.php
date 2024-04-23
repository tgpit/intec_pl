<?php

/**
 * �������������:
 * $v = new VideoThumb($link);
 * $v->getVideo(); //������ �� �����
 * $v->getTitle(); //�������� ������
 * $v->fetchImage($path) //������� ����� ������� ������ ������
 *
 * �������� ����:
 * VideoThumb::RunTest()
 */
namespace intec\core\helpers;

class VideoThumb
{
    /** ������ �� ����� */
    protected $link;

    /** ������������ ����� ������ */
    protected $link_parts;

    /** ������������ */
    protected $hosting;

    /** ������������� ����� */
    protected $id;

    /** ������������� ���������� ����� */
    protected $privateId;

    /** �������� */
    protected $image;

    /** �������� ����� */
    protected $title;

    /** ����� */
    protected $video;

    /** �������� ����� */
    protected $pageVideo;

    /** �������� API rutube */
    protected $rutubeAPI;

    const YOUTUBE = 'youtube';
    const VIMEO   = 'vimeo';
    const RUTUBE  = 'rutube';

    /** ��������� ��� ����������� ������������� � �������������� ������ */
    protected $regexp = array(
        self::YOUTUBE => array( //�� ������������
            '/[http|https]+:\/\/(?:www\.|)youtube\.com\/watch\?(?:.*)?v=([a-zA-Z0-9_\-]+)/i',
            '/[http|https]+:\/\/(?:www\.|)youtube\.com\/embed\/([a-zA-Z0-9_\-]+)/i',
            '/[http|https]+:\/\/(?:www\.|)youtu\.be\/([a-zA-Z0-9_\-]+)/i'
        ),
        self::VIMEO   => array( //�� ������������
            '/[http|https]+:\/\/(?:www\.|)vimeo\.com\/([a-zA-Z0-9_\-]+)(&.+)?/i',
            '/[http|https]+:\/\/player\.vimeo\.com\/video\/([a-zA-Z0-9_\-]+)(&.+)?/i'
        ),
        self::RUTUBE  => array(
            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/[play|video]+\/embed\/([a-zA-Z0-9_\-]+)\/\?p=([a-zA-Z0-9_\-]+)/i', //private embed
            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/[play|video]+\/embed\/([a-zA-Z0-9_\-]+)/i', //embed
            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)\/\?p=([a-zA-Z0-9_\-]+)/i', //private page
            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)/i', //page
//            '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/tracks\/([a-zA-Z0-9_\-]+)(&.+)?/i' // track
        )
    );

    /** ������ �� RUtube ��� �������������� � ������ */
    protected $regexp_rutube_extra = '/[http|https]+:\/\/(?:www\.|)rutube\.ru\/video\/([a-zA-Z0-9_\-]+)\//i';

    /** �������� ������, ������� �������������� */
    protected static $test = array(
        'https://youtube.com/watch?v=ShPq2Dmy6X8',
        'https://www.youtube.com/watch?v=6dwqZw0j_jY&feature=youtu.be',
        'https://www.youtube.com/watch?v=cKZDdG9FTKY&feature=channel',
        'www.youtube.com/watch?v=yZ-K7nCVnBI&playnext_from=TL&videos=osPknwzXEas&feature=sub',
        'https://www.youtube.com/embed/ShPq2Dmy6X8?rel=0',
        'https://youtu.be/ShPq2Dmy6X8',
        'youtu.be/6dwqZw0j_jY',
        'https://www.youtu.be/afa-5HQHiAs',

        'vimeo.com/55028438',
        'https://player.vimeo.com/video/55028438?title=0&byline=0&portrait=0&badge=0&color=e1a931',

        'https://rutube.ru/video/c9715d5b435cb9d9a2673b8aa4b1cbd1/#.UMQYln9yTWQ',
        'https://rutube.ru/video/c9715d5b435cb9d9a2673b8aa4b1cbd1/?ref=top',
        'rutube.ru/tracks/c9715d5b435cb9d9a2673b8aa4b1cbd1.html',
        'https://www.rutube.ru/video/embed/c9715d5b435cb9d9a2673b8aa4b1cbd1',
        'https://rutube.ru/play/embed/caafe83ff1c6ed38d394635b83ece578/?p=IBgzQQrKH4qB1bqm_91x7Q&skinColor=ff00ff',
    );

    /**
     * @param $link      ������ �� �����
     * @param $autostart ����� ���������� ������ � ����
     */
    function __construct($link = null, $autostart = true)
    {
        if ($link) {
            $this->setLink($link);
            if ($autostart) {
                $this->process();
            }
        }
    }

    /** ������������ */
    public function getHosting()
    {
        return $this->hosting;
    }

    /** ������������� ����� */
    public function getId()
    {
        return $this->id;
    }

    /** ������������� ���������� ����� */
    public function getPrivateId()
    {
        return $this->privateId;
    }

    /** ������ �� ������ */
    public function getImage()
    {
        return $this->image;
    }

    /** ������ �� embed ����� */
    public function getVideo()
    {
        return $this->video;
    }

    /** ������ �� page ����� */
    public function getPageVideo()
    {
        return $this->pageVideo;
    }

    /** ������ �� API ����� */
    public function getRutubeAPI()
    {
        return $this->rutubeAPI;
    }

    /** ������ �� HLS �����
     * ����������� ������ ���� User-Agent: Firefox
     * �.�. � ��� 2 ������ ������ ��� ������� � �����
     * � � firefox � api ���� ������������� �� json ��� CORS
     *
     * ����� ����� ����� ������ (���������) ������
     * �.�. ������ � ��� ��������� �� ��������
     */
    public function getRutubeHLS()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->rutubeAPI,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/119.0"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if(empty($err)) {
            $videoBalancerObj = json_decode($response);
            $HLSJson = file_get_contents($videoBalancerObj->video_balancer->json);
            $HLSObj = json_decode($HLSJson);

            return $HLSObj->results[1];
        }
    }

    /** �������� ����� */
    public function getTitle()
    {
        return $this->title;
    }

    /** ������ ������ �� ����� */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /** ��������� ������. ���������� ������������� ������������� ��� false */
    public function process()
    {
        if ($this->cleanLink()) {
            if ($this->maybeYoutube()) {
                return self::YOUTUBE;
            }

            if ($this->maybeVimeo()) {
                return self::VIMEO;
            }

            if ($this->maybeRutube()) {
                return self::RUTUBE;
            }
        }

        return false;
    }

    /** ������� ������. ���� �� ������� ��� ����� ��� ������ - ������� ������ ���������� ����� */
    public function fetchImage($filename = null)
    {
        if (!$url = $this->getImage()) {
            return false;
        }

        if (!$res = $this->fetchPage($url)) {
            return false;
        }

        return $filename
            ? file_put_contents($filename, $res)
            : $res;
    }

    /** �������� � ���������� ������ � ������ */
    protected function cleanLink()
    {
        if (!preg_match('/^(http|https)\:\/\//i', $this->link)) {
            $this->link = 'https://' . $this->link;
        }

        if (!$this->link_parts = parse_url($this->link)) {
            return false;
        }

        return true;
    }

    /** �������� YOUTUBE */
    protected function maybeYoutube()
    {
        $h = str_replace('www.', '', $this->link_parts['host']);
        $p = isset($this->link_parts['path']) ? $this->link_parts['path'] : false;

        if ('youtube.com' == $h) {
            parse_str($this->link_parts['query'], $q);

            if ('/watch' == $p && !empty($q['v'])) {
                return $this->foundYoutube($q['v']);
            }
            if (0 === strpos($p, '/embed/')) {
                return $this->foundYoutube(str_replace('/embed/', '', $p));
            }
        } elseif ('youtu.be' == $h) {
            return $this->foundYoutube(trim($p, '/'));
        }

        return false;
    }

    /** �������� VIMEO */
    protected function maybeVimeo()
    {
        $h = str_replace('www.', '', $this->link_parts['host']);
        $p = isset($this->link_parts['path']) ? $this->link_parts['path'] : false;

        if ('vimeo.com' == $h) {
            return $this->foundVimeo(trim($p, '/'));
        } elseif ('player.vimeo.com' == $h && 0 === strpos($p, '/video/')) {
            return $this->foundVimeo(str_replace('/video/', '', $p));
        }

        return false;
    }

    /** �������� RUTUBE */
    protected function maybeRutube($html = null)
    {
        $link = $html ?: $this->link;

        foreach ($this->regexp[self::RUTUBE] as $regexp) {
            if (preg_match($regexp, $link, $matches)) {
                return $this->foundRutube($matches[1], $matches[2]);
            }
        }

        // �������� �� ��������� ������ RUtube`a
        if (is_null($html) && preg_match($this->regexp_rutube_extra, $this->link, $matches)) {
            $html = $this->fetchPage($matches[0]);
            if ($r = $this->maybeRutube($html)) {
                return $r;
            }
        }

        return false;
    }

    /** ��������� YOUTUBE */
    protected function foundYoutube($id)
    {
        if (empty($id) || strlen($id) != 11) {
            return false;
        }

        $this->hosting = self::YOUTUBE;
        $this->id      = $id;
        $this->image   = 'https://img.youtube.com/vi/' . $id . '/0.jpg';
        $this->video   = 'https://www.youtube.com/embed/' . $id;

        $this->getYoutubeInfo($id);

        return true;
    }

    /** ��������� VIMEO */
    protected function foundVimeo($id)
    {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }

        $this->hosting = self::VIMEO;
        $this->id      = $id;
        $this->video   = 'https://player.vimeo.com/video/' . $id;

        $this->getVimeoInfo($id);

        return true;
    }

    /** ��������� RUTUBE */
    protected function foundRutube($id, $privateId = '')
    {
        $this->hosting = self::RUTUBE;
        $this->id      = $id;
        $this->privateId = null;

        if(empty($privateId)) {
            $this->video = 'https://rutube.ru/play/embed/' . $id;
            $this->pageVideo = 'https://rutube.ru/video/' . $id;
            $this->rutubeAPI = 'https://rutube.ru/api/play/options/' . $this->id;

            $this->getRutubeInfo($id);
        }else{
            $this->privateId = $privateId;
            $this->video = 'https://rutube.ru/play/embed/' . $id . '/?p=' . $this->privateId;
            $this->pageVideo = 'https://rutube.ru/video/' . $id . '/?p=' . $this->privateId;
            $this->rutubeAPI = 'https://rutube.ru/api/play/options/' . $this->id . '/?p=' . $this->privateId;

            $this->getRutubeInfo($id, $this->privateId);
        }

        return true;
    }

    /** ������� JSON �� RUTUBE � ����������� �������� */
    protected function getRutubeInfo($id, $privateId = '')
    {
        if(empty($privateId)) {
            $APIJson = file_get_contents("https://rutube.ru/api/video/" . $id);
            $APIObj = json_decode($APIJson);
            $this->title = $APIObj->title;
            $this->image = $APIObj->thumbnail_url;
        }else{
            $APIJson = file_get_contents("https://rutube.ru/api/video/" . $id . "/?p=" . $privateId);
            $APIObj = json_decode($APIJson);
            $this->title = $APIObj->title;
            $this->image = $APIObj->thumbnail_url;
        }
    }

    /** ������� XML �� VIMEO � ����������� �������� */
    protected function getVimeoInfo($id)
    {
        if (@$xml = simplexml_load_file('https://vimeo.com/api/v2/video/' . $id . '.xml')) {
            $this->title = (string)$xml->video->title;
            $this->image = (string)$xml->video->thumbnail_large ? : $xml->video->thumbnail_medium;
        }
    }

    /** ��������� �������� ������ */
    protected function getYoutubeInfo($id)
    {
        if (@$xml = simplexml_load_file('https://gdata.youtube.com/feeds/api/videos/' . $id)) {
            $this->title = (string)$xml->title;
        }
    }

    /** ���������� �������� � ������� CURL */
    protected function fetchPage($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);

        return curl_exec($ch);
    }

    /** ��������� ���� �� ����� URL */
    public static function RunTest($links = null)
    {
        if (!is_array($links)) {
            $links = static::$test;
        }

        foreach ($links as $link) {
            $v = new static($link);
            echo "<h1>$link</h1>\n"
                . "<h3>" . $v->getHosting() . "</h3>"
                . "<b>Video:</b> " . $v->getVideo() . "<br />\n"
                . "<b>Name:</b> " . $v->getTitle() . "<br />\n"
                . "<b>Picture:</b> " . $v->getImage() . "<hr />\n";
        }
    }
}