<?php
/**
 * Класс для вывода содержимого
 * методом http-include
 * @author Cord
 * @created 15.03.08
 * @modified 03.04.08
 */ 
/**
 * Идентификатор клиента в admin.allspo.ru
 */
define('ALLSPO_ID', 18200);

//-----------------------------
// размещение
$accommodation_arr = array('SGL' => '1 взрослый', 
'SGL+1CHD' => '1 взрослый с ребенком',
'SGL+2CHD' => '1 взрослый с двумя детьми',
'DBL' => '2-е взрослых',
'DBL+1CHD' => '2-е взрослых с ребенком',
'DBL+2CHD' => '2-е взрослых с двумя детьми',
'TRL' => '3-ое взрослых',
'TRL+1CHD' => '3-ое взрослых с ребенком',
'TRL+2CHD' => '3-ое взрослых с двумя детьми',
'QTRL' => '4-ро взрослых',
'QTRL+1CHD' => '4-ро взрослых с одним ребенком',
'QTRL+2CHD' => '4-ро взрослых с двумя детьми',
'5AD+1CHD' => '5 взрослых c ребенком',
'5AD+2CHD' => '5 взрослых c двумя детьми',
'5PPL' => '5 человек в номере',
'6PPL' => '6 человек в номере',
'7PPL' => '7 человек в номере',
'8PPL' => '8 человек в номере',
'9PPL' => '9 человек в номере',
'10PPL' => '10 человек в номере',
'11PPL' => '11 человек в номере',
'12PPL' => '12 человек в номере');


// питание
$food_arr = array(
'AI' => 'AI (питание + напитки местного производства)',
'AI+' => 'AI+ (расш. питание + напитки местного производства)',
'BB' => 'BB (завтрак)',
'BB+Dine Around' => 'BB+Dine Around (завтрак + Dine Around (ужин в ресторане на выбор))',
'BB+Lunch+Dine Around' => 'BB+Lunch+Dine Around (завтрак +обед+ужин в ресторане на выбор)',
'FB' => 'FB (завтрак + обед + ужин)',
'FB+' => 'FB+ (расшир. полн. пансион + напитки во время еды)',
'HB' => 'HB (завтрак + ужин)',
'HB+' => 'HB+ (завтрак + ужин + ограниченное доп. питание)',
'RO' => 'RO (без питания)',
'UAI' => 'UAI (питание целый день + доп. бесплатный сервис)',
'VIP AI' => 'VIP AI (питание целый день + индивидуальное обслуживание)',
'FBT' => 'FBT (завтрак + обед + ужин + лечением)',
'BB+LUNCH' => 'BB+LUNCH (завтрак + обед)',
'HBT' => 'HBT (завтрак + ужин + лечение)',
'BBT' => 'BBT (завтраки + лечение)',
'Dinner' => 'Dinner (Only Dinner)',
'Lunch+Dinner' => 'Lunch+Dinner (ланч + обед)',
'BB+Lunch+Dinner' => 'BB+Lunch+Dinner (завтрак+ланч+обед)');
//-----------------------------


class Webclient { 
	/**
	 * Основной метод, для получения данных,
	 * либо вывода сообщения об ошибки
	 *
	 * @param string $type тип страницы
	 */
	function getPage($type = "", $module = "find_tour") {
		global $accommodation_arr, $food_arr;
		/**
		 * Базовый URL
		 */
		$base_url = "http://www.webclient.touradmin.ru/b/main/" . ALLSPO_ID . "/" . $module . "/";
		
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
		$url = $base_url.$type."?".$_SERVER['QUERY_STRING'];

		$page = file_get_contents($url);
		
		//-------хак----------------
		if($type=='offers' || $type=='search') {
			global $current_user;
			get_currentuserinfo();
		}
		

		//if($current_user->ID)
	
		function change_text($matches) {
			global $current_user;
			
			//echo $matches[1].'<br>';
			//<a target="_blank" href="/tours/offer.php?id=160772196304@212" onClick='ShowWin(this.href,760,450,"offer","yes"); return false;' class="tp_offers_olink robots-nocontent" rel="nofollow">заказ</a>
			
			preg_match('/id=(.*?)"/',$matches[1],$m);
			//print_r($m);
			$tour_id = $m[1];		

			if($current_user->ID) {
				if(is_tour_in_cart($m[1])) {
					$res = '<td><div><a class="add_to_cart" href="'.$m[1].'"><span><b>В корзину</b></span></a></div></td>';
				} else {
					$res = '<td><div>Добавлен в корзину. <br><a href="/cabinet/">Перейти в корзину</a></div></td>';
				}
			} else {
				$res = '<td><div><a class="add_to_cart button_disabled" href="#"><span><b>В корзину</b></span></a></div></td>';
				//$res = '<td>Авторизуйтесь, чтобы бронировать тур</td>';
				//$res = '<td>Авторизуйтесь</td>';
			}
			return $res;
		}
		
		if($type=='offers') {
			$price_format = new classPrice();
			//--------
			$page = preg_replace_callback('/<td>(<a((?!(<tr>|<\/tr>|<td>|<\/td>|<th>|<\/th>))(.+?))заказ<\/a>)<\/td>/', 'change_text', $page);
			
			//------------------------------------
			include_once('./simplehtmldom/simple_html_dom.php');
			$html = str_get_html($page);
			//echo '------------';
			$table_content = $html->find('table[class=tp_offers_tbl]',0);
			$table_page = $html->find('table[class=tp_offers_pager]',1);
			$table_searchtext = $html->find('table[class=tp_offers_topline]',0);
			//------------------вывод постраничной разбивки-------------
			preg_match_all("/<td[^>]+>(.*)<\/td>/sU", $table_page->outertext, $tp_matches);
			//print_r($tp_matches);
			//$table_page_str = $table_page->outertext;
			$table_page_str = '<table class="search_nav"><tr>'.$tp_matches[0][0].'</tr></table>';
			//echo $table_page_str;
			//-------/-----------вывод постраничной разбивки-------------
			
			//---------------вывод строки поиска---------------------------------------------
			//$table_searchtext
			function cut_href($matches) {
				return '';//$matches[1];
			}
			$table_searchtext = preg_replace_callback('/<td[^>]+>(.*)<\/td>/sU',"cut_href",$table_searchtext);
			//------/---------вывод строки поиска---------------------------------------------
			
			//-------------результаты поиска-------------------
			$table_res_arr = array();
			$table_cont_tr = $table_content->find('tr');
			
			$odd = 0;
			foreach($table_cont_tr as $tr_num=>$tmp) {
				if($tr_num==0) continue;
				//echo $tmp->children('1')->plaintext.'<br>';
				//$td_list = $tmp->find('td');
				/*foreach($td_list as $num=> $td) {
					echo $num.'------------'.$td->plaintext.'<br>';
				}*/
				
				$acc = trim($tmp->children('6')->innertext);
				$acc_arr = explode('<br>',$acc);
				$acc1 = trim($acc_arr[0]);
				$acc2 = trim($acc_arr[1]);
				$ff = trim($tmp->children('5')->innertext);
				if($acc1) {
					$accomodation = ($accommodation_arr[$acc1] ? $accommodation_arr[$acc1] : $acc1).($acc2? '<br>'.$acc2: '');
				} else {
					$accomodation = $acc;
				}
				
				$food = ($food_arr[$ff] ? $food_arr[$ff] : $ff);
				
				$table_res_arr[$tr_num][0] = '<b>'.$tmp->children('0')->innertext.'</b><br>'.$tmp->children('1')->innertext;//
				$table_res_arr[$tr_num][1] = $tmp->children('2')->innertext;
				$table_res_arr[$tr_num][2] = $tmp->children('4')->innertext.'<br>'.$tmp->children('3')->innertext;
				$table_res_arr[$tr_num][3] = $food;
				$table_res_arr[$tr_num][4] = $accomodation;
				$table_res_arr[$tr_num][5] = $tmp->children('7')->innertext;
				$table_res_arr[$tr_num][6] = '<div>'.number_format($tmp->children('12')->innertext, 0, '.', ' ').' р.</div><b>'.$price_format->getEuroVal($tmp->children('12')->innertext).'</b>';
				$table_res_arr[$tr_num][7] = $tmp->children('14')->innertext;
				
				if($odd) {
					$table_res_arr[$tr_num]['8_odd'] = 1;
				}
				
				$odd = (!$odd ? 1 : 0);

			}
			
			if(count($table_res_arr)) {
				$table_res = '<table class="search_res_table">';
				$table_res .= '<tr>
							<th class="first th_flyaway">Вылет / Прилет</th>
							<th class="">Ночей</th>
							<th>Отель / Курорт</th>
							<th>Питание</th>
							<th>Размещение</th>
							<th>Номер</th>
							<th class="price">Стоимость</th>
							<th>Действие</th>
								</tr>';
				foreach ($table_res_arr as $tr) {
					$odd = '';
					if(isset($tr['8_odd']) && $tr['8_odd']) {
						$odd = ' class="odd" ';
					}
					$table_res .= '<tr'.$odd.'>';
					foreach($tr as $td_num=>$td) {
						if($td_num=='8_odd') continue;
						
						$class='';
						if($td_num==0) $class=' class="first" ';
						elseif($td_num==6) $class=' class="price" ';
						$table_res .= '<td'.$class.'>'.$td.'</td>';
					}
					$table_res .= '</tr>';
				}
				$table_res .= '</table>';
			}
			//echo $page;
			$page = $table_searchtext.$table_res.$table_page_str;
			//-------/------результаты поиска-------------------

		} elseif($type=='search') {
			include_once('./simplehtmldom/simple_html_dom.php');
			$html = str_get_html($page);
			//echo '------------';
			$td = $html->find('td[class=tp_big_tbl_line_td]');
			//$table_page = $html->find('table[class=tp_offers_pager]',1);
			//$table_page_str = $table_page->outertext;
			
			//tp_big_tbl_line_td 
			$out_text = '
			<script language="JavaScript">
<!--
var BASE_MODULE = "http://www.webclient.touradmin.ru/b/main/18200/find_tour/"; //используется для определения путей в фильтрах (filter_templ_.js)
var rs = new Array(); //массив количества детей по типам размещения, заполняется динамически
var js_url = \'/tours/filter_bigform.php\'; //путь для обновления динамических фильтров набора чекбоксов
var select_filter_url = \'http://www.webclient.touradmin.ru/b/main/18200/find_tour/get_data/\'; //путь для обновления динамических фильтров селектов
var rs_url = \'http://www.webclient.touradmin.ru/b/main/18200/find_tour/rs/\'; //путь для обновления массива rs (детки)
var qs_div_filter = \'?\';
var detail_url = \'\';
var page_id="";
//-->
</script>

			<!--<link rel="stylesheet" href="http://www.webclient.touradmin.ru/b/css/calendar.css" type="text/css">-->
			<link rel="stylesheet" href="'.get_bloginfo('template_url').'/style.css" type="text/css">
			

<script src="http://www.webclient.touradmin.ru/b/js/calendar_utf8.js" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/calendar-design_utf8.js" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/query_utf8.js" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/common_utf8.js" type="text/javascript" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/dynamic_list2_utf8.js" type="text/javascript" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/json_utf8.js" type="text/javascript" charset="UTF-8"></script>

<script src="http://www.webclient.touradmin.ru/b/js/select_utf8.js" type="text/javascript" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/chooser_utf8.js" type="text/javascript" charset="UTF-8"></script>
<script src="/tours/filter_utf8.js" type="text/javascript" charset="UTF-8"></script>
<script src="http://www.webclient.touradmin.ru/b/js/filter_templ_utf8.js" type="text/javascript" charset="UTF-8"></script>

<script src="http://www.webclient.touradmin.ru/b/js/init_form.js" type="text/javascript"></script>
<iframe src="" id="frame_js" name="frame_js" style="display: none;" width="0" height="0"></iframe>

<style type="text/css">body { min-width: 204px;  background-color: #F2F1EE;}</style>

<script>
function inst_submit_all_1(URL, fqs, Mode, myform) { 
	var QS=new Array();
	 QS=GetQS(fqs, myform);
	 
	// alert(QS);

	if (Mode=="popup") { 
		QS += "&ct="+window.parent.document.getElementById("select_ct").value;
		QS += "&co="+window.parent.document.getElementById("select_co").value;
		QS += "&nf="+window.parent.document.getElementById("select_nf").value;
		QS += "&rs="+window.parent.document.getElementById("select_rs").value;
		QS += "&df="+window.parent.document.getElementById("date_df").value;
		QS += "&dt="+window.parent.document.getElementById("date_dt").value;
		window.parent.document.location=URL+QS; 
	} //self.close(); }
	else if (Mode=="new") { window.open(URL+QS,""); }
	else if (Mode=="_top" || Mode=="_blank" || Mode=="_parent") { window.open(URL+QS,Mode); }
	else { document.location=URL+QS; }
	
}
</script>
<div class="search_b_form">
<form action="" name="inst_form" id="inst_form" onsubmit="return false;" style="margin:0">';

$ti_get = (isset($_GET['ti'])? trim($_GET['ti']) : '');
$out_text .= '<table class="tp_big_tbl" cellspacing="3" cellpadding="0" border="0"><tr valign="top">
<td class="tp_big_title">C авиабилетом?</td>
<td class="tp_big_text">
<input type="radio" '.($ti_get=='no'? 'checked' : '').' value="no" name="ti">не важно
<input type="radio" '.($ti_get=='yes'? 'checked' : '').' value="yes" name="ti">да</td>
</tr></table>';
			foreach($td as $num => $tmp) {
				//$tmp_cont = $tmp_1->find('table');
				//echo $num.'-------------'.$tmp;
				$in_text = $tmp->innertext;
				if(strstr($in_text,'Цена до')) {
					$td_title = $tmp->find('td[class=tp_big_title]',0);
					$td_body = $tmp->find('td[class=tp_big_text]',0);
					//$out_text .= str_replace("<script>inst_add_qs_name('pt');</script>", "", $in_text);
					$out_text .= str_replace("<script>inst_add_qs_name('pt');</script>", "",'<table class="tp_big_tbl" cellspacing="3" cellpadding="0" border="0"><tr>'
									.$td_title.$td_body.
									'</tr></table>');
					//echo $out_text.'------------------------------------';
				}
			}
			
			//tp_big_td
			$td_check = $html->find('td[class=tp_big_td]');
			$check_list = array();
			
			foreach($td_check as $num => $tmp) {
				$in_text = $tmp->innertext;
				
				$fl_1 = strstr($in_text,'Курорт:');
				$fl_2 = strstr($in_text,'Питание:');
				$fl_3 = strstr($in_text,'Категория отеля:');
				$fl_4 = strstr($in_text,'Отели:');
				
				if($fl_1 || $fl_2 || $fl_3 || $fl_4) {
					$in_text = preg_replace("/reload_filters\('\w{2}'\);/is", '', $in_text);
					
					/*if($fl_1 || $fl_2 || $fl_4) {
						//---------------------------
						$td_text = $tmp->find('td[class=tp_big_text]',0);
						echo $td_text->innertext;
						echo '===================================';
						//---------------------------
					}*/
					
					if($fl_1) {
						$check_list[2] = $in_text;
					} elseif($fl_2) {
						$check_list[0] = $in_text;
					} elseif($fl_3) {
						$check_list[1] = $in_text;
					} elseif($fl_4) {
						$check_list[3] = $in_text;
					}
					//$out_text .= $in_text;
				}
			}
			ksort($check_list,SORT_NUMERIC);
			$out_text .= implode('',$check_list);
			
			$out_text .= '<table cellspacing="3" cellpadding="0" border="0" style="width:100%" class="tp_big_tbl">
				<tr>
				<td class="tp_big_title">Дети:&nbsp;</td>
				</tr>
				<tr>
				<td class="tp_big_text">
					<table>
						<tr>
							<td>
								1 реб.
							</td>
							<td>
								<input type="text" style="width:55px" class="tp_big_select" value="'.(isset($_GET['ch1']) ? trim($_GET['ch1']) : '').'" maxlength="2" id="ch1" name="ch1">
							</td>
							<td>
								лет
							</td>
						</tr>
						
						<tr>
							<td>
								2 реб.
							</td>
							<td>
								<input type="text" style="width:55px" class="tp_big_select" value="'.(isset($_GET['ch2']) ? trim($_GET['ch2']) : '').'" maxlength="2" id="ch2" name="ch2">
							</td>
							<td>
								лет
							</td>
						</tr>
					</table>
				</td>
				</tr>
				</table>';
			
			$out_text .= '</form>';
			
			//script
			$script = $html->find('script');
			foreach($script as $num => $tmp) {
				$in_text = $tmp->outertext;
				if(strstr($in_text,'var form_name')) {
					$in_text = str_replace("['reloader'] = '1'", "['reloader'] = '0'", $in_text);
					$in_text = str_replace("init_form();", "", $in_text);
					$out_text .= $in_text;
					//echo $in_text.'------------------------------------';
				} elseif(strstr($in_text,'inst_add_qs_name')) {
					$out_text .= $in_text;
				} else {
					//echo $num.'==='.$tmp->outertext.'------------------------------------';
				}
			}
			$out_text .= '<a onclick="inst_submit_all_1(\'/hz\',\'?\',\'popup\',\'inst_form\');  return false;" class="but_calendar_search_1" href="#">Искать</a></div>';
			//style
			/*$style = $html->find('style');
			foreach($style as $num => $tmp) {
				$in_text = $tmp->outertext;
				$out_text .= $in_text;
				//echo $in_text.'------------------------------------';
			}*/
			
			// убрать из всех скриптов reload_filters
			//echo $page;
			$page = $out_text;
			
		}
		//--------------------------

		if(!($page === false)  && strlen($page) > 0) {
			//apply_filters("search_tours_results", $page);
			echo $page;			
		}
		else {
			echo "В настоящее время сервис недоступен";
		}		
	}
}    

?>
