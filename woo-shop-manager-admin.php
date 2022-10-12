<?php 
/**
 * Plugin Name: Shop Manager Admin for WooCommerce
 * Plugin URI:  https://www.zorem.com/shop
 * Description: Save time managing your WooCommerce shop! Shop Manager Admin adds a customizable WooCommerce quick-links menu to the WordPress admin bar (frontend & backend).
 * Version:     3.6.5
 * Author:      zorem
 * Author URI:  http://www.zorem.com/
 * License:     GPL-2.0+
 * License URI: http://www.zorem.com/
 * Text Domain: woocommerce-shop-manager-admin-bar
 * WC tested up to: 6.7.0
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woo_Shop_Manager_Admin {
	
	/*
	 * @var string
	 */
	public $version = '3.6.5';
	
	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		if ( !$this->is_wc_active() ) {
			return;
		}
		$this->includes();
		$this->init();
	}
	/**
 * Remove customizer options.
 *
 * @since 1.0.0
 * @param object $wp_customize
 */

	/**
	 * Check if WC is active
	 *
	 * @since  1.0.0
	 * @return bool
	*/
	
	private function is_wc_active() {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}
		
		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}		
		return $is_active;
	}
	
	/**
	 * Display WC active notice
	 *
	 * @since  1.0.0
	*/
	public function notice_activate_wc() {
		?>
		<div class="error">
			<?php /* translators: %s: search for a tag */ ?>
			<p><?php printf( esc_html__( 'Please install and activate %1$sWooCommerce%2$s for Shop Manager Admin for WooCommerce!', 'woocommerce-shop-manager-admin-bar' ), '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}
	
	public function includes() {
		
		//sma library
		require_once $this->get_plugin_path() . '/includes/class-sma-library.php';
		
		//sma menubar array
		require_once $this->get_plugin_path() . '/includes/class-sma-menubar-array.php';
		
		//admin ui
		require_once $this->get_plugin_path() . '/includes/class-sma-admin.php';
		$this->admin = Sma_Admin::get_instance();
		
		//switch user
		require_once $this->get_plugin_path() . '/includes/class-sma-switch-user.php';
		$this->switch_user = Sma_Switch_User::get_instance();

		//customizer
		require_once $this->get_plugin_path() . '/includes/customizer/wc-sma-login-page-customizer.php';
		$this->customizer = Sma_Login_Page_Customizer::get_instance();
		
	}
	
	/*
	* init when class loaded
	*/
	public function init() {
		// /***** Init Hook *****/
		add_action('plugins_loaded', array( $this,'sma_load_textdomain'));	
		// plugin page link hook
		add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'sma_plugin_action_links' ) );
		// Method enqueue_script js/css load
		add_action( 'admin_enqueue_scripts', array( $this, 'sma_admin_include_script' ), 200 );
		add_action( 'admin_enqueue_scripts', array( $this, 'switch_user_script' ), 200 );
		add_action( 'wp_enqueue_scripts', array( $this, 'switch_user_script' ), 200 );
	}
	
	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		if ( isset( $this->plugin_path ) ) {
			return $this->plugin_path;
		}
		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
		return $this->plugin_path;
	}
	
	/*
	* plugin file directory function
	*/	
	public function plugin_dir_url() {
		return plugin_dir_url( __FILE__ );
	}	
	
	/* 
	* plugin textdomain function 
	*/	
	public function sma_load_textdomain() {
		load_plugin_textdomain( 'woocommerce-shop-manager-admin-bar', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}
	
	/* 
	* include js and css function for switch user
	*/
	public function switch_user_script() {
		// switch user
		wp_register_script( 'sma_switch_user_script', plugins_url( '/assets/js/switch-user.js', __FILE__ ), array(), $this->version);
		wp_localize_script( 'sma_switch_user_script', 'sma_switch_user_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );
		wp_enqueue_script( 'sma_switch_user_script' );
	}

	/* 
	* include js and css function for admin 
	*/	
	public function sma_admin_include_script() {
		
		$page = isset( $_GET['page'] ) ? sanitize_text_field( $_GET['page'] ) : '';
		if ( 'woocommerce_shop_manager_admin_option' != $page && 'sma' != $page ) {
			return;
		}
		
		// Add the color picker css file       
		wp_enqueue_style( 'wp-color-picker' );
		
		// Add the WP Media 
		wp_enqueue_media();
		
		// Add tiptip js and css file
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'woocommerce_admin_styles' );
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		wp_enqueue_script( 'jquery-tiptip' );
		
		// Add custom css & js file 
		wp_enqueue_style( 'sma_admin_style', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/assets/css/admin-style.css', array(), $this->version );
		wp_enqueue_script( 'sma_admin_script', plugins_url( '/assets/js/admin-script.js', __FILE__ ), array( 'jquery','wp-color-picker' ), $this->version);
		
		$params = array(
			'page' => $page,
		);
		wp_localize_script( 'sma_admin_script', 'sma_options', $params );
	}

	/**
	* Add plugin action links.
	*
	* Add a link to the settings page on the plugins.php page.
	*
	* @since 2.6.5
	*
	* @param  array  $links List of existing plugin action links.
	* @return array         List of modified plugin action links.
	*/
	public function sma_plugin_action_links ( $links ) {
		return array_merge( array(
			'<a href="' . esc_url( admin_url( '/admin.php?page=woocommerce_shop_manager_admin_option' ) ) . '">' . __( 'Settings' ) . '</a>'
		), $links );		
	}
}

function Woo_Shop_Manager_Admin() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new Woo_Shop_Manager_Admin();
	}
	
	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
Woo_Shop_Manager_Admin();
