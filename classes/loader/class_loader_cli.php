<?php
/**
 * Системный класс загрузчика (CLI)
 */
class system_Loader extends system_Core
{
	/**
	 * Конструктор
	 */
	public function __construct()
	{
		if($GLOBALS['CORE']['loader'] === false)
		{
			// инициализация действий в консоли
		}
	}
}
