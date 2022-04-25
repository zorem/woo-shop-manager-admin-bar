<?php

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class Sma_Login_Page_Customizer {
	
	/**
	 * Get the class instance
	 *
	 * @since  1.0
	 * @return Sma_Login_Page_Customizer
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	*/
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	 * 
	 * @since  1.0
	*/
	public function __construct() {
		$this->init();
	}
	
	/*
	 * init function
	 *
	 * @since  1.0
	*/
	public function init() {

		//adding hooks
		add_action( 'admin_menu', array( $this, 'register_woocommerce_menu' ), 99 );
		
		//save of settings hook
		add_action( 'wp_ajax_save_login_settings', array( $this, 'customizer_save_login_settings' ) );
		//add_action( 'wp_ajax_save_header_setting', array( $this, 'customizer_header_section_settings' ) );
		
		// add_action( 'wp_ajax_login_preview', array( $this, 'get_preview_login' ) );
		
		//load javascript in admin
		add_action('admin_enqueue_scripts', array( $this, 'customizer_enqueue_scripts' ) );
		
	}
	
	/*
	 * Admin Menu add function
	 *
	 * @since  2.4
	 * WC sub menu 
	*/
	public function register_woocommerce_menu() {
		add_menu_page( __( 'SMA Customizer', 'woocommerce-shop-manager-admin-bar' ), __( 'SMA Customizer', 'woocommerce-shop-manager-admin-bar' ), 'manage_options', 'sma_customizer', array( $this, 'settingsPage' ) );
	}
	
	/*
	 * callback for settingsPage
	 *
	 * @since  2.4
	*/
	public function settingsPage() {

		$page = isset( $_GET['page'] ) ? sanitize_text_field($_GET['page']) : '' ;
		
		// Add condition for css & js include for admin page  
		if ( 'sma_customizer' != $page   ) {
			return;
		}
		
		$login_setting = get_option('sma_general_settings_option', array());
		
		// When load this page will not show adminbar
		?>
		<style type="text/css">
			#wpcontent, #wpbody-content, .wp-toolbar {margin: 0 !important;padding: 0 !important;}
			#adminmenuback, #adminmenuwrap, #wpadminbar, #wpfooter, .notice, div.error, div.updated { display: none !important; }
		</style>
		<script type="text/javascript" id="zoremlogin-onload">
			jQuery(document).ready( function() {
				jQuery('#adminmenuback, #adminmenuwrap, #wpadminbar, #wpfooter').remove();
			});
		</script>
		<section class="zoremlogin-layout zoremlogin-layout-has-sider">
			<form method="post" id="zoremlogin_customizer_options" class="zoremlogin_customizer_options" style="display: contents;">
				<section class="zoremlogin-layout zoremlogin-layout-has-content zoremlogin-layout-sider">
					<div class="zoremlogin-layout-slider-header">
						<div class="zoremlogin-layout-sider-heading">
							<img src="<?php echo esc_url ( woo_shop_manager_admin()->plugin_dir_url() . 'assets/images/SMA-logo.png' ); ?>" width="30px" height="30px">
							<h5 class="sider-heading"><?php esc_html_e( 'Customizing > WordPress login', 'woocommerce-shop-manager-admin-bar' ); ?></h5>
						</div>
					</div>
					<div class="zoremlogin-layout-slider-content">
						<div class="zoremlogin-layout-sider-container">
							<?php $this->get_html( $this->customize_setting_options_func() ); ?>
						</div>
						<div class="zoremlogin-back-wordpress">
							<a class="zoremlogin-back-wordpress-link" 
							href="<?php echo esc_url ( admin_url() . 'admin.php?page=woocommerce_shop_manager_admin_option&tab=settings' ); ?>">
							<span class="zoremlogin-back-wordpress-title"><span class="dashicons dashicons-arrow-left-alt"></span>
							<?php esc_html_e( 'BACK TO WORDPRESS', 'woocommerce-shop-manager-admin-bar' ); ?></span></a>
						</div>
					</div>
				</section>
				<section class="zoremlogin-layout zoremlogin-layout-has-content">
					<div class="zoremlogin-layout-content-header">
						<div class="header-panel options-content">
							<span class="" style="float: right;">
								<button name="save" class="wclp-btn wclp-save button-primary woocommerce-save-button" type="submit" value="Save changes" disabled><?php esc_html_e( 'Saved', 'woocommerce-shop-manager-admin-bar' ); ?></button>
								<?php wp_nonce_field( 'customizer_customizer_options_actions', 'customizer_customizer_options_nonce_field' ); ?>
								<input type="hidden" name="action" value="save_login_settings">
							</span>
						</div>
					</div>		
					<div class="zoremlogin-layout-content-container">
						<section class="zoremlogin-layout-content-preview customize-preview">
							<div id="overlay"></div>
							<iframe class="customizer_preview" id="customizer_preview" src="<?php echo esc_url( home_url( '/?sma-login-page-customizer-preview=1' ) ); ?>"></iframe>
						</section>
						<aside class="zoremlogin-layout-content-media">
							<a data-width="600px" data-iframe-width="100%"><span class="dashicons dashicons-desktop  desktop_color"></span></a>
							<a data-width="600px" data-iframe-width="610px"><span class="dashicons dashicons-tablet"></span></a>
							<a data-width="400px" data-iframe-width="410px"><span class="dashicons dashicons-smartphone"></span></a>
						</aside>
					</div>
				</section>
			</form>
		</section>
		<?php
	} 
	
	/*
	* Add admin javascript
	*
	* @since 1.0
	*/	
	public function customizer_enqueue_scripts() {
		$page = isset( $_GET['page'] ) ? sanitize_text_field($_GET['page']) : '' ;
		
		// Add condition for css & js include for admin page  
		if ( 'sma_customizer' != $page   ) {
			return;
		}
		
		wp_enqueue_media();

		//wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		//wp_enqueue_style( 'woocommerce_admin_styles' );
		
		// Add tiptip js and css file		
		wp_enqueue_style( 'sma-customizer', plugin_dir_url(__FILE__) . 'assets/Customizer.css', array(), woo_shop_manager_admin()->version );
		wp_enqueue_script( 'sma-customizer', plugin_dir_url(__FILE__) . 'assets/Customizer.js', array( 'jquery', 'wp-util', 'wp-color-picker','jquery-tiptip' ), woo_shop_manager_admin()->version, true );
		
		wp_localize_script('sma-customizer', 'sma_customizer', array(
			'default_admin_logo'   => admin_url( 'images/w-logo-blue.png' ),
		));		
		
	}

	/*
	* save settings function
	*/
	public function customizer_save_login_settings() {			
		
		if ( !current_user_can( 'manage_options' ) ) {
			echo json_encode( array('permission' => 'false') );
			die();
		}
		
		if ( ! empty( $_POST ) && check_admin_referer( 'customizer_customizer_options_actions', 'customizer_customizer_options_nonce_field' ) ) {

			//data to be saved
			
			$settings = $this->customize_setting_options_func();
			

			foreach ( $settings as $key=>$val ) {
				if ( 'section' != $val['type'] ) {
					if ( 'array' == $val['option_type'] ) {
						$option_data = get_option( $val['option_name'], array() );
						$option_data[$key] = isset( $_POST[$key]) ? sanitize_text_field($_POST[$key]) : '' ;
						update_option( $val['option_name'], $option_data );
					} else {
						update_option( $val[$key], wc_clean( $_POST[ $key ] ) );
					}					
				}
			}	
			echo json_encode( array('success' => 'true') );
			die();
	
		}
		
	}
	
	public function customize_setting_options_func() {	
			
		$login_settings = get_option('sma_general_settings_option', array());
		//echo '<pre>';print_r($login_settings);
							
		$settings = array(						
			'heading1'	=> array(
				'id'	=> 'customizer_setting',
				'title'	=> esc_html__( 'Login Page Settings', 'woocommerce-shop-manager-admin-bar' ),
				'type'	=> 'section',
				'show'	=> true,
				'option_name'=> 'heading1',
				'option_type'=> 'string',
			),
			'image_path' => array(	
				'type'		=> 'media',
				'title'    => esc_html__( 'Logo image', 'woocommerce-shop-manager-admin-bar' ),			
				'show'		=> true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',					
				'default'   => isset($login_settings['image_path']) ? $login_settings['image_path'] : '',
				'class'     => 'border-top-verification',
			),
			'logo_width' => array(
				'title'    => esc_html__( 'Logo width', 'woocommerce-shop-manager-admin-bar' ),
				'default'  => isset($login_settings['logo_width']) ? stripslashes($login_settings['logo_width']) : 84,
				'type'     => 'range',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'min'		=> 0,
				'max'		=> 320,
				'class'		=> 'logo_width',
			),
			'bg_color' => array(
				'title'    => esc_html__( 'Background color', 'woocommerce-shop-manager-admin-bar' ),
				'default'  => isset($login_settings['bg_color']) ? stripslashes($login_settings['bg_color']) : '#eee',
				'type'     => 'color',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset',
			),
			'header_font_color' => array(
				'title'    => esc_html__( 'Font color', 'woocommerce-shop-manager-admin-bar' ),
				'default'  => isset($login_settings['header_font_color']) ? stripslashes($login_settings['header_font_color']) : '#333',
				'type'     => 'color',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset',
			),
			'link_color' => array(
				'title'    => esc_html__( 'Links color', 'woocommerce-shop-manager-admin-bar' ),
				'default'  => isset($login_settings['link_color']) ? stripslashes($login_settings['link_color']) : '#135e96',
				'type'     => 'color',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset',
			),
			'login_header_text' => array(
				'title'    => esc_html__( 'Custom text after header:', 'woocommerce-shop-manager-admin-bar' ),
				'default'  => isset($login_settings['login_header_text']) ? stripslashes($login_settings['login_header_text']) : '',
				'type'     => 'textarea',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'	   => 'heading',
			),
			'heading2' => array(
				'id'     => 'widget_style',
				'title'       => esc_html__( 'Login box', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'section',
				'show'     => true,
				'option_name'=> 'heading2',
				'option_type'=> 'string',
			),
			'sma_login_box_type' => array(
				'title'    => esc_html__( 'Login box type', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'select',
				'default'  => isset($login_settings['sma_login_box_type']) ? $login_settings['sma_login_box_type'] : 'simple',
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'show'     => true,
				'options'  => array(
					'simple' => __( 'Simple', 'woocommerce-shop-manager-admin-bar' ),
					'boxed' => __( 'Boxed', 'woocommerce-shop-manager-admin-bar' ),
				),
			),
			'form_bg_color' => array(
				'title'    => esc_html__( 'Background color', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'color',
				'default'  => isset($login_settings['form_bg_color']) ? $login_settings['form_bg_color'] : '#ffffff',
				'show'     => true,
				'class'     => 'colorset simple_class',
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'hide'		=> true,
			),
			'form_font_color' => array(
				'title'    => esc_html__( 'Font color', 'woocommerce-shop-manager-admin-bar' ),
				'default'  => isset($login_settings['form_font_color']) ? stripslashes($login_settings['form_font_color']) : '#222',
				'type'     => 'color',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset',
			),
			'form_border_color' => array(
				'title'    => esc_html__( 'Border color', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'color',
				'default'  => isset($login_settings['form_border_color']) ? $login_settings['form_border_color'] : '#ffffff',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset simple_class',
				'hide'		=> true,
			),
			'sma_border_radius' => array(
				'title'    => esc_html__( 'Border radius', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'range',
				'default'  => isset($login_settings['sma_border_radius']) ? $login_settings['sma_border_radius'] : 0,
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'		=> 'border_radius simple_class',
				'step'		=> 5,
				'min'		=> 0,
				'max'		=> 25,
				'hide'		=> true,
			),
			'btn_color' => array(
				'title'    => esc_html__( 'Button color', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'color',
				'default'  => isset($login_settings['btn_color']) ? $login_settings['btn_color'] : '#3B64D2',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset',
			),
			'btn_font_color' => array(
				'title'    => esc_html__( 'Button font color', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'color',
				'default'  => isset($login_settings['btn_font_color']) ? $login_settings['btn_font_color'] : '#fff',
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'colorset',
			),
			'btn_expand' => array(
				'title'    => esc_html__( 'Expand button ', 'woocommerce-shop-manager-admin-bar' ),
				'type'     => 'expand',
				'default'  => isset($login_settings['btn_expand']) ? $login_settings['btn_expand'] : 1,
				'show'     => true,
				'option_name'=> 'sma_general_settings_option',
				'option_type'=> 'array',
				'class'     => 'expand_btn',
			),
		);
		
		$settings = apply_filters( 'customizer_customizer_options_array' , $settings );

	
		return $settings; 

	}
	
	/*
	* Get html of fields
	*/
	public function get_html( $arrays ) {
		foreach ( (array) $arrays as $id => $array ) {
			
			if ( isset($array['show']) && true != $array['show'] ) {
				continue; 
			}
			if ( 'expand' == $array['type'] ) {
				?>
					<tr valign="top titlerow">
						<td colspan="2" class="<?php echo esc_html( $array['class'] ); ?>">
							<div class="<?php echo esc_html( $array['class'] ); ?>">
								<?php
								if ( get_option( $id, $array['default'] ) ) {
									$checked = 'checked';
								} else {
									$checked = '';
								} 							
								?>
								<input type="hidden" name="<?php echo esc_html( $id ); ?>" value="0"/>
								<input class="tgl tgl-flat input-text regular-input zoremlogin-input" id="<?php echo esc_html( $id ); ?>" name="<?php echo esc_html( $id ); ?>" type="checkbox" <?php echo esc_html( $checked ); ?> value="1"/>
								<label class="tgl-btn" for="<?php echo esc_html( $id ); ?>"></label>
								<span class="menu-sub-title"><?php echo esc_html( $array['title'] ); ?>
								</span>
							</div>	
						</td>
					</tr>
				<?php
				continue;
			} 
			if ( isset($array['type']) && 'section' == $array['type'] ) {
				echo 'heading' != $id ? '</div>' : '';
				?>
				<div class="zoremlogin-menu-submenu-title">
					<span><?php esc_html_e( $array['title'] ); ?></span>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
				</div>
				<div class="zoremlogin-menu-contain">
				<?php
				
			} else {
				$value = isset( $array['default'] ) ? $array['default'] : '';
				$login_settings = get_option('sma_general_settings_option', array());
				$box_type = isset($login_settings['sma_login_box_type']) ? $login_settings['sma_login_box_type'] : '';
				$class = ( 'simple' == $box_type ) && isset( $array['hide'] ) ? 'simple_box_hide' : '';
				?>
				<div class="zoremlogin-menu zoremlogin-menu-inline zoremlogin-menu-sub <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?> <?php echo esc_html ( $class ); ?>">
					<div class="zoremlogin-menu-item">
						<div class="<?php esc_attr_e( $id ); ?> <?php esc_attr_e( $array['type'] ); ?>">
						
							<?php if ( isset($array['title']) && 'checkbox' != $array['type'] ) { ?>
								<span class="menu-sub-title"><?php esc_html_e( $array['title'] ); ?></span>
							<?php } ?>
							
							<?php if ( isset($array['type']) && 'text' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<input type="text" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $id ); ?>" placeholder="<?php isset($array['placeholder']) ? esc_attr_e($array['placeholder']) : ''; ?>" value="<?php echo isset($array['default']) ? esc_html_e($array['default']) : ''; ?>" class="zoremlogin-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
									<br>
								</div>
							<?php } else if ( isset($array['type']) && 'textarea' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<textarea id="<?php esc_attr_e( $id ); ?>" rows="4" name="<?php esc_attr_e( $id ); ?>" placeholder="<?php isset($array['placeholder']) ? esc_attr_e($array['placeholder']) : ''; ?>" class="zoremlogin-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>"><?php echo isset($array['default']) ? esc_html_e($array['default']) : ''; ?></textarea>
									<br>
								</div>
							<?php } else if ( isset($array['type']) && 'media' == $array['type'] ) { ?>	
								<fieldset>
									<input id="<?php echo esc_html( $id ); ?>" type="button" class="<?php echo !$value ? 'show' : 'hide'; ?> button upload-button" value="<?php esc_html_e('Select Image', 'woocommerce-shop-manager-admin-bar'); ?> " >
									<input id="uploaded_image" name="<?php echo esc_html ( $id ); ?>" type="hidden" value="<?php echo esc_html ( $value ); ?>" />
									<img class="<?php echo $value ? 'show' : 'hide'; ?>" id="widget-image"  src="<?php echo esc_url( $value ); ?>">
									<button type="button" class="<?php echo $value ? 'show' : 'hide'; ?> button sma-replace-btn">Replace</button>
									<button type="button" class="<?php echo $value ? 'show' : 'hide'; ?> button sma-remove-btn" style="margin-left:5px;">Remove</button>
								</fieldset>
							<?php } else if ( isset($array['type']) && 'range' == $array['type'] ) { ?>
								<?php //echo '<pre>';print_r($array);echo '</pre>'; ?>
								<div class="menu_sub_input_range">
									<input type="range" <?php echo isset( $array['step'] ) ? 'step="5"' : ''; ?> min="<?php esc_attr_e( $array['min'] ); ?>" max="<?php esc_attr_e( $array['max'] ); ?>" value="<?php echo isset( $array['default'] ) ? esc_html_e( $array['default'] ) : ''; ?>" style="width:78.4%" oninput="this.nextElementSibling.value = this.value" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $id ); ?>" class="zoremlogin-input <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
									
									<input class="slider__value" type="number" min="<?php esc_attr_e( $array['min'] ); ?>" max="<?php esc_attr_e( $array['max'] ); ?>" value="<?php echo isset( $array['default'] ) ? esc_html_e( $array['default'] ) : ''; ?>">
									
								</div>
							<?php } else if ( isset($array['type']) && 'select' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<select name="<?php esc_attr_e( $id ); ?>" id="<?php esc_attr_e( $id ); ?>" class="zoremlogin-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
										<?php foreach ( (array) $array['options'] as $key => $val ) { ?>
											<option value="<?php echo esc_html($key); ?>" <?php echo isset($array['default']) && $array['default'] == $key ? 'selected' : ''; ?>><?php echo esc_html($val); ?></option>
										<?php } ?>
									</select>
									<br>
								</div>
							<?php } else if ( isset($array['type']) && 'color' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<input type="text" name="<?php esc_attr_e( $id ); ?>" id="<?php esc_attr_e( $id ); ?>" class="input-text regular-input zoremlogin-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>" value="<?php echo isset($array['default']) ? esc_html_e($array['default']) : ''; ?>" placeholder="<?php isset($array['placeholder']) ? esc_attr_e($array['placeholder']) : ''; ?>">
									<br>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
			}
		} 
	}
}

