<?php
/**
 * Template Name: Login Customizer
 *
 * Template to display login page for customization purposes. It's used to avoid loading wp-login.php page, which isn't the best way to do it.
 * A stripped-down version of wp-login.php form made to work with Login Customizer.
 */

 /**
  * Redirect to homepage if not loaded inside Customizer.
  */
  
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php
$sma = new Sma_Admin();
$settings = woo_shop_manager_admin()->customizer->customize_setting_options_func();
$background_color = $sma->get_option_value_from_array('sma_general_settings_option', 'bg_color', '#eee');
$form_font_color = $sma->get_option_value_from_array('sma_general_settings_option', 'form_font_color', '#222');
$header_font_color = $sma->get_option_value_from_array('sma_general_settings_option', 'header_font_color', '#333');
$link_color = $sma->get_option_value_from_array('sma_general_settings_option', 'link_color', '#135e96');
$Custom_Text_After_Header = $sma->get_option_value_from_array('sma_general_settings_option', 'Custom_Text_After_Header', '');
$logo_width = $sma->get_option_value_from_array('sma_general_settings_option', 'logo_width', 84);
$form_bg_color = $sma->get_option_value_from_array('sma_general_settings_option', 'form_bg_color', '#ffffff');
$form_border_color = $sma->get_option_value_from_array('sma_general_settings_option', 'form_border_color', '#ffffff');
$sma_border_radius = $sma->get_option_value_from_array('sma_general_settings_option', 'sma_border_radius', 0);
$btn_color = $sma->get_option_value_from_array('sma_general_settings_option', 'btn_color', '#3B64D2');
$btn_font_color = $sma->get_option_value_from_array('sma_general_settings_option', 'btn_font_color', '#fff');
$btn_expand = $sma->get_option_value_from_array('sma_general_settings_option', 'btn_expand', 1);
$customizer_logo = $sma->get_option_value_from_array('sma_general_settings_option', 'image_path', '');
$login_header_text = $sma->get_option_value_from_array('sma_general_settings_option', 'login_header_text', '');
$sma_login_box_type = $sma->get_option_value_from_array('sma_general_settings_option', 'sma_login_box_type', 'simple');
$button_class = ( 1 == $btn_expand ) ? 'login-full-width-button' : 'login-width-auto-button';
 
?>
<title></title>
<?php
	wp_enqueue_style( 'login' );
	do_action( 'login_enqueue_scripts' );
	do_action( 'login_head' );
?>
<style>
html{
	margin-top: 0 !important;
}
.login form .input, .login input[type=text] {
	height: auto;
}
.login form{
	margin-top:0px;
}
/* rtl css */

.rtl body.wp-core-ui #loginform label{
	margin-left:7px;
}

/* rtl css end */
body.wp-core-ui #loginform label {
	font-weight: 400;
}
body.login.login-action-login.wp-core-ui{
	background-color:<?php echo esc_html($background_color); ?>;
}
body.wp-core-ui #loginform label{
	color:<?php echo esc_html($form_font_color); ?>;
}
body.wp-core-ui #backtoblog a, body.wp-core-ui #nav a{
	color:<?php echo esc_html($link_color); ?>;
}
.login h1 img { 
	background-size: <?php echo esc_html($logo_width); ?>px;
	width: <?php echo esc_html($logo_width); ?>px;
	display: initial !important ;
}
body.wp-core-ui #loginform{
	background:<?php echo esc_html($form_bg_color); ?>;
	border-color:<?php echo esc_html($form_border_color); ?>;
	border-radius:<?php echo esc_html($sma_border_radius); ?>px;
}

<?php if ( 'simple' == $sma_login_box_type ) { ?>
	body.wp-core-ui #loginform{
		background: <?php echo esc_html( $background_color ); ?>;
		border: none;
		box-shadow: none; 
		padding-top: 0;
		margin-top:0px;	
		margin-bottom:0px;
		padding:0px;	
	}
<?php } ?>
<?php if (  'boxed' == $sma_login_box_type ) { ?>
	body.wp-core-ui #loginform{
		margin-bottom:10px;
	}
<?php } ?>
.wp-core-ui .button-group.button-large .button, .wp-core-ui .button.button-large {
	background-color:<?php echo esc_html($btn_color); ?>;
	color:<?php echo esc_html($btn_font_color); ?>;
}
.wp-core-ui .button.button-large{
	min-height: 40px !important;
}
.wp-core-ui .button.login-full-width-button{
	width: 100% !important;
	margin-top:10px !important;
}
.wp-core-ui .button.login-width-auto-button{
	width: auto !important;
	margin-top:0 !important;
}
p.header_text{
	color: <?php echo esc_html( get_sma_general_settings('header_font_color') ); ?>;
}
#backtoblog{
	display: none;
}
p#nav{
	text-align:center;
}
.login #nav{
	margin-top:0px !important;
}
input#wp-submit{
	border:0;
	box-shadow: none;
	text-shadow:none;
}
p:empty {
	display: block !important;
}
}


</style>
</head>
<?php

 
	$action_login = 'login';

	$login_link_separator = apply_filters( 'login_link_separator', ' | ' );

	$classes = array( 'login-action-' . $action_login, 'wp-core-ui' );
if ( is_rtl() ) {
	$classes[] = 'rtl';
}
$classes = apply_filters( 'login_body_class', $classes, $action_login ); 
?>
<body class="login <?php echo esc_attr( implode( ' ', $classes ) ); ?>" >
<?php do_action( 'login_header' ); ?>
	<div id="login">
		<h1>
			<img src="
				<?php
				$default_image = admin_url( 'images/w-logo-blue.png' );
				if ( !empty( $customizer_logo ) ) {
					echo esc_url( $customizer_logo );
				} else {
					echo esc_url( $default_image );
				}
				?>
			">

			
		</h1>
		<p class="header_text" style="text-align:center;padding:15px 0 15px 0;">
		<?php
		echo esc_html__( $login_header_text, 'woocommerce-shop-manager-admin-bar' );
		?>
		</p>
		<form name="loginform" id="loginform" enctype="multipart/form-data" >
			<p>
				<label for="user_login"><?php esc_html_e( 'Username or Email Address' ); ?></label>
				<input type="text" name="log" id="user_login" class="input" value="" size="20" autocapitalize="off" />
			</p>
			<p>
				<label for="user_pass"><?php esc_html_e( 'Password' ); ?></label>
				<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" />
			</p>
			<?php do_action( 'login_form' ); ?>
			<p class="forgetmenot"><label for="rememberme"><input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e( 'Remember Me' ); ?></label></p>

			<p class="submit">
				<input type="button" name="wp-submit" id="wp-submit" class="button button-primary button-large <?php echo esc_html( $button_class ); ?>" value="<?php esc_attr_e( 'Log In' ); ?>" style="text-transform: capitalize;font-weight: 400;margin-bottom:10px;" checked/>
			</p>
			
		</form>
		<p id="nav">
				<?php
				if ( get_option( 'users_can_register' ) ) :
					$registration_url = sprintf( '<a href="%s">%s</a>', esc_url( wp_registration_url() ), __( 'Register') );
					/** This filter is documented in wp-includes/general-template.php */
					echo esc_html( apply_filters( 'register', $registration_url ) );
					echo esc_html( $login_link_separator );
				endif;
				
				?>
			<a  href=""><?php esc_html_e( 'Lost your password?' ); ?></a>
			</p>
		<p id="backtoblog">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
				/* translators: %s: site title */
				printf( esc_html( '&larr; Back to %s', 'site' ), esc_html( get_bloginfo( 'title', 'display' ) ) );
				?>
			</a>
		</p>
	</div>
</body>
</html>
