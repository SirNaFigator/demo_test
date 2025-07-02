<?php
/**
 * Получение курсов валют на определённую дату через API https://exchangerate.host
 */
trait component_Currencies
{
	/**
	 * URL конвертации пары валют на заданную дату
	 */
	private $url_convert	= 'https://api.exchangerate.host/convert';


	/**
	 * URL получения курсов списка валют на заданную дату
	 */
	private $url_historical	= 'https://api.exchangerate.host/historical';


	/**
	 * Метод получения текущих значений валют транзакций
	 */
	public function api_currencies_rates_actual($data)
	{
		$query = 'SELECT 
						dcr.rate_currency_target AS currency,
						dcr.rate_value
					FROM '.TBL_DATA_TRANSACTIONS_ACCOUNTS.' AS dta
						JOIN '.TBL_DATA_CURRENCIES_RATES.' AS dcr ON dcr.rate_currency_target = dta.account_currency
					WHERE 
						dcr.rate_currency_base = "CHF"
						AND dcr.rate_date = CURDATE()
					GROUP BY 
						dcr.rate_currency_target';

		$currencies_rate_data	= $this->get_data($query, null);

		if(!$currencies_rate_data || count($currencies_rate_data) === 0)
		{
			return $this->response('error', 'Нет актуальных курсов валют');
		}

		$this->response_data('items_list', $currencies_rate_data);

		return $this->response('success');
	}


	/**
	 * Метод конвертации пары валют на заданную дату
	 */
	private function currencies_rate_convert($currency_base = 'CHF', $currency_need = 'USD', $amount = 1, $date = 'latest')
	{
		if($date !== 'latest' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))
		{
			return false;
		}

		$api	= new system_apiRequest();
		$api->request_append_data('access_key',	EXCHANGERATE_KEY);
		$api->request_append_data('from',		$currency_base);
		$api->request_append_data('to',			$currency_need);
		$api->request_append_data('amount',		$amount);

		if($date !== 'latest')
		{
			$api->request_append_data('date',	$date);
		}

		$response = $api->request_send($this->url_convert, 'GET');

		if(!$response || !$response['success'] || !isset($response['result']))
		{
			return false;
		}

		return (float)$response['result'];
	}


	/**
	 * Метод получения курсов списка валют на заданную дату
	 */
	private function currencies_rate_historical($currency_base = 'CHF', $currency_need = [], $date = false)
	{
		if(!$date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))
		{
			return false;
		}

		$api	= new system_apiRequest();
		$api->request_append_data('access_key',		EXCHANGERATE_KEY);
		$api->request_append_data('date',			$date);
		$api->request_append_data('source',			$currency_base);
		$api->request_append_data('currencies',		implode(',', $currency_need));

		$response = $api->request_send($this->url_historical, 'GET');

		if(!$response || !$response['success'] || !isset($response['quotes']) || count($response['quotes']) == 0)
		{
			return false;
		}

		$return_data = [];

		foreach($response['quotes'] as $key => $value)
		{
			$return_data[substr($key, 3)] = (float)$value;
		}

		return $return_data;
	}


	/**
	 * Получение и запись в базу курсов списка валют по списку дат
	 */
	public function currencies_rates_update($currency_base = 'CHF', $transactions_dates = [], $transactions_currencies = [])
	{
		if(empty($transactions_dates) || empty($transactions_currencies))
		{
			return false;
		}

		$current_date	= date("Y-m-d");

		if(!in_array($current_date, $transactions_dates))
		{
			$transactions_dates[] = $current_date;
		}

		foreach($transactions_dates as $date)
		{
			if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))
			{
				// Снова упрощение. Рационально писать логи, отправлять уведомления или возвращать ошибку.
				continue;
			}

			$rates_list		= $this->currencies_rate_historical($currency_base, $transactions_currencies, $date);

			if($rates_list === false)
			{
				continue;
			}

			if($rates_list && count($rates_list) > 0)
			{
				foreach($rates_list as $currency_code => $rate_value)
				{
					$this->insert(TBL_DATA_CURRENCIES_RATES, [
						'rate_currency_base'	=> $currency_base,
						'rate_currency_target'	=> $currency_code,
						'rate_date'				=> $date,
						'rate_value'			=> (float)$rate_value
					], ['rate_value']);
				}
			}

			sleep(1);
		}
	}
}
