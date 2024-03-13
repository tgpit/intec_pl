<?php
namespace Avito\Export\Structure;

interface CategoryLevel
{
	public const CATEGORY = 'Category';
	public const GOODS_TYPE = 'GoodsType';
	public const PRODUCT_TYPE = 'ProductType';
	public const PRODUCTS_TYPE = 'ProductsType';
	public const GOODS_SUB_TYPE = 'GoodsSubType';

	public function categoryLevel() : ?string;
}