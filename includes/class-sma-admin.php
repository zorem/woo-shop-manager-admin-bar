<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sma_Admin {
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	*/
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	*/
	
	public function __construct() {
		$this->init();
	}

	/**
	 * Get the class instance
	 *
	 * @return smswoo_admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	 * init function
	 * run hooks
	*/
	
	public function init() {
		
		
		//register admin menu
		add_action('admin_menu', array( $this, 'register_woocommerce_menu' ), 100 );
		
		//ajax save: Admin Bar Menu tab
		add_action( 'wp_ajax_sma_update_adminbar_menu', array( $this, 'update_adminbar_menu_callback' ) );	
		
		//ajax save: WordPress Dashboard Widget tab 
		add_action( 'wp_ajax_sma_update_dashboard_widget', array( $this, 'update_dashboard_widget_callback' ) );
		
		//ajax save: General Settings tab 
		add_action( 'wp_ajax_sma_general_settings_form_update', array( $this, 'update_general_settings_callback') );
		//ajax save: Fast Customer Switching
		add_action( 'wp_ajax_sma_switch_form_update', array( $this, 'update_switch_user_callback') );
		
		//Remove wordpress logo from admin bar 
		add_action( 'wp_before_admin_bar_render', array( $this,'admin_menu_admin_bar_remove_logo'), 0 );
		/*
		* add custom columns of Total Spend in user admin panel
		*/
		add_filter( 'user_contactmethods', array( $this, 'new_total_spend_column'), 10, 1 );
		add_filter( 'manage_users_columns', array( $this, 'new_modify_total_spend_column_table') );
		add_filter( 'manage_users_custom_column', array( $this,'new_modify_total_spend_row_table'), 10, 3 );
		/*
		* add custom columns of Order Count in user admin panel
		*/
		add_filter( 'user_contactmethods', array( $this,'new_order_count_column'), 10, 1 );
		add_filter( 'manage_users_columns', array( $this,'new_modify_order_count_column_table') );
		add_filter( 'manage_users_custom_column', array( $this,'new_modify_order_count_row_table'), 10, 3 );
		/*
		* add custom columns of Signup Date in user admin panel
		*/
		add_filter( 'user_contactmethods', array( $this,'new_signup_date_column'), 10, 1 );
		add_filter( 'manage_users_columns', array( $this,'new_modify_signup_date_column_table') );
		add_filter( 'manage_users_custom_column', array( $this,'new_modify_signup_date_row_table'), 10, 3 );
		add_filter( 'manage_users_sortable_columns', array( $this,'make_signup_date_column_sortable') );
		
		/*call for admin footer text*/
		add_filter('admin_footer_text', array( $this,'change_admin_footer'));
		add_filter('admin_footer', array( $this,'admin_footer_style'));

		/*call for login page footer text*/
		// add footer text in custamizer privew and admin login page
		add_action( 'login_footer', array( $this,'sma_login_page_footer') ); 
		// replace image replace in custamizer privew and admin login page
		add_filter('login_headertext', array( $this,'logo_headertitle'), 10, 1); 
		// header text replace in custamizer privew and admin login page
		add_filter('login_message', array( $this,'logo_header_login_message'), 10, 1);
		
		/* change custom logo call */
		add_action( 'login_enqueue_scripts', array( $this,'change_login_page_logo') );

		/* change logo URL call */
		add_filter( 'login_headerurl', array( $this,'change_loginlogo_url' ) );
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'my_plugin_action_links' ) );
		add_action( 'template_redirect', array( $this, 'preview_login_page') );	
		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		if ( in_array( $page, array( 'woocommerce_shop_manager_admin_option', 'sma' ) ) ) {
			add_filter( 'admin_body_class', array( $this, 'sma_post_admin_body_class' ), 100 );
		}
		
		/*call for dashboard widgets*/
		add_action( 'wp_dashboard_setup', array( $this,'remove_dashboard_widgets' ), 20);
		add_action( 'wp_head', array( $this,'zorem_woocommerce_admin_bar_style') );
		add_action( 'admin_head', array( $this,'zorem_woocommerce_admin_bar_style') );
		
		/* Code for display admin bar in backend or not  */
		add_action( 'admin_bar_menu', 'sma_adminbar_array', 98 );		
		add_action( 'init', array( $this, 'sma_update_install_callback' ) );
			
	}
	
	/*
	* Admin Menu add function
	* WC sub menu 
	*/
	
	public function register_woocommerce_menu() {
		add_submenu_page( 'woocommerce', 'Shop Manager Admin', 'Shop Manager Admin', 'manage_options', 'woocommerce_shop_manager_admin_option', array( $this, 'woocommerce_admin_options_page' ) );
	}

	
	/*
	* woocommerce_admin_options_page
	*/
	public function woocommerce_admin_options_page() {
		$tab = isset( $_GET['tab'] ) ? wc_clean( $_GET['tab'] ): '';
		require_once( 'admin-html/header.phtml' );
		?>

<div class="woocommerce sma_admin_layout">
	<div class="sma_admin_content">
		<div class="zorem_sma_tab_name">
			<input id="tab1" type="radio" name="tabs" class="sma_tab_input" data-name="sma_content1"
				data-label="<?php esc_html_e('Settings', 'woocommerce'); ?>" data-tab="settings" checked>
				<label for="tab1"
				class="sma_tab_label first_label"><?php esc_html_e('Settings', 'woocommerce-shop-manager-admin-bar'); ?></label>
			<input id="tab5" type="radio" name="tabs" class="sma_tab_input"
				data-label="<?php esc_html_e('Customize', 'woocommerce-shop-manager-admin-bar'); ?>"
				data-tab="customize" <?php echo ( 'customize' == $tab ) ? 'checked' : ''; ?>>
				<a class="sma_tab_label" href='<?php echo esc_url(admin_url() . 'admin.php?page=sma_customizer'); ?>' ;">
				<label><?php esc_html_e( 'Wordpress login', 'woocommerce-shop-manager-admin-bar' ); ?></label></a>
			<input id="tab6" type="radio" name="tabs" class="sma_tab_input" data-name="sma_content_go_pro"
				data-label="<?php esc_html_e('Go Pro', 'woocommerce-shop-manager-admin-bar'); ?>" data-tab="go-pro"
				<?php echo 'go-pro' == $tab ? 'checked' : ''; ?>>
			<label for="tab6"
				class="sma_tab_label"><?php esc_html_e('Go Pro', 'woocommerce-shop-manager-admin-bar'); ?></label>
		</div>
		<div class="zorem_sma_tab_wraper">
			<?php
				require_once( 'admin-html/settings_tab.phtml' );
				require_once( 'admin-html/addons_tab.phtml' );
			?>
		</div>
	</div>
</div>
<?php
	}
	
	public function get_html( $sma_general_settings_tab ) {
		foreach ( (array) $sma_general_settings_tab as $key => $array ) {
			?>
<tr>
	<td colspan=2 scope="row">
		<label for="<?php echo esc_html( $key ); ?>">
			<input type="hidden" name="settings_menu[<?php echo esc_html( $key ); ?>]" value="no">
			<input name="settings_menu[<?php echo esc_html( $key ); ?>]" type="checkbox"
				id="<?php echo esc_html( $key ); ?>" value="yes"
				<?php echo get_sma_general_settings( $key , $array['default'] ) == 'yes' ? 'checked' : ''; ?>>
			<?php echo esc_html( $array['title'] ); ?>
			<span class="woocommerce-help-tip tipTip" title="<?php echo esc_html( $array['desc'] ); ?>"></span>
		</label>
	</td>
</tr>
<?php
		} 
	}
	
	public function get_html3( $sma_switch_user_tab ) {
		$i = 0;
		foreach ( (array) $sma_switch_user_tab as $key => $array ) {
			
			?>
<tr class=<?php echo ( 0 == $i ) ? '' : 'fc_hide'; ?>>
	<td colspan=2 scope="row">
		<label for="<?php echo esc_html( $key ); ?>">
			<input type="hidden" name="switch_user[<?php echo esc_html( $key ); ?>]" value="no">
			<input name="switch_user[<?php echo esc_html( $key ); ?>]" type="checkbox"
				id="<?php echo esc_html( $key ); ?>" value="yes"
				<?php echo get_sma_switch_user_search( $key , $array['default'] ) == 'yes' ? 'checked' : ''; ?>>
			<?php echo esc_html( $array['title'] ); ?>
			<span class="woocommerce-help-tip tipTip" title="<?php echo esc_html( $array['desc'] ); ?>"></span>
		</label>
	</td>
</tr>
<?php
		$i++;
		} 
	}

	public function sma_switch_user_tab() {
		$sma_switch_user_tab = array(
			'Enable_Switch_to_Customer'=> array(
				'title'					=> __( 'Enable Switch to Customer option in admin Bar', 'woocommerce-shop-manager-admin-bar' ),
				'default' 				=> 'yes',	
				'desc'					=> __('Enable this option to display Switch User.', 'woocommerce-shop-manager-admin-bar')
			),
			'Show_customer_name' => array(
				'title'					=> __( 'Show customer name', 'woocommerce-shop-manager-admin-bar' ),	
				'default' 				=> 'yes',	
				'id'					=> 'Show_customer_name',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option To Show customer name.', 'woocommerce-shop-manager-admin-bar')
			),
			'Show_username' => array(
				'title'					=> __( 'Show username', 'woocommerce-shop-manager-admin-bar' ),	
				'default' 				=> 'yes',	
				'id'					=> 'Show_username',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option To Show username.', 'woocommerce-shop-manager-admin-bar')
			),
			'Show_customer_role'=> array(
				'title'					=> __( 'Show customer role', 'woocommerce-shop-manager-admin-bar' ),	
				'default' 				=> 'yes',	
				'id'					=> 'Show_customer_role',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option To Show customer role.', 'woocommerce-shop-manager-admin-bar')
			),
			'Allow_Switch_to_Customer' => array(
				'title'					=> __( 'Allow Switch to Customer for Shop Manager role', 'woocommerce-shop-manager-admin-bar' ),		
				'default' 				=> 'yes',	
				'id'					=> 'Allow_Switch_to_Customer',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option To Allow Switch to Customer for Shop Manager role.', 'woocommerce-shop-manager-admin-bar')
			),
		);

		return $sma_switch_user_tab;
	}
	
	public function update_switch_user_callback() {			
		
		check_admin_referer( 'switch_user_form_action', 'switch_user_form_nonce_field' ); 
		if ( empty( $_POST ) ) {
			return;
		}
		$switch_user = isset( $_POST['switch_user'] ) ? wc_clean( $_POST['switch_user'] ) : '';
		foreach ( $switch_user as $key => $val ) {	
			update_switch_user( $key, $val );
		}
		
		echo json_encode( array('success' => 'true') );
		die();
	
	}
	
	/**
	* Get the settings tab array for General setting.
	*
	* @return array Array of settings sms_provider.
	*/
	public function sma_general_settings_tab() {
		$sma_general_settings_tab = array(
			'display_total_spend'=> array(
				'title'					=> __( 'Display total spend column in user Admin', 'woocommerce-shop-manager-admin-bar' ),
				'default' 				=> 'yes',	
				'desc'					=> __('Enable this option to display total spend column in user Admin .', 'woocommerce-shop-manager-admin-bar')
			),
			'display_signup_date' => array(
				'title'					=> __( 'Display signup date column in user Admin', 'woocommerce-shop-manager-admin-bar' ),	
				'default' 				=> 'yes',	
				'id'					=> 'display_signup_date',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option to display singup date column in users .', 'woocommerce-shop-manager-admin-bar')
			),
			'display_order_count' => array(
				'title'					=> __( 'Display order count column in user Admin', 'woocommerce-shop-manager-admin-bar' ),	
				'default' 				=> 'yes',	
				'id'					=> 'display_order_count',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option to display order count in users .', 'woocommerce-shop-manager-admin-bar')
			),
			'horizontal_scroll_orders_admin'=> array(
				'title'					=> __( 'Enable horizontal scroll in WooCommerce Orders admin', 'woocommerce-shop-manager-admin-bar' ),	
				'default' 				=> 'yes',	
				'id'					=> 'horizontal_scroll_orders_admin',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option to add a Horizontal scroll in orders admin.', 'woocommerce-shop-manager-admin-bar')
			),
			'remove_wordpress_logo' => array(
				'title'					=> __( 'Remove WordPress logo from admin menu & admin bar', 'woocommerce-shop-manager-admin-bar' ),		
				'default' 				=> 'no',	
				'id'					=> 'remove_wordpress_logo',
				'type'					=> 'checkbox',
				'desc'					=> __('Enable this option to remove WordPress logo from admin menu & admin bar.', 'woocommerce-shop-manager-admin-bar')
			),
		);
		return $sma_general_settings_tab;
	}
	
	public function get_html2 ( $get_dashboard_tab_data ) {
		echo '<tbody>';
		foreach ( (array) $get_dashboard_tab_data as $key => $array ) {
			?>
<tr class="<?php echo isset($array['sub_option']) ? 'has_sub_option' : ''; ?>">
	<td><?php echo esc_html( $array['title'] ); ?></td>
	<td class="toggle_td">
		<?php sma_toggle( "dashboard_widget[{$key}]", $key, get_sma_dashboard_widget( $key ), 'for_admin' ); ?>
	</td>
	<td class="toggle_td">
		<?php sma_toggle( "dashboard_widget[{$key}_sm]", $key . '_sm', get_sma_dashboard_widget( $key . '_sm' ), 'for_sma' ); ?>
	</td>
</tr>
<?php
			if ( isset( $array['sub_option'] ) ) {
				foreach ( (array) $array['sub_option'] as $sub_key => $sub_value ) {
					?>
<tr class="sub_option_one">
	<td>
		<div class="hide_widgets wc-status-child"><span class="dashicons dashicons-arrow-right-alt2"
				id="set_icon"></span><span class="set_title"
				style="padding:2px"><?php echo esc_html( $sub_value['title'] ); ?></span></div>
	</td>
	<td class="toggle_td sub_option_admin_checkbox">
		<?php sma_toggle( "dashboard_widget[{$sub_key}]", $sub_key, get_sma_dashboard_widget( $sub_key ), 'for_sub_admin' ); ?>
	</td>
	<td class="toggle_td sub_option_shopmanager_checkbox">
		<?php sma_toggle( "dashboard_widget[{$sub_key}_sm]", $sub_key . '_sm', get_sma_dashboard_widget( $sub_key . '_sm' ), 'for_sub_sma' ); ?>
	</td>
</tr>
<?php
				}
			}
		}
		echo '</tbody>';
	}	
	
	/*
	* get settings tab array data
	* return array
	*/
	public function get_dashboard_tab_data() {
		$dashboard_menu = get_option('sma_dashboard_widget_option', '1');
		$get_dashboard_tab_data = array(	
			'remove_welcome_panel' => array(
				'title'					=> __( 'WordPress Welcome', 'woocommerce-shop-manager-admin-bar' ),		
				'default' 				=> 'yes',				
			),
			'remove_wp_events' => array(
				'title'					=> __( 'WordPress Events and News', 'woocommerce-shop-manager-admin-bar' ),
				'default' 				=> 'yes',	
			),
			'remove_quick_draft' => array(
				'title'					=> __( 'Quick Draft', 'woocommerce-shop-manager-admin-bar' ),		
				'default' 				=> 'yes',	
			),
			'remove_dashboard_right_now' => array(
				'title'					=> __( 'At a Glance', 'woocommerce-shop-manager-admin-bar' ),		
				'default' 				=> 'yes',	
			),
			'remove_dashboard_activity' => array(
				'title'					=> __( 'Activity', 'woocommerce-shop-manager-admin-bar' ),
				'default' 				=> 'yes',	
			),
			'remove_woocommerce_dashboard_status' => array(
				'title'					=> __( 'WooCommerce status', 'woocommerce' ),		
				'default' 				=> 'yes',	
				'sub_option'			=> array(											
					'remove_woocommerce_status_processing' => array(
						'title'					=> __( 'Display processing', 'woocommerce-shop-manager-admin-bar' ),
						'default' 				=> 'yes',	
					),
					'remove_woocommerce_status_onhold' => array(
						'title'					=> __( 'Display on-hold', 'woocommerce-shop-manager-admin-bar' ),
						'default' 				=> 'yes',	
					),	
					'remove_woocommerce_status_stock_info' => array(
						'title'					=> __( 'Display Stock info', 'woocommerce-shop-manager-admin-bar' ),
						'default' 				=> 'yes',	
					),
				),
			),
			'remove_woocommerce_reviews' => array(
				'title'					=> __( 'WooCommerce recent reviews', 'woocommerce-shop-manager-admin-bar' ),
				'default' 				=> 'yes',	
			),
		);
		
		if ( is_plugin_active( 'wordfence/wordfence.php' ) ) {	
			$wordfence_data = array(	
				'remove_wordence_activity' => array(
					'title'					=> __( 'Wordfence activity in the past week', 'woocommerce-shop-manager-admin-bar' ),
					'default' 				=> 'yes',	
				),
			);	
			$get_dashboard_tab_data = array_merge($get_dashboard_tab_data, $wordfence_data);			
		}
		if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {	
			$yoast_seo = array(	
				'remove_yoast_seo_posts' => array(
					'title'					=> __( 'Yoast SEO Posts Overview', 'woocommerce' ),	
					'default' 				=> 'yes',	
				),
			);	
			$get_dashboard_tab_data = array_merge($get_dashboard_tab_data, $yoast_seo);			
		}
		return $get_dashboard_tab_data;
	}

	/*
	* ajax save: adminbar menu tab
	*/
	public function update_adminbar_menu_callback() {
		
		check_ajax_referer( 'admin_menu_form_action', 'admin_menu_form_nonce_field' );
		
		if ( empty( $_POST ) ) {
			return;
		}
		
		$admin_menu = isset( $_POST['admin_menu'] ) ? wc_clean( $_POST['admin_menu'] ) : '';
		foreach ( $admin_menu as $key => $val ) {
			update_sma_adminbar( $key, $val );
		}
		
		echo json_encode( array('success' => 'true') );
		die();
	}
	
	/*
	* ajax save: Dashboard widget tab
	*/
	public function update_dashboard_widget_callback() {			

		check_admin_referer( 'dashboard_form_action', 'dashboard_form_nonce_field' ) ;
		
		if ( empty( $_POST ) ) {
			return;
		}

		$dashboard_widget = isset( $_POST['dashboard_widget'] ) ? wc_clean( $_POST['dashboard_widget'] ) : '';
		foreach ( $dashboard_widget as $key => $val ) {	
			update_sma_dashboard_widget( $key, $val );
		}	
		
		echo json_encode( array('success' => 'true') );
		die();
	}
	
	/*
	* settings form save for General tab
	*/
	public function update_general_settings_callback() {			
		
		check_admin_referer( 'general_form_action', 'general_form_nonce_field' ); 
		if ( empty( $_POST ) ) {
			return;
		}
		$settings_menu = isset( $_POST['settings_menu'] ) ? wc_clean( $_POST['settings_menu'] ) : '';
		foreach ( $settings_menu as $key => $val ) {	
			update_general_settings_widget( $key, $val );
		}
		
		echo json_encode( array('success' => 'true') );
		die();
	
	}
	
	/* remove wordpress logo from admin bar */
	public function admin_menu_admin_bar_remove_logo() {
		global $wp_admin_bar;
		if ( get_sma_general_settings('remove_wordpress_logo', 'no') == 'yes' ) {
			 $wp_admin_bar->remove_menu( 'wp-logo' );
		}
		if ( get_sma_switch_user_search( 'Enable_Switch_to_Customer', 'yes' ) == 'no' ) {
			$wp_admin_bar->remove_menu( 'wsmab_main_switch_user' );	
		}
	}
	
	/*
	* add custom columns of Total Spend in user admin panel
	*/
	public function new_total_spend_column( $contactmethods ) {
		
		$contactmethods['sma_total_spend'] = 'Total Spend';
		return $contactmethods;
	}
	
	public function new_modify_total_spend_column_table( $column ) {
	
		$display_total_spend = get_sma_general_settings('display_total_spend');
		if ( '' == $display_total_spend || 'no' == $display_total_spend ) {
			return $column;
		}
	
		$column['sma_total_spend'] = 'Total Spend';
		return $column;
	}
	
	public function new_modify_total_spend_row_table( $val, $column_name, $user_id ) {
		$currency = get_woocommerce_currency();
		switch ( $column_name ) {
			case 'sma_total_spend':
				return get_woocommerce_currency_symbol($currency) . wc_get_customer_total_spent( $user_id );
				break;
			default:
		}
		return $val;
	}
	
	/*
	* add custom columns of Order Count in user admin panel
	*/
	public function new_order_count_column( $contactmethods ) {
	
		$contactmethods['sma_order_count'] = 'Order Count';
		return $contactmethods;
	}
	
	public function new_modify_order_count_column_table( $column ) {
		if ( get_sma_general_settings('display_order_count') == '' || get_sma_general_settings('display_order_count') == 'no') {
			return $column;
		}
		$column['sma_order_count'] = 'Order Count';
		return $column;
	}
	
	public function new_modify_order_count_row_table( $val, $column_name, $user_id ) {
		switch ($column_name) {
			case 'sma_order_count':
				return wc_get_customer_order_count( $user_id );
				break;
			default:
		}
		return $val;
	}
	
	/*
	* add custom columns of Signup Date in user admin panel
	*/
	public function new_signup_date_column( $contactmethods ) {
		
		$contactmethods['sma_signup_date'] = 'Signup Date';
		return $contactmethods;
	}

	public function new_modify_signup_date_column_table( $column ) {
		if ( get_sma_general_settings('display_signup_date') == '' || get_sma_general_settings('display_signup_date') == 'no' ) {
			return $column;
		}
		$column['sma_signup_date'] = 'Signup Date';
		return $column;
	}
	public function new_modify_signup_date_row_table ( $val, $column_name, $user_id ) {
		$date_format = get_option( 'date_format' );
		if ( 'F j, Y' == $date_format ) {
			$date_format = 'M j, Y';
		}
		$time_format = get_option( 'time_format' );
		switch ($column_name) {
			case 'sma_signup_date':
				return "<span title=''>" . gmdate( $date_format, strtotime( get_the_author_meta( 'registered', $user_id ) ) ) . '</span>';
				break;
			default:
		}
		return $val;
	}
	/*
	* Make our "Registration date" column sortable
	* @param array $columns Array of all user sortable columns {column ID} => {orderby GET-param} 
	*/
	public function make_signup_date_column_sortable ( $columns ) {
		return wp_parse_args( array( 'sma_signup_date' => 'registered' ), $columns );
	}
	
	/* 
	* dashboard footer text function 
	*/
	public function change_admin_footer() {
		$dashboard_footer_text= get_sma_general_settings('dashboard_footer_text', '');
		if ( empty( $dashboard_footer_text ) ) {
			echo '<span id="footer-note">Shop Manager Admin by <a href="https://www.zorem.com/?utm_source=wpadmin&utm_medium=SMA&utm_campaign=footer_txt">zorem</a></span>';
		} else {
			echo '<span id="footer-note">' . wp_kses_post( stripslashes( $dashboard_footer_text ) ) . '</span>';
		}
	}
	public function admin_footer_style() { 
		?>
		<style type="text/css">
			#toplevel_page_sma_customizer { display: none !important; }
		</style>
	<?php
	}
	
	public function sma_login_page_footer() {
	
		global $wp_meta_boxes;
		?>
		<?php if ( get_sma_general_settings('sma_login_box_type') == 'boxed' ) { ?>
			<style>
			body.wp-core-ui #loginform {
				margin-bottom: 10px;
			}
			.login form{
				margin-top:0px;
			}
			</style>
		<?php } ?>
			
		<div id="login_footer_note">
			<p class="footer-text"><?php echo esc_html( get_sma_general_settings('login_footer_text', '') ); ?></p>
		</div>
		<style type="text/css">
		#login_footer_note .footer-text {
			text-align: center;
			margin: 30px auto;
		}

		p#backtoblog {
			text-align: center;
		}

		p#nav {
			text-align: center;
		}

		.login #nav {
			margin-top: 6px;
		}
		</style>
		<?php  
	}

	// replace image replace in custamizer privew and admin login page
	public function logo_headertitle ( $login_header_title ) {
		$image_path = get_sma_general_settings('image_path', '');
		if ( !empty($image_path) ) {
			//echo wp_kses_post()
			return '<img src="' . $image_path . '">';
		} else {
			return 'https://wordpress.org/';	
		}
	}
	
	// header text replace in custamizer privew and admin login page
	public function logo_header_login_message() {
		$header_text = get_sma_general_settings( 'login_header_text', '' );
		if ( empty( $header_text ) ) {
			$message = '<p class="header-text" style="text-align: center;margin: 0 auto;padding: 15px 0 15px 0;"></p>';
			return $message ;
		}
		$message = '<p class="header-text" style="text-align: center;margin: 0 auto;padding: 15px 0;">' . $header_text . '</p>';
		return $message;
	}
	
	public function change_login_page_logo() { 
	
		global $wp_meta_boxes;
		$btn_color = get_sma_general_settings('btn_color', '#3B64D2');
		$logo_width = get_sma_general_settings('logo_width', 84);
		$header_font_color = get_sma_general_settings('header_font_color', '#333');
		$btn_expand = get_sma_general_settings( 'btn_expand', 1 );
		$btn_font_color = get_sma_general_settings( 'btn_font_color', '#fff' );
		
		?>
		
<style type="text/css">
	body.login {
		background: <?php echo esc_html(get_sma_general_settings('bg_color', '#eee' )); ?>;
	}

	<?php 
		if ( !empty( get_sma_general_settings( 'header_font_color', '#333' ) ) ) { 
			?>
			body.login {
				color: <?php echo esc_html(get_sma_general_settings('header_font_color', '#333')); ?>;
		}
	<?php
		}

		if ( get_sma_general_settings('sma_login_box_type', 'simple' ) == 'simple' ) {
			?>
	body.wp-core-ui form#loginform {
		background: none;
		border: 0;
		box-shadow: none;
		padding-bottom: 0;
		padding: 0 8px;
		margin-bottom: 0;
		margin-top:0px;
	}
	
	<?php
		}
		if ( !empty(get_sma_general_settings('form_bg_color', '#fff' ) ) ) {
			?>
	body.wp-core-ui #loginform {
		background: 
		<?php echo esc_html(get_sma_general_settings('form_bg_color', '#fff' )); ?>;
	}

	<?php
		}

		if ( !empty(get_sma_general_settings('sma_border_radius', 0))) {
			?>
	body.wp-core-ui #loginform {
		border-radius: 
		<?php echo esc_html(get_sma_general_settings('sma_border_radius', 0)); ?>px;
	}

	<?php
		}

		if ( !empty(get_sma_general_settings('form_border_color', '#fff'))) {
			?>
	body.wp-core-ui #loginform {
		border-color: 
		<?php echo esc_html(get_sma_general_settings('form_border_color', '#fff')); ?>;
	}
	<?php
		}

		if ( !empty(get_sma_general_settings('form_font_color', '#222' ))) {
			?>
	body.wp-core-ui #loginform label,
	body.wp-core-ui #loginform strong,
	body.wp-core-ui #loginform b {
		color: 
		<?php echo esc_html(get_sma_general_settings( 'form_font_color', '#222' )); ?>;
		font-weight: 400;
	}
	<?php
		}

		if ( !empty( $logo_width )) {
			?>
	body.login div#login h1 a {
		background-size: <?php echo esc_html($logo_width); ?>px;
		width: <?php echo esc_html($logo_width); ?>px;
		height: <?php echo esc_html($logo_width); ?>px;
		margin: 0 auto;
	}
<?php } ?>
body.wp-core-ui #backtoblog a, body.wp-core-ui #nav a {
	color: <?php echo esc_html(get_sma_general_settings('link_color', '#135e96' )); ?>;
}

<?php
		if ( !empty(get_sma_general_settings('image_path', ''))) {
			?>
	body.login div#login h1 a img {
		display: block;
		width: <?php echo esc_html(get_sma_general_settings('logo_width', 84)); ?>px;
		margin: 0 auto;
		max-width: 100%;
	}

	body.login div#login h1 a {
		height: auto !important;
		width: auto !important;
		background: none;
		margin: 0px !important;

	}
	<?php
		}

		if ( !empty($btn_color) ) {
			?>
	.wp-core-ui .button.button-large {
		background: <?php echo esc_html($btn_color); ?>;
		border-color: <?php echo esc_html($btn_color); ?>;
		box-shadow: 0 1px 0 <?php echo esc_html($btn_color); ?>;
		text-shadow: 0 -1px 1px <?php echo esc_html($btn_color); ?>, 1px 0 1px <?php echo esc_html($btn_color); ?>, 0 1px 1px <?php echo esc_html($btn_color); ?>, -1px 0 1px <?php echo esc_html($btn_color); ?>;
	}
	.wp-core-ui .button.button-large:hover,
	.wp-core-ui .button.button-large:focus,
	.wp-core-ui .button.button-large:active {
		background: <?php echo esc_html($btn_color); ?>;
		border-color: <?php echo esc_html($btn_color); ?>;
	}

	<?php
		}

		?>
<?php 
		if ( !empty(get_sma_general_settings('bottom_margin')) || get_sma_general_settings('bottom_margin')=='0') {
			?>
	body.wp-core-ui #loginform {
		margin-top: 0;
	}

	body.login div#login h1 a {
		margin-bottom: 
		<?php echo esc_html(get_sma_general_settings('bottom_margin')); ?>px;
	}

	<?php
		}

		if ( 1== $btn_expand ) {
			?>
	.wp-core-ui .button.button-large {
		width: 100% !important;
		margin-top: 10px !important;
	}
	<?php
		}

		if ( !empty(get_sma_general_settings('btn_font_color', '#fff' ))) {
			?>
	input#wp-submit {
		color: 
		<?php echo esc_html(get_sma_general_settings('btn_font_color', '#fff' )); ?>;
	}

	<?php
		}

		?>
.wp-core-ui .button.button-large {
	min-height: 40px !important;
	margin-bottom: 5px;
}
</style>

<?php 
	}
	
	public function change_loginlogo_url ( $url ) {
		$image_path = get_sma_general_settings('image_path', '');
		if ( !empty($image_path) ) {
			/* Get Home Url of main-site */
			$logo_url = home_url();
			return $logo_url;
		} else {
			return 'https://wordpress.org/';	
		}
	}
	
	/**
	 * Add plugin action links.
	 *
	 * Add a link to the settings page on the plugins.php page.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $links List of existing plugin action links.
	 * @return array         List of modified plugin action links.
	 */
	public function my_plugin_action_links( $links ) {
		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( '/admin.php?page=woocommerce_shop_manager_admin_option' ) ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>'
		), $links );
		return $links;
	}
	
	public static function preview_login_page() {
		$action = isset( $_REQUEST['sma-login-page-customizer-preview'] ) ? wc_clean( $_REQUEST['sma-login-page-customizer-preview'] ) : '';
		if ( '1' != $action ) {
			return;		
		}
		wp_head();
		include woo_shop_manager_admin()->get_plugin_path() . '/includes/customizer/preview/login_page_preview.php';		
		//wp_footer();				
		exit;
	}
	
	public function sma_post_admin_body_class ( $body_class ) {
		
		$body_class .= ' sma-shop-manager-admin-setting ';
		return $body_class;
	}
	
	/* 
	* Dashboard widgets customize function 
	*/
	public function remove_dashboard_widgets() {
		
		global $wp_meta_boxes;

		$current_role = wp_get_current_user(); 

		foreach ( $current_role->roles as $key=>$value ) {
		
			if ( 'administrator' == $value ) {
				if ( get_sma_dashboard_widget('remove_welcome_panel')!= 'yes' ) {
				remove_action('welcome_panel', 'wp_welcome_panel');		
				}
				if ( get_sma_dashboard_widget('remove_wp_events') != 'yes') {
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);	
				}
				if ( get_sma_dashboard_widget('remove_quick_draft') != 'yes') {
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
				} 
				if ( get_sma_dashboard_widget('remove_dashboard_right_now') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
				}
				if ( get_sma_dashboard_widget('remove_dashboard_activity') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
				}
				if ( get_sma_dashboard_widget('remove_woocommerce_dashboard_status') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_status']);
				}
				if ( get_sma_dashboard_widget('remove_woocommerce_reviews') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_recent_reviews']);
				}
				if ( get_sma_dashboard_widget('remove_yoast_seo_posts') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['wpseo-dashboard-overview']);
				}
				if ( get_sma_dashboard_widget('remove_wordence_activity') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['wordfence_activity_report_widget']);
				}	
			}
			if ( 'shop_manager' == $value ) {
				if ( get_sma_dashboard_widget('remove_welcome_panel_sm')!= 'yes' ) {
				remove_action('welcome_panel', 'wp_welcome_panel');		
				}
				if ( get_sma_dashboard_widget('remove_wp_events_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);	
				}
				if ( get_sma_dashboard_widget('remove_quick_draft_sm') != 'yes') {
					unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
				} 
				if ( get_sma_dashboard_widget('remove_dashboard_right_now_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
				}
				if ( get_sma_dashboard_widget('remove_dashboard_activity_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_activity']);
				}
				if ( get_sma_dashboard_widget('remove_woocommerce_dashboard_status_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_status']);
				}
				if ( get_sma_dashboard_widget('remove_woocommerce_reviews_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['woocommerce_dashboard_recent_reviews']);
				}
				if ( get_sma_dashboard_widget('remove_yoast_seo_posts_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['wpseo-dashboard-overview']);
				}
				if ( get_sma_dashboard_widget('remove_wordence_activity_sm') != 'yes' ) {
					unset($wp_meta_boxes['dashboard']['normal']['core']['wordfence_activity_report_widget']);
				}
			}
		}
	}
	
	public function zorem_woocommerce_admin_bar_style() {
		if ( !is_user_logged_in() ) {
			return;
		}
		$wsmab_zorem_icon = woo_shop_manager_admin()->plugin_dir_url() . 'assets/images/SMA-logo.png';
		?>

<style type="text/css">
.rtl #wpadminbar .ab-top-menu>li.menupop.icon-woocommerce>.ab-sub-wrapper>.ab-submenu>.zorem_powered_by {
	background-image: url('<?php echo esc_url( $wsmab_zorem_icon ); ?>');
	background-repeat: no-repeat;
	background-position: right;
	padding-right: 15px;
	background-size: contain;
	margin: 5px 0 10px 0px;
}

li.zorem_powered_by a.ab-item {
	text-align: center;
}

#wpadminbar .menupop li.hover>.ab-sub-wrapper,
#wpadminbar .menupop li:hover>.ab-sub-wrapper {
	padding-right: 10px !important;
}

#wpadminbar .ab-top-menu>li.menupop.icon-woocommerce>.ab-sub-wrapper>.ab-submenu>.zorem_powered_by {
	background-image: url('<?php echo esc_url( $wsmab_zorem_icon ); ?>');
	background-repeat: no-repeat;
	background-position: left;
	padding-left: 15px;
	background-size: contain;
	margin: 5px 0 10px 4px;
}

li.zorem_powered_by a.ab-item {
	text-align: center;
}

<?php 
		if (get_sma_general_settings('horizontal_scroll_orders_admin')=='yes') {
			?>
	body.post-type-shop_order #posts-filter {
		overflow: scroll !important;
		width: 100% !important;
	}

	<?php
		}

$current_role=wp_get_current_user();

		foreach ($current_role->roles as $key=>$value) {

			if ('administrator'==$value) {
				if (get_sma_dashboard_widget('remove_woocommerce_status_processing') !='yes') {
					?>
			#woocommerce_dashboard_status .wc_status_list li.processing-orders {
				display: none;
			}

			<?php
				}

				?>
		<?php 
				if (get_sma_dashboard_widget('remove_woocommerce_status_onhold') !='yes') {
					?>
			#woocommerce_dashboard_status .wc_status_list li.on-hold-orders {
				display: none;
			}

			<?php
				}

				?>
			<?php 
				if (get_sma_dashboard_widget('remove_woocommerce_status_stock_info') !='yes') {

					?>
			#woocommerce_dashboard_status .wc_status_list li.low-in-stock,
			#woocommerce_dashboard_status .wc_status_list li.out-of-stock {
				display: none;
			}

			<?php
				}
			}

			if ('shop_manager'==$value) {
				if (get_sma_dashboard_widget('remove_woocommerce_status_processing_sm') !='yes') {
					?>
			#woocommerce_dashboard_status .wc_status_list li.processing-orders {
				display: none;
			}

			<?php
				}

				?>
		<?php 
				if (get_sma_dashboard_widget('remove_woocommerce_status_onhold_sm') !='yes') {
					?>
			#woocommerce_dashboard_status .wc_status_list li.on-hold-orders {
				display: none;
			}

			<?php
				}

				?>
		<?php
				if ('yes' !=get_sma_dashboard_widget('remove_woocommerce_status_stock_info_sm')) {

					?>
			#woocommerce_dashboard_status .wc_status_list li.low-in-stock,
			#woocommerce_dashboard_status .wc_status_list li.out-of-stock {
				display: none;
			}

			<?php
				}
			}
		}

		?>
/* sma switch user css */
#wpadminbar .quicklinks #wp-admin-bar-wsmab_main_switch_user ul li .ab-item {
	height: auto;
	min-width: 286px;
	padding: 5px;
}

#wpadminbar .quicklinks #wp-admin-bar-wsmab_main_switch_user #sma_search_username {
	height: 32px;
	font-size: 13px !important;
	padding: 0 5px;
	width: 145px;
	border-radius: 2px !important;
	box-sizing: border-box !important;
}

#sma_user_search_form {
	width: auto;
	box-sizing: border-box
}

#sma_switch_user_search_submit {
	padding: 0;
	font-size: 13px !important;
	border: 0 !important;
	background-color: #fff !important;
	border-radius: 2px !important;
	width: 74px;
	box-sizing: border-box;
	color: #000 !important;
	margin: 0 3px;
}

#sma_user_search_result {
	width: 100%;
	max-height: 320px;
	overflow-y: auto;
	margin-top: 10px;
	float: left;
}

#sma_user_search_form {
	width: 226px;
}

#sma_recent_users {
	width: 100%;
	float: left;
}

form#sma_user_search_form input[type="text"] {
	background-color: #fff !important;
}

#wpadminbar .quicklinks .menupop ul li a,
#wpadminbar .quicklinks .menupop.hover ul li a {
	color: #b4b9be;
	padding: 0;
}

#wpadminbar .menupop .ab-sub-wrapper,
#wpadminbar .shortlink-input {
	padding: 0px 0 0 10px !important;
}

.rtl #wpadminbar .menupop .ab-sub-wrapper {
	padding: 0 10px 0 10px !important;
}

.rtl #wpadminbar .menupop .menupop>.ab-item .wp-admin-bar-arrow:before {
	left: 0px !important;
}

.absolute-footer,
html {
	background-color: none !importent;
}

.login #backtoblog {
	text-align: center !important;

}
</style>
<?php 
	}
	
	/// order status function ////
	public function get_orders_count_from_status( $status ) {
		
		global $wpdb;
		// We add 'wc-' prefix when is missing from order staus
		$status = 'wc-' . str_replace('wc-', '', $status);
	
		$count = $wpdb->get_var( $wpdb->prepare("SELECT count(ID) FROM {$wpdb->prefix}posts WHERE post_status LIKE %s AND post_type LIKE 'shop_order'", $status ) );
		if ( '0' != $count ) {
			return ' (' . $count . ')';
		}
	}
	public function get_option_value_from_array( $array, $key, $default_value ) {		

		$array_data = get_option($array);	
		$value = '';
		
		if (isset($array_data[$key])) {
			$value = $array_data[$key];	
		}					
		
		if ('' == $value) {
			$value = $default_value;
		}
		return $value;
	}
	/**
	* Function callback for add not existing key in database.
	**/
	public function sma_update_install_callback() {
		if ( version_compare( get_option( 'sma_setting_migrate' ), '3.3.0', '<' ) ) {
			update_general_settings_widget( 'image_path', get_option('image_path') );
			update_general_settings_widget( 'link_color', get_option('link_color') );
			update_general_settings_widget( 'bottom_margin', get_option('bottom_margin') );
			update_general_settings_widget( 'bg_color', get_option('bg_color') );
			update_general_settings_widget( 'font_color', get_option('font_color') );
			update_general_settings_widget( 'form_font_color', get_option('form_font_color') );
			update_general_settings_widget( 'form_bg_color', get_option('form_bg_color') );
			update_general_settings_widget( 'btn_color', get_option('btn_color') );
			update_general_settings_widget( 'login_footer_text', get_option('login_footer_text') );
			update_option( 'sma_setting_migrate', '3.3.0' );	
		}
	}

	/*
	* transaltion function for loco generater
	* this function is not called from any function
	*/
	public function translation_func() {
		__( 'General Settings', 'woocommerce-shop-manager-admin-bar' );
		__( 'Admin Bar Menu' , 'woocommerce-shop-manager-admin-bar' );
		__( 'WordPress Dashboard Widgets', 'woocommerce-shop-manager-admin-bar' );
		__( 'Fast Customer Switching', 'woocommerce-shop-manager-admin-bar' );
		__( 'Save Changes', 'woocommerce-shop-manager-admin-bar' );
		__( 'Go Pro', 'woocommerce-shop-manager-admin-bar' );
		__( 'Admin Footer Text' , 'woocommerce-shop-manager-admin-bar' );
		__( 'Shop Manager Admin by zorem', 'woocommerce-shop-manager-admin-bar' );
		__( 'Widget' , 'woocommerce-shop-manager-admin-bar' );
		__( 'Menu' , 'woocommerce-shop-manager-admin-bar' );
		__( 'Administrator' , 'woocommerce-shop-manager-admin-bar' );
		__( 'Shop Manager' , 'woocommerce-shop-manager-admin-bar' );
	}	
}
