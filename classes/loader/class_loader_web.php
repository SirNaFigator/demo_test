<?php
/**
 * Класс загрузчика WEB
 */
class system_Loader extends system_Core
{
	/**
	 * Подключение обработчика загружаемых файлов
	 */
	use component_Uploads;


	/**
	 * Подключение класса работы с транзакциями
	 */
	use component_Transactions;


	/**
	 * Подключение класса работы с валютами
	 */
	use component_Currencies;


	/**
	 * Подключение класса валидатора
	 */
	use system_Validator;


	/**
	 * Конструктор
	 */
	public function __construct()
	{
		if($GLOBALS['CORE']['loader'] === false)
		{
			system_Router::check_request_link();
			system_Router::prepare_data();
			system_Router::get_route();
			system_Router::get_client_ip();
			system_Router::get_client_agent();
			system_Router::detect_module();
			system_Router::detect_module_action();
			system_Router::detect_handler();
		}
	}


	/**
	 * Эмуляция метода проверки наличия вызываемого метода в списке доступных для вызова,
	 * проверки логина и прав пользователя...
	 */
	public function module_handler()
	{
		return true;
	}


	/**
	 * Получение списка валют
	 */
	public function currencies_list()
	{
		$query	= 'SELECT * FROM '.TBL_SYSTEM_CURRENCIES;

		return $this->get_data($query, null, 'rows', 'currency_code');
	}


	/**
	 * Проверка доступа к тестовой зоне
	 */
	public function loader_testzone_access()
	{
		if(isset($_COOKIE['testzone_token']) && system_Filter::token_check($_COOKIE['testzone_token']))
		{
			return true;
		}

		if(isset($GLOBALS['REQUEST']['route_parts'][0]) && system_Filter::token_check($GLOBALS['REQUEST']['route_parts'][0]))
		{
			setcookie('testzone_token', $GLOBALS['REQUEST']['route_parts'][0], time() + 180000, '/', COOKIE_DOMAIN);
			$this->redirect('/');

			return true;
		}

		return false;
	}
}
