<?php
/**
 * Вывод результатов проверки тура
 * методом http-include
 * @author Bnik
 * @created 02.07.10
 */ 


	/**
		 * Базовый URL
	*/

	$base_url = "http://www.webclient.touradmin.ru/b/checkoffer/";
		
		/**
		 * Отключаем вывод ошибок,
		 * и ставим таймаут. 29 секунд.
		 */
	error_reporting(0);
	ini_set('default_socket_timeout', 29);

		/**
		 * Получаем страницу. Если не удается получить из-за какой-то ошибки,
		 * или если нет ответа в течение долгого времени, или если ответ содержит 0 байт -
		 * выводим сообщение о недоступности сервиса.
		 * Иначе выводим страницу.
		 */

		//убираем переменную MODx
	$MyQS=$_SERVER['QUERY_STRING'];
	$MyQS=preg_replace("/\&?q\=[^\&]*\&?/",'', $MyQS);
		
	$url = $base_url . "?" . $MyQS;

//$url .= "&rewrite=".time();

	$page = file_get_contents($url);
		
//	$page = str_replace("[[", "[ [", $page);
//	$page = str_replace("]]", "] ]", $page);

	if(!($page === false)  && strlen($page) > 0) {
		echo $page;			
	}
	else {
		echo "";
	}		

?>