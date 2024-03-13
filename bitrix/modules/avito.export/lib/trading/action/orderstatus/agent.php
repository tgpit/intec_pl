<?php
namespace Avito\Export\Trading\Action\OrderStatus;

use Avito\Export\Config;
use Avito\Export\Data;
use Avito\Export\Glossary;
use Avito\Export\Psr;
use Avito\Export\Push;
use Avito\Export\Api;
use Avito\Export\Watcher;
use Avito\Export\Trading;

class Agent extends Trading\Action\Reference\OrderAgent
{
	protected const ERROR_LIMIT = 5;
	protected const ERROR_DELAY = 600;

	public static function getDefaultParams() : array
	{
		return [
			'interval' => 3600,
		];
	}

	public static function start(int $exchangeId) : void
	{
		static::register([
			'method' => 'process',
			'interval' => 5,
			'arguments' => [
				$exchangeId,
				1,
				null,
				static::stopLimit($exchangeId)
			],
		]);
	}

	protected static function processOrders(Trading\Setup\Model $trading, Api\OrderManagement\Model\Orders $orders, int $orderOffset = null, int $stopOrder = null)
	{
		$limitResource = new Watcher\Engine\LimitResource();
		$offsetFound = ($orderOffset === null);

		/** @var Api\OrderManagement\Model\Order $order */
		foreach ($orders as $order)
		{
			if ($orderOffset === $order->id())
			{
				$offsetFound = true;
				continue;
			}

			if (!$offsetFound) { continue; }

			if (!static::isUnnecessaryOrder($order))
			{
				static::touchStopLimit($trading->getId(), $order);
				static::callAction($trading, $order);
			}

			if (static::isStopOrder($stopOrder, $order->id()))
			{
				return false;
			}

			/** @noinspection DisconnectedForeachInstructionInspection */
			$limitResource->tick();

			if ($limitResource->isExpired())
			{
				return $order->id();
			}
		}

		return true;
	}

	protected static function isUnnecessaryOrder(Api\OrderManagement\Model\Order $order) : bool
	{
		return (static::isFinalStatus($order) && static::isOldOrder($order));
	}

	protected static function stopLimit(int $exchangeId) : ?int
	{
		return Data\Number::cast(Config::getOption('trading_order_status_last_' . $exchangeId));
	}

	protected static function touchStopLimit(int $exchangeId, Api\OrderManagement\Model\Order $order) : void
	{
		if (static::isFinalStatus($order)) { return; }

		Config::setOption('trading_order_status_last_' . $exchangeId, $order->id());
	}

	protected static function callAction(Trading\Setup\Model $trading, Api\OrderManagement\Model\Order $order) : void
	{
		try
		{
			$action = new Action($trading, new Command($order));
			$action->process();
		}
		catch (\Throwable $exception)
		{
			static::logger($trading->getId())->error($exception, [
				'ENTITY_TYPE' => Glossary::ENTITY_ORDER,
				'ENTITY_ID' => $order->number(),
			]);
		}
	}

	protected static function isFinalStatus(Api\OrderManagement\Model\Order $order) : bool
	{
		return in_array($order->status(), [
			Trading\Service\Status::STATUS_CLOSED,
			Trading\Service\Status::STATUS_CANCELED
		], true);
	}

	protected static function isStopOrder(?int $stopOrderId, int $orderId) : bool
	{
		return ($stopOrderId === $orderId);
	}
}