<?php
	/* WP e-Commerce extention for Twitter Shopping Cart */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
			
	class wptsc_wpecommerce extends wptsc_ecommerce {
		
		public $product_var = 'wpsc-product';
		
		function __construct() {
			
			if (!class_exists( 'WP_eCommerce' )) :
				return;
			endif;
			$this->set_active_store(get_class());
		}
		
		function add_to_cart($product_id, $quantity = 1) {
		
			global $wpsc_cart;
			
			/// default values
			$default_parameters = array();
			$default_parameters['variation_values'] = null;
			$default_parameters['quantity'] = 1;
			$default_parameters['provided_price'] = null;
			$default_parameters['comment'] = null;
			$default_parameters['time_requested'] = null;
			$default_parameters['custom_message'] = null;
			$default_parameters['file_data'] = null;
			$default_parameters['is_customisable'] = false;
			$default_parameters['meta'] = null;
	
			$cart_item = $wpsc_cart->set_item( $product_id , $default_parameters );
			
		}
	
		
	}
?>