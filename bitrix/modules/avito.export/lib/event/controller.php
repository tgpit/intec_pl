<?php

namespace Avito\Export\Event;

use Avito\Export\Config;
use Avito\Export\Utils\Tools;
use Bitrix\Main;

class Controller
{
	/**
	 * ��������� ����������
	 *
	 * @param $className     string
	 * @param $handlerParams array
	 *
	 * @throws \Bitrix\Main\NotImplementedException
	 * @throws \Bitrix\Main\SystemException
	 */
	public static function register(string $className, array $handlerParams):void
	{
		$handlerDescription = static::getHandlerDescription($className, $handlerParams);

		static::saveHandler($handlerDescription);
	}

	/**
	 * ���������� �������� ����������� ��� �����������, ��������� ������������� ������
	 *
	 * @param $className     class-string<Base>
	 * @param $handlerParams array|null ��������� �����������
	 *
	 * @return array
	 * @throws \Bitrix\Main\NotImplementedException
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected static function getHandlerDescription(string $className, ?array $handlerParams):array
	{
		if (empty($handlerParams['module']) || empty($handlerParams['event']))
		{
			throw new Main\ArgumentException('Require module and event param in ' . $className);
		}

		$method = $handlerParams['method'] ?? $handlerParams['event'];

		if (!method_exists($className, $method))
		{
			throw new Main\NotImplementedException('Method ' . $method . ' not defined in ' . $className
				. ' and cannot be registered as event handler');
		}

		return array(
			'module' => $handlerParams['module'],
			'event' => $handlerParams['event'],
			'class' => $className::getClassName(),
			'method' => $method,
			'sort' => isset($handlerParams['sort']) ? (int)$handlerParams['sort'] : 100,
			'arguments' => $handlerParams['arguments'] ?? '',
		);
	}

	/**
	 * ������������ ���������� � ���� ������
	 *
	 * @param $handlerDescription array ����������
	 */
	protected static function saveHandler(array $handlerDescription):void
	{
		$eventManager = Main\EventManager::getInstance();

		$eventManager->registerEventHandler($handlerDescription['module'], $handlerDescription['event'],
			Config::getModuleName(), $handlerDescription['class'], $handlerDescription['method'],
			$handlerDescription['sort'], '', $handlerDescription['arguments']);
	}

	/**
	 * ������� ����������
	 *
	 * @param $className
	 * @param $handlerParams
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\Db\SqlQueryException
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public static function unregister($className, $handlerParams):void
	{
		$handlerDescription = static::getHandlerDescription($className, $handlerParams);
		$registeredList = static::getRegisteredHandlers($className);
		$handlerKey = static::getHandlerKey($handlerDescription, true);

		if (isset($registeredList[$handlerKey]))
		{
			static::deleteHandler($registeredList[$handlerKey]);
		}
	}

	/**
	 * �������� ������ ����� ������������������ ������������
	 *
	 * @param $baseClassName   string �������� ������, ����������� �������� ���������� ��������
	 * @param $isBaseNamespace bool ������ �������� �� �������� �������
	 *
	 * @return array ������ ������������������ ������������
	 * @throws \Bitrix\Main\Db\SqlQueryException
	 */
	protected static function getRegisteredHandlers(string $baseClassName, bool $isBaseNamespace = false):array
	{
		$registeredList = array();
		$namespaceLower = str_replace('\\', '\\\\', strtolower(Config::getNamespace()));
		$connection = Main\Application::getConnection();
		$sqlHelper = $connection->getSqlHelper();
		$query = $connection->query('SELECT * FROM b_module_to_module WHERE TO_CLASS like "'
			. $sqlHelper->forSql($namespaceLower) . '%"');

		while ($handlerRow = $query->fetch())
		{
			$handlerClassName = $handlerRow['TO_CLASS'];

			if ($isBaseNamespace
				|| $handlerClassName === $baseClassName
				|| !class_exists($handlerClassName)
				|| is_subclass_of($handlerClassName, $baseClassName))
			{
				$handlerKey = static::getHandlerKey($handlerRow);
				$registeredList[$handlerKey] = $handlerRow;
			}
		}

		return $registeredList;
	}

	/**
	 * ���� ������� ��� ����������� �������
	 *
	 * @param $handlerData   array
	 * @param $byDescription boolean ������������ ���� �� �������� ��� ������������������� �����������
	 *
	 * @return string
	 */
	protected static function getHandlerKey(array $handlerData, bool $byDescription = false):string
	{
		$signKeys = array(
			'module' => 'FROM_MODULE_ID',
			'event' => 'MESSAGE_ID',
			'class' => 'TO_CLASS',
			'method' => 'TO_METHOD',
			'arguments' => 'TO_METHOD_ARG',
		);
		$values = array();

		foreach ($signKeys as $descriptionKey => $rowKey)
		{
			$key = $byDescription ? $descriptionKey : $rowKey;
			$values[] = is_array($handlerData[$key]) && !empty($handlerData[$key]) ? serialize($handlerData[$key]) :
				$handlerData[$key];
		}

		return strtolower(implode('|', $values));
	}

	/**
	 * ������� ���������� �� ���� ������
	 *
	 * @param $handlerRow array ����� ������������������ ����������
	 */
	protected static function deleteHandler(array $handlerRow):void
	{
		$eventManager = Main\EventManager::getInstance();

		$handlerArgs = $handlerRow['TO_METHOD_ARG'];

		if (is_string($handlerArgs))
		{
			$handlerArgsUnSerialize = unserialize($handlerArgs, ['allowed_classes' => false]);

			if (is_array($handlerArgsUnSerialize) && !empty($handlerArgsUnSerialize))
			{
				$handlerArgs = $handlerArgsUnSerialize;
			}
		}

		$eventManager->unregisterEventHandler($handlerRow['FROM_MODULE_ID'], $handlerRow['MESSAGE_ID'],
			Config::getModuleName(), $handlerRow['TO_CLASS'], $handlerRow['TO_METHOD'], '', $handlerArgs);
	}

	/**
	 * ��������� �������� ���������� ������������ �������
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function updateRegular() : void
	{
		$baseClassName = Regular::getClassName();

		$classList = Tools::getClassList($baseClassName);
		$handlerList = static::getClassHandlers($classList);
		$registeredList = static::getRegisteredHandlers($baseClassName);

		static::saveHandlers($handlerList);
		static::deleteHandlers($handlerList, $registeredList);
	}

	/**
	 * ������� ������ ������� � ������� ������ ��� ������
	 *
	 * @param $classList array ������ �������
	 *
	 * @return array ������ ������������ ��� �����������
	 * @throws \Bitrix\Main\NotImplementedException
	 * @throws \Bitrix\Main\ArgumentException
	 */
	protected static function getClassHandlers(array $classList):array
	{
		$result = array();

		/** @var Regular $className */
		foreach ($classList as $className)
		{
			$normalizedClassName = $className::getClassName();
			$handlers = $className::getHandlers();

			foreach ($handlers as $handler)
			{
				$handlerDescription = static::getHandlerDescription($normalizedClassName, $handler);
				$handlerKey = static::getHandlerKey($handlerDescription, true);

				$result[$handlerKey] = $handlerDescription;
			}
		}

		return $result;
	}

	/**
	 * ������������ ��� ����������� � ���� ������
	 *
	 * @param $handlerList array ������ ������������ ��� �����������
	 */
	protected static function saveHandlers(array $handlerList):void
	{
		foreach ($handlerList as $handlerDescription)
		{
			static::saveHandler($handlerDescription);
		}
	}

	/**
	 * ������� �������������� ����������� �� ���� ������
	 *
	 * @param $handlerList    array ������ ������������ ��� �����������
	 * @param $registeredList array ������ ����� ������������������ ������������
	 */
	protected static function deleteHandlers(array $handlerList, array $registeredList):void
	{
		foreach ($registeredList as $handlerKey => $handlerRow)
		{
			if (!isset($handlerList[$handlerKey]))
			{
				static::deleteHandler($handlerRow);
			}
		}
	}

	/**
	 * ������� ��� �������
	 */
	public static function deleteAll(string $baseClassName = null) : void
	{
		if ($baseClassName === null)
		{
			$isBaseNamespace = true;
			$baseClassName = Config::getNamespace();
		}
		else
		{
			$isBaseNamespace = false;
		}

		$baseClassName = '\\' . ltrim($baseClassName, '\\');
		$registeredList = static::getRegisteredHandlers($baseClassName, $isBaseNamespace);

		static::deleteHandlers([], $registeredList);
	}
}
