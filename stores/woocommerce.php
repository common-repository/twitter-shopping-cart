<?php
	/* WooCommerce extention for Twitter Shopping Cart */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
			
	class wptsc_woocommerce extends wptsc_ecommerce {
		
		public $product_var = 'product';
		
		function __construct() {
			
			if (!class_exists( 'WooCommerce' )) :
				return;
			endif;
			$this->set_active_store(get_class());
		}
		
		function add_to_cart($product_id, $quantity = 1) {
		
			return WC()->cart->add_to_cart( $product_id, $quantity );
			
		}
	
		
	}
?>