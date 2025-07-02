#!/usr/bin/php
<?php
date_default_timezone_set('Europe/Moscow');
ini_set('memory_limit', '32M');
ini_set("max_execution_time", 0);


/**
 * Задание типа лоадера для работы
 */
define('LOADER_TYPE', 'cli');


/**
 *  Загрузка лоадера (CLI)
 */
require_once '../maincore.php';

$loader				= new system_Loader();
$currencies_file	= 'currencies_data.json';
$currencies_data	= file_get_contents($currencies_file);
$currencies_data	= json_decode($currencies_data, true);

$loader->delete(TBL_SYSTEM_CURRENCIES);

foreach($currencies_data as $item)
{
	$loader->insert(TBL_SYSTEM_CURRENCIES, [
		'currency_code'				=> $item['code'],
		'currency_name'				=> $item['name'],
		'currency_symbol'			=> $item['symbol'],
		'currency_symbol_native'	=> $item['symbol_native']
	]);
}
