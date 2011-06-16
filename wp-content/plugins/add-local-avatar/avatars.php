<?php
/*
	Plugin Name:	Avatars
	Plugin URI:		http://www.sterling-adventures.co.uk/blog/2008/03/01/avatars-plugin/
	Description:	A plugin to manage public and private avatars.
	Author:			Peter Sterling
	Version:		10.0
	Changes:		0.1 -	Initial release.
					1.0 -	Added pagination of users list.
					2.0 -	Added pagination of the commenters list too.
					2.1 -	Added example formatting information.
					3.0 -	Added ability to place avatars in written post content (plus other tweaks).
					3.1 -	Minor tweaks to usage text and options.
					3.2 -	Added check for administration pages to stop user URL wrapping breaking comment editing.
					3.3 -	Spelling fixes!
					4.0 -	Wavatar, Monster ID and Identicon can be used.
					4.1 -	Author credit.
					4.2 -	Fix for credit option un-setting.
					5.0 -	Avatar options should only be managed by Administrators.
					5.1 -	Minor fix to repetition of show avatars WordPress setting.
					5.2 -	Cope with WP 2.6 avatar default.
					6.0 -	Added feature to allow users to upload their own avatar.
					6.1 -	Explanation of directory structure and 'chmod' fix, thanks to Tobias Schwarz.
					6.2 -	Improved unique file name creation, optional avatar upload resizing/cropping, and PHP 4 fix. Thanks to Gioele Agostinelli.
					6.3 -	Oops, a bug (mistake) with the scaling size fixed.
					6.4 -	Error in file naming fixed, with some help from "noyz319".
					6.5 -	Upload file type check (thanks to SumoSulsi) and internationalisation preparation.
					6.6 -	Fix to scaling when upgrading from old version of plugin without scaling option.
					6.7 -	Fix for uppercase extensions.
					6.8 -	Option for nickname / first name & surname.
					7.0 -	Support for user profile widget plug-in.
					7.1 -	Update for Marc Adrian to provide support for option for showing text in the optional widget.
					7.2 -	Class added to help with styling widget.
					7.3 -	Fix for user avatar upload that doesn't need re-sizing and a Russian translation thanks to Fatcow - http://www.fatcow.com/
					7.4 -	Root directory no longer DOCUMENT_ROOT.
					7.5 -	Use DOCUMENT_ROOT option for legacy users.
					7.6 -	Check for required core WP upload functions, only required for themes that expose the user profile pages.
					8.0 -	Added option to try to use a Twitter avatar.
					8.1 -	Simplified Twitter image URL logic.
					8.2 -	Control anchor wrapping of Avatars.
					8.3 -	Allow Twitter ID for optional widget.
					9.0 -	WPMU/Network re-work.  Thanks to Michael D. Tran for his efforts!
					9.1 -	Update for Admin Bar in WordPress v3.1
					9.2 -	Fix for local avatar upload to cope with the ever changing WP!
					10.0 -	New option to upsize local avatar images that are smaller than the set size.  Thanks to Nicholas Craig.
	Author URI:		http://www.sterling-adventures.co.uk/blog/
*/

define('AVATAR_VER', '10.0');															// Plug-in version.
define('UNKNOWN', 'unknown@gravatar.com');												// Unknown e-mail.
define('BLANK', 'blank');																// Blank e-mail.
define('FALLBACK', 'http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536');	// Fallback Gravatar URL (blank man).
define('SCALED_SIZE', '80');															// Default size to scale uploads to.
define('TWITTER_URL', 'http://api.twitter.com/1/users/show.xml?id=');					// Twitter's XML URL.
define('TWITTER_STATIC', 'static.twitter.com');											// URL tell-tale for default Twitter avatar.

define('TYPE_TWITTER',	'T');															// Avatar types.
define('TYPE_GLOBAL',	'G');
define('TYPE_LOCAL',	'L');



class add_local_avatars {

	public $avatar_options = '';
	private $networked = false;		// Are we in WP Network mode or is this a WPMU install?
	private $requestURI = "";		// If installed under mu-plugins, then the call is different, full path from the root.

	function __construct()
	{
		if (function_exists("is_multisite")) {	// WP 3+
			$this->networked = is_multisite();
		}
		elseif (defined("WPMU")) {				// WPMU
			if (WPMU) {
				$this->networked = true;
			}
		}
		
		$this->requestURI = $_SERVER['SCRIPT_NAME'] . '?page=' . @$_GET['page'];
	
		// Default options...
		$this->avatar_options = ($this->networked) ? get_site_option('plugin_avatars') : get_option('plugin_avatars') ;
		if(!is_array($this->avatar_options)) {
			// Options do not exist or have not yet been loaded so we define standard options...
			$this->avatar_options = array(
				'size' => '30',
				'scale' => SCALED_SIZE,
				'resize' => 'off',
				'upsize' => 'off',
				'snapshots' => 'off',
				'in_posts' => 'on',
				'credit' => 'on',
				'twitter' => 'off',
				'default' => '',
				'upload_dir' => '',
				'name' => 'on',
				'widget_enabled' => 'on',
				'url_wrap' => 'on',
				'location' => 'website',
				'legacy' => 'off',
				'upload_allowed' => 'Y'
			);
			if ($this->networked) {
				update_site_option('plugin_avatars', $this->avatar_options);
			}
			else {
				update_option('plugin_avatars', $this->avatar_options);
			}
		}
		if(!isset($this->avatar_options['credit'])) {
			$this->avatar_options['credit'] = 'on';
			
			if ($this->networked) {
				update_site_option('plugin_avatars', $this->avatar_options);
			}
			else {
				update_option('plugin_avatars', $this->avatar_options);
			}
		}

	
		// User profile widget included?
		if(file_exists(ABSPATH . '/wp-content/plugins/add-local-avatar/avatars-widget.php')) {
			include(ABSPATH . '/wp-content/plugins/add-local-avatar/avatars-widget.php');
			if(function_exists('avatars_logged_in_user_widget_init')) {
				if($this->avatar_options['widget_enabled'] == 'on') {
					add_action('plugins_loaded', 'avatars_logged_in_user_widget_init');
				}
			}
		}
		
		// Hooks...
		add_action('admin_menu', array(&$this, 'avatar_menu'));
		add_filter('the_content', array(&$this, 'generate_avatar_in_posts'));
		add_action('get_footer', array(&$this, 'avatar_footer'));
		add_action('show_user_profile', array(&$this, 'avatar_uploader_option'));
		add_action('edit_user_profile', array(&$this, 'avatar_uploader_option'));
		add_action('profile_update', array(&$this, 'avatar_upload'));
		add_action('init', array(&$this, 'set_avatars_textdomain'));
	
	} // end constructor

		
		
	// Helper function to find root directory.
	function avatar_root()
	{
		if ($this->avatar_options['legacy'] == 'on') {
			return $_SERVER['DOCUMENT_ROOT'];
		}
		else {
			return substr(ABSPATH, 0, -strlen(strrchr(substr(ABSPATH, 0, -1), '/')) - 1);
		}
	}




	// Set up the required text domain for the chosen language.
	function set_avatars_textdomain()
	{
		$test = WPLANG;
		if(!empty($test)) {
			load_plugin_textdomain('avatars', 'wp-content/plugins/add-local-avatar');
		}
	}


	// Try to get a Twitter avatar.
	function get_twitter_avatar($email)
	{
		// CURL will be used to open the URL and retrieve the user info based on the email.
		$ch = curl_init(TWITTER_URL . urlencode($email));

		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($ch);
		$response_info = curl_getinfo($ch);
		curl_close($ch);

		$data = false;

		if(intval($response_info['http_code']) == 200) {
			$data = new SimpleXMLElement($response);
		}

		if($data === false) return "";

		// Check Twitter URI for default tell-tale.
		if(strpos($data->profile_image_url, TWITTER_STATIC) !== false) return "";

		// Complete image url...
		return $data->profile_image_url;
	}


	// Helper function to select correct default avatar image.
	function check_switch($chk, $default, $size = SCALED_SIZE)
	{
		switch ($chk) {
			case 'custom': return $default;
			case 'mystery': return urlencode(FALLBACK . "?s=" . $size);
			case 'blank': return includes_url('images/blank.gif');
			case 'gravatar_default': return "";
			default: return urlencode($chk);
		}
	}


	// Return the type of avatar last returned.
	function get_avatar_type()
	{
		global $avatar_type;

		switch($avatar_type) {
			case TYPE_TWITTER:	return __('Twitter', 'avatars');
			case TYPE_GLOBAL:	return __('Global', 'avatars');
			case TYPE_LOCAL:	return __('Local', 'avatars');
			default:			return __('Unknown (!)', 'avatars');
		}
	}


	// Manage avatars...
	function manage_avatar_cache()
	{
		global $wpdb;

		$msg = '';

		// Show commenter avatars too?
		$all = (@$_GET['act'] == 'all');

		// Check table updates...
		if(isset($_GET['user_id'])) {
			$msg = __('Avatar', 'avatars') . ' ' . (empty($_GET['avatar']) ? __('removed', 'avatars') : __('updated', 'avatars')) . '.';
			update_usermeta($_GET['user_id'], 'avatar', $_GET['avatar']);
			update_usermeta($_GET['user_id'], 'twitter_id', $_GET['twitter_id']);
		}

		// Check form submission and update options...
		if(isset($_POST['submit'])) {
			$options_update = array (
				'size' => $_POST['size'],
				'scale' => $_POST['scale'],
				'resize' => $_POST['resize'],
				'upsize' => $_POST['upsize'],
				'snapshots' => $_POST['snapshots'],
				'in_posts' => $_POST['in_posts'],
				'credit' => $_POST['credit'],
				'twitter' => $_POST['twitter'],
				'default' => $_POST['default'],
				'upload_dir' => $_POST['upload_dir'],
				'url_wrap' => $_POST['url_wrap'],
				'name' => $_POST['name'],
				'location' => $_POST['location'],
				'legacy' => $_POST['legacy'],
				'widget_enabled' => $_POST['widget_enabled'],
				'upload_allowed' => $_POST['upload_allowed']
			);
			
			if ($this->networked) {
				update_site_option('plugin_avatars', $options_update);
			}
			else {
				update_option('plugin_avatars', $options_update);
				update_option('show_avatars', ($_POST['show_avatars'] == 'on' ? 1 : 0));
				update_option('avatar_rating', $_POST['avatar_rating']);
				update_option('avatar_default', $_POST['wavatar']);
			}
			
			$msg = __('Options saved', 'avatars');
		} // end if

		// Get options and set form action var
		if ($this->networked) {
			$this->avatar_options = get_site_option('plugin_avatars');
			$form_action = $_SERVER['PHP_SELF'] . '?page=add_local_avatar&updated=true';
		}
		else {
			$this->avatar_options = get_option('plugin_avatars');
			$wavatar = get_option('avatar_default');
			$form_action = $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__) . '&updated=true';
		}

		// Output any action message (note, can only be from a POST or GET not both).
		if(!empty($msg)) echo "<div id='message' class='updated fade'><p>", $msg, "</p></div>";
	?>
		<script language="Javascript">
			function set_input_values(num)
			{
				var h = document.getElementById('href-' + num);
				h.href = h.href + '&avatar=' + document.getElementById('avatar-' + num).value;
				h.href = h.href + '&twitter_id=' + document.getElementById('twitter_id-' + num).value;
			}
		</script>

		<div class="wrap">
			<h2><?php echo __('Avatar Settings', 'avatars'); ?> (v<?php echo AVATAR_VER; ?>)</h2>
			<?php
				if ($this->networked && strpos(__FILE__, WPMU_PLUGIN_DIR) === false) {
					// display a warning if in networked mode and not installed under mu-plugins
					echo "<div class='updated'><p>" . __("WPMU/Network mode detected, you should install this into your mu-plugins folder.", "avatars") . "</p></div>";
				}
			?>
			<p>
				<?php echo __("Please visit the author's site,", 'avatars'); ?> <a href='http://www.sterling-adventures.co.uk/blog/' title='Sterling Adventures'>Sterling Adventures</a>, <?php echo __('and say "Hi"', 'avatars'); ?>...<br />
				<?php echo __('Control the behaviour of the avatar plug-in.', 'avatars'); ?>
			</p>
			
			<h3><?php echo __('User Avatars', 'avatars'); ?></h3>
			<?php
			
				// do not show the table of this site's users/commentors if avatars are disabled
				// instead place a hyperlink for them to enable it 
				if (!get_option('show_avatars')) {
					echo __('Avatars have been disabled for this site.  Enable avatars under <a href="/wp-admin/options-discussion.php">Settings &gt; Discussion</a>', "avatars");
				}
				else {
					
					$user_search = new WP_User_Search('', $_GET['userspage'], '');

					// Do we have to page the results?
					if($user_search->total_users_for_query > $user_search->users_per_page) {
						if ($this->networked)
							$paging_base = basename($this->requestURI) . '&amp;%_%';
						else
							$paging_base = 'users.php?page=avatars.php&amp;%_%';
						
						$user_search->paging_text = paginate_links(array(
							'total' => ceil($user_search->total_users_for_query / $user_search->users_per_page),
							'current' => $user_search->page,
							'base' => $paging_base,
							'format' => 'userspage=%#%'
						));
					}

					// How many per page (for commenters, if shown)?
					$per_page = $user_search->users_per_page;
			?>

			<div class="tablenav">
				<?php if($user_search->results_are_paged()) : ?>
					<div class="tablenav-pages"><?php $user_search->page_links(); ?></div>
				<?php endif; ?>
			</div>

			<table class='widefat'>
				<thead>
					<tr>
						<th><?php echo __('Username', 'avatars'); ?></th>
						<th><?php echo __('Name (Nickname)', 'avatars'); ?></th>
						<th><?php echo __('e-Mail', 'avatars'); ?></th>
						<th><?php echo __('Twitter ID', 'avatars'); ?></th>
						<th><?php echo __('Local', 'avatars'); ?></th>
						<th style="text-align: center;"><?php echo __('Avatar', 'avatars'); ?></th>
						<th><?php echo __('Type', 'avatars'); ?></th><th><?php echo __('Action', 'avatars'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					$i = 0;
					foreach($user_search->get_results() as $id) {
						$user = new WP_User($id);
						echo '<tr', ($i % 2 == 0 ? " class='alternate'" : ""), '>' .
							'<td><a href="', 'user-edit.php?user_id=', $id, '">', $user->user_login, '</a></td>' .
							'<td>', $user->first_name, ' ', $user->last_name, (empty($user->nickname) ? '-' : ' (' . $user->nickname . ')'), '</a></td>' .
							'<td><a href="mailto:', $user->user_email, '">', $user->user_email, '</td>' .
							'<td><input type="text" value="', $user->twitter_id, '" size="35" id="twitter_id-', $i, '" /></td>' .
							'<td><input type="text" value="', $user->avatar, '" size="35" id="avatar-', $i, '" /></td>' .
							'<td style="text-align: center;">', get_avatar($id), '</td>' .
							'<td>', $this->get_avatar_type(), '</td>' .
							'<td><a href="' . $this->requestURI . '&amp;user_id=', $id, '" class="edit" onclick="set_input_values(', $i, ');" id="href-', $i, '">', __('Update', 'avatars'), '</a></td>' .
							"</tr>\n";
						$i++;
					}
				?>
				</tbody>
			</table>

			<div class="tablenav">
				<?php if($user_search->results_are_paged()) : ?>
					<div class="tablenav-pages"><?php $user_search->page_links(); ?></div>
				<?php endif; ?>
			</div>

			<p><?php
					if(!$all) echo __("Not showing avatars for commenters.", 'avatars'), " <a href='" . $this->requestURI . "&amp;act=all'>", __('Click here', 'avatars'), '</a> ', __("to show commenter avatars.", 'avatars');
					else echo __("Showing avatars for commenters.", 'avatars'), " <a href='" . $this->requestURI . "'>", __('Click here', 'avatars'), '</a> ', __("to hide commenter avatars.", 'avatars');
			?></p>
			<?php
					$com_page = (isset($_GET['comspage']) ? $_GET['comspage'] : 1);

					if($all) {
						$total = $wpdb->get_var("select count(distinct comment_author_email) from $wpdb->comments where comment_author_email != ''");
						$limit_start = ($com_page - 1) * $per_page;
						$coms = $wpdb->get_results("select comment_author_email EML, comment_author ATH, count(comment_content) CNT from $wpdb->comments where comment_author_email != '' group by comment_author_email order by CNT DESC limit $limit_start, $per_page");

						if($total > $per_page) {
							$paging_text = paginate_links(array(
								'total' => ceil($total / $per_page),
								'current' => $com_page,
								'base' => 'users.php?page=avatars.php&amp;act=all&amp;%_%',
								'format' => 'comspage=%#%'
							));
						}

						if($coms) { ?>
							<h3><?php echo __('Commenter Avatars', 'avatars'); ?></h3>

							<div class="tablenav">
								<?php if($paging_text) : ?>
									<div class="tablenav-pages"><?php echo $paging_text; ?></div>
								<?php endif; ?>
								<br class="clear" />
							</div>
							<br class="clear" />

							<table class='widefat'>
								<thead>
									<tr><th><?php echo __('Name', 'avatars'); ?></th><th><?php echo __('e-Mail', 'avatars'); ?></th><th class="num"><div class="vers"><img alt="Comments" src="images/comment-grey-bubble.png" /></div></th><th style="text-align: center;"><?php echo __('Avatar', 'avatars'); ?></th></tr>
								</thead>
								<tbody> <?php
								$i = 0;
								foreach($coms as $com) if(!empty($com->EML)) {
									echo '<tr', ($i % 2 == 0 ? " class='alternate'" : ""), '>' .
										'<td>', $com->ATH, '</td>' .
										'<td><a href="mailto:', $com->EML, '">', $com->EML, '</td>' .
										'<td class="num"><div class="post-com-count-wrapper post-com-count"><span class="comment-count">', $com->CNT, '</span></div></td>' .
										'<td style="text-align: center;">', get_avatar($com->EML), '</td>' .
										"</tr>\n";
									$i++;
								} ?>
								</tbody>
							</table>

							<div class="tablenav">
								<?php if($paging_text) : ?>
									<div class="tablenav-pages"><?php echo $paging_text; ?></div>
								<?php endif; ?>
								<br class="clear" />
							</div>
							<br class="clear" />
						<?php }
						
					} // end if 
					
				} // end if 
			?>

			<h3><?php echo __('Avatar Options', 'avatars'); ?></h3>
			<form method="post" action="<?php echo $form_action; ?>">
				<table class='form-table'>
				<?php
					if (!$this->networked) :
						// do not repeat Settings > Discussion options in networked mode
				?>
					<tr>
						<td><label for="show_avatars"><?php echo __('Show avatars:', 'avatars'); ?></label><br /><small><?php echo __('Repeated from <i>Settings  &raquo;  Discussion</i>', 'avatars'); ?></small></td>
						<td><input type="checkbox" name="show_avatars" <?php echo (get_option('show_avatars') ? 'checked' : ''); ?> /></td>
						<td><small><?php echo __('Enable Avatars.', 'avatars'); ?></small></td>
					</tr>
					<tr>
						<td><?php echo __('Avatar rating:', 'avatars'); ?><br /><small><?php echo __('Repeated from <i>Settings  &raquo;  Discussion</i>', 'avatars'); ?></small></td>
						<td>
							<input type='radio' name='avatar_rating' value='G'  <?php echo (get_option('avatar_rating') == 'G'  ? 'checked="checked"' : ''); ?> /> G <br />
							<input type='radio' name='avatar_rating' value='PG' <?php echo (get_option('avatar_rating') == 'PG' ? 'checked="checked"' : ''); ?> /> PG<br />
							<input type='radio' name='avatar_rating' value='R'  <?php echo (get_option('avatar_rating') == 'R'  ? 'checked="checked"' : ''); ?> /> R <br />
							<input type='radio' name='avatar_rating' value='X'  <?php echo (get_option('avatar_rating') == 'X'  ? 'checked="checked"' : ''); ?> /> X
						</td>
						<td>
							<small>- <?php echo __('Suitable for all audiences', 'avatars'); ?></small><br />
							<small>- <?php echo __('Possibly offensive, usually for audiences 13 and above', 'avatars'); ?></small><br />
							<small>- <?php echo __('Intended for adult audiences above 17', 'avatars'); ?></small><br />
							<small>- <?php echo __('Even more mature than above', 'avatars'); ?></small>
						</td>
					</tr>
				<?php
					endif;
				?>
					<tr>
						<td><label for="idAvatarSize"><?php echo __('Size:', 'avatars'); ?></label></td>
						<td style="width: 70px;"><select id="idAvatarSize" name='size'><?php
							for ($i = 10; $i <= 80; $i = $i + 10) {
								echo "<option value='$i'";
								if($i == $this->avatar_options['size']) echo " selected";
								echo ">$i</option>";
							}
						?></select></td>
						<td><label for="idAvatarSize">px</label></td>
					</tr>
					<?php if(class_exists('SimpleXMLElement')) { ?>
						<tr>
							<td><label for="idTwitter"><?php echo __('Twitter Avatar:', 'avatars'); ?></label></td>
							<td><input type="checkbox" id="idTwitter" name="twitter" <?php echo $this->avatar_options['twitter'] == 'on' ? 'checked' : ''; ?> /></td>
							<td><small><label for="idTwitter">
								<?php
									echo __('Try to use <a href="http://twitter.com/" target="_blank">Twitter</a> avatar if no local is avatar defined.', 'avatars');
									echo ' ', __('Order of precedence is; <i>Local</i>, <i>Twitter</i>, <i>Global</i>.', 'avatars');
								?>
							</label></small></td>
						</tr>
					<?php 
						} 
					
						if (!$this->networked) {
					?>
					<tr>
						<td><?php echo __('Gravatar default:', 'avatars'); ?><br /><small><?php echo __('Enhanced repeat from <i>Settings &raquo; Discussion</i>', 'avatars'); ?></small></td>
						<td><?php echo get_avatar($wavatar, $this->avatar_options['size'], $wavatar); ?></td>
						<td>
							<select name='wavatar'>
								<option value="custom" <?php echo ($wavatar == 'custom' ? 'selected' : ''); ?> >- <?php echo __('none', 'avatars'); ?> -</option>
								<option value="mystery" <?php echo ($wavatar == 'mystery' ? 'selected' : ''); ?> ><?php echo __('Mystery Man', 'avatars'); ?></option>
								<option value="blank" <?php echo ($wavatar == 'blank' ? 'selected' : ''); ?> ><?php echo __('Blank', 'avatars'); ?></option>
								<option value="gravatar_default" <?php echo ($wavatar == 'gravatar_default' ? 'selected' : ''); ?> >Gravatar <?php echo __('Logo', 'avatars'); ?></option>
								<option value="wavatar" <?php echo ($wavatar == 'wavatar' ? 'selected' : ''); ?> >Wavatar</option>
								<option value="monsterid" <?php echo ($wavatar == 'monsterid' ? 'selected' : ''); ?> >Monster ID</option>
								<option value="identicon" <?php echo ($wavatar == 'identicon' ? 'selected' : ''); ?> >Identicon</option>
							</select>
							<br />
							<small><?php echo __('Give users without Global or Local avatars a unique avatar.', 'avatars'); ?></small>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td><label for="idAvatarDefault"><?php echo __('Default image:', 'avatars'); ?></label></td>
						<td><?php echo get_avatar('', '', $this->avatar_options['default']); ?></td>
						<td>
							<input type='text' name='default' id="idAvatarDefault" value='<?php echo $this->avatar_options['default']; ?>' size='70' />
							<br />
							<small><?php echo __('The default avatar (a working URI) for users without Global or Local avatars.  Used for trackbacks.', 'avatars'); ?></small>
						</td>
					</tr>
					<tr>
						<td><label for="idAvatarSnapshots"><?php echo __('Use Snapshots:', 'avatars'); ?></label></td>
						<td><input type="checkbox" id="idAvatarSnapshots" name="snapshots" <?php echo $this->avatar_options['snapshots'] == 'on' ? 'checked' : ''; ?> /></td>
						<td><label for="idAvatarSnapshots"><small><?php echo __('If you have enabled', 'avatars'); ?> <a href="http://www.snap.com">snapshots</a>, <?php echo __('clearing this will disable them for avatar links.', 'avatars'); ?></small></label></td>
					</tr>
					<tr>
						<td><label for="idAvatarInPosts"><?php echo __('Avatars in posts:', 'avatars'); ?></label></td>
						<td><input type="checkbox" name="in_posts" id="idAvatarInPosts" <?php echo $this->avatar_options['in_posts'] == 'on' ? 'checked' : ''; ?> /></td>
						<td><label for="idAvatarInPosts"><small><?php echo __('Replaces', 'avatars'); ?> </small><code>&lt;!-- avatar <b>e-mail</b> --&gt;</code><small> <?php echo __('with an avatar for that email address in post content.', 'avatars'); ?></small></label></td>
					</tr>
					<tr>
						<td><label for="idAvatarUploadAllowed"><?php echo __('User uploads:', 'avatars'); ?></label></td>
						<td><input type="checkbox" id="idAvatarUploadAllowed" name="upload_allowed" <?php echo $this->avatar_options['upload_allowed'] == 'on' ? 'checked' : ''; ?> /></td>
						<td>
							<input type='text' id="idAvatarUploadDir" name='upload_dir' value='<?php echo $this->avatar_options['upload_dir']; ?>' size='70' />
							<br />
							<small><label for="idAvatarUploadDir"><?php echo __('If allowed, use this directory for user avatar uploads, e.g.', 'avatars'); ?> <code>/avatars</code>. <?php echo __('Must have write access and is relative to ', 'avatars'); ?><code><?php echo $this->avatar_root(); ?></code>.</label></small>
							<br />
							<label for="idAvatarLegacy">Or, use legacy (v7.3 and lower) <code>$_SERVER['DOCUMENT_ROOT']</code> method </label><input type="checkbox" id="idAvatarLegacy" name="legacy" <?php echo $this->avatar_options['legacy'] == 'on' ? 'checked' : ''; ?> />, <?php echo __('this option often helps when using sub-domains.', 'avatars'); ?>
						</td>
					</tr>
					<tr>
						<td><label for="idAvatarResize"><?php echo __('Resize uploads:', 'avatars'); ?></label></td>
						<td><input type="checkbox" id="idAvatarResize" name="resize" <?php if($this->avatar_options['upload_allowed'] != 'on') echo 'disabled="true"'; ?> <?php echo $this->avatar_options['resize'] == 'on' ? 'checked' : ''; ?> /></td>
						<td>
							<label for="idAvatarResize"><small><?php echo __('Non-square uploads will be cropped.', 'avatars'); ?></small></label>
							<br />
							<input type="checkbox" id="idAvatarUpsize" name="upsize" <?php echo $this->avatar_options['upsize'] == 'on' ? 'checked' : ''; ?> /> <label for="idAvatarUpsize"><?php echo __('pad images smaller than <i>resize</i> set below with a white background?  This option stops small images becoming pixelated.'); ?></label>
						</td>
					</tr>
					<tr>
						<td><label for="idAvatarResizeSize"><?php echo __('Resize uploads size:', 'avatars'); ?></label></td>
						<td><select name='scale' id="idAvatarResize" <?php if($this->avatar_options['resize'] != 'on' || $this->avatar_options['upload_allowed'] != 'on') echo 'disabled="true"'; ?>><?php
							if(empty($this->avatar_options['scale'])) $def = true;
							else $def = false;
							for ($i = $this->avatar_options['size']; $i <= 200; $i = $i + 10) {
								echo "<option value='$i'";
								if($i == $this->avatar_options['scale'] || ($def && $i == SCALED_SIZE)) echo " selected";
								echo ">$i</option>";
							}
						?></select></td>
						<td><label for="idAvatarResize">px</label></td>
					</tr>
					<tr>
						<?php if(file_exists(ABSPATH . '/wp-content/plugins/add-local-avatar/avatars-widget.php')) { ?>
							<td><?php echo __('Enable user profile widget:', 'avatars'); ?></td>
							<td><input type="checkbox" name="widget_enabled" <?php echo $this->avatar_options['widget_enabled'] == 'on' ? 'checked' : ''; ?> /></td>
							<td><small><?php echo __('Enable the user profile widget; configure the widget at <i>Appearance &raquo; Widgets</i>.', 'avatars'); ?></small></td>
						<?php } else { ?>
							<td colspan="3"><?php echo __('Get the user profile widget at the <a href="http://www.sterling-adventures.co.uk/blog/2008/03/01/avatars-plugin/">Avatars Home Page</a>, and enable avatar upload from your blog sidebar.', 'avatars'); ?></td>
						<?php } ?>
					</tr>
					<tr>
						<td><label for="idWrapAvatar"><?php echo __('Wrap Avatars with URL:', 'avatars'); ?></label></td>
						<td><input type="checkbox" id="idWrapAvatar" name="url_wrap" <?php echo $this->avatar_options['url_wrap'] == 'on' ? 'checked' : ''; ?> /></td>
						<td><small><label for="idWrapAvatar"><?php echo __("Wrap Avatar with URL (from User's profile or Comment form data).", 'avatars'); ?></label></small></td>
					</tr>
					<tr>
						<td><label for="idNickName"><?php echo __('Nickname:', 'avatars'); ?></label></td>
						<td><input type="checkbox" name="name" <?php echo $this->avatar_options['name'] == 'on' ? 'checked' : ''; ?> id="idNickName" /></td>
						<td>
							<input type='text' name='location' value='<?php echo empty($this->avatar_options['location']) ? 'website' : $this->avatar_options['location']; ?>' size='10' id="idNickLocation" />
							<br />
							<small><label for="idNickLocation"><?php echo __("User's nickname used for avatar titles (tooltip).", 'avatars'); ?></label></small>
						</td>
					</tr>
					<tr>
						<td><?php echo __('Credit:', 'avatars'); ?><br /><small><?php echo __("Link to the author if you value the plugin.", 'avatars'); ?></small></td>
						<td>
							<input type='radio' name='credit' value='vis' <?php echo ($this->avatar_options['credit'] == 'vis' ? 'checked="checked"' : ''); ?> id='idCreditVis' /><label for="idCreditVis">&nbsp;<?php echo __('Visible', 'avatars'); ?></label><br />
							<input type='radio' name='credit' value='on'  <?php echo ($this->avatar_options['credit'] == 'on'  ? 'checked="checked"' : ''); ?> id='idCreditOn'  /><label for="idCreditOn">&nbsp;<?php echo __('Hidden', 'avatars'); ?></label><br />
							<input type='radio' name='credit' value='off' <?php echo ($this->avatar_options['credit'] == 'off' ? 'checked="checked"' : ''); ?> id='idCreditOff' /><label for="idCreditOff">&nbsp;<?php echo __('None', 'avatars'); ?></label><br />
						</td>
						<td>
							<small>- <?php echo __('Includes a visible credit. Customise the style in', 'avatars'), ' <code>', dirname(__FILE__); ?>/avatars.css</code>.</small><br />
							<small>- <?php echo __('Includes an invisible credit. Invisibile to preserve the <i>look</i> of your WP theme.', 'avatars'); ?></small><br />
							<small>- <?php echo __('No credit.', 'avatars'); ?></small>
						</td>
					</tr>
				</table>
				<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php echo __('Update Avatar Options', 'avatars'); ?>" /></p>
			</form>

			<h3><?php echo __('Avatars Usage', 'avatars'); ?></h3>
			<p><?php echo __('Put this code in your template files where you want avatars to appear:', 'avatars'); ?><br />
			<code>&lt;?php $avtr = get_avatar(id [, size [, default-image-url]]); echo $avtr; ?&gt;</code></p>
			<p><?php echo __('The function takes the following parameters:', 'avatars'); ?><br />
			<ol>
				<li><code>id</code>: <?php echo __('Identifier; required, a blog user ID, an e-mail address, or a comment object from a WordPress comment loop (for comments).', 'avatars'); ?></li>
				<li><code>size</code>: <?php echo __('Size (pixels); optional, defaulted to value set above.', 'avatars'); ?></li>
				<li><code>default-image-url</code>: <?php echo __('Default image if no Global (public) or Local (private) avatar found; optional, defaulted to value set above.', 'avatars'); ?></li>
			</ol></p>
			<p><?php echo __('Apply format to the avatars with something like the following in your', 'avatars'); ?> <code>style.css</code> <?php echo __('theme file:', 'avatars'); ?><br /><ul>
				<li><?php echo __('For comment avatars,', 'avatars'); ?> <code>.avatar { float: left; padding: 2px; margin: 0; border: 1px solid #ddd; background: white; }</code></li>
				<li><?php echo __('For avatars in post content,', 'avatars'); ?> <code>.post_avatar { padding: 2px; margin: 0; border: 1px solid #ddd; background: white; }</code></li>
			</ul></p>
			<p><?php echo __("Examples for your theme's template files:", 'avatars'); ?><br />
			<ul>
				<li><?php echo __('In', 'avatars'); ?> <code>single.php</code> <?php echo __('declare', 'avatars'); ?> <code>&lt;?php global $post; ?&gt;</code> <?php echo __('if not already declared and then use', 'avatars'); ?> <code>&lt;?php echo get_avatar($post->post_author); ?&gt;</code> <?php echo __("to show the post author's avatar.", 'avatars'); ?></li>
				<li><?php echo __('Inside the comment loop of', 'avatars'); ?> <code>comments.php</code> <?php echo __('use', 'avatars'); ?> <code>&lt;?php echo get_avatar($comment); ?&gt;</code> <?php echo __("to show the comment author's avatar.", 'avatars'); ?></li>
			</ul></p>
		</div>
<style type="text/css">
table.form-table td {
	border-bottom: 1px solid #e3e3e3;
	padding-bottom: 1.25em !important;
	vertical-align: top;
}
</style>
	<?php }


	// Add credit.
	function avatar_footer()
	{
		if($this->avatar_options['credit'] != 'off') {
			if($this->avatar_options['credit'] == 'vis') {
			//TODO
				printf("<link rel='stylesheet' media='screen' type='text/css' href='%s/wp-content/plugins/add-local-avatar/avatars.css' />\n", get_settings('home'));
			}
			else {
				echo '<style type="text/css" media="all">#avatar_footer { display: none; } /* Change this in Users > Avatars. */ </style>';
			}
			echo '<div id="avatar_footer">Avatars by <a href="http://www.sterling-adventures.co.uk/blog/">Sterling Adventures</a></div>';
		}
	}


	// Add sub-menus...
	function avatar_menu()
	{
		if ($this->networked) {
			if( is_site_admin() ) {
				add_submenu_page('wpmu-admin.php', __('Avatars', 'avatars'), __('Avatars', 'avatars'), 'unfiltered_html', 'add_local_avatar', array(&$this, 'manage_avatar_cache'));
			}
		}
		else			
			if(current_user_can('manage_options')) 
				add_users_page(__('Avatars', 'avatars'), __('Avatars', 'avatars'), 1, basename(__FILE__), array(&$this, 'manage_avatar_cache') );
	}


	// Replace <!-- avatar e-mail --> in post content with an avatar.
	function generate_avatar_in_posts($content)
	{
		// Is there content to work with?
		if(!empty($content)) {
			$matches = array();
			$replacement = array();
			$counter = 0;

			// Look for all instances of <!-- avatar ??? --> in the content...
			preg_match_all("/<!-- avatar ([^>]+) -->/", $content, $matches);

			// For each instance, let's try to parse it...
			foreach($matches['1'] as $email) {
				// Check if we should replace with an avatar or with 'nothing' (to protect email addresses from prying eyes/robots.
				if(!get_option('show_avatars') || $this->avatar_options['in_posts'] != 'on') $replacement[$counter] = '';
				else $replacement[$counter] = get_avatar($email, $this->avatar_options['size'], $this->avatar_options['default'], true);
				$counter++;
			}

			// Replace...
			for($i = 0; $i <= $counter; $i++) {
				$content = str_replace($matches[0][$i], $replacement[$i], $content);
			}
		}

		return $content;
	}


	// Add upload option to user profile page.
	function avatar_uploader_option($profileuser)
	{ ?>
		<h3><?php echo __('Avatar Upload', 'avatars'); ?></h3>

		<script type="text/javascript">
			var form = document.getElementById('your-profile');
			form.encoding = "multipart/form-data";
			form.setAttribute('enctype', 'multipart/form-data');
		</script>

	<?php
		// Display any error text.
		if($profileuser->avatar_error) { ?>
			<div id='message' class='error fade'><b><?php echo __('Upload error:', 'avatars'); ?></b> <?php echo $profileuser->avatar_error; ?></div>
		<?php }
		delete_usermeta($profileuser->ID, "avatar_error");
		?>

		<table class="form-table"><tr>
			<th><?php echo __('Current Avatar', 'avatars'); ?></th>
			<td>
				<?php $this->avatar_uploader_table($profileuser, PROFILE_SIZE); ?>
			</td>
		</tr></table>
	<?php
	}


	// Generic table for avatar upload form.
	function avatar_uploader_table($user, $size, $widget = false, $show_text = true)
	{
		echo ($widget ? '<span class="avatar_avatar">' : '<table><tr><td>');
			// Display the profile's avatar.
			echo get_avatar($user->ID, $size);
		echo ($widget ? '</span>' : '</td>');
		echo ($widget ? '<span class="avatar_text">' : '<td>');
			if($this->avatar_options['upload_allowed'] == 'on' || current_user_can('edit_users')) {
				if(empty($user->avatar)) {
					if(!$widget || ($widget && $show_text)) {
						echo $this->get_avatar_type(), __(' avatar, override with a local avatar...', 'avatars'); ?>
						<br />
					<?php } ?>
					<input type="file" name="avatar_file" id="avatar_file" />
					<br />
					<?php if(!$widget) { ?>
						<span class='field-hint'>
							<?php echo __('Hints: Square images make better avatars.', 'avatars'); ?>
							<br />
							<?php echo __('Small image files are best for avatars, e.g. approx. 10K or smaller.', 'avatars'); ?>
						</span>
					<?php }
					if($this->avatar_options['twitter'] == 'on') {
						echo '<p><label for="idAvatarTwitter">Twitter ID:</label>';
						printf('<input type="text" value="%s" size="%d" id="idAvatarTwitter" name="twitter_id" /></p>', $user->twitter_id, $widget ? 20 : 35);
					}
				}
				else {
					if(!$widget || ($widget && $show_text)) {
						echo __('Local avatar, delete to revert to global avatar.', 'avatars'); ?>
						<br />
					<?php } ?>
					<input type="checkbox" name="avatar_delete" id="avatar_delete" /> <?php echo __('delete local avatar.', 'avatars');
				}
			}
			else {
				if($widget && $show_text) echo __('Current avatar, contact blog administrator to change.', 'avatars');
				else if(!$widget) echo __('Avatar uploads not allowed (Administrator may set on <i>Users &raquo; Avatars</i> page).', 'avatars');
				echo '<p style="clear: both;"> </p>';
			}
		echo ($widget ? '</span>' : '</td></tr></table>');
	}


	// Save the uploaded avatar.
	function avatar_upload($user_id)
	{
		if(!function_exists('wp_load_image')) include_once(ABSPATH . '/wp-admin/includes/image.php');
		if(!function_exists('image_resize')) include_once(ABSPATH . '/wp-admin/includes/media.php');

		define('TRIES', 4);			// Number of attempts to create a unique file name.
		define('SUFFIX', 'avatar');	// Suffix for cropped avatar files.

		// Valid file types for upload.
		$valid_file_types = array(
			"image/jpeg" => true,
			"image/pjpeg" => true,
			"image/gif" => true,
			"image/png" => true,
			"image/x-png" => true
		);

		// Save Twitter ID.
		update_usermeta($user_id, 'twitter_id', $_POST['twitter_id']);

		// The web-server root directory.  Used to create absolute paths.
		$root = $this->avatar_root();

		// Remove local avatar data and file.
		if($_POST['avatar_delete'] == 'on') {
			$user = get_userdata($user_id);

			// Remove uploaded file.
			$file = $root . $user->avatar;
			if(file_exists($file)) @unlink($file);

			// Remove cropped upload file.
			$parts = pathinfo($file);
			$base = basename($parts['basename'], '.' . $parts['extension']);
			$file = $parts['dirname'] . '/' . substr($base, 0, strlen($base) - (strlen(SUFFIX) + 1)) . '.' . $parts['extension'];
			if(file_exists($file)) @unlink($file);

			update_usermeta($user_id, 'avatar', '');
		}

		// Upload a local avatar.
		if(isset($_FILES['avatar_file']) && @$_FILES['avatar_file']['name']) {	// Something uploaded?
			if($_FILES['avatar_file']['error']) $error = 'Upload error.';		// Any errors?
			else if(@$valid_file_types[$_FILES['avatar_file']['type']]) {		// Valid types?
				$path = trailingslashit($this->avatar_options['upload_dir']);
				$file = $_FILES['avatar_file']['name'];

				// Directory exists?
				if(!file_exists($root . $path) && @!mkdir($root . $path, 0777)) $error = __("Upload directory doesn't exist.", 'avatars');
				else {
					// Get a unique filename.
					// First, if already there, include the User's ID; this should be enough.
					if(file_exists($root . $path . $file)) {
						$parts = pathinfo($file);
						$file = basename($parts['basename'], '.' . $parts['extension']) . '-' . $user_id . '.' . $parts['extension'];
					}

					// Second, if required loop to create a unique file name.
					$i = 0;
					while(file_exists($root . $path . $file) && $i < TRIES) {
						$i++;
						$parts = pathinfo($file);
						$file = substr(basename($parts['basename'], '.' . $parts['extension']), 0, strlen(basename($parts['basename'], '.' . $parts['extension'])) - ($i > 1 ? 2 : 0)) . '-' . $i . '.' . $parts['extension'];
					}
					if($i >= TRIES) $error = __('Too many tries to find non-existent file.', 'avatars');

					$file = strtolower($file);

					// Copy uploaded file.
					if(!move_uploaded_file($_FILES['avatar_file']['tmp_name'], $root . $path . $file)) $error = __('File upload failed.', 'avatars');
					else chmod($root . $path . $file, 0644);

					// Resize required?
					if($this->avatar_options['resize'] == 'on') {
						$scaled_size = (empty($this->avatar_options['scale']) ? SCALED_SIZE : $this->avatar_options['scale']);

						// Required, but is it needed?
						$info = getimagesize($root . $path . $file);
						if($info[0] > $scaled_size || $info[1] > $scaled_size) {
							// Resize required and needed...
							$resized_file = image_resize($root . $path . $file, $scaled_size, $scaled_size, true, SUFFIX);
							if(!is_wp_error($resized_file) && $resized_file && $info = getimagesize($resized_file)) {
								$parts = pathinfo($file);
								$file = basename($resized_file, '.' . $parts['extension']) . '.' . $parts['extension'];
							}
							else $error = __('Unable to resize image.', 'avatars');
						}
						// Image is too small, and upscale turned on...
						else if($this->avatar_options['upsize'] == 'on') {
							$resized_file = $this->image_upsize($root . $path . $file, $scaled_size, $scaled_size, "FFFFFF", SUFFIX);
							if(!is_wp_error($resized_file) && $resized_file && $info = getimagesize($resized_file)) {
								$parts = pathinfo($file);
								$file = basename($resized_file, '.' . $parts['extension']) . '.' . $parts['extension'];
							}
							else $error = __('Unable to upsize image.', 'avatars');
						}

					}
				}
			}
			else $error = __('Wrong type.', 'avatars');

			// Save the new local avatar for this user.
			if(empty($error)) update_usermeta($user_id, 'avatar', $path . $file);
		}

		// If there was an an error, record the text for display.
		if(!empty($error)) update_usermeta($user_id, 'avatar_error', $error);
	}


	// Upsize Avatar images that are too small.
	function image_upsize($file, $max_w, $max_h, $color = null, $suffix = null, $dest_path = null, $jpeg_quality = 90 )
	{
		$image = wp_load_image($file);
		if(!is_resource($image)) return new WP_Error('error_loading_image', $image, $file);

		$size = @getimagesize($file);
		if(!$size) return new WP_Error('invalid_image', __('Could not read image size'), $file);

		list($orig_w, $orig_h, $orig_type) = $size;
		$dst_x = (int)($max_w/2) - ($orig_w/2);
		$dst_y = (int)($max_h/2) - ($orig_h/2);
		$src_x = 0;
		$src_y = 0;
		$dst_w = $max_w;
		$dst_h = $max_h;
		$src_w = $orig_w;
		$src_h = $orig_h;

		$newimage = wp_imagecreatetruecolor($dst_w, $dst_h);

		if(!empty($color)) {
			$r = base_convert(substr($color, 0, 2), 16, 10);
			$g = base_convert(substr($color, 2, 2), 16, 10);
			$b = base_convert(substr($color, 4, 2), 16, 10);
			$background = imagecolorallocate($newimage,  $r, $g, $b);
			imagefill($newimage, 0, 0, $background);
		}
		imagecopyresampled($newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $src_w, $src_h);

		// Convert from full colors to index colors, like original PNG.
		if(IMAGETYPE_PNG == $orig_type && function_exists('imageistruecolor') && !imageistruecolor($image)) imagetruecolortopalette($newimage, false, imagecolorstotal($image));

		// We don't need the original in memory anymore.
		imagedestroy($image);

		// $suffix will be appended to the destination filename, just before the extension.
		if(!$suffix) $suffix = "{$dst_w}x{$dst_h}";

		$info = pathinfo($file);
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = basename($file, ".{$ext}");
		if(!is_null($dest_path) && $_dest_path = realpath($dest_path)) $dir = $_dest_path;

		$destfilename = "{$dir}/{$name}-{$suffix}.{$ext}";

		if(IMAGETYPE_GIF == $orig_type) {
			if(!imagegif($newimage, $destfilename)) return new WP_Error('resize_path_invalid', __('Resize path invalid'));
		}
		elseif(IMAGETYPE_PNG == $orig_type) {
			if(!imagepng($newimage, $destfilename)) return new WP_Error('resize_path_invalid', __('Resize path invalid'));
		}
		else {
			// All other formats are converted to jpg.
			$destfilename = "{$dir}/{$name}-{$suffix}.jpg";
			if(!imagejpeg($newimage, $destfilename, apply_filters('jpeg_quality', $jpeg_quality, 'image_resize'))) return new WP_Error('resize_path_invalid', __('Resize path invalid'));
		}

		imagedestroy($newimage);

		// Set correct file permissions.
		$stat = stat(dirname($destfilename));
		$perms = $stat['mode'] & 0000666; // Same permissions as parent folder, strip off the executable bits.
		@chmod( $destfilename, $perms);

		// Delete old image.
		unlink($file);
		return $destfilename;
	}


} // end class.



// do the magic
$add_local_avatars = new add_local_avatars();

	
// Main template tag - outputs the avatar (returns false if avatars are switched off)...
if(!function_exists('get_avatar')) :
function get_avatar($id_or_email, $size = '', $default = '', $post = false)
{
	global $avatar_type, $add_local_avatars;

	if(!get_option('show_avatars')) return false;							// Check if avatars are turned on.

	if(!is_numeric($size) || $size == '') $size = $add_local_avatars->avatar_options['size'];	// Check default avatar size.

	$email = '';															// E-mail key for Gravatar.com
	$name = '';																// Name for anchor title attribute.
	$url = '';																// Anchor.
	$id = '';																// User ID.
	$src = '';																// Image source;

	$avatar_type = '';														// Global to advertise type of Avatar.

	if(is_numeric($id_or_email)) {											// Numeric - user ID...
		$id = (int)$id_or_email;
		$user = get_userdata($id);
		if($user) {
			$email = $user->user_email;
			$name = ($add_local_avatars->avatar_options['name'] == 'on' ? $user->nickname : $user->first_name . ' ' . $user->last_name);
			$url = $user->user_url;
		}
	}
	elseif(is_object($id_or_email)) {										// Comment object...
		if(!empty($id_or_email->user_id)) {									// Object has a user ID, commenter was registered & logged in...
			$id = (int)$id_or_email->user_id;
			$user = get_userdata($id);
			if($user) {
				$email = $user->user_email;
				$name = ($add_local_avatars->avatar_options['name'] == 'on' ? $user->nickname : $user->first_name . ' ' . $user->last_name);
				$url = $user->user_url;
			}
		}
		else {																// Comment object...
			$name = $id_or_email->comment_author;

			switch($id_or_email->comment_type) {
			case 'trackback':												// Trackback...
			case 'pingback':
				if(!empty($add_local_avatars->avatar_options['default'])) $src = $add_local_avatars->avatar_options['default'];
				$url_array = parse_url($id_or_email->comment_author_url);
				$url = "http://" . $url_array['host'];
				break;

			case 'comment':													// Comment...
			case '':
				if(!empty($id_or_email->comment_author_email)) $email = $id_or_email->comment_author_email;
				$user = get_user_by_email($email);
				if($user) $id = $user->ID;									// Set ID if we can to check for local avatar.
				$url = $id_or_email->comment_author_url;
				break;
			}
		}
	}
	else {																	// Assume we have been passed an e-mail address...
		if(!empty($id_or_email)) $email = $id_or_email;
		$user = get_user_by_email($email);
		if($user) $id = $user->ID;											// Set ID if we can to check for local avatar.
	}

	// What class to apply to avatar images?
	$class = ($post ? 'post_avatar no-rate' : 'avatar');

	// Try to use local avatar.
	if($id) {
		$local = get_usermeta($id, 'avatar');
		if(!empty($local)) {
			$src = $local;
			$avatar_type = TYPE_LOCAL;
		}
	}

	// No local avatar source, Twitter is turned on, and we have a Twitter ID; so see if a Twitter avatar is available...
	if(!$src && $add_local_avatars->avatar_options['twitter'] == 'on') {
		if(!empty($user->twitter_id)) {
			$twitter = $add_local_avatars->get_twitter_avatar($user->twitter_id);
			if(!empty($twitter)) {
				$src = $twitter;
				$avatar_type = TYPE_TWITTER;
			}
		}
	}	
	
	// No local avatar source, so build global avatar source...
	if(!$src) {
		if ( !empty($email) )
			$email_hash = md5( strtolower( $email ) );

		if ( is_ssl() ) {
			$src = 'https://secure.gravatar.com/avatar/';
		} else {
			if ( !empty($email) )
				$src = sprintf( "http://%d.gravatar.com/avatar/", ( hexdec( $email_hash{0} ) % 2 ) );
			else
				$src = 'http://0.gravatar.com/avatar/';
		}
		
		if(empty($email)) $src .= md5(strtolower((empty($default) ? UNKNOWN : BLANK)));
		else $src .= md5(strtolower($email));
		$src .= '?s=' . $size;

		$wavatar = get_option('avatar_default');
		$src .= '&amp;d=';
		if (!empty($wavatar) && !empty($email) && empty($default))
			$src .= $add_local_avatars->check_switch($wavatar, $add_local_avatars->avatar_options['default'], $size);
		elseif (!empty($default))
			$src .= $add_local_avatars->check_switch($default, $add_local_avatars->avatar_options['default'], $size);
		else
			$src .= urlencode(FALLBACK);
		
		$rating = get_option('avatar_rating');
		if(!empty($rating)) $src .= "&amp;r={$rating}";

		$avatar_type = TYPE_GLOBAL;
	}

	$avatar = "<img  src='{$src}' class='{$class} avatar-{$size} avatar-default' height='{$size}' width='{$size}' style='width: {$size}px; height: {$size}px;' />";

	// Hack to stop URL wrapping if the caller is the 'Admin Bar'.
	$backtrace = debug_backtrace();
	if($backtrace[1]['function'] != 'wp_admin_bar_my_account_menu') {
		// If not in admin pages and there is a URL, wrap the avatar markup with an anchor.
		if(!empty($url) && $url != 'http://' && !is_admin() && $add_local_avatars->avatar_options['url_wrap'] == 'on') {
			$avatar = sprintf("<a href='%s' rel='external nofollow' %s title='%s' %s>%s</a>", attribute_escape($url), ($user ? "" : "target='_blank'"), (empty($name) ? '' : __('Visit', 'avatars') . " $name&rsquo;" . (substr($name, -1) == 's' ? "" : "s") . " " . (empty($add_local_avatars->avatar_options['location']) ? 'website' : $add_local_avatars->avatar_options['location'])), ($add_local_avatars->avatar_options['snapshots'] == 'on' ? '' : "class='snap_noshots'"), $avatar);
		}
	}

	// Return the filtered result.
	return apply_filters('get_avatar', $avatar, $id_or_email, $size, $default);
}
endif;
	
?>