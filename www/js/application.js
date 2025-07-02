let APP	= false;

;(function($, window, document, undefined)
{
	let ControllerApplication	= function(elem, options){
		this.environment_ready					= false;
		this.modules_list						= [];
	};

	ControllerApplication.prototype	= {
		/**
		 * Инициализация приложения
		 */
		init: function(options)
		{
			$('body').find('a[href="#"]').on('click', function(event){
				event.preventDefault();
			});

			let modules_list	= $('[data-module]');

			if(modules_list.length > 0)
			{
				$.each(modules_list, (i, module_element) =>
				{
					let module_name	= $(module_element).data('module');

					if($.fn[module_name])
					{
						APP[module_name]('init');
						this.modules_list.push(module_name);
					}else{
						console.log('No component ' + module_name);
					}
				});
			}

			return this;
		},


		/**
		 * Запуск обновления всех виджетов
		 */
		update_interface: function(data)
		{
			$.each(this.modules_list, function(i, module_name)
			{
				APP[module_name]('update_interface');
			});
		},


		/**
		 * Общий массив настроек Datatables
		 */
		datatables_settings: function(row_id)
		{
			return {
				autoWidth: false,
				fixedHeader: false,
				order: [[ 0, 'desc' ]],
// 				dom: '<"datatable-header"flB<"#custom_toolbar">><"datatable-scroll-lg"t><"datatable-footer"ip>',
				dom: 'Bfrtip',
				lengthMenu: [10, 25, 50, 75, 100],
				pageLength: 25,
				stateSave: true,
				buttons: [
					{
						extend: 'excelHtml5',
						text: 'Excel',
						className: 'btn btn-default'
					},
					{
						extend: 'pdfHtml5',
						text: 'PDF',
						className: 'btn btn-default'
					}
				],
				ajax: {
					processing: true,
					serverSide: true,
					url: "/",
					type: "POST",
					cache: false,
					crossDomain: false,
					dataType: "json",
					dataSrc: function(json)
					{
						if(typeof json.data!== 'undefined' && typeof json.data.items_list !== 'undefined')
						{
							return json.data.items_list;
						}else{
							return false;
						}
					}
				},
				rowId: row_id,
				createdRow: function(row, data, dataIndex){
					$(row).attr('data-item_id', data[row_id]);
				},
				drawCallback: function(settings){
					$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
				}
			};
		},
	};


	/**
	 * Непосредственная регистрация модуля
	 */
	$.fn.controller = function(method, options)
	{
		let $this	= $(this);
		let data	= $(this).data('controller');

		if(!data)
		{
			$this.data('controller', data = new ControllerApplication(this));
		}

		if(data[method]){
			return data[method](options);
		}else{
			$.error('No method ' +  method + ' in ControllerApplication');
		}
	};

})(jQuery, window, document);


/**
 * Запуск приложения
 */
$(function()
{
	APP	= $('body');
	APP.controller('init');
});
