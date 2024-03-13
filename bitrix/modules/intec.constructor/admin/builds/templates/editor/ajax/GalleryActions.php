<?php

namespace intec\constructor\builds\templates\editor\ajax;

use intec\constructor\models\Build;
use intec\constructor\models\build\Gallery;
use intec\constructor\models\build\gallery\File;
use intec\Core;
use intec\core\handling\Action;
use intec\core\helpers\Encoding;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;
use intec\core\web\UploadedFile;

class GalleryActions extends Actions
{
    /**
     * Текущий билд конструктора.
     * @var Build
     */
    public $build;

    /**
     * Галерея.
     * @var Gallery
     */
    public $gallery;

    /**
     * Получение текущего билда конструктора и галереи.
     * @param Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            global $build;

            $this->build = $build;
            $this->gallery = $build->getGallery();

            return true;
        }

        return false;
    }

    /**
     * Получение информации о файле.
     * @param File $file
     * @return array|null
     */
    public function getFileStructure($file)
    {
        if (!$file instanceof File)
            return null;

        return [
            'name' => $file->getName(),
            'path' => Url::encodeParts(
                $file->getPath(Gallery::DIRECTORY_RELATIVE_SITE, '/'),
                '/',
                true
            ),
            'value' => '#TEMPLATE#/'.Url::encodeParts(
                $file->getPath(Gallery::DIRECTORY_RELATIVE_BUILD, '/'),
                '/',
                true
            )
        ];
    }

    public function actionUploadFile()
    {
        $file = UploadedFile::getInstanceByName('file');

        if (!empty($file)) {
            $file->name = StringHelper::convert(
                $file->name,
                null,
                Encoding::UTF8
            );

            $file = $this->gallery->addFile($file);

            if (!empty($file))
                return $this->successResponse($this->getFileStructure($file));
        }

        return $this->errorResponse();
    }

    public function actionUploadFileByLink()
    {
        $link = $this->request->post('link');

        if (!empty($link) || Type::isNumeric($link)) {
            $url = new Url($link);

            if ($url->getScheme() !== null) {
                $file = $this->gallery->addFile($url->build());

                if (!empty($file))
                    return $this->successResponse($this->getFileStructure($file));
            }
        }

        return $this->errorResponse();
    }

    /** Удаление файла.
     * @return array
     */
    public function actionDeleteFile()
    {
        $file = Core::$app->request->post('name');
        $file = StringHelper::convert(
            $file,
            null,
            Encoding::UTF8
        );

        $file = new File($this->gallery, $file);

        if ($file->delete())
            return $this->successResponse();

        return $this->errorResponse();
    }

    /**
     * Список файлов.
     * @return array
     */
    public function actionGetItems()
    {
        $files = $this->gallery->getFiles();
        $data = [];

        foreach ($files as $file) {
            $data[] = $this->getFileStructure($file);
        }

        return $this->successResponse($data);
    }
}