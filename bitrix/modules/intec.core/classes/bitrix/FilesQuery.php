<?php

namespace intec\core\bitrix;

use CFile;
use intec\core\base\Collection;
use intec\core\helpers\Type;

/**
 * Class FilesQuery
 * @package intec\core\bitrix
 * @deprecated
 */
class FilesQuery extends Query
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Добавляет идентификатор файла в запрос.
     * @param integer $id Идентификатор файла.
     * @return $this
     */
    public function add($id)
    {
        if (Type::isNumeric($id)) {
            $id = Type::toInteger($id);
            $this->collection->addUnique($id);
        }

        return $this;
    }

    /**
     * Добавляет идентификаторы файлов в запрос.
     * @param array|Collection $ids
     * @return $this
     */
    public function addRange($ids)
    {
        if (Type::isArrayable($ids))
            foreach ($ids as $id)
                $this->add($id);

        return $this;
    }

    /**
     * Запускает запрос и возвращает результат в виде списка файлов.
     * @return Files
     */
    public function execute()
    {
        $result = Files::from();

        if (!$this->collection->isEmpty()) {
            $result = Files::fromDBResult(CFile::GetList([], [
                '@ID' => $this->collection->asArray()
            ]), false, function ($file) {
                $file['SRC'] = CFile::GetFileSRC($file);

                return [
                    'key' => $file['ID'],
                    'value' => $file
                ];
            });
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->collection = new Collection();
    }
}
