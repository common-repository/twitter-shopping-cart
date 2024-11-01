<?php
	/* Cart66 extention for Twitter Shopping Cart */
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
			
	class wptsc_cart66 extends wptsc_ecommerce {
		
		public $product_var = '';
		
		function __construct() {
			
			if (!class_exists( 'Cart66' )) :
				return;
			endif;
			$this->set_active_store(get_class());
		}
		
		function add_to_cart($product_id, $quantity = 1) {
			
			echo 'add to cart cart66';
			
			$_POST['cart66ItemId'] = $product_id;
			Cart66Session::get('Cart66Cart')->addToCart();
			
		}
	
		
	}
?>