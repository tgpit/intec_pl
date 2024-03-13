<?php
namespace Avito\Export\Trading\State;

use Avito\Export\Agent;
use Avito\Export\Config;
use Avito\Export\DB;
use Avito\Export\Glossary;
use Avito\Export\Logger;
use Avito\Export\Exchange;
use Avito\Export\Trading;
use Bitrix\Main;

class Cleaner extends Agent\Base
{
	public static function run() : bool
	{
		try
		{
			static::clean([
				RepositoryTable::class,
				Trading\Entity\SaleCrm\Internals\WaitChatTable::class
			]);
		}
		catch (Main\SystemException $exception)
		{
			$setupId = static::setupId();

			if ($setupId === null) { return false; }

			static::logException($setupId, $exception);
		}

		return true;
	}

	protected static function clean(array $tables) : void
	{
		$expireDate = static::expireDate();

		foreach ($tables as $table)
		{
			$batch = new DB\Facade\BatchDelete($table);
			$batch->run([
				'filter' => [ '<TIMESTAMP_X' => $expireDate ],
			]);
		}
	}

	protected static function expireDate() : Main\Type\DateTime
	{
		$days = max(1, (int)Config::getOption('trading_expire_days', 60));
		$date = new Main\Type\DateTime();
		$date->add(sprintf('-P%sD', $days));

		return $date;
	}

	protected static function setupId() : ?int
	{
		$result = null;

		$query = Exchange\Setup\RepositoryTable::getList([
			'select' => [ 'ID' ],
			'limit' => 1,
		]);

		if ($row = $query->fetch())
		{
			$result = (int)$row['ID'];
		}

		return $result;
	}

	protected static function logException(int $setupId, \Throwable $exception) : void
	{
		$logger = new Logger\Logger(Glossary::SERVICE_TRADING, $setupId);
		$logger->error($exception);
	}
}

