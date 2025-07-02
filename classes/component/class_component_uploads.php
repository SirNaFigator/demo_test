<?php
/**
 * Класс обработки загружаемых файлов
 */
trait component_Uploads
{
	/**
	 * Получение и проверка полученного файла транзакций
	 */
	public function api_uploads_receive_transactions($data)
	{
		if(!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK)
		{
			return $this->response('error', 'Файл не был загружен');
		}

		$allowed_mimes	= [
			'application/vnd.ms-excel', 										// .xls
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'	// .xlsx
		];

		$tmp_file		= $_FILES['file']['tmp_name'];
		$mime			= mime_content_type($tmp_file);

		if(!in_array($mime, $allowed_mimes))
		{
			return $this->response('error', 'Недопустимый формат файла');
		}

		return $this->transactions_file_parse($tmp_file);
	}
}