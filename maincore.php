<?php
/**
 * Регистрация метода авто-загрузчика классов
 */
spl_autoload_register('autoload');


/**
 * Регистрация метода обработчика ошибок
 */
set_error_handler('error_handler');


/**
 * Регистрация метода обработчика исключений
 */
set_exception_handler('exception_handler');


/**
 * Указание внутренней кодировки
 */
mb_internal_encoding("UTF-8");


/**
 * Указание временной зоны
 */
date_default_timezone_set('Europe/Moscow');


/**
 * Данные соединений с базами данных
 */
$GLOBALS['DB']['link']							= false;		// соединение с базой


/**
 * Основное ядро
 */
$GLOBALS['CORE']['loader']						= false;		// флаг загрузки ядра


/**
 * Данные запроса
 */
$GLOBALS['REQUEST']['data']						= false;		// массив отфильтрованных входящих данных GET и POST
$GLOBALS['REQUEST']['handler']					= false;		// запрошенный Handler (ajax)
$GLOBALS['REQUEST']['module']					= 'index';		// запрошенный модуль
$GLOBALS['REQUEST']['action']					= 'index';		// запрошенное действие модуля
$GLOBALS['REQUEST']['route_params']				= false;		// строка запроса после ?
$GLOBALS['REQUEST']['route_string']				= false;		// строка запроса до ?
$GLOBALS['REQUEST']['route_parts']				= false;		// массив частей строки запроса


/**
 * Данные шаблонизатора
 */
$GLOBALS['VIEW']['data']						= [];


/**
 * Подключение конфига сервера
 */
require_once 'mainconfig.php';


/**
 * Корневая папка
 */
define('ROOT',									__DIR__.'/');


/**
 * Путь к папке логов
 */
define('DIR_SYSTEM_LOG_PATH',					ROOT.'log/');


/**
 * Системная папка классов
 */
define('DIR_SYSTEM_CLASSES',					ROOT.'classes/');


/**
 * Системная папка шаблонов
 */
define('DIR_SYSTEM_TEMPLATES',					ROOT.'templates/');


/**
 * Основная база данных
 */
define('DB_DATA',								'swa_luchkinpro_demo_data');
define('TBL_DATA_TRANSACTIONS',					DB_DATA.'.'.'data_transactions');
define('TBL_DATA_TRANSACTIONS_ACCOUNTS',		DB_DATA.'.'.'data_transactions_accounts');
define('TBL_DATA_CURRENCIES_RATES',				DB_DATA.'.'.'data_currencies_rates');


/**
 * Системная база данных
 */
define('DB_SYSTEM',								'swa_luchkinpro_demo_system');
define('TBL_SYSTEM_CURRENCIES',					DB_SYSTEM.'.'.'system_currencies');


/**
 * Обработка и вывод ошибок
 */
function error_handler($error_number, $error_string, $error_file, $error_line)
{
	$error_separator_begin	= date("Y.m.d H:i:s").' ========================================='.PHP_EOL;
	$error_separator_end	= '=============================================================='.PHP_EOL.PHP_EOL;

	$string_display			= '<h3>Something wrong ['.$error_number.']</h3><br/>';
	$string_display			.= '<b>Error:</b> '.$error_string.'<br/>';
	$string_display			.= '<b>File:</b> '.$error_file.'<br/>';
	$string_display			.= '<b>Line:</b> '.$error_line.'<br/>';

	if(DEBUG_ERRORS === true)
	{
		echo $string_display;
	}

	$string_log	= $error_separator_begin.$error_string.PHP_EOL.$error_file.' '.$error_line.PHP_EOL.$error_separator_end;

	@file_put_contents(DIR_SYSTEM_LOG_PATH.LOG_FILE_SYSTEM, $string_log, FILE_APPEND);
}


/**
 * Обработка и вывод исключений
 */
function exception_handler($exception)
{
	error_handler(get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());
}


/**
 * Автозагрузка
 */
function autoload($class_name)
{
	$path	= explode('_', $class_name);
	$class	= DIR_SYSTEM_CLASSES.$path[0].'/class_'.$path[0].'_'.strtolower($path[1]).'.php';

	if(file_exists($class))
	{
		include_once $class;
	}else{
		error_handler('CORE_ERROR', 'ERROR LOAD CLASS: '.$class_name.PHP_EOL.$class, __FILE__, __LINE__);
	}
}


/**
 * Подключение загрузчика
 */
require_once DIR_SYSTEM_CLASSES.'loader/class_loader_'.LOADER_TYPE.'.php';
