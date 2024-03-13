<?php
namespace Avito\Export\Api\Messenger\V2\Accounts\User\Chats;

use Avito\Export\Api;

class Response extends Api\Response
{
	public function chats() : Api\Messenger\V2\Model\Chats
	{
		return $this->requireCollection('chats', Api\Messenger\V2\Model\Chats::class);
	}
}