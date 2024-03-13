<?php
namespace intec\core\platform\main;

use CFile;
use intec\core\base\ArrayModel;
use intec\core\helpers\Type;

/**
 * Класс, представляющий файл платформы.
 * Class File
 * @package intec\core\platform\main
 * @author apocalypsisdimon@gmail.com
 */
class File extends ArrayModel
{
    /**
     * Возвращает идентификатор файла.
     * @return integer
     */
    public function getId()
    {
        return Type::toInteger($this->_fields['ID']);
    }

    /**
     * Возвращает наименование файла.
     * @return string
     */
    public function getName()
    {
        return $this->_fields['ORIGINAL_NAME'];
    }

    /**
     * Возвращает путь до файла.
     * @return string|null
     */
    public function getPath()
    {
        if (!$this->_fields->exists('SRC'))
            $this->_fields['SRC'] = CFile::GetFileSRC($this->asArray());

        return $this->_fields['SRC'];
    }

    /**
     * Возвращает путь до изображения, подстроенное под указанный размер.
     * @param integer $width Ширина.
     * @param integer $height Высота.
     * @param integer $type Тип операции изменения размера.
     * @return string|null
     */
    public function getResizedPicturePath($width, $height, $type = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $result = CFile::ResizeImageGet($this->asArray(), [
            'width' => $width,
            'height' => $height
        ], $type);

        if (empty($result))
            return null;

        return $result['src'];
    }

    /**
     * Возвращает размер файла.
     * @return integer
     */
    public function getSize()
    {
        return Type::toInteger($this->_fields['FILE_SIZE']);
    }
}
