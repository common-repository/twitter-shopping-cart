<?php
	class wptsc_ecommerce {
		
		public $active_store = false;
		public $product_var = 'product';
			
		function __construct() {
			
			$this->load_stores();
		}
	
		public function load_stores() {
			//global $wptsc_store;
			foreach ( glob( plugin_dir_path( __FILE__ ).'../stores/*.php' ) as $file ) :
				include_once $file;
				
				$path 			= pathinfo($file);
				$filename 		= $path['filename'];				
				$class_name 	= 'wptsc_'.$filename;
				$store			= new $class_name();
				$active_store	= $store->active_store;
				if ($active_store) :
					$this->set_active_store($active_store);
					$this->store = $store;
					break;
					//we only work with 1 store so far, so when we find a store thats installed on your WP installation we use that
				endif;
			endforeach;
			
			//var_dump($this->get_active_store());
		}
		
		function set_active_store($store) {
			$this->active_store = $store;
		}
		
		function get_active_store() {
			return $this->active_store;	
		}
		
		function add_to_cart($product_id, $quantity = 1) {
			
			if (method_exists($this->store,'add_to_cart')) :
				//TODO check if we can validate of adding to cart was succesful
				$val =  $this->store->add_to_cart($product_id);				
			endif;
			
		}
		
		
	}
?>