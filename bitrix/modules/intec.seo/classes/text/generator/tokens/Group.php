<?php
namespace intec\seo\text\generator\tokens;

use intec\core\helpers\ArrayHelper;
use intec\seo\text\generator\Token;

/**
 * Класс, представляющий группу.
 * Class Group
 * @package intec\seo\text\generator\tokens
 * @author apocalypsisdimon@gmail.com
 */
class Group extends Token
{
    /**
     * @var GroupItems
     */
    protected $_items;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $this->_items = new GroupItems();

        parent::__construct($config);
    }

    /**
     * Возвращает коллекцию элементов.
     * @return GroupItems
     */
    public function getItems()
    {
        return $this->_items;
    }

    /**
     * Устанавливает новые элементы коллекции.
     * @param GroupItems|GroupItem[]|array $value
     * @return $this
     */
    public function setItems($value)
    {
        $this->_items->removeAll();
        $this->_items->setRange($value);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function transform($macros = [])
    {
        $count = $this->_items->count();

        if ($count === 0)
            return '';

        $items = $this->_items->asArray();
        $items = ArrayHelper::getValues($items);

        /** @var GroupItem $item */
        $item = $items[rand(0, $count - 1)];

        return $item->getTokens()->transform($macros);
    }
}