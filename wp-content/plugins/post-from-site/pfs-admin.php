<?php
/* * *
 * What to display in the admin menu
 */
function pfs_settings() { ?>
	<script language="Javascript">
	function filesize_bytes() {
		document.getElementById('pfs_mfs').value = document.getElementById('pfs_mfs').value.toUpperCase();
		var re = /^([0-9.]*)([KMGT]?B)?$/;
		var KB = 1;
		var MB = 2;
		var GB = 3;
		var TB = 4;
		var m = re.exec(document.getElementById('pfs_mfs').value);
		if (m == null) {
			document.getElementById('pfs_mfs').style.border="1px solid #880000";
			document.getElementById('filesize_alert').innerHTML='<?php _e('Default: 3MB','pfs_domain'); echo "<br />"; _e('Not a valid filesize','pfs_domain'); ?>';
		} else {
			var size = 0;
			if (m[2] == null) size = m[1];
			else if (m[2] == 'B') size = m[1];
			else if (m[2] == 'KB') size = m[1]*1024;
			else if (m[2] == 'MB') size = m[1]*1024*1024;
			else if (m[2] == 'GB') size = m[1]*1024*1024*1024;
			else if (m[2] == 'TB') size = m[1]*1024*1024*1024*1024;
			document.getElementById('pfs_mfs').style.border="1px solid #DFDFDF";
			document.getElementById('filesize_alert').innerHTML='<?php _e('Default: 3MB','pfs_domain');?>';
			document.getElementById('pfs_mfsHidden').value = size;
		}
	}
	function genCode(){
		if (document.getElementById("cat").value == ''){cat = "''";} else {cat=document.getElementById("cat").value;}
		linktext = document.getElementById('pfs_indlinktxt').value.replace(/'/g, "\\'");
		linktext = linktext.replace(/"/g, "&amp;quot;");
		document.getElementById('gendCode').innerHTML = "&lt;?php if (function_exists('post_from_site')) {post_from_site("+cat+",'"+linktext+"');} ?&gt;";
	}
	</script>
	<style type='text/css'>
	.pfs th {
		font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
		font-size:12pt;
		font-style:italic;
		font-weight:bold;
	}
	.pfs td {
		font-size:13px;
	}
	</style>
	<div class="wrap pfs">
		<h2><?php _e('Post From Site Settings','pfs_domain'); ?></h2>

		<form method="post" action="options.php" id="options">
			<?php settings_fields('pfs_options'); ?>
			<?php $pfs_options = get_option('pfs_options');?>

			<table class="form-table">
				<tr><td><?php _e('What text do you want do display as the link text?','pfs_domain');?></td><td><input type='text' id='pfs_linktext' name='pfs_options[pfs_linktext]' value='<?php echo $pfs_options['pfs_linktext'];?>' /></td></tr>
				<tr><th colspan='2'><?php _e('User Permissions','pfs_domain');?></th></tr>
				<tr><td><?php _e("What categories can't users post to (ie, which to exclude)?",'pfs_domain');?><br /><small><?php _e('use cat IDs, comma seperated values, please.','pfs_domain');?></small></td><td><input type='text' name='pfs_options[pfs_excats]' value='<?php echo $pfs_options['pfs_excats'];?>' /></td><td class='notes'><?php _e('Default: none','pfs_domain');?></td></tr>
				<tr><td><?php _e('Allow post tags (includes ability to create new tags)?','pfs_domain');?></td><td><select name='pfs_options[pfs_allowtag]'><option value='1' <?php echo ($pfs_options['pfs_allowtag'])?'selected':'';?>><?php _e('Yes','pfs_domain');?></option><option value='0' <?php echo ($pfs_options['pfs_allowtag'])?'':'selected';?>><?php _e('No','pfs_domain');?></option></select></td><td class='notes'></td></tr>
				<tr><td><?php _e('Allow users to upload an image?','pfs_domain');?></td><td><select name='pfs_options[pfs_allowimg]'><option value='1' <?php echo ($pfs_options['pfs_allowimg'])?'selected':'';?>><?php _e('Yes','pfs_domain');?></option><option value='0' <?php echo ($pfs_options['pfs_allowimg'])?'':'selected';?>><?php _e('No','pfs_domain');?></option></select></td><td class='notes'><?php _e("Note: Images automatically uploaded to 'uploads' directory of wp-content -- just like uploading through the write-post/write-page pages.",'pfs_domain');?></td></tr>
				<tr><td><?php _e('Maximum file size for uploaded images?','pfs_domain');?></td><td><input type='text' id='pfs_mfs' onblur='javascript:filesize_bytes()' value='<?php echo display_filesize($pfs_options['pfs_maxfilesize']);?>' /></td><td class='notes' id="filesize_alert"><?php _e('Default: 3MB','pfs_domain');?></td></tr>
				<input type="hidden" id='pfs_mfsHidden' name='pfs_options[pfs_maxfilesize]' value='<?php echo intval($pfs_options['pfs_maxfilesize'])?>' />
				<tr><td><?php _e("Post status? (set to draft or pending if you don't want these posts seen before approval)",'pfs_domain');?></td><td><select name='pfs_options[pfs_post_status]'>
					<option value='draft' <?php echo ('draft'==$pfs_options['pfs_post_status'])?'selected':'';?>><?php _e('Draft','pfs_domain');?></option>
					<option value='pending'<?php echo ('pending'==$pfs_options['pfs_post_status'])?'selected':'';?>><?php _e('Pending','pfs_domain');?></option>
					<option value='publish' <?php echo ('publish'==$pfs_options['pfs_post_status'])?'selected':'';?>><?php _e('Publish','pfs_domain');?></option>
				</select></td><td class='notes'><?php _e('Default: Publish','pfs_domain');?></td></tr>
				<tr><td><?php _e('Comment status? (closed means no one can comment on these posts)','pfs_domain');?></td><td><select name='pfs_options[pfs_comment_status]'>
					<option value='closed' <?php echo ('closed'==$pfs_options['pfs_comment_status'])?'selected':'';?>><?php _e('Closed','pfs_domain');?></option>
					<option value='open' <?php echo ('open'==$pfs_options['pfs_comment_status'])?'selected':'';?>><?php _e('Open','pfs_domain');?></option>
				</select></td><td class='notes'><?php _e('Default: Open','pfs_domain');?></td></tr>
				
				<tr><th colspan='2'><?php _e('Post-box Style','pfs_domain');?></th></tr>
				<tr><td><?php _e('Container background color?','pfs_domain');?></td><td><input type='text' name='pfs_options[pfs_bgcolor]' value='<?php echo $pfs_options['pfs_bgcolor'];?>' /></td><td class='notes'><?php _e('Default: #EDF0CF','pfs_domain');?></td></tr>
				<tr><td><?php _e('Top-left corner image location? (path/to/filename.jpg)','pfs_domain');?><br /><small><?php _e('relative to plugin folder','pfs_domain');?></small></td><td><input type='text' name='pfs_options[pfs_bgimg]' value='<?php echo $pfs_options['pfs_bgimg'];?>' /></td><td class='notes'><?php _e('Default: pfs_title.png','pfs_domain');?></td></tr>
				<tr><td><?php _e('Title text color?','pfs_domain');?></td><td><input type='text' name='pfs_options[pfs_titlecolor]' value='<?php echo $pfs_options['pfs_titlecolor'];?>' /></td><td class='notes'><?php _e('Default: none (inherited)','pfs_domain');?></td></tr>
				<tr><td><?php _e('Regular text color?','pfs_domain');?></td><td><input type='text' name='pfs_options[pfs_textcolor]' value='<?php echo $pfs_options['pfs_textcolor'];?>' /></td><td class='notes'><?php _e('Default: black','pfs_domain');?></td></tr>
				<tr><td><?php _e('Add your own CSS:','pfs_domain');?></td><td colspan='2'><textarea name='pfs_options[pfs_customcss]' rows='5' cols='50'><?php echo $pfs_options['pfs_customcss'];?></textarea></td></tr>
			</table>
			
			<p class="submit">
				<input type="submit" name="Submit" value="<?php _e('Save Changes','pfs_domain') ?>" />
			</p>
		</form>
		
		<h2><?php _e('Installation','pfs_domain');?></h2>
		<p><?php _e('Add the following code wherever you want the link to appear in your theme.','pfs_domain');?></p>
		<p><code>&lt;?php if (function_exists('post_from_site')) {post_from_site();} ?&gt;</code></p>
		<p><?php _e('To generate individual links to specific category posts:','pfs_domain');?> <small><?php _e("(like, 'click here to post in the general category')",'pfs_domain');?></small></p> 
		<p><?php _e('Category:','pfs_domain');?> <select id="cat" class="postform"><?php 
			$categories = wp_dropdown_categories("echo=0&hide_empty=0");
			preg_match_all('/\s*<option class="(\S*)" value="(\S*)">(.*)<\/option>\s*/', $categories, $matches, PREG_SET_ORDER);
			echo "<option class='{$matches[0][1]}' value=''></option>";
			foreach ($matches as $match) echo $match[0]; 
		?></select> &nbsp; <?php _e('Link Text:','pfs_domain'); ?> <input type="text" id="pfs_indlinktxt" /> &nbsp; <input type="submit" value="<?php _e('generate code','pfs_domain');?>" onclick="javascript:genCode();"/></p>
		<p><code id="gendCode"></code></p>
	</div>
<?php 
}
?>
