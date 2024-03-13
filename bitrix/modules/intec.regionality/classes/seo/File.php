<?php
namespace intec\regionality\seo;

use intec\core\base\BaseObject;
use intec\core\base\InvalidParamException;
use intec\core\helpers\FileHelper;
use intec\core\helpers\StringHelper;
use intec\core\io\Path;

/**
 * Класс, представляющий файл SEO оптимизации.
 * Class File
 * @property boolean $isExists Файл существует. Только для чтения.
 * @package intec\regionality\seo
 * @author apocalypsisdimon@gmail.com
 */
class File extends BaseObject
{
    /**
     * Путь до файла.
     * @var Path
     */
    protected $_path;

    /**
     * Возвращает путь до файла.
     * @return Path
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @inheritdoc
     * @param Path|string $path
     */
    public function __construct($path, $config = [])
    {
        $this->_path = Path::from($path);

        if ($this->_path->getIsRelative())
            throw new InvalidParamException("Invalid path. Need absolute path.");

        parent::__construct($config);
    }

    /**
     * Считывает содержимое файла.
     * @return string|null
     */
    public function read()
    {
        return FileHelper::getFileData($this->_path->getValue());
    }

    /**
     * Записывает содержимое в файл.
     * @param string|null $data
     */
    public function write($data)
    {
        FileHelper::setFileData($this->_path->getValue(), $data);
    }

    /**
     * Удаляет файл.
     */
    public function delete()
    {
        if ($this->getIsExists())
            return @unlink($this->_path->getValue());

        return true;
    }

    /**
     * Проверяет существование файла.
     * @return boolean
     */
    public function getIsExists()
    {
        return is_file($this->_path->getValue());
    }

    /**
     * Возвращает правило для файла .htaccess.
     * @param Path|string $rootPath
     * @param Path|string $fromPath
     * @return null|string
     */
    public function getHtaccessRule($rootPath, $fromPath)
    {
        $rootPath = Path::from($rootPath);
        $toPath = $this->_path;
        $fromPath = Path::from($fromPath);

        if ($rootPath->getIsRelative() || $fromPath->getIsRelative())
            return null;

        return 'RewriteRule ^'.($fromPath->getRelativeFrom($rootPath)->getValue('/')).'$ '.($toPath->getRelativeFrom($rootPath)->getValue('/')).' [L,QSA]';
    }

    /**
     * Регистрирует файл в .htaccess.
     * @param Path|string $path
     * @param Path|string $rootPath
     * @param Path|string $fromPath
     * @return boolean
     */
    public function registerHtaccessRule($path, $rootPath, $fromPath)
    {
        $path = Path::from($path);

        if ($path->getIsRelative())
            return false;

        $rule = $this->getHtaccessRule($rootPath, $fromPath);

        if (empty($rule))
            return false;

        $content = FileHelper::getFileData($path->getValue());

        if ($this->isHtaccessRuleRegistered($path, $rootPath, $fromPath))
            return true;

        if (StringHelper::position('<IfModule mod_rewrite.c>', $content) === false)
            $content .= "\r\n".'<IfModule mod_rewrite.c>'."\r\n".'</IfModule>';

        $beginPosition = StringHelper::position('<IfModule mod_rewrite.c>', $content);
        $endPosition = StringHelper::position('</IfModule>', $content, $beginPosition);

        $content =
            StringHelper::cut($content, 0, $endPosition).'  '.
            $rule."\r\n".
            StringHelper::cut($content, $endPosition);

        return FileHelper::setFileData($path->getValue(), $content);
    }

    /**
     * Проверяет состояние регистрации файла в .htaccess.
     * @param Path|string $path
     * @param Path|string $rootPath
     * @param Path|string $fromPath
     * @return boolean
     */
    public function isHtaccessRuleRegistered($path, $rootPath, $fromPath)
    {
        $path = Path::from($path);

        if ($path->getIsRelative())
            return false;

        $rule = $this->getHtaccessRule($rootPath, $fromPath);

        if (empty($rule))
            return false;

        $content = FileHelper::getFileData($path->getValue());

        if (StringHelper::position($rule, $content) !== false)
            return true;

        return false;
    }

    /**
     * Удаляет регистрацию файла в .htaccess.
     * @param Path|string $path
     * @param Path|string $rootPath
     * @param Path|string $fromPath
     * @return boolean
     */
    public function unRegisterHtaccessRule($path, $rootPath, $fromPath)
    {
        $path = Path::from($path);

        if ($path->getIsRelative())
            return false;

        $rule = $this->getHtaccessRule($rootPath, $fromPath);

        if (empty($rule))
            return false;

        if (!$this->isHtaccessRuleRegistered($path, $rootPath, $fromPath))
            return true;

        $content = FileHelper::getFileData($path->getValue());
        $ruleLength = StringHelper::length($rule);
        $rulePosition = StringHelper::position($rule, $content);
        $beginContent = StringHelper::cut($content, 0, $rulePosition);
        $endContent = StringHelper::cut($content, $rulePosition + $ruleLength);

        while (StringHelper::endsWith($beginContent, ' '))
            $beginContent = StringHelper::cut($beginContent, 0, StringHelper::length($beginContent) - 1);

        if (StringHelper::startsWith($endContent, "\r\n"))
            $endContent = StringHelper::cut($endContent, 2);

        return FileHelper::setFileData($path->getValue(), $beginContent.$endContent);
    }
}