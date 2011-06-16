<?php get_header(); ?>

  <?php //get_sidebar('left'); ?>
  <?php //get_sidebar('right'); ?>
  <div id="content">
  <style>
  html>/**/body #content {	
	float:none;
}
  </style>
	
	<?php get_template_part( '_search', 'tour' ); ?>
	
	
	<div class="l_search_res">
	
	<div style="height:30px;">
	<!--	<a href="/cabinet/">Личный кабинет</a>-->
	</div>
	<?php
	include ($_SERVER['DOCUMENT_ROOT'].'/tours/offers.php');
	?>
	</div>
	
	<div class="l_search_form" id="l_search_form">
	
		<?php
			$data = $_SERVER['QUERY_STRING'];
			if (!get_magic_quotes_gpc()){
				$data = addslashes ($data);
			}
		?>
		<iframe width="204px;" height="1500px" src="/search/?<?php echo $data ?>" scrolling="no" frameborder="0"  ></iframe>
	</div>	
	
	<?php
		/*	
	<script>
	//var href_text = $('.tp_offers_topline_link').attr('href').replace('/tours/search.php','/poisk-turov/');
	//$('.tp_offers_topline_link').attr('href',href_text );
		
	$data = $_SERVER['QUERY_STRING'];
		if (!get_magic_quotes_gpc()){
			$data = addslashes ($data);
		}
	
	
		
	//----форма поиска-----------
	$.ajax({
		type: "GET",
		url: "/search_form/",
		dataType: "script",
		async:true,
	    data: "<?php echo $data?>",
		success: function(data){
			$('#l_search_form').html(data);
		}
	 });
	//-----------------


	</script>*/
?>
	
	<div class="clear"></div>
  </div>

<?php get_footer(); ?>