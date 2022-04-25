<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function sma_adminbar_array() {
	
	/*include file shop manager menu options*/ 
	//require_once( 'include/sma_shop_manager_menu.php' );
	
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );	
	
	global $wp_admin_bar;
	$processingordercount = woo_shop_manager_admin()->admin->get_orders_count_from_status( 'processing' );
	
	$enable_taxes = wc_tax_enabled();
	
	global $wp_meta_boxes;

	$admin_menu = get_option('sma_adminbar_option', '1');
	if ( empty( $admin_menu ) ) {
		$admin_menu = array();
	}
	if ( class_exists( 'WooCommerce' ) || class_exists( 'Woocommerce' ) ) {
		
		/** Main item URL helpers */
		$wsmab_woocomerce_main_url_orders   = ( current_user_can( 'edit_shop_orders' ) ) ? admin_url( 'edit.php?post_type=shop_order' ) : '#';
		$wsmab_woocomerce_settings_url   = '';
		$wsmab_woocomerce_main_url_settings = ( current_user_can( 'manage_woocommerce' ) ) ?  $wsmab_woocomerce_settings_url : '#';
			
		/** Filter the main item icon's url */
		$wsmab_woocomerce_main_url = apply_filters(
			'wcaba_filter_main_item_url',
			( defined( 'WP_DEBUG') && WP_DEBUG ) ? $wsmab_woocomerce_main_url_settings : $wsmab_woocomerce_main_url_orders
		);
		
		$wsmab_menu_main_item_title = apply_filters(
			'wsmab_filter_menu_main_item',
			_x( 'Shop Manager', 'Translators: Main item', 'woocommerce-shop-manager-admin-bar' )
		);

		/** Filter the main item name's tooltip */
		$wsmab_menu_main_item_title_tooltip = apply_filters(
			'wsmab_filter_menu_main_item_tooltip',
			_x(
				'Current Orders - WooCommerce Shop',
				'Translators: Main item - for the tooltip',
				'woocommerce-shop-manager-admin-bar'
			)
		);
	
		/** Filter the main item icon's class/display */
		$wsmab_main_icon_display = apply_filters( 'wsmab_filter_main_icon_display', 'icon-woocommerce' );
		$prefix = 'ddw-woocommerce-';
		$wsmab_main = 'wsmab_main';
		$woocommerce = 'woocommerce';
		$elementor_main_menu = $prefix . 'elementor_main_menu';
		$customers = 'customers';
		$woo_home = 'woo-home';
		$woo_analytics = $prefix . 'woo_analytics';
		$page_builder = 'page-builder';
		$plugin_setting = 'plugin-setting';
		$shop_plugins = $prefix . 'shop-plugins';
		$pages = $prefix . 'pages';
		$posts = $prefix . 'posts';
		$users = $prefix . 'users';
		$all_users = $prefix . 'all_users';
		$ux_blocks = $prefix . 'ux-blocks';
		$wpml = $prefix . 'wpml';
		$contact = $prefix . 'contact';
		$products = $prefix . 'products';
		$woocommerce_main_menu = $prefix . 'woocommerce_main_menu';
		$orders = $prefix . 'orders';
		$fulfillment = $prefix . 'fulfillment';
		$trackship_for_woocommerce = $prefix . 'trackship_for_woocommerce';
		$automatewoo = $prefix . 'automatewoo';
		$subscription = $prefix . 'subscription';
		$wcgroup = $prefix . 'wcgroup';
		$marketing = $prefix . 'marketing';
		$reports = $prefix . 'reports';
		$status = $prefix . 'status';
		$shop_settings = $prefix . 'shop-settings';
		$wp_setting = $prefix . 'wp-setting';
		$shipping_settings = $prefix . 'shipping-settings';
		$payment_settings = $prefix . 'payment-settings';
		$email_settings = $prefix . 'email-settings';
		$subscription_emails = $prefix . 'subscription-emails';
		$all_order_status = wc_get_order_statuses();	
		global $wpdb;
		$order_status = wc_get_order_statuses();
		$order_status = array_keys($order_status);
		$order_status_string = implode("','", $order_status);		
		
		$order_totals = apply_filters( 'woocommerce_reports_sales_overview_order_totals', $wpdb->get_row( "
		SELECT SUM(meta.meta_value) AS total_sales, COUNT(posts.ID) AS total_orders
		FROM {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
		WHERE meta.meta_key = '_order_total'
			AND posts.post_type = 'shop_order'
			AND posts.post_status IN ( '" . $order_status_string . "' )
		" ) );
		$all_order_label = __( 'All Orders', 'woocommerce-shop-manager-admin-bar' ) . '&nbsp;(' . $order_totals->total_orders . ')';
		
		/** Add the top-level menu item */
		$wp_admin_bar->add_menu( array(
			'id'    => $wsmab_main,
			'title' => $wsmab_menu_main_item_title,
			'href'  => esc_url( $wsmab_woocomerce_main_url ),
			'meta'  => array(
				'class' => $wsmab_main_icon_display,
				'title' => $wsmab_menu_main_item_title_tooltip
			)
		) );
		
		if ( current_user_can( 'edit_shop_orders' ) ) {
			$menu_items[ 'orders' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Orders', 'woocommerce' ) . $processingordercount,
				'href'   => admin_url( 'edit.php?post_type=shop_order' ),
				'meta'   => array(
					'title'  => __( 'Orders', 'woocommerce' )
				)
			);
			/** Display order status links if theme support is added */
			$menu_items[ 'o-all-order' ] = array(
				'parent' => $orders,
				'title'  => __( $all_order_label, 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'edit.php?post_type=shop_order' ),
				'meta'   => array(
					'title'  => __( $all_order_label, 'woocommerce-shop-manager-admin-bar' )
				)
			);
										
			foreach ($all_order_status as $order_status_slug => $order_status_name ) {
				
				$order_totals = apply_filters( 'woocommerce_reports_sales_overview_order_totals', $wpdb->get_row( $wpdb->prepare( "
					SELECT SUM(meta.meta_value) AS total_sales, COUNT(posts.ID) AS total_orders FROM {$wpdb->posts} AS posts
						LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
					WHERE meta.meta_key = '_order_total'
						AND posts.post_type = 'shop_order'
						AND posts.post_status IN ( %s )
				", $order_status_slug ) ) );
				
				if ( '0' == $order_totals->total_orders ) { 
					$all_order_label = '';
				} else {
					if ( !in_array( $order_status_slug, (array) $admin_menu ) ) {
						$all_order_label = $order_status_name . '&nbsp;(' . $order_totals->total_orders . ')';
						$menu_items[ $order_status_slug ] = array(
							'parent' => $orders,
							'title'  => __( $all_order_label, 'woocommerce-shop-manager-admin-bar' ),
							'href'   => admin_url( 'edit.php?post_status=' . $order_status_slug . '&post_type=shop_order' ),
							'meta'   => array(
							 'title'  => __( $all_order_label, 'woocommerce-shop-manager-admin-bar' )
							)
						);
					}
				}
			}
		}
		$menu_items[ 'woocommerce_main_menu' ] = array(
			'parent' => $wsmab_main,
			'id'     => $woocommerce,
			'title'  => __( 'WooCommerce', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin' ),
			'meta'   => array(
				'title'  => __( 'WooCommerce', 'woocommerce' )
			)
		);
		if ( current_user_can( 'edit_products' ) ) {
			$menu_items[ 'products' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Products', 'woocommerce' ),
				'href'   => admin_url( 'edit.php?post_type=product' ),
				'meta'   => array(
					'title'  => __( 'Products', 'woocommerce' )
				)
			);
			
			/** Various 'product' taxonomies */
			if ( current_user_can( 'manage_product_terms' ) ) {
				$menu_items[ 'p-product-add-new' ] = array(
					'parent' => $products,
					'title'  => __( 'Add new', 'woocommerce' ),
					'href'   => admin_url( 'post-new.php?post_type=product' ),
					'meta'   => array(
						'title'  => __( 'Add new', 'woocommerce' )
					)
				);
				$menu_items[ 'p-product-categories' ] = array(
					'parent' => $products,
					'title'  => __( 'Categories', 'woocommerce' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=product_cat&post_type=product' ),
					'meta'   => array(
						'title'  => __( 'Categories', 'woocommerce' )
					)
				);
				$menu_items[ 'p-product-tags' ] = array(
					'parent' => $products,
					'title'  => __( 'Tags', 'woocommerce' ),
					'href'   => admin_url( 'edit-tags.php?taxonomy=product_tag&post_type=product' ),
					'meta'   => array(
						'title'  => __( 'Tags', 'woocommerce' )
					)
				);
				$menu_items[ 'p-product-attributes' ] = array(
					'parent' => $products,
					'title'  => __( 'Attributes', 'woocommerce' ),
					'href'   => admin_url( 'edit.php?post_type=product&page=product_attributes' ),
					'meta'   => array(
						'title'  => __( 'Attributes', 'woocommerce' )
					)
				);	
			}  // end if
		}  // end if products cap check
		$menu_items[ 'woo_analytics' ] = array(
			'parent' => $wsmab_main,
			'id'     => $woocommerce,
			'title'  => __( 'Analytics', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Foverview' ),
			'meta'   => array(
				'title'  => __( 'Analytics', 'woocommerce' )
			)
		);	
		$menu_items[ 'overview_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Overview', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Foverview' ),
			'meta'   => array(
				'title'  => __( 'Overview', 'woocommerce' )
			)
		);
		$menu_items[ 'revenue_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Revenue', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Frevenue' ),
			'meta'   => array(
				'title'  => __( 'Revenue', 'woocommerce' )
			)
		);
		$menu_items[ 'orders_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Orders', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Forders' ),
			'meta'   => array(
				'title'  => __( 'Orders', 'woocommerce' )
			)
		);
		$menu_items[ 'products_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Products', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Fproducts' ),
			'meta'   => array(
				'title'  => __( 'Products', 'woocommerce' )
			)
		);
		$menu_items[ 'categories_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Categories', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Fcategories' ),
			'meta'   => array(
				'title'  => __( 'Categories', 'woocommerce' )
			)
		);
		$menu_items[ 'coupons_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Coupons', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Fcoupons' ),
			'meta'   => array(
				'title'  => __( 'Coupons', 'woocommerce' )
			)
		);
		$menu_items[ 'taxes_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Taxes', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Ftaxes' ),
			'meta'   => array(
				'title'  => __( 'Taxes', 'woocommerce' )
			)
		);
		$menu_items[ 'downloads_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Downloads', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Fdownloads' ),
			'meta'   => array(
				'title'  => __( 'Downloads', 'woocommerce' )
			)
		);
		$menu_items[ 'stcok_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Stock', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Fstock' ),
			'meta'   => array(
				'title'  => __( 'Stock', 'woocommerce' )
			)
		);
		$menu_items[ 'settings_analytics' ] = array(
			'parent' => $woo_analytics,
			'title'  => __( 'Settings', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=%2Fanalytics%2Fsettings' ),
			'meta'   => array(
				'title'  => __( 'Settings', 'woocommerce' )
			)
		);
		$menu_items[ 'marketing' ] = array(
			'parent' => $wsmab_main,
			'id'     => $woocommerce,
			'title'  => __( 'Marketing', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=/marketing' ),
			'meta'   => array(
				'title'  => __( 'Marketing', 'woocommerce' )
			)
		);
		if ( class_exists('Ast_Pro') || class_exists('Advanced_local_pickup_PRO') ) {
			if ( class_exists('Ast_Pro') ) {
				$fulfillment_link = admin_url( 'admin.php?page=fulfillment-dashboard' );
			} elseif ( class_exists( 'Trackship_For_Woocommerce' ) ) {
				$fulfillment_link = admin_url( 'admin.php?page=trackship-shipments' );
			} else {
				$fulfillment_link = admin_url( 'admin.php?page=alp-fulfillment-dashboard' );
			}
			$menu_items[ 'fulfillment' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Fulfillment', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => $fulfillment_link,
				'meta'   => array(
					'title'  => __( 'Fulfillment', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			if ( class_exists('Ast_Pro') ) {
				$menu_items[ 'unfulfilled' ] = array(
					'parent' => $fulfillment,
					'title'  => __( 'Unfulfilled order', 'woocommerce' ),
					'href'   => $fulfillment_link,
					'meta'   => array(
						'title'  => __( 'Unfulfilled order', 'woocommerce' )
					)
				);
			}
			if ( class_exists('Advanced_local_pickup_PRO') ) {
				$menu_items[ 'alp_store_pickup' ] = array(
					'parent' => $fulfillment,
					'title'  => __( 'Store Pickup', 'woocommerce' ),
					'href'   => admin_url( 'admin.php?page=alp-fulfillment-dashboard' ),
					'meta'   => array(
						'title'  => __( 'Store Pickup', 'woocommerce' )
					)
				);
			}
			if ( class_exists('Advanced_local_pickup_PRO') ) {
				$menu_items[ 'alp_store_pickup' ] = array(
					'parent' => $fulfillment,
					'title'  => __( 'Store Pickup', 'woocommerce' ),
					'href'   => admin_url( 'admin.php?page=alp-fulfillment-store-pickup' ),
					'meta'   => array(
						'title'  => __( 'Store Pickup', 'woocommerce' )
					)
				);
			}
			if ( class_exists('Ast_Pro') ) {
				$menu_items[ 'csv_import' ] = array(
					'parent' => $fulfillment,
					'title'  => __( 'CSV import', 'woocommerce' ),
					'href'   => admin_url( 'admin.php?page=ast-csv-import' ),
					'meta'   => array(
						'title'  => __( 'CSV import', 'woocommerce' )
					)
				);
			}
		}
		
		$subscriptions = get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'shop_subscription', // WC orders post type
			'post_status' => 'any' // Only orders with status "completed"
		) );
		$result = array();
		$results = array();
		if ( !empty( $subscriptions ) ) {
			foreach ( $subscriptions as $val ) {
				if ( property_exists( $val, 'post_status') ) {
					$result[$val->post_status][] = $val;
				} else {
					$result[''][] = $val;
				}
			}
		}
		foreach ( $result as $key => $value ) {
			$results[$key] = count($value);
		}
		if ( !empty( $result ) ) {
			arsort($results);
		}

		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			$menu_items[ 'subscription' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Subscriptions', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'edit.php?post_type=shop_subscription' ),
				'meta'   => array(
					'title'  => __( 'Subscriptions', 'woocommerce-subscriptions' ),
				)
			);
			foreach ( $results as $key => $value ) {
				$menu_items[ $key ] = array(
					'parent' => $subscription,
					'title'  => __( ucfirst(substr($key, 3)) . ' (' . $value . ')', 'woocommerce-subscriptions' ),
					'href'   => admin_url( 'edit.php?post_status=' . $key . '&post_type=shop_subscription' ),
					'meta'   => array(
						'title'  => __( ucfirst(substr($key, 3)) . ' (' . $value . ')', 'woocommerce-subscriptions' )
					)
				);
			}
		}
		if ( is_plugin_active( 'trackship-for-woocommerce/trackship-for-woocommerce.php' ) ) {
			$menu_items[ 'trackship_for_woocommerce' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'TrackShip', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=trackship-dashboard' ),
				'meta'   => array(
					'title'  => __( 'TrackShip', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		$menu_items[ 'Dashboard' ] = array(
			'parent' => $trackship_for_woocommerce,
			'title'  => __( 'Dashboard', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'admin.php?page=trackship-dashboard' ),
			'meta'   => array(
				'title'  => __( 'Dashboard', 'woocommerce-shop-manager-admin-bar' )
			)
		);
		$menu_items[ 'Shipments' ] = array(
			'parent' => $trackship_for_woocommerce,
			'title'  => __( 'Shipments', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'admin.php?page=trackship-shipments' ),
			'meta'   => array(
				'title'  => __( 'Shipments', 'woocommerce-shop-manager-admin-bar' )
			)
		);
		$menu_items[ 'Logs_trackship' ] = array(
			'parent' => $trackship_for_woocommerce,
			'title'  => __( 'Logs', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'admin.php?page=trackship-logs' ),
			'meta'   => array(
				'title'  => __( 'Logs', 'woocommerce-shop-manager-admin-bar' )
			)
		);	
		$menu_items[ 'Settings' ] = array(
			'parent' => $trackship_for_woocommerce,
			'title'  => __( 'Settings', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'admin.php?page=trackship-for-woocommerce&tab=settingsz' ),
			'meta'   => array(
				'title'  => __( 'Settings', 'woocommerce-shop-manager-admin-bar' )
			)
		);	
		$menu_items[ 'Tools_trackship' ] = array(
			'parent' => $trackship_for_woocommerce,
			'title'  => __( 'Tools', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'admin.php?page=trackship-tools' ),
			'meta'   => array(
				'title'  => __( 'Tools', 'woocommerce-shop-manager-admin-bar' )
			)
		);
		/** Display "Products" section only for users with the capability 'edit_products' */
		
		
	
		$menu_items[ 'customers' ] = array(
			'parent' => $woocommerce_main_menu,
			'title'  => __( 'Customers', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-admin&path=/marketing' ),
			'meta'   => array(
				'title'  => __( 'Customers', 'woocommerce' )
			)
		);		


		/** Display "Coupons" section only for users with the capability 'edit_shop_coupon' */
		if ( current_user_can( 'edit_shop_coupons' ) ) {
			$menu_items[ 'coupons' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Coupons', 'woocommerce' ),
				'href'   => admin_url( 'edit.php?post_type=shop_coupon' ),
				'meta'   => array(
					'title'  => __( 'Coupons', 'woocommerce' )
				)
			);
		}  // end if

		/** Reports */
		$menu_items[ 'reports' ] = array(
			'parent' => $woocommerce_main_menu,
			'title'  => __( 'Reports', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-reports' ),
			'meta'   => array(
				'title'  => __( 'Reports', 'woocommerce' )
			)
		);
		
		$menu_items[ 'shop-settings' ] = array(
			'parent' => $woocommerce_main_menu,
			'title'  => __( 'Settings', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings' ),
			'meta'   => array(
				'title'  => __( 'Settings', 'woocommerce' )
			)
		);
		
		$menu_items[ 'general-settings' ] = array(
			'parent' => $shop_settings,
			'field'	 =>	'no-html',
			'title'  => __( 'General', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=general' ),
			'meta'   => array(
				'title'  => __( 'General', 'woocommerce' )
			)
		);
			
		$menu_items[ 'product-shop-settings' ] = array(
			'parent' => $shop_settings,
			'title'  => __( 'Products', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=products' ),
			'meta'   => array(
				'title'  => __( 'Products', 'woocommerce' )
			)
		);
		
		/** Display "Texes" section if enable*/
		if ( $enable_taxes ) {
			$menu_items[ 'taxes' ] = array(
				'parent' => $shop_settings,
				'title'  => __( 'Tax', 'woocommerce' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=tax' ),
				'meta'   => array(
					'title'  => __( 'Tax', 'woocommerce' )
				)
			);
		}
		
		$menu_items[ 'shipping-settings' ] = array(
			'parent' => $shop_settings,
			'field'	 =>	'no-html',
			'title'  => __( 'Shipping', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=shipping' ),
			'meta'   => array(
				'title'  => __( 'Shipping', 'woocommerce' )
			)
		);
		
		$menu_items[ 'shipping-zones' ] = array(
			'parent' => $shipping_settings,
			'title'  => __( 'Shipping zones', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=shipping' ),
			'meta'   => array(
				'title'  => __( 'Shipping zones', 'woocommerce' )
			)
		);
	
		$menu_items[ 'shipping-options' ] = array(
			'parent' => $shipping_settings,
			'title'  => __( 'Shipping options', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=shipping&section=options' ),
			'meta'   => array(
				'title'  => __( 'Shipping options', 'woocommerce' )
			)
		);
	
		$menu_items[ 'shipping-classes' ] = array(
			'parent' => $shipping_settings,
			'title'  => __( 'Shipping classes', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=shipping&section=classes' ),
			'meta'   => array(
				'title'  => __( 'Shipping classes', 'woocommerce' )
			)
		);
	
		$menu_items[ 'payment-settings' ] = array(
			'parent' => $shop_settings,
			'title'  => __( 'Payments', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=checkout' ),
			'meta'   => array(
				'title'  => __( 'Payments', 'woocommerce' )
			)
		);
		
		$installed_payment_methods = WC()->payment_gateways->payment_gateways();
		foreach ( $installed_payment_methods as $id=>$method ) {
			$menu_items[ $id ] = array(
				'parent' => $payment_settings,
				'title'  => __( $method->title ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=checkout&section=' . $id ),
				'meta'   => array(
					'title'  => __( $method->title )
				)
			);
		}	
		
		$menu_items[ 'account-settings' ] = array(
			'parent' => $shop_settings,
			'title'  => __( 'Accounts & Privacy', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=account' ),
			'meta'   => array(
				'title'  => __( 'Accounts & Privacy', 'woocommerce' )
			)
		);
		
		/** Orders Reports */
		$menu_items[ 'order-reports' ] = array(
			'parent' => $reports,
			'title'  => __( 'Orders', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders' ),
			'meta'   => array(
				'title'  => __( 'Orders', 'woocommerce' )
			)
		);
		
		/** Customer Reports */
		$menu_items[ 'customer-reports' ] = array(
			'parent' => $reports,
			'title'  => __( 'Customers', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=customers' ),
			'meta'   => array(
				'title'  => __( 'Customers', 'woocommerce' )
			)
		);		
		
		/** Stock Reports */
		$menu_items[ 'stock-reports' ] = array(
			'parent' => $reports,
			'title'  => __( 'Stock', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-reports&tab=stock' ),
			'meta'   => array(
				'title'  => __( 'Stock', 'woocommerce' )
			)
		);
				
		if ( $enable_taxes ) {
			/** Taxes Reports */
			$menu_items[ 'taxes-reports' ] = array(
				'parent' => $reports,
				'title'  => __( 'Taxes', 'woocommerce' ),
				'href'   => admin_url( 'admin.php?page=wc-reports&tab=taxes' ),
				'meta'   => array(
					'title'  => __( 'Taxes', 'woocommerce' )
				)
			);
		}
		/** Status */
		$menu_items[ 'status' ] = array(
			'parent' => $woocommerce_main_menu,
			'title'  => __( 'Status', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-status' ),
			'meta'   => array(
				'title'  => __( 'Status', 'woocommerce' )
			)
		);

		/** Status */
		$menu_items[ 'Extensions' ] = array(
			'parent' => $woocommerce_main_menu,
			'title'  => __( 'Extensions', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-addons' ),
			'meta'   => array(
				'title'  => __( 'Extensions', 'woocommerce' )
			)
		);
		if ( is_plugin_active( 'automatewoo/automatewoo.php' ) ) {
			$menu_items[ 'automatewoo' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'AutomateWoo', 'automatewoo' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-dashboard' ),
				'meta'   => array(
					'title'  => __( 'AutomateWoo', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		
		/** System Status Status */
		$menu_items[ 'system-status' ] = array(
			'parent' => $status,
			'title'  => __( 'System status', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-status' ),
			'meta'   => array(
				'title'  => __( 'System status', 'woocommerce' )
			)
		);
		
		/** Tools Status */
		$menu_items[ 'tools-status' ] = array(
			'parent' => $status,
			'title'  => __( 'Tools', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-status&tab=tools' ),
			'meta'   => array(
				'title'  => __( 'Tools', 'woocommerce' )
			)
		);
		
		/** Logs Status */
		$menu_items[ 'logs-status' ] = array(
			'parent' => $status,
			'title'  => __( 'Logs', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-status&tab=logs' ),
			'meta'   => array(
				'title'  => __( 'Logs', 'woocommerce' )
			)
		);
		
		/** Scheduled Actions Status */
		$menu_items[ 'scheduled-actions-status' ] = array(
			'parent' => $status,
			'title'  => __( 'Scheduled Actions', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-status&tab=action-scheduler' ),
			'meta'   => array(
				'title'  => __( 'Scheduled Actions', 'woocommerce' )
			)
		);
	
		$menu_items[ 'email-settings' ] = array(
			'parent' => $shop_settings,
			'title'  => __( 'Emails', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email' ),
			'meta'   => array(
				'title'  => __( 'Emails', 'woocommerce' )
			)
		);
		
		$menu_items[ 'new-order' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'New order', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_new_order' ),
			'meta'   => array(
				'title'  => __( 'New order', 'woocommerce' )
			)
		);
		
		$menu_items[ 'cancelled-order' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Cancelled order', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_cancelled_order' ),
			'meta'   => array(
				'target' => '',
				'title'  => __( 'Cancelled order', 'woocommerce' )
			)
		);
		
		$menu_items[ 'failed-order' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Failed order', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_failed_order' ),
			'meta'   => array(
				'title'  => __( 'Failed order', 'woocommerce' )
			)
		);
		
		$menu_items[ 'order-on-hold' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Order on-hold ', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_on_hold_order' ),
			'meta'   => array(
				'title'  => __( 'Order on-hold ', 'woocommerce' )
			)
		);
		
		$menu_items[ 'processing-order' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Processing order', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_processing_order' ),
			'meta'   => array(
				'title'  => __( 'Processing order', 'woocommerce' )
			)
		);

		if ( '1' == get_option( 'wc_ast_status_shipped' ) ) {
			if ( is_plugin_active( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ) ) {
				$menu_items[ 'completed-order' ] = array(
					'parent' => $email_settings,
					'title'  => __( 'Shipped order ', 'woocommerce' ),
					'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_completed_order' ),
					'meta'   => array(
						'title'  => __( 'Shipped order ', 'woocommerce' )
					)
				);
			}
		} else {
			$menu_items[ 'completed-order' ] = array(
				'parent' => $email_settings,
				'title'  => __( 'Completed order ', 'woocommerce' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_completed_order' ),
				'meta'   => array(
					'title'  => __( 'Completed order ', 'woocommerce' )
				)
			);	
		}
		
		$menu_items[ 'refunded-order' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Refunded order', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_refunded_order' ),
			'meta'   => array(
				'title'  => __( 'Refunded order', 'woocommerce' )
			)
		);
		
		$menu_items[ 'customer-note' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Customer note', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_note' ),
			'meta'   => array(
				'title'  => __( 'Customer note', 'woocommerce' )
			)
		);
		
		$menu_items[ 'reset-password' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'Reset password', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_reset_password' ),
			'meta'   => array(
				'title'  => __( 'Reset password', 'woocommerce' )
			)
		);
		
		$menu_items[ 'new-account' ] = array(
			'parent' => $email_settings,
			'title'  => __( 'New account', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_new_account' ),
			'meta'   => array(
				'title'  => __( 'New account', 'woocommerce' )
			)
		);
		
		if ( is_plugin_active( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ) ) {	
			$menu_items[ 'delivered-order' ] = array(
				'parent' => $email_settings,
				'title'  => __( 'Delivered order', 'woo-advanced-shipment-tracking' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wc_email_customer_delivered_order' ),
				'meta'   => array(
					'title'  => __( 'Delivered order', 'woo-advanced-shipment-tracking' )
				)
			);
		}
		
		if ( is_plugin_active( 'woo-bit-payment-gateway/woocommerce-bit-payment-gateway.php' ) ) {	
			$menu_items[ 'bit-payment' ] = array(
				'parent' => $email_settings,
				'title'  => __( 'Bit Payment', 'wc-bit-payment-gateway' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=bit_payment_email' ),
				'meta'   => array(
					'title'  => __( 'Bit Payment', 'wc-bit-payment-gateway' )
				)
			);
		}
		
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {	
			$menu_items[ 'subscription-emails' ] = array(
				'parent' => $shop_settings,
				'title'  => __( 'Subscription Emails', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email' ),
				'meta'   => array(
					'title'  => __( 'Subscription Emails', 'woocommerce-shop-manager-admin-bar' )
				)
			);
		
			$menu_items[ 'new-renewal-order' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'New Renewal Order', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_new_renewal_order' ),
				'meta'   => array(
					'title'  => __( 'New Renewal Order', 'woocommerce-subscriptions' )
				)
			);
		
			$menu_items[ 'subscription-switched' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Subscription Switched', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_new_switch_order' ),
				'meta'   => array(
					'title'  => __( 'Subscription Switched', 'woocommerce-subscriptions' )
				)
			);
		
			$menu_items[ 'processing-renewal-order' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Processing Renewal order', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_processing_renewal_order' ),
				'meta'   => array(
					'title'  => __( 'Processing Renewal order', 'woocommerce-subscriptions' )
				)
			);
		
			$menu_items[ 'completed-renewal-order' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Completed Renewal Order', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_completed_renewal_order' ),
				'meta'   => array(
					'title'  => __( 'Completed Renewal Order', 'woocommerce-subscriptions' )
				)
			);
		
			$menu_items[ 'on-hold-renewal-order' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'On-hold Renewal Order', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_customer_on_hold_renewal_order' ),
				'meta'   => array(
					'title'  => __( 'On-hold Renewal Order', 'woocommerce-subscriptions' )
				)
			);
		
			$menu_items[ 'on-hold-renewal-order' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'On-hold Renewal Order', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_customer_on_hold_renewal_order' ),
				'meta'   => array(
					'title'  => __( 'On-hold Renewal Order', 'woocommerce-subscriptions' )
				)
			);
			
			$menu_items[ 'subscription-switch-complete' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Subscription Switch Complete', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_completed_switch_order' ),
				'meta'   => array(
					'title'  => __( 'Subscription Switch Complete', 'woocommerce-subscriptions' )
				)
			);
			
			$menu_items[ 'customer-renewal-invoice' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Customer Renewal Invoice', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_customer_renewal_invoice' ),
				'meta'   => array(
					'title'  => __( 'Customer Renewal Invoice', 'woocommerce-subscriptions' )
				)
			);
			
			$menu_items[ 'cancelled-subscription' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Cancelled Subscription', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_cancelled_subscription' ),
				'meta'   => array(
					'title'  => __( 'Cancelled Subscription', 'woocommerce-subscriptions' )
				)
			);
			
			$menu_items[ 'expired-subscription' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Expired Subscription', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_expired_subscription' ),
				'meta'   => array(
					'title'  => __( 'Expired Subscription', 'woocommerce-subscriptions' )
				)
			);
			
			$menu_items[ 'suspended-subscription' ] = array(
				'parent' => $subscription_emails,
				'title'  => __( 'Suspended Subscription', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=email&section=wcs_email_on_hold_subscription' ),
				'meta'   => array(
					'title'  => __( 'Suspended Subscription', 'woocommerce-subscriptions' )
				)
			);
			
			$menu_items[ 'subscription-reports' ] = array(
				'parent' => $reports,
				'title'  => __( 'Subscription', 'woocommerce-subscriptions' ),
				'href'   => admin_url( 'admin.php?page=wc-reports&tab=subscriptions' ),
				'meta'   => array(
					'title'  => __( 'Subscription', 'woocommerce-subscriptions' )
				)
			);			
		}
		
		$menu_items[ 'integration-settings' ] = array(
			'parent' => $shop_settings,
			'title'  => __( 'Integration', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=integration' ),
			'meta'   => array(
				'title'  => __( 'Integration', 'woocommerce' )
			)
		);
		
		$menu_items[ 'advance-settings' ] = array(
			'parent' => $shop_settings,
			'title'  => __( 'Advanced', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=wc-settings&tab=advanced' ),
			'meta'   => array(
				'title'  => __( 'Advanced', 'woocommerce' )
			)
		);
		
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			$menu_items[ 'subscriptions-settings' ] = array(
				'parent' => $shop_settings,
				'title'  => __( 'Subscriptions', 'woocommerce' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=subscriptions' ),
				'meta'   => array(
					'title'  => __( 'Subscriptions', 'woocommerce' )
				)
			);
		}
		
		if ( is_plugin_active( 'affiliate-for-woocommerce/affiliate-for-woocommerce.php' ) ) {
			$menu_items[ 'affiliate-settings' ] = array(
				'parent' => $shop_settings,
				'title'  => __( 'Affiliate', 'affiliate-for-woocommerce' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=affiliate-for-woocommerce-settings' ),
				'meta'   => array(
					'title'  => __( 'Affiliate', 'affiliate-for-woocommerce' )
				)
			);
		}
		
		if ( is_plugin_active( 'woocommerce-social-login/woocommerce-social-login.php' ) ) {
			$menu_items[ 'woocommerce-social-login' ] = array(
				'parent' => $shop_settings,					
				'title'  => __( 'Social Login Subscriptions Affiliate', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=general' ),
				'meta'   => array(
					'title'  => __( 'Social Login Subscriptions Affiliate', 'woocommerce-shop-manager-admin-bar' )
				)
			);
		}
		
		if ( is_plugin_active( 'woo-product-country-base-restrictions/woocommerce-product-country-base-restrictions.php' ) || is_plugin_active( 'country-base-restrictions-pro-addon/country-base-restrictions-pro-addon.php' ) ) {
			$menu_items[ 'woocommerce-product-country-base-restrictions_shopparent' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Country Restrictions', 'woo-product-country-base-restrictions' ),
				'href'   => admin_url( 'admin.php?page=woocommerce-product-country-base-restrictions' ),
				'meta'   => array(
					'title'  => __( 'Country Restrictions', 'woo-product-country-base-restrictions' ),
				)
			);
		}
		
		if ( is_plugin_active( 'woo-ajax-loginregister/woocommerce-ajax-login-register.php' ) ) {
			$menu_items[ 'woo-ajax-loginregister' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Ajax Login/Register', 'woo-ajax-loginregister' ),
				'href'   => admin_url( 'admin.php?page=ajax_login_register' ),
				'meta'   => array(
					'title'  => __( 'Ajax Login/Register', 'woo-ajax-loginregister' ),
				)
			);
		}
		
		if ( is_plugin_active( 'woocommerce-email-control/ec-email-control.php' ) ) {
			$menu_items[ 'email_customizer_shopparent' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Email Customizer', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=woocommerce_email_control' ),
				'meta'   => array(
					'title'  => __( 'Email Customizer', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
	
		if ( is_plugin_active( 'woocommerce_email_cuztomizer_with_drag_and_drop_builder/woo-email-customizer-page-builder.php' ) ) {
			$menu_items[ 'drag-drop_email_customizer_shopparent' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Drag and Drop Email Builder', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=woo_email_customizer_page_builder' ),
				'meta'   => array(
					'title'  => __( 'Drag and Drop Email Builder', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		
		if ( is_plugin_active( 'kadence-woocommerce-email-designer/kadence-woocommerce-email-designer.php' ) ) {
			$menu_items[ 'kadence_email_customizer_shopparent' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Email Customizer', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => add_query_arg( array(
						'kt-woomail-customize' => '1',
						'url'                  => urlencode( add_query_arg( array( 'kt-woomail-preview' => '1' ), home_url( '/' ) ) ),
						'return'               => urlencode( Kadence_Woomail_Woo::get_email_settings_page_url() ),
					), admin_url( 'customize.php' ) ),
				'meta'   => array(
					'title'  => __( 'Email Customizer', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
			
		if ( is_plugin_active( 'custom-order-numbers-for-woocommerce/custom-order-numbers-for-woocommerce.php' ) ) {
			$menu_items[ 'Custom_Order_Numbers_shopparent' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Custom Order Numbers', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=wc-settings&tab=alg_wc_custom_order_numbers' ),
				'meta'   => array(
					'title'  => __( 'Custom Order Numbers', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		
		if ( is_plugin_active( 'affiliate-for-woocommerce/affiliate-for-woocommerce.php' ) ) {
			$menu_items[ 'affiliate-shopparent' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Affiliate Dashboard', 'affiliate-for-woocommerce' ),
				'href'   => admin_url( 'admin.php?page=affiliate-for-woocommerce#!/dashboard' ),
				'meta'   => array(
					'title'  => __( 'Affiliate Dashboard', 'affiliate-for-woocommerce' ),
				)
			);
		}
		
		if ( is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			$menu_items[ 'contact' ] = array(
				'parent' => $woocommerce_main_menu,
				'id'     => $plugin_setting,
				'title'  => __( 'Contact Forms 7', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=wpcf7' ),
				'meta'   => array(
					'title'  => __( 'Contact Forms 7', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		if ( current_user_can( 'edit_pages' ) ) {
			$menu_items[ 'pages' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Pages', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'edit.php?post_type=page' ),
				'meta'   => array(
					'title'  => __( 'Pages', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			
			$menu_items[ 'all_pages' ] = array(
				'parent' => $pages,
				'title'  => __( 'All Pages' ),
				'href'   => admin_url( 'edit.php?post_type=page' ),
				'meta'   => array(
					'title'  => __( 'All Pages' )
				)
			);
		}
		
		if ( current_user_can( 'edit_posts' ) ) {
			$menu_items[ 'posts' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Posts'),
				'href'   => admin_url( 'edit.php' ),
				'meta'   => array(
					'title'  => __( 'Posts' )
				)
			);
		
			$menu_items[ 'all_posts' ] = array(
				'parent' => $posts,
				'title'  => __( 'All Posts' ),
				'href'   => admin_url( 'edit.php' ),
				'meta'   => array(
					'title'  => __( 'All Posts' )
				)
			);
			
			$menu_items[ 'categories' ] = array(
				'parent' => $posts,
				'title'  => __( 'Categories', 'woocommerce' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=category' ),
				'meta'   => array(
					'title'  => __( 'Categories', 'woocommerce' )
				)
			);
			
			$menu_items[ 'tags' ] = array(
				'parent' => $posts,
				'title'  => __( 'Tags', 'woocommerce' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=post_tag' ),
				'meta'   => array(
					'title'  => __( 'Tags', 'woocommerce' )
				)
			);
		
		}
		$menu_items[ 'users' ] = array(
			'parent' => $wsmab_main,
			'id'     => $woocommerce,
			'title'  => __( 'Users', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'users.php' ),
			'meta'   => array(
				'title'  => __( 'Users', 'woocommerce-shop-manager-admin-bar' )
			)
		);
			
		$menu_items[ 'all_users' ] = array(
			'parent' => $users,
			'title'  => __( 'All Users' ),
			'href'   => admin_url( 'users.php' ),
			'meta'   => array(
				'title'  => __( 'All Users' )
			)
		);
		
		$result = count_users();
		foreach ( $result['avail_roles'] as $key => $val ) {
			if ( 'none' != $key ) {
				if ( 'bbp' == substr( $key, 0, 3 ) ) {
					$key = str_replace('bbp_', '', $key);
				}
				$role = str_replace( '_', ' ', $key );
				$menu_items[ $role ] = array(
					'parent' => $all_users,
					'title'  => __( ucfirst( $role ) ) . ' (' . ( $val ) . ')',
					'href'   => '',
					'meta'   => array(
						'title'  => __( ucfirst($role) )
					)
				);
			}
		}
		
		$menu_items[ 'user_profile' ] = array(
			'parent' => $users,
			'title'  => __( 'Your Profile', 'woocommerce' ),
			'href'   => admin_url( 'profile.php' ),
			'meta'   => array(
				'title'  => __( 'Your Profile', 'woocommerce' )
			)
		);
		
		$theme = wp_get_theme(); // gets the current theme
		if ( 'Flatsome' == $theme->name || 'Flatsome Child' == $theme->name || 'Flatsome' == $theme->parent_theme ) {
			$menu_items[ 'ux-blocks' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'UX Blocks'),
				'href'   => admin_url( 'edit.php?post_type=blocks' ),
				'meta'   => array(
					'title'  => __( 'UX Blocks' )
				)
			);
			
			$menu_items[ 'categories' ] = array(
				'parent' => $ux_blocks,
				'title'  => __( 'Categories', 'woocommerce' ),
				'href'   => admin_url( 'edit-tags.php?taxonomy=block_categories&post_type=blocks' ),
				'meta'   => array(
					'title'  => __( 'Categories', 'woocommerce' )
				)
			);
		}
		
		if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			$menu_items[ 'wpml' ] = array(
				'parent' => $wsmab_main,
				'id'     => $plugin_setting,
				'title'  => __( 'WPML', 'sitepress-multilingual-cms' ),
				'href'   => admin_url( 'admin.php?page=sitepress-multilingual-cms/menu/languages.php' ),
				'meta'   => array(
					'title'  => __( 'WPML', 'sitepress-multilingual-cms' ),
				)
			);
			
			if ( is_plugin_active( 'wpml-string-translation/plugin.php' ) ) {
				$menu_items[ 'string-translation' ] = array(
					'parent' => $wpml,
					'title'  => __( 'String Translation', 'wpml-string-translation' ),
					'href'   => admin_url( 'admin.php?page=wpml-string-translation/menu/string-translation.php' ),
					'meta'   => array(
						'title'  => __( 'String Translation', 'wpml-string-translation' ),
					)
				);
			}	
		}

		if ( is_plugin_active( 'wedocs/wedocs.php' ) ) {
			$menu_items[ 'wp-wedocs' ] = array(
				'parent' => $wsmab_main,
				//'id'     => $wp_wedocs,
				'title'  => __( 'weDocs', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=wedocs' ),
				'meta'   => array(
					'title'  => __( 'weDocs', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		//elementor plugin

		if ( is_plugin_active( 'elementor/elementor.php' ) ) {
			$menu_items[ 'elementor_main_menu' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'Elementor', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=elementor' ),
				'meta'   => array(
					'title'  => __( 'Elementor', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
		}
		$menu_items[ 'Setting' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Setting', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor' ),
			'meta'   => array(
				'title'  => __( 'Setting', 'woocommerce' )
			)
		);
		$menu_items[ 'Role Manager' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Role Manager', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor-role-manager' ),
			'meta'   => array(
				'title'  => __( 'Role Manager', 'woocommerce' )
			)
		);
		$menu_items[ 'Tools' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Tools', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor-tools' ),
			'meta'   => array(
				'title'  => __( 'Tools', 'woocommerce' )
			)
		);
		$menu_items[ 'System Info' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'System Info', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor-system-info' ),
			'meta'   => array(
				'title'  => __( 'System Info', 'woocommerce' )
			)
		);
		$menu_items[ 'Getting Started' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Getting Started', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor-getting-started' ),
			'meta'   => array(
				'title'  => __( 'Getting Started', 'woocommerce' )
			)
		);
		$menu_items[ 'Get Help' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Get Help', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=go_knowledge_base_site' ),
			'meta'   => array(
				'title'  => __( 'Get Help', 'woocommerce' )
			)
		);
		$menu_items[ 'Submissions' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Submissions', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=e-form-submissions' ),
			'meta'   => array(
				'title'  => __( 'Submissions', 'woocommerce' )
			)
		);
		$menu_items[ 'Custom Fonts' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Custom Fonts', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor_custom_fonts' ),
			'meta'   => array(
				'title'  => __( 'Custom Fonts', 'woocommerce' )
			)
		);
		$menu_items[ 'Custom Icons' ] = array(
			'parent' => $elementor_main_menu,
			'title'  => __( 'Custom Icons', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=elementor_custom_icons' ),
			'meta'   => array(
				'title'  => __( 'Custom Icons', 'woocommerce' )
			)
		);
		 
		if ( is_plugin_active( 'elementor-pro/elementor-pro.php' ) ) {
			$menu_items[ 'Custom Code' ] = array(
				'parent' => $elementor_main_menu,
				'title'  => __( 'Custom Code', 'woocommerce' ),
				'href'   => admin_url( 'edit.php?post_type=elementor_snippet' ),
				'meta'   => array(
					'title'  => __( 'Custom Code', 'woocommerce' )
				)
			);
			$menu_items[ 'License' ] = array(
				'parent' => $elementor_main_menu,
				'title'  => __( 'License', 'woocommerce' ),
				'href'   => admin_url( 'admin.php?page=elementor-license' ),
				'meta'   => array(
					'title'  => __( 'License', 'woocommerce' )
				)
			);
		} else {
			$menu_items[ ' Go Pro' ] = array(
				'parent' => $elementor_main_menu,
				'title'  => __( ' Go Pro', 'woocommerce' ),
				'href'   => admin_url( 'admin.php?page=go_elementor_pro' ),
				'meta'   => array(
					'title'  => __( ' Go Pro', 'woocommerce' )
				)
			);
		}
		
		
		// elementor plugin end
			// automatewoo plugin start
		if ( is_plugin_active( 'automatewoo/automatewoo.php' ) ) {
				$menu_items[ 'automatewoo' ] = array(
					'parent' => $wsmab_main,
					'id'     => $woocommerce,
					'title'  => __( 'AutomateWoo', 'automatewoo' ),
					'href'   => admin_url( 'admin.php?page=automatewoo-dashboard' ),
					'meta'   => array(
						'title'  => __( 'AutomateWoo', 'woocommerce-shop-manager-admin-bar' ),
					)
				);
		}
			
			
			
			$menu_items[ 'Dashboard_automatewoo' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Dashboard', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-dashboard' ),
				'meta'   => array(
					'title'  => __( 'Dashboard', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Workflows' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Workflows', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'edit.php?post_type=aw_workflow' ),
				'meta'   => array(
					'title'  => __( 'Workflows', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Logs' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Logs', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-logs' ),
				'meta'   => array(
					'title'  => __( 'Logs', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Queue' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Queue', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-queue' ),
				'meta'   => array(
					'title'  => __( 'Queue', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Carts' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Carts', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-carts' ),
				'meta'   => array(
					'title'  => __( 'Carts', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Guests' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Guests', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-guests' ),
				'meta'   => array(
					'title'  => __( 'Guests', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Opt-ins' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Opt-ins', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-opt-ins' ),
				'meta'   => array(
					'title'  => __( 'Opt-ins', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Reports' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Reports', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-reports' ),
				'meta'   => array(
					'title'  => __( 'Reports', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Tools_automatewoo' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Tools', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-tools' ),
				'meta'   => array(
					'title'  => __( 'Tools', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			$menu_items[ 'Settings_automatewoo' ] = array(
				'parent' => $automatewoo,
				'title'  => __( 'Settings', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=automatewoo-settings' ),
				'meta'   => array(
					'title'  => __( 'Settings', 'woocommerce-shop-manager-admin-bar' )
				)
			);
			
			// automatewoo plugin end
		$menu_items[ 'wp-setting' ] = array(				
			'parent' => $wsmab_main,
			'id'     => $wp_setting,
			'title'  => __( 'Settings', 'woocommerce' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_shop_manager_admin_option&tab=settings' ),
			'meta'   => array(
				'title'  => __( 'Setting', 'woocommerce' )
			)
		);
		
			if ( is_plugin_active( 'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php' ) || class_exists('Ast_Pro') ) {
			$menu_items[ 'ast-settings' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Shipment Tracking', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=woocommerce-advanced-shipment-tracking' ),
				'meta'   => array(
					'title'  => __( 'Shipment Tracking', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
			if ( is_plugin_active( 'wc-dynamic-pricing-and-discounts/wc-dynamic-pricing-and-discounts.php' ) ) {
			$menu_items[ 'wc-dynamic-pricing-and-discounts' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Pricing & Discounts', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=rp_wcdpd_settings' ),
				'meta'   => array(
					'title'  => __( 'Pricing & Discounts', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
				
			if ( is_plugin_active( 'advanced-local-pickup-for-woocommerce/woo-advanced-local-pickup.php' ) || class_exists('Advanced_local_pickup_PRO') ) {
			$menu_items[ 'woocommerce-local-pickup' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Local Pickup', 'woo-local-pickup' ),
				'href'   => admin_url( 'admin.php?page=local_pickup' ),
				'meta'   => array(
					'title'  => __( 'Local Pickup', 'woo-local-pickup' ),
				)
			);
			}
		
			if ( is_plugin_active( 'advanced-order-status-manager/advanced-order-status-manager.php' ) ) {
			$menu_items[ 'advanced-order-status-manager' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Order Statuses', 'advanced-order-status-manager' ),
				'href'   => admin_url( 'admin.php?page=advanced_order_status_manager' ),
				'meta'   => array(
					'title'  => __( 'Order Statuses', 'advanced-order-status-manager' ),
				)
			);
			}
		
			if ( is_plugin_active( 'customer-email-verification-for-woocommerce/customer-email-verification-for-woocommerce.php' ) || is_plugin_active( 'customer-email-verification-pro/customer-email-verification-pro.php' )  || is_plugin_active( 'customer-email-verification/customer-email-verification.php' ) ) {
			$menu_items[ 'woocommerce-customer-verification' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Customer Verification', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=customer-email-verification-for-woocommerce' ),
				'meta'   => array(
					'title'  => __( 'Customer Verification', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
		
			if ( is_plugin_active( 'woo-advanced-sales-report-email/woocommerce-advanced-sales-report-email.php' ) || is_plugin_active( 'sales-report-email-pro/sales-report-email-pro.php' ) ) {
			$menu_items[ 'woocommerce-sales-report-email' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Sales Report Email', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=woocommerce-advanced-sales-report-email' ),
				'meta'   => array(
					'title'  => __( 'Sales Report Email', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
		
			if ( is_plugin_active( 'woo-sales-by-country-reports/woocommerce-sales-by-country-report.php' ) ) {
			$menu_items[ 'woocommerce-sales-by-country' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Sales Report Country', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=wc-reports&tab=orders&report=sales_by_country' ),
				'meta'   => array(
					'title'  => __( 'Sales Report Country', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
		
			if ( function_exists( 'SMSWOO' ) ) {
			$menu_items[ 'sms-for-woocommerce' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'SMS for WooCommerce', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=sms-for-woocommerce' ),
				'meta'   => array(
					'title'  => __( 'SMS for WooCommerce', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
			if ( is_plugin_active( 'trackship-for-woocommerce/trackship-for-woocommerce.php' ) ) {
			$menu_items[ 'trackship_for_woocommerce' ] = array(
				'parent' => $wsmab_main,
				'id'     => $woocommerce,
				'title'  => __( 'TrackShip', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=trackship-dashboard' ),
				'meta'   => array(
					'title'  => __( 'TrackShip', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}

			if ( is_plugin_active( 'refund-for-woocommerce/refund-for-woocommerce.php' ) ) {
			$menu_items[ 'return-refund' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Refund for WooCommerce', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'admin.php?page=woo-refund' ),
				'meta'   => array(
					'title'  => __( 'Refund for WooCommerce', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
		
			if ( is_plugin_active( 'woocommerce-bookings/woocommerce-bookings.php' ) ) {
			$menu_items[ 'wc_bookings' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'WooCommerce Bookings', 'woocommerce-shop-manager-admin-bar' ),
				'href'   => admin_url( 'edit.php?post_type=wc_booking' ),
				'meta'   => array(
					'title'  => __( 'WooCommerce Bookings', 'woocommerce-shop-manager-admin-bar' ),
				)
			);
			}
		
		$menu_items[ 'woocommerce-admin-bar-option' ] = array(
			'parent' => $woocommerce_main_menu,
			'title'  => __( 'Shop Manager Admin', 'woocommerce-shop-manager-admin-bar' ),
			'href'   => admin_url( 'admin.php?page=woocommerce_shop_manager_admin_option' ),
			'meta'   => array(
				'title'  => __( 'Shop Manager Admin', 'woocommerce-shop-manager-admin-bar' ),
			)
		);
		
			if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			$menu_items[ 'woocommerce-multilingual' ] = array(
				'parent' => $woocommerce_main_menu,
				'title'  => __( 'Multilingual', 'woocommerce-multilingual' ),
				'href'   => admin_url( 'admin.php?page=wpml-wcml' ),
				'meta'   => array(
					'title'  => __( 'Multilingual', 'woocommerce-multilingual' ),
				)
			);
			}
		
		$menu_items[ 'powered-by' ] = array(
			'parent' => $wsmab_main,
			'title'  => __( 'Powered by zorem' ),
			'href'   => 'http://www.zorem.com/',
			'meta'   => array(
				'target' => 'blank',
				'title'  => __( 'Powered by zorem' ),
				'class' => 'zorem_powered_by'
			)
		);
		$nonce = wp_create_nonce( 'sma_search_user_nonce' );
		// search form
		$html = '<div id="sma_switch_user_search">';
			$html .= '<form action="#" method="POST" id="sma_user_search_form" class="clear" autocomplete="off">';
				$html .= '<input type="text" name="sma_search_username" id="sma_search_username" placeholder="' . __('Username or ID', 'fast-user-switching') . '">';
				$html .= '<input type="submit" value="' . __('Search', 'fast-user-switching') . '" id="sma_switch_user_search_submit">';
				$html .= '<input type="hidden" id="sma_search_user_nonce" name="sma_search_user_nonce" value="' . $nonce . '">';
				$html .= '<div class="wp-clearfix"></div>';
			$html .= '</form>';
			$html .= '<div id="sma_user_search_result"></div>';
			$html .= '<div id="sma_recent_users">';
				$html .= '<strong>' . __('Recent Users', 'woocommerce-shop-manager-admin-bar') . '</strong>';
				$html .= '<hr>' . woo_shop_manager_admin()->switch_user->recent_users();
			$html .= '</div>';
		$html .= '</div>';
		
		$allo_sma = get_sma_switch_user_search( 'Allow_Switch_to_Customer', 'yes' );
		$allo_admini = get_sma_switch_user_search( 'Enable_Switch_to_Customer', 'yes' );
		
		$current_role = wp_get_current_user();
			foreach ( $current_role->roles as $key=>$value ) {
				if ( 'yes' == $allo_admini && ( 'administrator' == $value || 'yes' == $allo_sma ) ) {
				/** Add the top-level menu Switch user */
				$wp_admin_bar->add_menu( array(
					'id'    => 'wsmab_main_switch_user',
					'title' => 'Switch User',
				) );
				$wp_admin_bar->add_menu(
					array(
						'id'		=> 'user_serch',
						'parent'	=> 'wsmab_main_switch_user',
						'title'		=> $html,
					)
				);
				}
			}
		
		/** Loop through the menu items */
			foreach ( $menu_items as $id => $menu_item ) {
			
			/** Add in the item ID */
			$menu_item[ 'id' ] = $prefix . $id;

			/** Add meta target to each item where it's not already set, so links open in new window/tab */
		
		
			$current_role = wp_get_current_user();
				foreach ( $current_role->roles as $key=>$value ) {
					if ( 'administrator' == $value ) {
						if ( !isset($admin_menu[$id]) || ( isset($admin_menu[$id]) && 'no' != $admin_menu[$id] ) ) {
						/** Add menu items */
						$wp_admin_bar->add_menu( $menu_item );
						}
					}
					if ( 'shop_manager' == $value ) {
						if ( !isset($admin_menu[$id . '_sm']) || ( isset($admin_menu[$id . '_sm']) && 'no' != $admin_menu[$id . '_sm'] ) ) {
						/** Add menu items */
						$wp_admin_bar->add_menu( $menu_item );
						}
					}
				}
			}	
		
		$wp_admin_bar->add_group( array(
			'parent' => $wsmab_main,
			'id'     => $wcgroup,
			'meta'   => array( 'class' => 'ab-sub-secondary' )
		) );
	}
	return $menu_items;
}
