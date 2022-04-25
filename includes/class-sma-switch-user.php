<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sma_Switch_User {
	
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

		/* swicth user serch */
		add_action( 'wp_ajax_sma_switch_user_search', array( $this,'sma_switch_user_search' ) );
				
		if ( isset( $_GET['switch_user'] ) && !empty( $_GET['switch_user'] ) ) {
			add_action( 'admin_init', array( $this, 'sma_switch_user' ) );
		}
		
		// WP logout hook
		add_action('wp_logout',	array( $this, 'un_sma_switch_user'), 1);
		add_action('admin_bar_menu', array( $this, 'switch_own_user' ) );
	}
	
	/* 
	* Serch userr from switch user
	*/

	public function sma_switch_user_search() {
		check_ajax_referer( 'sma_search_user_nonce', 'security' );
		$query = isset($_POST['username']) ? wc_clean($_POST['username']) : '';
		
		$args = array(
			'search'	=> is_numeric( $query ) ? $query : '*' . $query . '*'
		);
	
		if ( !is_email( $query ) && strpos($query, '@') !== false ) {
			$args['search_columns'] = ['user_login','user_email','user_role'];
		}
	
		if ( !current_user_can( 'manage_options' ) ) {
			$args['role__not_in'] = 'Administrator';
		}
	
		$user_query = new WP_User_Query( $args );
	
		$ret = '';
		
		$all_roles = wp_roles()->roles;
		
		$return_array = [];
		
		foreach ( $all_roles as $key => $role ) {
			$site_roles[$key] = $role['name'];
		}

		if ( !empty($user_query->results) ) {
				
			foreach ( $user_query->results as $user ) {
	
				if ( get_current_user_id() == $user->ID ) {
					continue;
				}
	
				$name_display = $user->first_name . ' ' . $user->last_name;
	
				$user_role_display = $site_roles[array_shift($user->roles)];
	
				if ( empty($user_role_display) ) {
					$user_role_display = $user->user_login;
				} else {
					$user_role_display .= ' - ' . $user->user_login;
				}
				$user_role_display = trim($user_role_display);

				if ( !empty($name_display) && !empty($user_role_display) ) {
					$user_role_display = sprintf('( %s )', $user_role_display);
				}
	
				$ret .='<a href="' . admin_url("?switch_user={$user->ID}") . '">' . $name_display . ' ' . $user_role_display . '</a>' . PHP_EOL;
			}
		} else {
			$ret .='<strong>' . __('No user found!', 'fast-user-switching') . '</strong>' . PHP_EOL;
		}
	
		echo wp_kses_post( $ret ) ;
		die();
	}
	
	/**
	* Get get user id and switch to
	*/

	public function Sma_Switch_User() {

		global $current_user;
		$user_id = isset( $_GET['switch_user'] ) ? sanitize_text_field( $_GET['switch_user'] ) : '';
		$block_attempt = false;
		$user = get_userdata( $user_id );
		if ( false == $user ) {

			$block_attempt = true;
		}

		if ( !current_user_can( 'manage_options' ) ) {
			if ( in_array( 'administrator', (array) $user->roles ) ) { 
				$block_attempt = true;
			}
		}

		if ( true === $block_attempt ) {
			$redirect = add_query_arg( 'switch_user', 'true2', admin_url() );
			return wp_redirect( $redirect );
		}

		$this->save_recent_user( $user_id, $user );
		
		// We need to know what user we were before so we can go back
		$hashed_id = $this->encrypt_decrypt('encrypt', $current_user->ID);
		setcookie('sma_switch_user_' . COOKIEHASH, $hashed_id, 0, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);
		
		// Login as the other user
		wp_set_auth_cookie($user_id, false);

		// If impresonate user is vendor than set vendor cookies.
		if ( class_exists('WC_Product_Vendors_Utils') ) {
			if ( WC_Product_Vendors_Utils::is_vendor( $user_id ) ) {
				$vendor_data = WC_Product_Vendors_Utils::get_all_vendor_data( $user_id );
				$vendor_id = key($vendor_data);
				setcookie('woocommerce_pv_vendor_id_' . COOKIEHASH, absint($vendor_id), 0, SITECOOKIEPATH, COOKIE_DOMAIN);
			}				
		}//End if

		if ( isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ) {
			$redirect_url = sanitize_text_field( $_SERVER['HTTP_REFERER'] );
			if ( strpos($redirect_url, '/wp-admin/') != false ) {
				$redirect_url = admin_url();
			}
		} else {
			$redirect_url = admin_url();
		}
		$redirect_url = $redirect_url . '?smasu=true';
		wp_redirect( $redirect_url );
		exit;
	}
	
	/**
	* Switch back to old user
	*/

	public function un_sma_switch_user() {
		$coockie_user = $this->sma_switch_user_cookie();
		if ( !empty( $coockie_user ) ) {
			wp_set_auth_cookie($coockie_user, false);
			// Unset the cookie
			setcookie('sma_switch_user_' . COOKIEHASH, 0, time()-3600, SITECOOKIEPATH, COOKIE_DOMAIN, false, true);

			if ( isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'] ) ) {
				$redirect_url = sanitize_text_field( $_SERVER['HTTP_REFERER'] );
			} else {
				$redirect_url = admin_url();
			}

			wp_redirect( $redirect_url );
			exit;
		}
	}
	
	public function switch_own_user( $wp_admin_bar ) {
		// If user is switched, change the logout text
		$switched_cookie = $this->sma_switch_user_cookie();
		if ( !empty( $switched_cookie ) ) {
			$args = array(
				'id'    => 'logout',
				'title' => __('Switch to own user', 'fast-user-switching'),
				'meta'  => array( 'class' => 'logout' )
			);
			$wp_admin_bar->add_node($args);
		}
	}
	
	public function sma_switch_user_cookie() {
		$key = 'sma_switch_user_' . COOKIEHASH;
		if ( isset( $_COOKIE[$key] ) && !empty( $_COOKIE[$key] ) ) {
			$user_id = $this->encrypt_decrypt('decrypt', sanitize_text_field( $_COOKIE[$key] ) );
			return $user_id;
		} else {
			return false;
		}
	}
	
	public function encrypt_decrypt ( $action, $string ) {
		$output = false;
		$encrypt_method = 'AES-256-CBC';
		
		$secret_key = wp_salt();
		$secret_iv = wp_salt('secure_auth');

		$key = hash('sha256', $secret_key);

		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ( 'encrypt' == $action ) {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ( 'decrypt' == $action ) {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
	
	/**
	 * Return list of recent users list.
	 * @return string [description]
	 */
	 
	public function recent_users() {
	
		$user_id = get_current_user_id();
		$user = wp_get_current_user();
		$this->save_recent_user( $user_id, $user );
		$recent_user = '';
  
		
		if ( current_user_can('manage_options') ) {
			$opt = get_option('sma_switch_recent_user', []);
		} else {
			$opt = get_user_meta( get_current_user_id(), 'sma_switch_recent_user', true );
		}
		if ( !empty( $opt ) ) {
			foreach ( $opt as $key => $value ) {
				
				$user_role_display = '';
				$user = explode('&', $value);
				$user = array_filter($user);
				$user_id = isset($user[0]) ? $user[0] : '';
				$user_name = isset($user[1]) ? trim($user[1]) : '';	
				$user_role = isset($user[2]) ? trim($user[2]) : '';	
				$last_login_date = isset($user[3]) ? trim($user[3]) : '';
				$last_login_date = gmdate('M, d', strtotime($last_login_date)) ;
				$rc = explode( '-', $user_role );
				$rc = array_map( 'trim', $rc );
				$rc = array_filter( $rc );
				
				if ( get_sma_switch_user_search('Show_customer_role', 'yes') == 'yes' ) {
					$u_role= isset( $rc[0] ) ? $rc[0] : '';
				} else {
					$u_role= '';
				}	
				
				if ( get_sma_switch_user_search('Show_username', 'yes') == 'yes' ) {
					$u_name= isset( $rc[1] ) ? $rc[1] : '';   
				} else {
					$u_name= '';
				}

				if ( get_sma_switch_user_search('Show_customer_role', 'yes') == 'yes'  && ( get_sma_switch_user_search('Show_username', 'yes') == 'yes' )) {
					$show_name_role = $u_name . ' ' . $u_role;
				} else {
					$show_name_role = $u_name . '' . $u_role;
				}
				
				if ( get_sma_switch_user_search('Show_customer_name', 'yes') == 'yes' ) {
					$user_name = isset($user[1]) ? trim($user[1]) : '';			
				} else {
					$user_name= '';			
				}
					
					$user_role_display = sprintf('(%s)', $show_name_role );
					$show_name_role =  $user_name . ' ' . $user_role_display ;
				
				
				if (empty($u_name) && empty($u_role)) {
					$show_name_role = $user_name;
				}
				
				$last_login_date = sprintf( '<span class="small-date">%s</span>', $last_login_date );

				$id_select = 'shop_maneger_id';
				$recent_user .= '<a id=' . $id_select . ' href="' . admin_url("?switch_user=$user_id") . '">' . $show_name_role . ' ' . $last_login_date . '</a>' . PHP_EOL;
			}
		}
		return $recent_user;
	}
	
	public function save_recent_user( $user_id, $user ) {
		if ( current_user_can('manage_options') ) {
			$recent_user = get_option( 'sma_switch_recent_user', [] );
		} else {
			$recent_user = get_user_meta( $user_id, 'sma_switch_recent_user', true );
			if ( empty( $recent_user ) ) {
				$recent_user = [];
			}
		}

		$wp_date_format = get_option('date_format');
		if ( isset( $user->roles[0] ) && $user->roles[0] ) {
			$roles= $user->roles[0];
		} else {
			return;
		}
		$roles = $this->read_able_role_name ( $roles );
		$name_display = $user->first_name . ' ' . $user->last_name;				
		$roles .= ' - ' . $user->user_login;
		
		$date_display = gmdate($wp_date_format);
	
		$array = $user->data->ID . '&' . $name_display . '&' . $roles . '&' . $date_display;
		if ( !in_array( $array, $recent_user ) ) {
			array_unshift( $recent_user, $array );
		}
	
		if ( in_array( $array, $recent_user ) && $recent_user[0] !== $array ) {
			$key = array_search($array, $recent_user);
			unset($recent_user[$key]);
			array_unshift($recent_user, $array);
		}
	
		$recent_user = array_slice( $recent_user, 0, 5);
		if ( current_user_can('manage_options') ) {
			update_option('sma_switch_recent_user', $recent_user);
		} else {
			update_user_meta( $user_id, 'sma_switch_recent_user', $recent_user, '' );
		}
	
	}
	
	public function read_able_role_name ( $role ) {
		// print_r($role);
		$all_roles = wp_roles()->roles;
		
		$return_array = [];
		
		foreach ($all_roles as $key => $role1) {
			$return_array[$key] = $role1['name'];
		}
		//print_r($return_array[ $role ]);
		$roles = isset($return_array[$role]) && $return_array[$role] ? $return_array[$role] : 'subscriber';

		return $roles;
	}
}
