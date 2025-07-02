;(function($, window, document, undefined)
{
	let widgetUpload	= function(elem, options){
		this.options			= options;


		/**
		 * Основной контейнер виджета
		 */
		this.container			= false;


		/**
		 * Экземпляр объекта Dropzone
		 */
		this.dropzone_instance	= false;


		/**
		 * Элементы управления панели
		 */
		this.elements			= {
			dropzone:				false
		};
	};

	widgetUpload.prototype	= {
		/**
		 * Инициализация компонента
		 */
		init: function()
		{
			this.container			= $('[data-module="widget_upload"]');
			this.elements.dropzone	= $("#dropzone_single");

			this.dropzone_init();

			return this;
		},


		/**
		 * Метод обновления данных виджета
		 */
		update_interface: function(){},


		/**
		 * Инициализация плагина
		 */
		dropzone_init: function()
		{
			Dropzone.autoDiscover 	= false;	// отключение автозапуска плагина

			this.elements.dropzone.addClass('dropzone');

			this.dropzone_instance	= new Dropzone("#dropzone_single", {
				url: "/",
				paramName: "file",
				acceptedFiles: ".xls,.xlsx",
				maxFilesize: 10,
				maxFiles: 1,
				autoProcessQueue: true,
				addRemoveLinks: true,
				dictDefaultMessage: 'Drop file to upload <span>or CLICK</span>',
				init: function()
				{
					this.on('addedfile', function(file)
					{
						if(this.fileTracker)
						{
							this.removeFile(this.fileTracker);
						}

						this.fileTracker = file;
					});

					this.on("sending", function(file, xhr, formData)
					{
						formData.append("handler", 'system');
						formData.append("action", 'api_uploads_receive_transactions');
					});

					this.on("success", function(file, response)
					{
						if(response.message)
						{
							let msgElement = Dropzone.createElement("<div class='dz-success-message'>" + response.message + "</div>");
							file.previewElement.appendChild(msgElement);
						}

						this.removeFile(file);
						APP.controller('update_interface');
					});

					this.on("error", function(file, errorMessage, xhr)
					{
						let msgElement = Dropzone.createElement("<div class='dz-error-message'>" + errorMessage + "</div>");
						file.previewElement.appendChild(msgElement);

						this.removeFile(file);
					});
				}
			});
		}
	};


	/**
	 * Непосредственная регистрация компонента
	 */
	$.fn.widget_upload = function(method, options)
	{
		let $this		= $(this);
		let data		= $this.data('widget_upload');

		if(!data)
		{
			$this.data('widget_upload', data = new widgetUpload(this));
		}

		if(data[method]){
			return data[method](options);
		}else{
			$.error('No method ' +  method + ' in widgetUpload');
		}
	};

})(jQuery, window, document);
