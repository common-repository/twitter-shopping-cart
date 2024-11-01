<?php
	/* Shopp extention for Twitter Shopping Cart */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
			
	class wptsc_shopp extends wptsc_ecommerce {
		
		public $product_var = 'shopp_product';
		
		function __construct() {
			
			if (!class_exists( 'Shopp' )) :
				return;
			endif;
			$this->set_active_store(get_class());
		}
		
		function add_to_cart($product_id, $quantity = 1) {
			
			shopp_add_cart_product($product_id, $quantity);
			
		}
	
		
	}
?>