<?php

namespace Avito\Export\Agent;

use Bitrix\Main;

abstract class Base
{
	public static function isRegistered(array $agentParams = null) : bool
	{
		$className = static::getClassName();

		$agentParams = !isset($agentParams)
			? static::getDefaultParams()
			: array_merge(static::getDefaultParams(), $agentParams);

		return Controller::isRegistered($className, $agentParams);
	}

	/**
	 * ��������� �����
	 *
	 * @param $agentParams array|null ��������� ������, �����:
	 *                     method => string # �������� ������ (�������������)
	 *                     arguments => array # ��������� ������ ������ (�������������)
	 *                     interval => integer, # �������� �������, � �������� (�������������)
	 *                     sort => integer, # ����������, ��-��������� � 100 (�������������)
	 *                     next_exec => string, # ���� � ������� Y-m-d H:i:s (�������������)
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function register(array $agentParams = null) : void
	{
		$className = static::getClassName();

		$agentParams = !isset($agentParams)
			? static::getDefaultParams()
			: array_merge(static::getDefaultParams(), $agentParams);

		Controller::register($className, $agentParams);
	}

	public static function getClassName() : string
	{
		return '\\' . static::class;
	}

	/**
	 * @return array �������� ������ ��� ���������� �� ��������� (����� run), �����:
	 *               method => string # �������� ������ (�������������)
	 *               arguments => array # ��������� ������ ������ (�������������)
	 *               interval => integer, # �������� �������, � �������� (�������������)
	 *               sort => integer, # ����������, ��-��������� � 100 (�������������)
	 *               next_exec => string, # ���� � ������� Y-m-d H:i:s (�������������)
	 * */

	public static function getDefaultParams() : array
	{
		return [];
	}

	public static function unregister(array $agentParams = null) : void
	{
		$className = static::getClassName();

		$agentParams = !isset($agentParams)
			? static::getDefaultParams()
			: array_merge(static::getDefaultParams(), $agentParams);

		Controller::unregister($className, $agentParams);
	}

	public static function callAgent(string $method, array $arguments = null) : ?string
	{
		$className = static::getClassName();
		$result = '';

		if (is_array($arguments))
		{
			$callResult = call_user_func_array(array($className, $method), $arguments);
		}
		else
		{
			$callResult = call_user_func(array($className, $method));
		}

		if ($callResult !== false)
		{
			if (is_array($callResult))
			{
				$arguments = $callResult;
			}

			$result = Controller::getAgentCall($className, $method, $arguments);
		}

		return $result;
	}

	/**
	 * @noinspection PhpMissingReturnTypeInspection
	 * @noinspection ReturnTypeCanBeDeclaredInspection
	 */
	public static function run()
	{
		return false;
	}
}
