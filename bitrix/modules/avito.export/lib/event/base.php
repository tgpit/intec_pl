<?php

namespace Avito\Export\Event;

use Bitrix\Main;

abstract class Base
{

	/**
	 * ��������� �������
	 *
	 * @param $handlerParams array|null ��������� �����������, �����:
	 *                       module => string # �������� ������
	 *                       event => string, # �������� �������
	 *                       method => string, # �������� ������ (�������������)
	 *                       sort => integer, # ���������� (�������������)
	 *                       arguments => array # ��������� (�������������)
	 *
	 * @throws Main\NotImplementedException
	 * @throws Main\SystemException
	 * */
	public static function register(array $handlerParams = null):void
	{
		$className = static::getClassName();

		$handlerParams = !isset($handlerParams) ? static::getDefaultParams() :
			array_merge(static::getDefaultParams(), $handlerParams);

		Controller::register($className, $handlerParams);
	}

	public static function getClassName():string
	{
		return '\\' . static::class;
	}

	/**
	 * @return array �������� ����������� ��� ���������� �� ���������, �����:
	 *               module => string # �������� ������
	 *               event => string, # �������� �������
	 *               method => string, # �������� ������ (�������������)
	 *               sort => integer, # ���������� (�������������)
	 *               arguments => array # ��������� (�������������)
	 * */

	public static function getDefaultParams():array
	{
		return array();
	}

	/**
	 * @param null $handlerParams
	 *
	 * @throws \Bitrix\Main\ArgumentException
	 * @throws \Bitrix\Main\Db\SqlQueryException
	 * @throws \Bitrix\Main\NotImplementedException
	 */
	public static function unregister($handlerParams = null):void
	{
		$className = static::getClassName();

		$handlerParams = !isset($handlerParams) ? static::getDefaultParams() :
			array_merge(static::getDefaultParams(), $handlerParams);

		Controller::unregister($className, $handlerParams);
	}
}
