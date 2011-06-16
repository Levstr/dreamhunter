<?php
  $base_url = get_bloginfo('url');
  $template_url = get_bloginfo('template_url');
  
  global $current_user;
  get_currentuserinfo();
  
?>
	
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html <?php language_attributes(); ?>>

<head>
   <meta charset="<?php bloginfo( 'charset' ); ?>" />
  <title><?php wp_title('»', true, 'right'); ?></title>
  
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
  
  <!--[if lte IE 8]><script>var isRunningIE8orLighter = true;</script><![endif]-->
  <!--[if lte IE 7]><script>var isRunningIE7orLighter = true;</script><![endif]-->
  <!--[if lte IE 6]><script>var isRunningIE6orLighter = true;</script><![endif]-->
  
    <?php
    wp_enqueue_style("swfobject", "$template_url/js/swfobject/swfobject.css");
    wp_enqueue_style("facebox", "$template_url/js/facebox/facebox.css");
    wp_enqueue_style("aviaslider", "$template_url/js/aviaslider/aviaslider.css");
    wp_enqueue_style("style", "$template_url/style.css");
	//--------------
	wp_enqueue_style("form", "$template_url/css/form.css");
	wp_enqueue_style("jquery-ui-1.8.11.custom", "$template_url/css/cupertino/jquery-ui-1.8.11.custom.css");
	wp_enqueue_style("calendar", "$template_url/css/calendar.css");
	wp_enqueue_style("tooltip", "$template_url/css/jquery.tooltip.css");
	wp_enqueue_style("jquery.selectbox.css", "$template_url/css/jquery.selectbox.css");
	//------------
    wp_enqueue_conditional_style("ie7", "$template_url/css/ie7.css", "lte IE 7");
    wp_enqueue_conditional_style("ie6", "$template_url/css/ie6.css", "lte IE 6");

    wp_deregister_script(array("jquery", "jquery-ui-core", "swfobject", "facebox", "png", "_TEMPLATE"));
  	//---------------
		
	wp_enqueue_script("jquery", "$template_url/js/jquery-1.5.1.min.js");
	wp_enqueue_script("jquery-ui-1.8.11.custom.min", "$template_url/js/jquery-ui-1.8.11.custom.min.js");
	
	//wp_enqueue_script("custom-form-elements", "$template_url/js/custom-form-elements.js");
	wp_enqueue_script("jquery.ui.datepicker-ru", "$template_url/js/jquery.ui.datepicker-ru.js");
	wp_enqueue_script("jquery.tooltip.min", "$template_url/js/jquery.tooltip.min.js");
	wp_enqueue_script("jquery.selectbox-0.6.1.js", "$template_url/js/jquery.selectbox-0.6.1.js");
	//---------------
    //wp_enqueue_script("jquery-ui-core", "$template_url/js/jquery-ui.min.js");
    wp_enqueue_script("jquery-sizes", "$template_url/js/jquery.sizes.js");
    wp_enqueue_script("innertitle", "$template_url/js/jquery.innertitle.js");
    wp_enqueue_script("swfobject", "$template_url/js/swfobject/swfobject.js");
    wp_enqueue_script("facebox", "$template_url/js/facebox/facebox.js");
    wp_enqueue_script("aviaslider", "$template_url/js/aviaslider/jquery.aviaSlider.js");
    wp_enqueue_script("png", "$template_url/js/DD_belatedPNG.js");
	
    wp_enqueue_script("mousewheel", "$template_url/js/mousewheel.js");
	
	//=========кусок скриптов из сервиса туров===============================
	/* wp_enqueue_script("common_utf8", "$template_url/js/tours/common_utf8.js"); 
	 wp_enqueue_script("select_utf8", "$template_url/js/tours/select_utf8.js"); 
	 wp_enqueue_script("json_utf8", "$template_url/js/tours/json_utf8.js"); 
	 wp_enqueue_script("dynamic_list2_utf8", "$template_url/js/tours/dynamic_list2_utf8.js"); 
	 wp_enqueue_script("tour_script", "$template_url/js/tours/tour_script.js"); */
	//=======================================================================
   		
		
    wp_enqueue_script("main", "$template_url/js/main.js");
	

    wp_head();
	
	dh_show_admin_panel_filter();
  ?>
	
</head>

<body <?php body_class(); ?>>
	<div class="d_form_login" id="form_login_reg">
			<?php
				get_template_part( 'login', 'form' );
			?>
		</div>
		
  <div id="wrapper-main" class="container_16">
    <div id="header">
		<div class="header_cont">
      <a id="logo" href="/"><h1><?php bloginfo('title')?></h1></a>
		<div class="but_cab">
			<?php 
				if(!$current_user->ID) {
					$cabinet_href = 'href="#" id="login_open" ';
					?><script type='text/javascript' src='<?php echo $template_url?>/js/login.js'></script> <?php
				} else {
					$cabinet_href = 'href="/cabinet/" ';
				}
			?>
			<a <?php echo $cabinet_href;?>><span>Личный кабинет</span></a>
        </div>
		
		<div class="d_form_login" id="form_login">
			<?php
				get_template_part( 'login', 'form' );
			?>
		</div>
		<div id="contacts">
			<div class="d_tel">+7(495)<b>783-12-34</b></div>
			<div class="d_icq">12345678910</div>
			<div class="d_skype">dreamhunter</div>
		</div>
		
		<div class="d_work">
			 <?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-help-top','menu_id' => 'nav-help-top','depth' => 1, 'menu_class'=>'menu_help', 'before'=>'&nbsp;|&nbsp;'))?>
			<!--<a href="#">Как работаем?</a> | <a href="#">Чем отличаемся?</a>-->
		</div>
		
		<div class="d_vk" id="d_vk_top">
			<div>
				<?php echo getlinks(8); ?> 
				<!--<a href="#">Мы вконтакте</a>-->
			</div>
		</div>
		
		<div id="d_share">
			<span>Рассказать друзьям:</span>
			<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>
			<span class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="none" data-yashareLink="<?php echo $base_url; ?>/" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></span> 
		</div>
		
		<div class="d_inf_search">
			Поиск и <?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-header-online','menu_id' => 'nav-header-online','depth' => 1))?> путешествий в удобной форме
		</div>
		
		<div class="d_top_buts">
			 <?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-top-but','menu_id' => 'nav-top-but','depth' => 1))?>
			 
			<!--<a class="but_top but_top_1">Поиск туров</a>
			<a class="but_top but_top_2">Горячие предложения</a>
			<a class="but_top but_top_3">Друзья</a>-->
		</div>

		</div>
    </div>

    <div id="main">
