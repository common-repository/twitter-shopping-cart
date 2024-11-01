<?php
	/* Jigoshop extention for Twitter Shopping Cart */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
			
	class wptsc_jigoshop extends wptsc_ecommerce {
		
		public $product_var = 'product';
		
		function __construct() {
			
			if (!class_exists( 'jigoshop' )) :
				return;
			endif;
			$this->set_active_store(get_class());
		}
		
		function add_to_cart($product_id, $quantity = 1) {
			if (class_exists('jigoshop_cart')) :
				jigoshop_cart::add_to_cart($product_id, $quantity);
			endif;
		}
	
		
	}
?>