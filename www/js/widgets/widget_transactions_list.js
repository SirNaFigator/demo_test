;(function($, window, document, undefined)
{
	let widgetTransactionsList	= function(elem, options)
	{
		this.options			= options;


		/**
		 * Основной контейнер виджета
		 */
		this.container			= false;


		/**
		 * Элементы Datatable
		 */
		this.datatable_element	= false;


		/**
		 * Экземпляр Editor
		 */
		this.editor = false;
	};

	widgetTransactionsList.prototype	= {
		/**
		 * Инициализация компонента
		 */
		init: function()
		{
			this.container			= $('[data-module="widget_transactions_list"]');
			this.datatable_element	= $("#transactions_list");

			this.datatable_transactions_list_init();

			return this;
		},


		/**
		 * Метод обновления данных виджета
		 */
		update_interface: function()
		{
			this.datatable_element.DataTable().ajax.reload(function(json){}, false);
		},


		/**
		 * Инициализация таблицы
		 */
		datatable_transactions_list_init: function()
		{
			let self = this;

			self.editor = new $.fn.dataTable.Editor({
				idSrc: "transaction_id",
				ajax: {
					url: '/',
					type: 'POST',
					data: function(d){
						return d;
					}
				},
				table: "#transactions_list",
				fields: [
					{ label: "Account", name: "account_name", type: "text", attr: { readonly: true, disabled: true } },
					{ label: "Transaction No", name: "transaction_number" },
					{ label: "Amount", name: "transaction_amount" },
					{ label: "Currency", name: "account_currency", type: "text", attr: { readonly: true, disabled: true } },
					{
						label: "Date",
						name: "transaction_date",
						type: "datetime",
						format: "YYYY-MM-DD HH:mm:ss",
						attr: { readonly: true }
					}
				]
			});

			self.datatable_element.DataTable($.extend(true, APP.controller('datatables_settings', 'transaction_id'), {
				ajax: {
					data: function(){
						return {
							handler: 'system',
							action: 'api_transactions_list'
						};
					}
				},
				dom:
					'<"datatable-header-top"<"datatable-header-left"l><"datatable-header-right"p>>' +
					'<"datatable-toolbar"<"datatable-export-label"f><"datatable-export-buttons"B>>' +
					'<"datatable-scroll-lg"t>',
				language: {
					lengthMenu: '<span>Show:</span> _MENU_ entries',
				},
				initComplete: function(){
					$('.datatable-export-label').html('<span>Export full table:</span>');
				},
				pageLength: 10,
				columns: [
					{ data: 'account_name', title: 'Account' },
					{ data: 'transaction_number', title: 'Transaction No' },
					{ data: 'transaction_amount', title: 'Amount' },
					{ data: 'account_currency', title: 'Currency' },
					{ data: 'transaction_date', title: 'Date' },
					{
						data: null,
						orderable: false,
						searchable: false,
						className: 'text-center',
						defaultContent: '<a href="#" class="btn-delete-transaction"><i class="icon-trash"></i></a>'
					}
				],
				select: true,
				order: [[0, 'desc']],
				buttons: [
					{
						extend: 'excelHtml5',
						className: 'btn btn-default',
						title: '',
						text: 'Excel',
						customize: function(xlsx)		// лютая ручная пересборка генерируемого excel, иначе PHPExcel под PHP 5.6 его не читает
						{
							const parser		= new DOMParser();
							const serializer	= new XMLSerializer();

							if(!xlsx.xl['sharedStrings.xml'])
							{
								const xml = '<?xml version="1.0" encoding="UTF-8"?>' +
									'<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="1" uniqueCount="1">' +
									'<si><t>placeholder</t></si>' +
									'</sst>';

								const xmlDoc = parser.parseFromString(xml, 'application/xml');
								xlsx.xl['sharedStrings.xml'] = xmlDoc;
							}

							const relsPath	= 'workbook.xml.rels';
							let relsDoc		= xlsx.xl._rels[relsPath];

							if(typeof relsDoc === 'string')
							{
								relsDoc = parser.parseFromString(relsDoc, 'application/xml');
							}

							const hasSharedStrings = relsDoc.querySelector(
								'Relationship[Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings"]'
							);

							if(!hasSharedStrings)
							{
								const Relationships = relsDoc.documentElement;
								const rel = relsDoc.createElementNS(
									'http://schemas.openxmlformats.org/package/2006/relationships',
									'Relationship'
								);
								rel.setAttribute('Id', 'rIdSharedStrings');
								rel.setAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings');
								rel.setAttribute('Target', 'sharedStrings.xml');
								Relationships.appendChild(rel);
							}

							xlsx.xl._rels[relsPath] = relsDoc;
						}
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

				if(['transaction_number', 'transaction_amount', 'transaction_date'].includes(columnName))
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

				data.action = 'api_transactions_transaction_edit';
				data.handler = 'system';
			});

			self.editor.on('postSubmit', function(e, json, data, action, xhr)
			{
				APP.controller('update_interface');
			});

			self.datatable_element.on('click', '.btn-delete-transaction', function(e)
			{
				e.preventDefault();

				let table	= self.datatable_element.DataTable();
				let row		= table.row($(this).closest('tr'));
				let rowData	= row.data();

				if(!rowData || !rowData.transaction_id)
				{
					return;
				}

				if(confirm('Really?'))
				{
					$.ajax({
						url: '/',
						type: 'POST',
						data: {
							handler: 'system',
							action: 'api_transactions_transaction_delete',
							transaction_id: rowData.transaction_id
						},
						success: function(response)
						{
							APP.controller('update_interface');
						},
						error: function(xhr)
						{
							APP.controller('update_interface');
						}
					});
				}
			});
		}
	};


	/**
	 * Непосредственная регистрация компонента
	 */
	$.fn.widget_transactions_list = function(method, options)
	{
		let $this		= $(this);
		let data		= $this.data('widget_transactions_list');

		if(!data)
		{
			$this.data('widget_transactions_list', data = new widgetTransactionsList(this));
		}

		if(data[method]){
			return data[method](options);
		}else{
			$.error('No method ' +  method + ' in widgetTransactionsList');
		}
	};

})(jQuery, window, document);
