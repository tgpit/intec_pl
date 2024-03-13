<?php
namespace Avito\Export\Api\OrderManagement\Model\Order\Item;

use Avito\Export\Api;

class Prices extends Api\Response
{
	public function commission() : float
	{
		return (float)$this->getValue('commission');
	}

	public function discountSum() : float
	{
		return (float)$this->getValue('discountSum');
	}

	public function price() : float
	{
		return (float)$this->requireValue('price');
	}

	public function total() : float
	{
		return (float)$this->requireValue('total');
	}
}