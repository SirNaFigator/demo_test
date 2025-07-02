;(function($, window, document, undefined)
{
	let widgetCashForecast = function(elem, options)
	{
		this.options   = options;
		this.container = $(elem);
	};

	widgetCashForecast.prototype = {
		/**
		 * Инициализация виджета
		 */
		init: function()
		{
			this.update_interface();
			return this;
		},


		/**
		 * Обновление данных виджета
		 */
		update_interface: function()
		{
			$.ajax({
				url: '/',
				type: 'POST',
				dataType: 'json',
				data: {
					handler: 'system',
					action: 'api_transactions_forecast_data'
				},
				success: (response) =>
				{
					if(response.status === 'success' && response.data)
					{
						this.render_chart(response.data.categories, response.data.series);
					}else{
						alert('Ошибка в ответе сервера');
					}
				},
				error: (xhr, status, error) =>
				{
					alert('Ошибка AJAX');
				}
			});
		},


		/**
		 * Непосредственная отрисовка графика
		 */
		render_chart: function(categories, series)
		{
			let chartContainer = this.container.find('#cash_forecast_chart');

			if(chartContainer.length === 0)
			{
				return;
			}

			Highcharts.chart(chartContainer[0], {
				chart: {
					type: 'line',
					style: {
						fontSize: '14px',
						fontFamily: 'Arial, sans-serif'
					}
				},
				title: {
					text: '',
					enabled: false
				},
				xAxis: {
					categories: categories,
					tickInterval: Math.ceil(categories.length / 10),
					labels: { style: { fontSize: '13px' } }
				},
				yAxis: {
					title: {
						text: '',
						enabled: false
					},
					labels: { style: { fontSize: '13px' } }
				},
				tooltip: {
					shared: true,
					crosshairs: true
				},
				legend: {
					enabled: true,
					itemStyle: {
						fontSize: '13px'
					}
				},
				exporting: {
					enabled: true
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					series: {
						lineWidth: 3,
						marker: { enabled: false }
					}
				},
				series: series
			});
		}
	};


	/**
	 * Непосредственная регистрация компонента
	 */
	$.fn.widget_cash_forecast = function(method, options)
	{
		let $this = $(this);
		let data  = $this.data('widget_cash_forecast');

		if (!data)
		{
			$this.data('widget_cash_forecast', data = new widgetCashForecast(this, options));
		}

		if (data[method])
		{
			return data[method](options);
		}
		else
		{
			$.error('Метод ' + method + ' не найден в widgetCashForecast');
		}
	};

})(jQuery, window, document);
