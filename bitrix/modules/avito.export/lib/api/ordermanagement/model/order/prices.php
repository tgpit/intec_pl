<?php
namespace Avito\Export\Api\OrderManagement\Model\Order;

use Avito\Export\Api;

class Prices extends Api\Response
{
	public function commission() : float
	{
		return (float)$this->getValue('commission');
	}

	public function discount() : float
	{
		return (float)$this->getValue('discount');
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