<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//tab1
function get_sma_general_settings( $key, $default = 'yes' ) {
	$sma_general_settings_option = get_option( 'sma_general_settings_option', array() );
	return isset( $sma_general_settings_option[$key] ) ? stripslashes($sma_general_settings_option[$key]): stripslashes($default) ;
}
function update_general_settings_widget( $key, $val ) {
	$sma_general_settings_option = get_option( 'sma_general_settings_option', array() );
	$sma_general_settings_option[$key] = $val;
	update_option( 'sma_general_settings_option', $sma_general_settings_option );
}

//tab2
function get_sma_dashboard_widget( $key ) {
	$sma_dashboard_widget_option = get_option( 'sma_dashboard_widget_option', array() );
	return isset( $sma_dashboard_widget_option[$key] ) ? $sma_dashboard_widget_option[$key] : 'yes';
}
function update_sma_dashboard_widget( $key, $val ) {
	$sma_dashboard_widget_option = get_option( 'sma_dashboard_widget_option', array() );
	$sma_dashboard_widget_option[$key] = $val;
	update_option( 'sma_dashboard_widget_option', $sma_dashboard_widget_option );
}

//tab3
function get_sma_adminbar( $key ) {
	$sma_admin_menu_option = get_option( 'sma_adminbar_option', array() );
	return isset( $sma_admin_menu_option[$key] ) ? $sma_admin_menu_option[$key] : 'yes';
}
function update_sma_adminbar( $key, $val ) {
	$sma_admin_menu_option = get_option( 'sma_adminbar_option', array() );
	$sma_admin_menu_option[$key] = $val;
	update_option( 'sma_adminbar_option', $sma_admin_menu_option );
}

//switch user option 
function get_sma_switch_user_search( $key, $default = 'yes' ) {
	$sma_switch_user_option = get_option( 'sma_switch_user_option' );
	return isset( $sma_switch_user_option[$key] ) ? stripslashes($sma_switch_user_option[$key]): stripslashes($default) ;
}
function update_switch_user( $key, $val ) {
	$sma_switch_user_option = get_option( 'sma_switch_user_option', array() );
	$sma_switch_user_option[$key] = $val;
	update_option( 'sma_switch_user_option', $sma_switch_user_option );
}

//toggle function
function sma_toggle( $name, $key, $val, $parent ) {
	?>
	<input type="hidden" name="<?php echo esc_html( $name ); ?>" value="no"/>
	<input class="tgl tgl-flat <?php echo esc_html( $key ); ?><?php echo !empty($parent) ? ' ' . esc_html( $parent ) : ''; ?>" id="<?php echo esc_html( $key ); ?>" name="<?php echo esc_html( $name ); ?>" type="checkbox" <?php echo 'yes' == $val ? 'checked' : ''; ?> value="yes"/>
	<label id="<?php echo esc_html( $key ); ?>-checkbox" class="tgl-btn" for="<?php echo esc_html( $key ); ?>"></label>
	<?php
}
