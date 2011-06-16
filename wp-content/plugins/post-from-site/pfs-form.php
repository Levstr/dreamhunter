<?php

/* * *
 * Creates link and postbox (initially hidden with display:none), calls pfs_submit on form-submission.
 * @param string $cat Category ID for posting specifically to one category. Default is '', which allows user to choose from allowed categories.
 * @param string $linktext Link text for post link. Default is set in admin settings, any text here will override that. 
 */
function post_from_site($cat = '', $linktext = ''){
	$pfs_options = get_option('pfs_options');
	//------------
	$idtext = 'foto';
	//-------------
?>
<div id="pfs-post-box-shadow" style="display:none">
	<div class="pfs-post-box" id="pfs-post-box-<?php echo "$idtext"; ?>" style="display:none" class="pfs_postbox">
		<div id="closex">x</div>
		<form class="pfs" id="pfs_form" method="post" action="<?php echo plugins_url('pfs-submit.php',__FILE__); ?>" enctype='multipart/form-data'>
			<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $pfs_options['pfs_maxfilesize'];?>" />
  		<div id="pfs-alert" style='display:none;'></div>

		  <label><strong class="red">Изображения  </strong> (.jpg, каждая не более 2Мб.)
			  <?php if ($pfs_options['pfs_allowimg']) echo "<div id='pfs-imgdiv$idtext'><input type='file' class='multi' name='image[]' accept='jpg|jpeg'/></div>"; ?>
		  </label>

		  <!--<label><strong class="red">Краткое описание *</strong> (Имя, возраст, ситуация на фото и т.п.)
		    <input name="title" id="pfs_title" value="" size="50" />
		  </label>-->

		 <!-- <label>Поля, помеченные знаком <strong style="color:#F0307D">*</strong>, обязательны к заполнению</label>-->

			<div class="clear"></div>
			<input type="submit" id="post" class="submit" name="post" value="Загрузить фото" />
		</form>
		<div class="clear"></div>
		<br/>
	</div>
</div>
<?php
}
?>
