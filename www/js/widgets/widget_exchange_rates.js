;(function($, window, document, undefined)
{
	let widgetExchangeRates = function(elem, options){
		this.options   = options;
		this.container = false;
	};

	widgetExchangeRates.prototype = {
		/**
		 * Инициализация компонента
		 */
		init: function()
		{
			this.container			= $('[data-module="widget_exchange_rates"]');
			this.table_body			= this.container.find("tbody");

			this.update_interface();
			return this;
		},


		/**
		 * Метод обновления данных виджета
		 */
		update_interface: function()
		{
			$.ajax({
				url: '/',
				type: 'POST',
				dataType: 'json',
				data: {
					handler: 'system',
					action: 'api_currencies_rates_actual'
				},
				success: (response) =>
				{
					if(response.status === 'success' && response.data && Array.isArray(response.data.items_list))
					{
						this.render_table(response.data.items_list);
					}else{
						alert('Ошибка: некорректный ответ от сервера');
					}
				},
				error: function(xhr, status, error)
				{
					alert('Ошибка AJAX-запроса');
				}
			});
		},


		/**
		 * Отрисовка таблицы с курсами
		 */
		render_table: function(rates)
		{
			this.table_body.html("");

			if(typeof rates !== 'undefined')
			{
				$.each(rates, (i, rate_data) =>
				{
					let td_name		= $('<td>').addClass('text-center').text(rate_data.currency);
					let td_value	= $('<td>').addClass('text-center').text(parseFloat(rate_data.rate_value).toFixed(2));
					let row			= $('<tr />').append(td_name, td_value);

					this.table_body.append(row);
				});
			}
		}
	};


	/**
	 * Непосредственная регистрация компонента
	 */
	$.fn.widget_exchange_rates = function(method, options)
	{
		let $this = $(this);
		let data  = $this.data('widget_exchange_rates');

		if(!data)
		{
			$this.data('widget_exchange_rates', data = new widgetExchangeRates(this));
		}

		if(data[method])
		{
			return data[method](options);
		}else{
			$.error('No method ' + method + ' in widgetExchangeRates');
		}
	};

})(jQuery, window, document);
