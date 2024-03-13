<?php
namespace Avito\Export\Api\Messenger\V2\Model\Chat\Context;

use Avito\Export\Api;

class Value extends Api\Response
{
	public function id() : int
	{
		return $this->requireValue('id');
	}

	public function priceString() : string
	{
		return $this->requireValue('price_string');
	}

	public function statusId() : int
	{
		return $this->requireValue('status_id');
	}

	public function title() : string
	{
		return $this->requireValue('title');
	}

	public function url() : string
	{
		return $this->requireValue('url');
	}

	public function userId() : int
	{
		return $this->requireValue('user_id');
	}

	public function images() : Value\Images
	{
		return $this->requireModel('images', Value\Images::class);
	}
}