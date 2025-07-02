<?php
trait component_Transactions
{
	/**
	 * Список ID используемых аккаунтов
	 */
	private $accounts_ids	= [];


	/**
	 * Данные используемых аккаунтов
	 */
	private $accounts_data	= [];


	/**
	 * Получение списка аккаунтов
	 */
	public function api_transactions_accounts_list($data)
	{
		$accounts_list	= $this->transactions_accounts_list();

		if($accounts_list && count($accounts_list) > 0)
		{
			foreach($accounts_list as $key => $account)
			{
				if($account['account_currency'] == 'CHF')
				{
					$accounts_list[$key]['account_balance_end_chf'] = $account['account_balance_end'];
				}
			}
		}

		$this->response_data('items_list', array_values($accounts_list));

		return $this->response('success');
	}


	/**
	 * Получение списка транзакций
	 */
	public function api_transactions_list($data)
	{
		$transactions_list	= $this->transactions_list();

		$this->response_data('items_list', array_values($transactions_list));

		return $this->response('success');
	}


	/**
	 * Редактирование полей аккаунта
	 */
	public function api_transactions_account_edit($data)
	{
		$valid_data			= $this->validate([
			'item_id'			=> ['required', 'int', 'number_min:1'],
			'item_field'		=> ['required', 'in_array:account_name,account_balance_start'],
			'item_value'		=> ['required', 'string_min:1'],
		], $data);

		if($valid_data === false)
		{
			return $this->response('error');
		}

		$account_data		= $this->transactions_account_data($valid_data['item_id']);

		if($account_data === false)
		{
			return $this->response('error');
		}

		switch($valid_data['item_field'])
		{
			case 'account_name':
				$new_value		= system_Filter::crop_string(htmlspecialchars(trim($valid_data['item_value'])), 32);
			break;

			case 'account_balance_start':
				$new_value		= (float)str_replace(',', '.', trim($valid_data['item_value']));
			break;
		}

		$this->update(TBL_DATA_TRANSACTIONS_ACCOUNTS, [$valid_data['item_field'] => $new_value], ['account_id' => $account_data['account_id']]);
		$this->transactions_recalculate();

		return $this->response('success');
	}


	/**
	 * Редактирование полей транзакции
	 */
	public function api_transactions_transaction_edit($data)
	{
		$valid_data = $this->validate([
			'item_id'     => ['required', 'int', 'number_min:1'],
			'item_field'  => ['required', 'in_array:transaction_number,transaction_amount,transaction_date'],
			'item_value'  => ['required', 'string_min:1'],
		], $data);

		if($valid_data === false)
		{
			return $this->response('error');
		}

		$transaction_data = $this->transaction_data($valid_data['item_id']);

		if($transaction_data === false)
		{
			return $this->response('error');
		}

		switch ($valid_data['item_field'])
		{
			case 'transaction_number':
				$new_value = system_Filter::crop_string(htmlspecialchars(trim($valid_data['item_value'])), 64);
			break;

			case 'transaction_amount':
				$new_value = (float) str_replace(',', '.', trim($valid_data['item_value']));
			break;

			case 'transaction_date':
				$raw_date	= trim($valid_data['item_value']);
				$timestamp	= strtotime($raw_date);

				if(!$timestamp || $timestamp < 0)
				{
					return $this->response('error');
				}

				$new_value = date('Y-m-d H:i:s', $timestamp);
			break;
		}

		$this->update(TBL_DATA_TRANSACTIONS, [$valid_data['item_field'] => $new_value], ['transaction_id' => $transaction_data['transaction_id']]);
		$this->transactions_recalculate();

		return $this->response('success');
	}


	/**
	 * Удаление транзакции
	 * В реальном сервисе, полное удаление записи крайне нежелательно, предпочтительно "пометить удаленным" и не показывать пользователю
	 */
	public function api_transactions_transaction_delete($data)
	{
		$valid_data = $this->validate([
			'transaction_id'     => ['required', 'int', 'number_min:1']
		], $data);

		if($valid_data === false)
		{
			return $this->response('error');
		}

		$transaction_data = $this->transaction_data($valid_data['transaction_id']);

		if($transaction_data === false)
		{
			return $this->response('error');
		}

		$this->delete(TBL_DATA_TRANSACTIONS, ['transaction_id' => $transaction_data['transaction_id']]);
		$this->transactions_recalculate();

		return $this->response('success');
	}


	/**
	 * Получение данных графика
	 */
	public function api_transactions_forecast_data($data)
	{
		$transactions = $this->transactions_list();

		if(empty($transactions))
		{
			return $this->response('error', 'Нет данных для отображения');
		}

		$min_date = null;
		$max_date = null;

		foreach($transactions as $txn)
		{
			$date = date('Y-m-d', strtotime($txn['transaction_date']));

			if(is_null($min_date) || $date < $min_date)
			{
				$min_date = $date;
			}

			if(is_null($max_date) || $date > $max_date)
			{
				$max_date = $date;
			}
		}

		$dates = [];
		$current = strtotime($min_date);
		$end = strtotime($max_date);

		while($current <= $end)
		{
			$dates[] = date('Y-m-d', $current);
			$current = strtotime('+1 day', $current);
		}

		$accounts_start = [];
		foreach($this->transactions_accounts_list() as $acc)
		{
			$key = $acc['account_name'].' ('.$acc['account_currency'].')';
			$accounts_start[$key] = floatval($acc['account_balance_start']);
		}

		$accounts = [];
		$total = [];

		foreach($transactions as $txn)
		{
			$account_key = $txn['account_name'] . ' (' . $txn['account_currency'] . ')';
			$txn_date = date('Y-m-d', strtotime($txn['transaction_date']));

			if(!isset($accounts[$account_key]))
			{
				$accounts[$account_key] = array_fill_keys($dates, 0);

				if(isset($accounts_start[$account_key]))
				{
					$first_date = $dates[0];
					$accounts[$account_key][$first_date] += $accounts_start[$account_key];
				}
			}

			if(!isset($total[$txn_date]))
			{
				$total[$txn_date] = 0;
			}

			$amount = $txn['transaction_amount'];

			if(!empty($txn['rate_value']) && $txn['rate_value'] > 0)
			{
				$amount *= $txn['rate_value'];
			}

			$accounts[$account_key][$txn_date] += $amount;
			$total[$txn_date] += $amount;
		}

		$series = [];

		foreach($accounts as $account_name => $account_balances)
		{
			$cumulative = 0;
			$data = [];

			foreach($dates as $date)
			{
				$cumulative += $account_balances[$date];
				$data[] = round($cumulative, 2);
			}

			$series[] = [
				'name' => $account_name,
				'data' => $data,
				'marker' => ['enabled' => false]
			];
		}

		$this->response_data('categories', $dates);
		$this->response_data('series', $series);

		return $this->response('success');
	}


	/**
	 * Парсинг Excel-файла с данными транзакций
	 */
	public function transactions_file_parse($tmp_file)
	{
		require_once DIR_SYSTEM_CLASSES.'/excel/PHPExcel.php';

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime  = finfo_file($finfo, $tmp_file);
		finfo_close($finfo);

		if($mime !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
		{
			return ['error' => 'Файл не является допустимым Excel-документом'];
		}

		if(!file_exists($tmp_file) || filesize($tmp_file) < 1000)
		{
			return ['error' => 'Файл повреждён или пустой'];
		}

		try {
			$data   = [];
			$excel  = PHPExcel_IOFactory::load($tmp_file);
			$sheet  = $excel->getActiveSheet();

			// Проверка заголовков — строго в нужном порядке
			$expectedHeaders = ['Account', 'Transaction No', 'Amount', 'Currency', 'Date'];
			$actualHeaders   = [];

			for($col = 0; $col < count($expectedHeaders); $col++)
			{
				$cellValue = trim($sheet->getCellByColumnAndRow($col, 1)->getValue());
				$actualHeaders[] = $cellValue;
			}

			if($actualHeaders !== $expectedHeaders)
			{
				return ['error' => 'Неверные заголовки в таблице. Ожидается: '.implode(', ', $expectedHeaders)];
			}

			foreach($sheet->getRowIterator(2) as $row)
			{
				$rowIndex   = $row->getRowIndex();
				$account    = trim($sheet->getCell("A$rowIndex")->getValue());
				$transaction= trim($sheet->getCell("B$rowIndex")->getValue());
				$amount     = trim($sheet->getCell("C$rowIndex")->getValue());
				$currency   = trim($sheet->getCell("D$rowIndex")->getValue());
				$date       = trim($sheet->getCell("E$rowIndex")->getFormattedValue());

				if(!empty($account) && !empty($transaction) && is_numeric($amount) && preg_match('/^[A-Z]{3}$/', $currency) && preg_match('/^\d{4}-\d{2}-\d{2}/', $date))
				{
					$data[] = [
						'account'     => $account,
						'transaction' => $transaction,
						'amount'      => floatval($amount),
						'currency'    => strtoupper($currency),
						'date'        => $date
					];
				}
			}

			if($this->transactions_data_set($data))
			{
				return [
					'success' => true,
					'message' => 'Файл успешно загружен и обработан'
				];
			}else{
				return [
					'success' => false,
					'message' => 'Ошибка при записи данных'
				];
			}
		}catch(Exception $e){
			return ['error' => 'Ошибка при обработке Excel: '.$e->getMessage()];
		}
	}


	/**
	 * Получение списка аккаунтов
	 */
	private function transactions_accounts_list()
	{
		$query	= 'SELECT * FROM '.TBL_DATA_TRANSACTIONS_ACCOUNTS;

		return $this->get_data($query, null);
	}


	/**
	 * Запись данных транзакций, полученных из Excel-файла
	 */
	private function transactions_data_set($data)
	{
		if(count($data) == 0)
		{
			return false;
		}

		$this->delete(TBL_DATA_TRANSACTIONS);
		$this->delete(TBL_DATA_TRANSACTIONS_ACCOUNTS);

		$currencies_list	= $this->currencies_list();
		$currencies_codes	= array_keys($currencies_list);

		foreach($data as $item)
		{
			if(!in_array($item['currency'], $currencies_codes))
			{
				continue;
			}

			$transaction_stamp	= strtotime($item['date']);

			if($transaction_stamp && $transaction_stamp > 0)	// проверяем получившуюся дату, можно выдавать ошибку или не записывать строку или записывать со значением NULL...
			{
				$this->insert(TBL_DATA_TRANSACTIONS, [
					'account_id'			=> $this->transactions_account_id($item['account'], $item['currency']),
					'transaction_number'	=> system_Filter::crop_string($item['transaction'], 64, false, true),	// обрезка до 64 знаков, удаление html-тегов
					'transaction_amount'	=> (float)$item['amount'],
					'transaction_date'		=> date('Y-m-d H:i:s', $transaction_stamp)
				]);
			}
		}

		$this->transactions_recalculate();

		return true;
	}


	/**
	 * Получение ID аккаунта по имени и коду валюты
	 */
	private function transactions_account_id($account_name, $account_currency)
	{
		// если уже находили данную комбинацию - не мучаем базу, отдаем из кэша
		if(array_key_exists($account_name.'_'.$account_currency, $this->accounts_ids))
		{
			return $this->accounts_ids[$account_name.'_'.$account_currency];
		}

		$query			= 'SELECT
									account_id,
									account_name
								FROM '.TBL_DATA_TRANSACTIONS_ACCOUNTS.'
								WHERE 
									account_name = :account_name
									AND account_currency = :account_currency
								LIMIT 1';

		$account_data	= $this->get_data($query, ['account_name' => $account_name, 'account_currency' => $account_currency], 'row');

		if($account_data)
		{
			// записываем в кэш
			$this->accounts_ids[$account_data['account_name'].'_'.$account_data['account_currency']] = $account_data['account_id'];

			return $account_data['account_id'];
		}else{
			$account_id = $this->insert(TBL_DATA_TRANSACTIONS_ACCOUNTS, [
				'account_name'		=> system_Filter::crop_string($account_name, 32, false, true),	// обрезка до 32 знаков, удаление html-тегов
				'account_currency'	=> $account_currency
			]);

			// записываем в кэш
			$this->accounts_ids[$account_name.'_'.$account_currency] = $account_id;

			return $account_id;
		}
	}


	/**
	 * Получение данных аккаунта
	 */
	private function transactions_account_data($account_id)
	{
		// если уже находили аккаунт - не мучаем базу, отдаем из кэша
		if(array_key_exists($account_id, $this->accounts_data))
		{
			return $this->accounts_data[$account_id];
		}

		$query			= 'SELECT
									*
								FROM '.TBL_DATA_TRANSACTIONS_ACCOUNTS.'
								WHERE 
									account_id = :account_id
								LIMIT 1';

		return $this->get_data($query, ['account_id' => $account_id], 'row');
	}


	/**
	 * Перерасчет сумм транзакций по аккаунтам
	 */
	private function transactions_recalculate()
	{
		$transactions_list			= $this->transactions_list();	// снова упрощение, ибо транзакций может быть очень много и обрабатывать нужно порциями или через очередь
		$transactions_currencies	= [];
		$transactions_dates			= [];

		if($transactions_list && !empty($transactions_list))
		{
			foreach($transactions_list as $transaction)
			{
				$transaction_date	= date('Y-m-d', strtotime($transaction['transaction_date']));

				if(!in_array($transaction_date, $transactions_dates) && empty($transaction['rate_value']))
				{
					$transactions_dates[] = $transaction_date;
				}

				if(!isset($this->accounts_data[$transaction['account_id']]))
				{
					$account_data							= $this->transactions_account_data($transaction['account_id']);
					$account_data['account_balance_end']	= $account_data['account_balance_start'];

					if($account_data['account_currency'] !== 'CHF' && !in_array($account_data['account_currency'], $transactions_currencies) && empty($transaction['rate_value']))
					{
						$transactions_currencies[] = $account_data['account_currency'];
					}

					$this->accounts_data[$transaction['account_id']] = $account_data;
				}else{
					$account_data	= $this->accounts_data[$transaction['account_id']];

					if($account_data['account_currency'] !== 'CHF' && !in_array($account_data['account_currency'], $transactions_currencies) && empty($transaction['rate_value']))
					{
						$transactions_currencies[] = $account_data['account_currency'];
					}

					$this->accounts_data[$transaction['account_id']]['account_balance_end'] += $transaction['transaction_amount'];
				}
			}

			// Данный метод обновления курсов списка валют по списку дат. В реальном проекте нужно вынести из основного потока в очередь,
			// например в Redis для асинхронного обновления, здесь опять же упрощено
//			$this->currencies_rates_update('CHF', $transactions_dates, $transactions_currencies);
		}

		if(count($this->accounts_data) > 0)
		{
			foreach($this->accounts_data as $account_id => $account_data)
			{
				$this->update(TBL_DATA_TRANSACTIONS_ACCOUNTS, [
					'account_balance_end' 		=> $this->accounts_data[$account_id]['account_balance_end'],
					'account_balance_end_chf'	=> $this->accounts_data[$account_id]['account_balance_end_chf']
				], ['account_id' => $account_id]);
			}
		}
	}


	/**
	 * Получение списка транзакций
	 *
	 * В рамках текущей постановки ни каких фильтров, сортировок и пагинации не предусмотрено,
	 * однако они должны быть здесь.
	 *
	 * Преобразование даты DATE(dt.transaction_date) потенциально может странно себя вести с индексами,
	 * но это упрощенная версия с большим количеством допущений...
	 */
	private function transactions_list($currency_base = 'CHF')
	{
		$query = 'SELECT 
    					dt.*,
    					dta.account_name,
    					dta.account_currency,
    					dcr.rate_value
					FROM '.TBL_DATA_TRANSACTIONS.' dt
						LEFT JOIN '.TBL_DATA_TRANSACTIONS_ACCOUNTS.' AS dta	ON dta.account_id = dt.account_id
						LEFT JOIN '.TBL_DATA_CURRENCIES_RATES.' AS dcr ON dcr.rate_currency_base = :currency_base 
							AND dcr.rate_currency_target = dta.account_currency
							AND dcr.rate_date = DATE(dt.transaction_date)
					ORDER BY dt.transaction_date DESC';

		return $this->get_data($query, ['currency_base' => $currency_base]);
	}


	/**
	 * Получение данных транзакции
	 */
	private function transaction_data($transaction_id)
	{
		$query = 'SELECT 
    					dt.*,
    					dta.account_name,
    					dta.account_currency
					FROM '.TBL_DATA_TRANSACTIONS.' dt
						LEFT JOIN '.TBL_DATA_TRANSACTIONS_ACCOUNTS.' AS dta	ON dta.account_id = dt.account_id
					WHERE dt.transaction_id = :transaction_id
					LIMIT 1';

		return $this->get_data($query, ['transaction_id' => $transaction_id], 'row');
	}
}