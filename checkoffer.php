<?php
/**
 * ����� ����������� �������� ����
 * ������� http-include
 * @author Bnik
 * @created 02.07.10
 */ 


	/**
		 * ������� URL
	*/

	$base_url = "http://www.webclient.touradmin.ru/b/checkoffer/";
		
		/**
		 * ��������� ����� ������,
		 * � ������ �������. 29 ������.
		 */
	error_reporting(0);
	ini_set('default_socket_timeout', 29);

		/**
		 * �������� ��������. ���� �� ������� �������� ��-�� �����-�� ������,
		 * ��� ���� ��� ������ � ������� ������� �������, ��� ���� ����� �������� 0 ���� -
		 * ������� ��������� � ������������� �������.
		 * ����� ������� ��������.
		 */

		//������� ���������� MODx
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