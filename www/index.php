<?php
ini_set('memory_limit', '24M');
ini_set("max_execution_time", 30);


/**
 * Задаем тип загрузчика
 */
define('LOADER_TYPE', 'web');


/**
 * Подключение ядра
 */
require_once "../maincore.php";


/**
 * Экземпляр основного загрузчика
 */
$loader = new system_Loader();


if($loader->loader_testzone_access() === false)
{
	exit('Доступ запрещен');
}


/**
 * Обработка AJAX-запросов
 */
if($GLOBALS['REQUEST']['handler'] && system_Router::is_ajax())
{
	$loader->send_headers('200', 'json');

	if(!empty($GLOBALS['REQUEST']['action']))
	{
		if($loader->module_handler() && @method_exists($loader, $GLOBALS['REQUEST']['data']['action']))
		{
			echo system_Filter::json_encode_cyr($loader->{$GLOBALS['REQUEST']['data']['action']}($GLOBALS['REQUEST']['data']));
		}else{
			echo system_Filter::json_encode_cyr($loader->response('error', 'Запрошена не верная операция'));
		}
	}else{
		echo system_Filter::json_encode_cyr($loader->response('error', 'Запрошена не верная операция'));
	}

	exit();
}


/**
 * Отправка заголовков и загрузка шаблона
 * (эмуляция динамической загрузки одного из модулей системы)
 */
$loader->send_headers(200, 'html');
system_Viewer::display(DIR_SYSTEM_TEMPLATES.'template_component_header.tpl');
system_Viewer::display(DIR_SYSTEM_TEMPLATES.'template_page_module.tpl');
system_Viewer::display(DIR_SYSTEM_TEMPLATES.'template_component_footer.tpl');
