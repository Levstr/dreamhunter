<?php 

function lc_check_ssl(){
  if ( force_ssl_admin() && !is_ssl() ) {
  	if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
  		wp_redirect(preg_replace('|^http://|', 'https://', $_SERVER['REQUEST_URI']));
  		exit();
  	} else {
  		wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  		exit();
  	}
  }
}


function lc_pre_login(){
  $secure_cookie = '';
	$interim_login = isset($_REQUEST['interim-login']);

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_userdatabylogin($user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}

	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}

	$reauth = empty($_REQUEST['reauth']) ? false : true;

	// If the user was redirected to a secure login form from a non-secure admin page, and secure login is required but secure admin is not, then don't use a secure
	// cookie and redirect back to the referring non-secure admin page.  This allows logins to always be POSTed over SSL while allowing the user to choose visiting
	// the admin via http or https.
	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);
	
  if ( !is_wp_error($user) && !$reauth ) {
		if ( $interim_login ) {
			$message = '<p class="message">' . __('You have logged in successfully.') . '</p>';
			login_header( '', $message ); ?>
			<script type="text/javascript">setTimeout( function(){window.close()}, 8000);</script>
			<p class="alignright">
			<input type="button" class="button-primary" value="<?php esc_attr_e('Close'); ?>" onclick="window.close()" /></p>
			</div></body></html>
<?php		exit;
		}
		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) )
			$redirect_to = admin_url('profile.php');
		wp_safe_redirect($redirect_to);
		exit();
	}
	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) || $reauth )
		$errors = new WP_Error();

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));

	// Some parts of this script use the main login form to display a message
	if ( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )
		$errors->add('loggedout', __('You are now logged out.'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )
		$errors->add('registerdisabled', __('User registration is currently not allowed.'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )
		$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )
		$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )
		$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');
	elseif	( $interim_login )
		$errors->add('expired', __('Your session has expired. Please log-in again.'), 'message');

	// Clear any stale cookies.
	if ( $reauth )
		wp_clear_auth_cookie();

	$GLOBALS['lc_errors'] = $errors;

	if ( isset($_POST['log']) )
		$GLOBALS['lc_user_login'] = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(stripslashes($_POST['log'])) : '';
	$GLOBALS['lc_rememberme'] = ! empty( $_POST['rememberme'] );
	$GLOBALS['lc_redirect_to'] = $redirect_to;
}

function lc_pre_register(){
  if ( is_multisite() ) {
		// Multisite uses wp-signup.php
		echo ('multisite mode is not implemented yet.');
		exit;
	}

	if ( !get_option('users_can_register') ) {
		wp_redirect('wp-login.php?registration=disabled');
		exit();
	}

	$user_login = '';
	$user_email = '';
	$http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
	if ( $http_post ) {
		require_once( ABSPATH . WPINC . '/registration.php');

		$user_login = $_POST['user_login'];
		$user_email = $_POST['user_email'];
		$errors = lc_register_new_user($user_login, $user_email);
		if ( !is_wp_error($errors) ) {
			$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] : lc_get_registered_url();
    	$GLOBALS['lc_messages'][] = "<strong>SUCCESS</strong>: You have registered successfully.";
      wp_safe_redirect($redirect_to);
	    exit();
		}
	}

	$GLOBALS['lc_redirect_to'] = apply_filters( 'registration_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );
	$GLOBALS['lc_user_login'] = $user_login;
	$GLOBALS['lc_user_email'] = $user_email;
	$GLOBALS['lc_errors'] = $errors;
}

function lc_pre_retrievepassword(){
  $http_post = ('POST' == $_SERVER['REQUEST_METHOD']);
  if ( $http_post ) {
		$errors = lc_retrieve_password();
		if ( !is_wp_error($errors) ) {		  
			$redirect_to = !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : lc_get_login_url() . "?checkemail=confirm";
			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	if ( isset($_GET['error']) && 'invalidkey' == $_GET['error'] ) $errors->add('invalidkey', __('Sorry, that key does not appear to be valid.'));
	$redirect_to = apply_filters( 'lostpassword_redirect', !empty( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '' );

	do_action('lost_password');
	
  $GLOBALS['lc_errors'] = $errors;
	$GLOBALS['lc_user_login'] = isset($_POST['user_login']) ? stripslashes($_POST['user_login']) : '';
	$GLOBALS['lc_redirect_to'] = $redirect_to;
}

function lc_pre_confirm_password_change(){
  $errors = lc_reset_password($_GET['key'], $_GET['login']);

	if ( ! is_wp_error($errors) ) {
		wp_redirect(lc_get_login_url() . '?checkemail=newpass');
		exit();
	}

	wp_redirect(lc_get_login_url() . '?error=invalidkey');
	exit();
}