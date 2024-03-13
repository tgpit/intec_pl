<?php
namespace intec\constructor\models\build;

use intec\core\base\Exception;
use intec\core\base\InvalidParamException;
use intec\core\base\Model;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;
use intec\core\io\Path;
use intec\constructor\base\Renderable;
use intec\constructor\models\Build;
use intec\constructor\models\build\Template as BuildTemplate;
use intec\constructor\models\build\layout\Renderer;
use intec\constructor\models\build\layout\Zone;
use intec\constructor\models\build\layout\Zones;

class Layout extends Model implements Renderable
{
    /**
     * @var Build
     */
    protected $_build;
    /**
     * @var string
     */
    protected $_code;
    /**
     * @var string
     */
    protected $_name;
    /**
     * @var Path
     */
    protected $_path;
    /**
     * @var Zones
     */
    protected $_zones;
    /**
     * @var array
     */
    protected $_meta;

    /**
     * @inheritdoc
     */
    public function __construct($build, $path, $code, $config = [])
    {
        if (!($build instanceof Build))
            throw new InvalidParamException('Parameter "build" should be a "'.Build::className().'"');

        if (empty($code) && !Type::isNumeric($code))
            throw new InvalidParamException('Parameter "code" cannot be empty');

        $path = Path::from($path);

        if ($path->getIsRelative())
            throw new InvalidParamException('Parameter "path" invalid');

        $this->_build = $build;
        $this->_code = $code;
        $this->_path = $path;

        if (!FileHelper::isFile($this->getTemplatePath()->getValue()))
            throw new Exception('Invalid layout structure of "'.$code.'" layout');

        $meta = $path->add('meta.php')->getValue('/');

        if (FileHelper::isFile($meta)) {
            $meta = include($meta);
        } else {
            $meta = [];
        }

        $meta = ArrayHelper::merge([
            'name' => null,
            'zones' => null
        ], $meta);

        $this->_name = $meta['name'];
        $this->_zones = new Zones();

        if (Type::isArray($meta['zones']))
            foreach ($meta['zones'] as $zone) {
                if (Type::isArray($zone)) {
                    $zone = ArrayHelper::merge([
                        'code' => null,
                        'name' => null
                    ], $zone);

                    if (!empty($zone['code']) || Type::isNumeric($zone['code']))
                        $this->_zones->set($zone['code'], new Zone($zone['code'], $zone['name']));
                }
            }

        unset($meta['name'], $meta['zones']);

        $this->_meta = $meta;

        parent::__construct($config);
    }

    /**
     * @return Build
     */
    public function getBuild()
    {
        return $this->_build;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return Path
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @return Path
     */
    public function getPicturePath()
    {
        return $this->_path->add('icon.png');
    }

    /**
     * @return Path
     */
    public function getTemplatePath()
    {
        return $this->_path->add('template.php');
    }

    /**
     * @return Zones
     */
    public function getZones()
    {
        return $this->_zones;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->_meta;
    }

    /**
     * @param Renderer $renderer
     * @param BuildTemplate $template
     */
    public function render($renderer = null, $template = null)
    {
        if (!($renderer instanceof Renderer))
            return;

        if ($renderer->renderStart($this, $template)) {
            $renderer->render();
            $renderer->renderEnd();
        }
    }
}