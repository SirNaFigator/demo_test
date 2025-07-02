;(function($, window, document, undefined)
{
	let widgetBankAccountsList	= function(elem, options){
		this.options			= options;


		/**
		 * Основной контейнер виджета
		 */
		this.container			= false;


		/**
		 * Элементы управления панели
		 */
		this.datatable_element	= false;


		/**
		 * Экземпляр Editor
		 */
		this.editor = false;
	};

	widgetBankAccountsList.prototype	= {
		/**
		 * Инициализация компонента
		 */
		init: function()
		{
			this.container			= $('[data-module="widget_bank_accounts_list"]');
			this.datatable_element	= $("#bank_accounts_list");

			this.datatable_bank_accounts_list_init();

			return this;
		},


		/**
		 * Обновление данных виджета
		 */
		update_interface: function()
		{
			this.datatable_element.DataTable().ajax.reload(function(json){}, false);
		},


		/**
		 * Инициализация таблицы списка аккаунтов
		 */
		datatable_bank_accounts_list_init: function()
		{
			let self = this;

			self.editor = new $.fn.dataTable.Editor({
				idSrc: "account_id",
				ajax: {
					url: '/',
					type: 'POST',
					data: function(d){
						return d;
					}
				},
				table: "#bank_accounts_list",
				fields: [
					{ label: "Bank name", name: "account_name" },
					{ label: "Currency", name: "account_currency", type: "text", attr: { readonly: true, disabled: true } },
					{ label: "Starting balance", name: "account_balance_start" },
					{ label: "End balance", name: "account_balance_end", type: "text", attr: { readonly: true, disabled: true } },
					{ label: "End balance (CHF)", name: "account_balance_end_chf", type: "text", attr: { readonly: true, disabled: true } }
				]
			});

			self.datatable_element.DataTable($.extend(true, APP.controller('datatables_settings', 'account_id'), {
				ajax: {
					data: function(){
						return {
							handler: 'system',
							action: 'api_transactions_accounts_list'
						};
					}
				},
				dom: 't',
				columns: [
					{ data: 'account_name', title: 'Bank name' },
					{ data: 'account_currency', title: 'Currency' },
					{ data: 'account_balance_start', title: 'Starting balance' },
					{ data: 'account_balance_end', title: 'End balance' },
					{ data: 'account_balance_end_chf', title: 'End balance (CHF)' }
				],
				select: true,
				pageLength: 50,
				order: [[0, 'desc']],
				buttons: [
					{
						extend: 'excelHtml5',
						className: 'btn btn-default',
						text: 'Excel'
					},
					{
						extend: 'pdfHtml5',
						className: 'btn btn-default',
						text: 'PDF'
					}
				]
			}));

			self.datatable_element.on('click', 'tbody td', function(e)
			{
				let table		= self.datatable_element.DataTable();
				let cellIndex	= table.cell(this).index();
				let columnName 	= table.column(cellIndex.column).dataSrc();

				if(['account_name', 'account_balance_start'].includes(columnName))
				{
					self.editor.inline(this);
				}
			});

			self.editor.on('preSubmit', function (e, data, action)
			{
				if(action === 'edit')
				{
					let submitted	= data.data;
					let item_id		= Object.keys(submitted)[0];
					let item_fields	= submitted[item_id];
					let item_field	= Object.keys(item_fields)[0];
					let item_value	= item_fields[item_field];

					data.item_id 	= item_id;
					data.item_field = item_field;
					data.item_value = item_value;

					delete data.data;
				}

				data.action = 'api_transactions_account_edit';
				data.handler = 'system';
			});

			self.editor.on('postSubmit', function(e, json, data, action, xhr)
			{
				APP.controller('update_interface');
			});
		}

	};


	/**
	 * Непосредственная регистрация компонента
	 */
	$.fn.widget_bank_accounts_list = function(method, options)
	{
		let $this		= $(this);
		let data		= $this.data('widget_bank_accounts_list');

		if(!data)
		{
			$this.data('widget_bank_accounts_list', data = new widgetBankAccountsList(this));
		}

		if(data[method]){
			return data[method](options);
		}else{
			$.error('No method ' +  method + ' in widgetBankAccountsList');
		}
	};

})(jQuery, window, document);
