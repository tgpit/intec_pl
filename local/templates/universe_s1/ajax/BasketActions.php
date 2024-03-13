<?php
namespace intec\template\ajax;

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\BasketItem;
use Bitrix\Sale\Fuser;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Context;
use intec\Core;
use intec\core\handling\Actions;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use CCatalogMeasureRatio;
use CStartShopBasket;
use CStartShopPrice;

class BasketActions extends Actions
{
    /**
     * @var Basket
     */
    protected $basket = null;
    /**
     * @var boolean
     */
    protected $base = true;

    /**
     * @inheritdoc
     */
    public function beforeAction ($action)
    {
        if (parent::beforeAction($action)) {
            if (!Loader::includeModule('iblock'))
                return false;

            if (
                !Loader::includeModule('catalog') ||
                !Loader::includeModule('sale')
            ) {
                if (!Loader::includeModule('intec.startshop')) {
                    return false;
                } else {
                    $this->base = false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Возвращает данные запроса.
     * @return array|mixed
     */
    protected function getData()
    {
        if (!Type::isArray($this->data))
            $this->data = [];

        return $this->data;
    }

    /**
     * Возвращает экземпляр корзины текущего пользователя.
     * @return Basket
     */
    protected function getBasket()
    {
        if ($this->basket === null)
            if ($this->base)
                $this->basket = Basket::loadItemsForFUser(
                    Fuser::getId(),
                    Context::getCurrent()->getSite()
                );

        return $this->basket;
    }

    /**
     * Вовзращает элемент корзины.
     * @param string $module
     * @param integer $id
     * @param Basket|null $basket
     * @return BasketItem|null
     */
    protected function getBasketItem($module, $id, $basket = null)
    {
        if ($basket === null)
            $basket = $this->getBasket();

        if (empty($basket))
            return null;

        /** @var BasketItem $item */
        foreach ($basket as $item)
            if ($item->getField('MODULE') == $module && $item->getProductId() == $id)
                return $item;

        return null;
    }

    /**
     * Возвращает структуру элементов.
     * @param $id
     * @return array
     */
    protected function getElements($id)
    {
        $sections = [];
        $elements = [];
        $result = \CIBlockElement::GetList([], [
            'ID' => $id
        ]);

        while ($element = $result->Fetch()) {
            $element = [
                'id' => Type::toInteger($element['ID']),
                'name' => $element['NAME'],
                'isDelay' => false,
                'section' => $element['IBLOCK_SECTION_ID'],
                'price' => null,
                'quantity' => 1
            ];

            if (!empty($element['section']) && !ArrayHelper::isIn($element['section'], $sections))
                $sections[] = $element['section'];

            $elements[] = $element;
        }

        if (!empty($sections)) {
            $result = \CIBlockSection::GetList([], [
                'ID' => $sections
            ]);

            $sections = [];

            while ($section = $result->Fetch()) {
                $section = [
                    'id' => Type::toInteger($section['ID']),
                    'name' => $section['NAME']
                ];

                $sections[$section['id']] = $section;
            }

            unset($section, $sectionsResult);

            foreach ($elements as &$element) {
                if (!empty($element['section'])) {
                    $section = isset($sections[$element['section']]) ? $sections[$element['section']] : null;

                    if (!empty($section)) {
                        $element['section'] = $section;
                    } else {
                        $element['section'] = null;
                    }
                } else {
                    $element['section'] = null;
                }
            }

            unset($element);
        }

        unset($sections, $result);

        if ($this->base) {
            foreach ($elements as &$element) {
                /** @var BasketItem $item */
                $item = $this->getBasketItem('catalog', $element['id']);

                if (empty($item)) {
                    unset($element['price'], $element['quantity']);
                    continue;
                }

                $element['isDelay'] = $item->getField('DELAY') === 'Y';
                $element['price'] = $item->getPrice();
                $element['quantity'] = $item->getField('QUANTITY');
                $element['quantity'] = Type::toInteger($element['quantity']);
            }

            unset($element);
        } else {
            $result = CStartShopBasket::GetList();
            $items = [];

            while ($item = $result->Fetch())
                $items[$item['ID']] = $item;

            unset($result);

            foreach ($elements as &$element) {
                if (!isset($items[$element['id']])) {
                    unset($element['price'], $element['quantity']);
                    continue;
                }

                $item = $items[$element['id']];
                $element['price'] = !empty($item['STARTSHOP']['BASKET']['PRICE']) ? $item['STARTSHOP']['BASKET']['PRICE']['VALUE'] : null;
                $element['quantity'] = $item['STARTSHOP']['BASKET']['QUANTITY'];
            }

            unset($element, $item, $items);
        }

        return $elements;
    }

    /**
     * Возвращает структуру элемента.
     * @param $id
     * @return array|mixed
     */
    protected function getElement($id)
    {
        $result = $this->getElements($id);

        if (count($result) > 0) {
            $result = $result[0];
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Создание товара в корзине.
     * @param array $data Данные элемента инфоблока.
     * @post int $id Идентификатор элемента инфоблока.
     * @post int $quantity Количество. Необязательно.
     * @post array $properties Свойства, добавляемые в корзину. Необязательны.
     * @post string $currency Код валюты. Необязателен.
     * @post string $delay Добавить в отложенные. (Y/N).
     * @return bool
     */
    public function createItem($data = [])
    {
        $id = ArrayHelper::getValue($data, 'id');
        $id = Type::toInteger($id);
        $price = ArrayHelper::getValue($data, 'price');
        $quantity = ArrayHelper::getValue($data, 'quantity');

        if ($this->base) {
            $quantity = Type::toFloat($quantity);
            $ratio = CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $id]);
            $ratio = $ratio->Fetch();
            $ratio = !empty($ratio) ? Type::toFloat($ratio['RATIO']) : 1;
            $quantity = $quantity < $ratio ? $ratio : $quantity;
            $properties = ArrayHelper::getValue($data, 'properties');
            $currency = ArrayHelper::getValue($data, 'currency');
            $delay = ArrayHelper::getValue($data, 'delay');
            $delay = $delay == 'Y' ? 'Y' : 'N';

            if (empty($id))
                return false;

            if (empty($currency))
                $currency = CurrencyManager::getBaseCurrency();

            $arElement = \CIBlockElement::GetByID($id)->GetNext();

            if (empty($arElement))
                return false;

            $arProduct = \CCatalogSku::GetProductInfo($id);

            $basket = $this->getBasket();

            if ($item = $this->getBasketItem('catalog', $id)) {
                $item->setFields(['DELAY' => $delay]);
            } else {
                /** @var BasketItem $item */
                $item = $basket->createItem('catalog', $id);
                $item->setFields([
                    'PRODUCT_ID' => $id,
                    'QUANTITY' => $quantity,
                    'CURRENCY' => $currency,
                    'DELAY' => $delay,
                    'LID' => Context::getCurrent()->getSite(),
                    'PRODUCT_PROVIDER_CLASS' => class_exists('\Bitrix\Catalog\Product\CatalogProvider') ?
                        '\Bitrix\Catalog\Product\CatalogProvider' :
                        'CCatalogProductProvider',
                    'CATALOG_XML_ID' => $arElement['IBLOCK_EXTERNAL_ID'],
                    'PRODUCT_XML_ID' => $arElement['EXTERNAL_ID']
                ]);
            }

            $collection = $item->getPropertyCollection();

            if (!empty($arProduct) && Type::isArray($properties)) {
                $properties = \CIBlockPriceTools::GetOfferProperties(
                    $id,
                    $arElement['IBLOCK_ID'],
                    $properties
                );

                if (!empty($properties))
                    $collection->setProperty($properties);
            }

            $required = [];

            if (!empty($arElement['IBLOCK_EXTERNAL_ID']))
                $required[] = [
                    'NAME' => 'Catalog XML_ID',
                    'CODE' => 'CATALOG.XML_ID',
                    'VALUE' => $arElement['IBLOCK_EXTERNAL_ID'],
                    'SORT' => 100
                ];

            if (!empty($arElement['EXTERNAL_ID']))
                $required[] = [
                    'NAME' => 'Product XML_ID',
                    'CODE' => 'PRODUCT.XML_ID',
                    'VALUE' => $arElement['EXTERNAL_ID'],
                    'SORT' => 100
                ];

            if (!empty($required))
                $collection->setProperty($required);

            $basket->save();
        } else {
            $quantity = Type::toFloat($quantity);
            $quantity = $quantity < 0 ? 0 : $quantity;

            if (empty($id))
                return false;

            if (CStartShopBasket::InBasket($id))
                return true;

            if (!empty($price)) {
                $price = CStartShopPrice::GetByID($price)->Fetch();
                $price = !empty($price) ? $price['CODE'] : null;
            } else {
                $price = false;
            }

            if (CStartShopBasket::Add($id, $quantity, $price) === false)
                return false;
        }

        return $id;
    }

    /**
     * Множественное добавление товаров в корзину
     * @return array
     */
    public function actionAddMultiple()
    {
        $data = $this->getData();
        $elements = [];

        if (!empty($data))
            foreach ($data as $item) {
                $result = $this->createItem($item);

                if ($result !== false)
                    $elements[] = $result;
            }

        if (!empty($elements))
            $elements = $this->getElements($elements);

        return $elements;
    }

    /**
     * Добавление товара в корзину
     * @return bool
     */
    public function actionAdd()
    {
        $data = $this->getData();
        $element = $this->createItem($data);

        if ($element !== false) {
            $element = $this->getElement($element);
        } else {
            $element = null;
        }

        return $element;
    }

    /**
     * Изменение количества товара в корзине.
     * @post int $id Идентификатор элемента инфоблока.
     * @post int $quantity Количество. Необязательно.
     * @return bool
     */
    public function actionSetQuantity()
    {
        $data = $this->getData();
        $id = ArrayHelper::getValue($data, 'id');
        $id = Type::toInteger($id);

        if ($this->base) {
            $quantity = ArrayHelper::getValue($data, 'quantity');
            $quantity = Type::toFloat($quantity);
            $ratio = CCatalogMeasureRatio::getList([], ['PRODUCT_ID' => $id]);
            $ratio = $ratio->Fetch();
            $ratio = !empty($ratio) ? Type::toFloat($ratio['RATIO']) : 1;
            $quantity = $quantity < $ratio ? $ratio : $quantity;

            $basket = $this->getBasket();

            if ($item = $this->getBasketItem('catalog', $id)) {
                $item->setFields(['QUANTITY' => $quantity]);
                $basket->save();
            }
        } else {
            $quantity = ArrayHelper::getValue($data, 'quantity');
            $quantity = Type::toFloat($quantity);
            $quantity = $quantity < 0 ? 0 : $quantity;

            if (CStartShopBasket::InBasket($id)) {
                CStartShopBasket::SetQuantity($id, $quantity);
            } else {
                return null;
            }
        }

        return $this->getElement($id);
    }

    /**
     * Возвращает список товаров в корзине.
     * @return array
     */
    public function actionGetItems()
    {
        $result = null;
        $id = [];

        if ($this->base) {
            $basket = $this->getBasket();

            foreach ($basket as $item)
                if ($item->getField('MODULE') == 'catalog')
                    $id[] = $item->getProductId();


        } else {
            $result = CStartShopBasket::GetList();

            while ($item = $result->Fetch())
                $id[] = $item['ID'];
        }

        if (!empty($id)) {
            $result = $this->getElements($id);
        } else {
            $result = [];
        }

        return $result;
    }

    /**
     * Удаление товара из корзины.
     * @post int $id Идентификатор элемента инфоблока.
     * @return bool
     */
    public function actionRemove()
    {
        $data = $this->getData();
        $id = ArrayHelper::getValue($data, 'id');
        $id = Type::toInteger($id);
        $result = null;

        if (empty($id))
            return false;

        if ($this->base) {
            $basket = $this->getBasket();

            if ($item = $this->getBasketItem('catalog', $id)) {
                $result = $this->getElement($id);

                $item->delete();
                $basket->save();
            }
        } else {
            if (CStartShopBasket::InBasket($id)) {
                $result = $this->getElement($id);

                CStartShopBasket::Delete($id);
            }
        }

        return $result;
    }

    /**
     * Очистка корзины.
     * @post string $basket Очищать ли корзину. (Y/N).
     * @post string $delay Очищать ли отложенные. (Y/N).
     * @return bool
     */
    public function actionClear()
    {
        $result = [];
        $data = $this->getData();
        $id = [];

        if ($this->base) {
            $basket = ArrayHelper::getValue($data, 'basket');
            $basket = $basket == 'Y';
            $delay = ArrayHelper::getValue($data, 'delay');
            $delay = $delay == 'Y';

            if (!$basket && !$delay) {
                $basket = true;
                $delay = true;
            }

            /** @var BasketItem[] $items */
            $items = $this->getBasket();

            foreach ($items as $item) {
                if (!$item->isDelay() && $basket)
                    $id[] = $item->getField('PRODUCT_ID');

                if ($item->isDelay() && $delay)
                    $id[] = $item->getField('PRODUCT_ID');
            }

            if (!empty($id))
                $result = $this->getElements($id);

            foreach ($items as $item) {
                if (!$item->isDelay() && $basket)
                    $item->delete();

                if ($item->isDelay() && $delay)
                    $item->delete();
            }

            $items->save();
        } else {
            $items = CStartShopBasket::GetList();

            while ($item = $items->Fetch())
                $id[] = $item['ID'];

            if (!empty($id))
                $result = $this->getElements($id);

            CStartShopBasket::Clear();
        }

        return $result;
    }
}