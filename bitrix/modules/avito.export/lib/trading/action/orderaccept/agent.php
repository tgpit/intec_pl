<?php
namespace Avito\Export\Trading\Action\OrderAccept;

use Avito\Export\Config;
use Avito\Export\Glossary;
use Avito\Export\Psr;
use Avito\Export\Push;
use Avito\Export\Trading;
use Avito\Export\Api;
use Avito\Export\Watcher;
use Avito\Export\Data;

class Agent extends Trading\Action\Reference\OrderAgent
{
	protected const ERROR_LIMIT = 5;
	protected const ERROR_DELAY = 600;

	public static function getDefaultParams() : array
	{
		return [
			'interval' => 600,
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
			if ($stopOrder === $order->id())
			{
				return false;
			}

			if ($orderOffset === $order->id())
			{
				$offsetFound = true;
				continue;
			}

			if (!$offsetFound) { continue; }

			static::checkStopLimit($trading->getId(), $order);

			if (!static::isUnnecessaryOrder($order))
			{
				static::callAction($trading, $order);
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
		return (
			$order->status() === Trading\Service\Status::STATUS_CLOSED
			|| (
				$order->status() === Trading\Service\Status::STATUS_CANCELED
				&& static::isOldOrder($order, 7)
			)
		);
	}

	protected static function callAction(Trading\Setup\Model $trading, Api\OrderManagement\Model\Order $order) : void
	{
		try
		{
			static::callProcedure($trading, $order, 'order/accept');
			static::callProcedure($trading, $order, 'order/status');
		}
		catch (\Throwable $exception)
		{
			static::logger($trading->getId())->error($exception, [
				'ENTITY_TYPE' => Glossary::ENTITY_ORDER,
				'ENTITY_ID' => $order->number()
			]);
		}
	}

	protected static function callProcedure(Trading\Setup\Model $trading, Api\OrderManagement\Model\Order $order, string $path) : void
	{
		$procedure = new Trading\Action\Procedure($trading, $path, [ 'order' => $order ]);
		$procedure->run();
	}

	protected static function stopLimit(int $exchangeId) : ?int
	{
		return Data\Number::cast(Config::getOption('trading_order_accept_last_' . $exchangeId));
	}

	protected static function checkStopLimit(int $exchangeId, Api\OrderManagement\Model\Order $order) : void
	{
		$option = Config::getOption('trading_order_accept_last_' . $exchangeId);

		if ($option === null || $order->id() > $option)
		{
			Config::setOption('trading_order_accept_last_' . $exchangeId, $order->id());
		}
	}
}