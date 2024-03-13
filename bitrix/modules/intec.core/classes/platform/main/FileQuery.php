<?php
namespace intec\core\platform\main;

use CFile;
use intec\core\base\Collection;
use intec\core\base\Query;

/**
 * Класс, представляющий запрос на выборку файлов по идентификаторам.
 * Class FileQuery
 * @package intec\core\platform\main
 * @author apocalypsisdimon@gmail.com
 */
class FileQuery extends Query
{
    /**
     * @var Collection
     */
    protected $_ids;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_ids = new Collection();
    }

    /**
     * Добавляет идентификатор в фильтр.
     * @param integer $id
     * @return static
     */
    public function add($id)
    {
        $this->_ids->addUnique($id);

        return $this;
    }

    /**
     * Добавляет несколько идентификаторов в фильтр.
     * @param array $ids
     * @return static
     */
    public function addRange($ids)
    {
        $this->_ids->addUniqueRange($ids);

        return $this;
    }

    /**
     * @inheritdoc
     * @return Files
     */
    public function execute()
    {
        $result = new Files();

        if (!$this->_ids->isEmpty()) {
            $files = CFile::GetList([], [
                '@ID' => implode(',', $this->_ids->asArray())
            ]);

            while ($file = $files->Fetch()) {
                $file = new File($file);
                $result->set($file->getId(), $file);
            }
        }

        return $result;
    }
}
