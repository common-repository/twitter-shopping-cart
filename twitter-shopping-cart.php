<?php
/*
Plugin Name: Twitter to Shopping Cart
Plugin URI: http://onlineboswachters.nl
Description: Use a unique hashtag (like Amazon does) and everyone that connects their Twitter username to their User Account and uses the hashtag as a reply to one of your products on your e-commerce store will automatically be added to their shopping cart.
Version: 0.1
Author: Online Boswachters
Author URI: http://onlineboswachters.nl
*/

/* Copyright 2014 Online Boswachters (email : info@onlineboswachters.nl) */

$pluginurl = plugin_dir_url(__FILE__);	
define( 'wptsc_FRONT_URL', $pluginurl );
define( 'wptsc_URL', plugin_dir_url(__FILE__) );
define( 'wptsc_PATH', plugin_dir_path(__FILE__) );
define( 'wptsc_BASENAME', plugin_basename( __FILE__ ) );
define( 'wptsc_VERSION', '0.1' );

class wptsc {
	
	private $db_version = wptsc_VERSION;
	private $table_name = 'wptsc';
	
	function __construct() {
		
		register_activation_hook( __FILE__, array(&$this,'wptsc_activate'));
		//register_activation_hook( __FILE__, array(&$this,'wptsc_install_data'));
		//register_deactivation_hook( __FILE__, array(&$this,'wptsc_deactivation'));
		//register_uninstall_hook( __FILE__, array(&$this,'wptsc_uninstall'));
		//add_action( 'plugins_loaded', array(&$this,'_update_db_check') );
		//add_action( 'init', array(&$this, 'load_classes') );
		$this->get_options();
		if (isset($this->options)) $this->load_classes();
		
		/* CRON */
		add_action( 'do_wptsc', array(&$this, 'do_wptsc') );
		//frontend hook only
		add_action( 'wp', array(&$this, 'wptsc_setup_schedule') );
		
		if (is_admin()) :
			$this->add_admin_includes();
			//add_action( 'admin_enqueue_scripts', array(&$this, 'enqueue_scripts'), 11);
			add_action( 'admin_init', array( $this, 'options_init' ) );
		else :
			add_action( 'wp', array( $this, 'do_wptsc_frontend' ) );
		endif;
		
		if (isset($_GET['wptsc_manual']) && $_GET['wptsc_manual'] == 1) :
			$this->do_wptsc();
		endif;
		
	}
	
	public function load_classes() {
        foreach ( glob( plugin_dir_path( __FILE__ ).'classes/*.php' ) as $file ) :
            include_once $file;
		endforeach;
    }
	
	/* ACTIVATE PLUGIN */
	function wptsc_activate() {
		
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );
	
	   	global $wpdb;
		
		$table_name = $wpdb->prefix . $this->table_name; 
		
		$sql = "CREATE TABLE $table_name (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  user_id int(11) NOT NULL,
		  tweet_id bigint(11) NOT NULL,
		  tweet_username varchar(5) NOT NULL,
		  tweet_content text NOT NULL,
		  tweet_url VARCHAR(55) DEFAULT '' NOT NULL,
		  tweet_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  tweet_parent_id bigint(11) NOT NULL,
		  post_id int(11) NOT NULL,
		  script_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  insert_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  UNIQUE KEY id (id)
		);";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		add_option( "wptsc_db_version", $this->db_version );
		
		wp_schedule_event( time(), 'hourly', 'do_wptsc' );
	}
	
	/* UNINSTALL */
	function wptsc_uninstall() {
		
		if ( ! current_user_can( 'activate_plugins' ) )
			return;
		check_admin_referer( 'bulk-plugins' );
	}
	
	/* DEACTIVATE PLUGIN */
	function wptsc_deactivate() {
		
		if ( ! current_user_can( 'activate_plugins' ) )
        return;
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "deactivate-plugin_{$plugin}" );
	
		wp_clear_scheduled_hook( 'do_wptsc' );
	}
	
	/* FALLBACK WP CRON */
	/**
	 * On an early action hook, check if the hook is scheduled - if not, schedule it.
	 */
	function wptsc_setup_schedule() {
		if ( ! wp_next_scheduled( 'do_wptsc' ) ) {
			wp_schedule_event( time(), 'hourly', 'do_wptsc');
		}
	}
	
	/* UPGRADE */
	function upgrade() {
	
		$installed_ver 		= get_option( "wptsc_db_version" );
		$wptsc_db_version 	= $this->db_version;
		
		if( $installed_ver != $wptsc_db_version ) {
			//upgrade something
 			//update_option( "wptsc_db_version", $wptsc_db_version );
		}
		
	}
	
	function wptsc_update_db_check() {
		if (get_site_option( 'wptsc_db_version' ) != $this->db_version) {
			$this->install();
		}
	}
	
	/* OPTIONS */
	
	/**
	 * Register the options needed for this plugins configuration pages.
	 */
	function options_init() {
		register_setting( 'wptsc_settings', 'wptsc_settings' );
	}
	
	/**
	 * Retrieve an option for the configuration page.
	 */
	function get_option($key = '') {
		if (!empty($this->options) && isset($this->options[$key])) {
			if (is_array($this->options)) :
				return $this->options[$key];
			else :
				return stripslashes($this->options[$key]);
			endif;
		}
		return false;
	}
	
	/**
	 * Retrieve all options for the configuration page from WP Options.
	 */
	function get_options() {
		if (isset($this->options)) return $this->options;
		if ($options = get_option('wptsc_settings')) {
			if (is_array($options)) :
				$this->options = $options;
			else :
				$this->options = unserialize($options);	
			endif;
		}
	}
	
	/**
	 * Save all options to WP Options database
	 */
	function save_options() {
		if (!empty($this->options)) {
			update_option('wptsc_settings', serialize($this->options));	
		}
	}
	
	/**
	 * Save a specifix option to WP Option database
	 */
	function save_option($key, $value, $save_to_db = false) {
		if (!empty($this->options)) {
			$this->options[$key] = $value;
		}
		if ($save_to_db == true) {
			$this->save_options();	
		}
	}
	
	/* INCLUDES */
	
	/**
	 * Include specific PHP files when visiting an admin page
	 */
	function add_admin_includes() {
		$includes = array('plugin-admin'); //add includes here that are in the includes folder, without the .php
		$this->add_includes($includes);
	}
	
	/**
	 * Include specific PHP files when visiting a page on the website
	 */
	function add_includes($includes_new = array()) {
		$includes = array(); //add includes here that are in the includes folder, without the .php
		if (is_array($includes_new)) $includes = $includes_new;
		if (!count($includes)) return false;
		foreach ($includes as $_include) :		
			$path = wptsc_PATH.'includes/'.$_include.'.php';
			if (!file_exists($path)) continue;
			include_once($path);
		endforeach;
	}
	
	/* TWITTER */
	function do_wptsc() {
		global $wptsc_twitter;
		
		$hashtag = $this->get_option('hashtag');
		if (empty($hashtag)) return;
		if (strpos($hashtag,'#') === false) $hashtag = '#'.$hashtag;
				
		$since_id = $this->get_highest_tweet_id();
		$tweets = $wptsc_twitter->find_tweets($hashtag, array('since_id'=>$since_id));
		if (!$tweets) return;
		$this->save_tweets($tweets);
	}
	
	function get_highest_tweet_id() {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->table_name;
		$sql 		= "SELECT MAX(tweet_id) FROM $table_name ORDER BY tweet_id DESC LIMIT 0,1";
		$id 		= $wpdb->get_var( $sql );
		return $id;
	}
	
	function tweet_id_already_exists($tweet_id) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->table_name;
		$sql 		= "SELECT id FROM $table_name WHERE tweet_id = '".$tweet_id."'";
		$id 		= $wpdb->get_var( $sql );
		return $id;
	}
	
	function save_tweets($tweets = array()) {
		global $wpdb, $wptsc_twitter;
		$table = $wpdb->prefix . $this->table_name;		
		foreach ($tweets as $_tweet) :
			$tweet_id 				= $_tweet['id'];
			$tweet_exists			= $this->tweet_id_already_exists($tweet_id);
			if ($tweet_exists) continue;
			
			$tweet_content 			= $_tweet['text'];
			$tweet_username 		= $_tweet['user']['screen_name'];
			$user_id 				= $this->get_user_by_twitter_username($tweet_username);
			//if (empty($user_id)) continue;
			//TODO: empty user_id still save, if someone adds twitter user name later we can connect the products to the user_id	
			$tweet_parent_id 		= $_tweet['in_reply_to_status_id'];
			if (empty($tweet_parent_id)) continue;
			$parent_tweet 			= $wptsc_twitter->get_tweet_by_id($tweet_parent_id);
			if (!isset($parent_tweet['entities']['urls'])) continue;
			$post_id 				= $this->product_url_in_array($parent_tweet['entities']['urls']);
			if(!$post_id) continue;
			
			$tweet_url 				= 'https://twitter.com/'.$tweet_username.'/status/'.$tweet_id;
			$tweet_date 			= $_tweet['created_at'];	
			$date 					= new DateTime($tweet_date);
			$tweet_date				= $date->format('Y-m-d H:i:s');	
			$scripts_date 			= date('Y-m-d H:i:s',time());
			
			$data = array( 
				'user_id' 				=> $user_id, 
				'tweet_id' 				=> $tweet_id,
				'tweet_content' 		=> $tweet_content,
				'tweet_url' 			=> $tweet_url,
				'tweet_date' 			=> $tweet_date,
				'tweet_parent_id' 		=> $tweet_parent_id,
				'tweet_username' 		=> $tweet_username,
				'post_id' 				=> $post_id,
				'script_date' 			=> $scripts_date
			);
			$format = array( 
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d', 
				'%s',
				'%d',
				'%s'
			);
			$insert_id = $wpdb->insert($table, $data, $format);
		endforeach;
	}
	
	function get_user_by_twitter_username($username) {
		//TODO: What do we do if we have double Twitter user names? we have to use our own twitter user field then? To prevent this?
		//TODO build a migration from plugin X to this one, so if you use a plugin for a very long time, you wont loose any data
		//TODO use hook when saving users where we can hook into to prevent double twitter names, but how can we verify that someones twitter name is valid?
		
		$args = array(
			'meta_key' 		=> 'twitter',
			'meta_value' 	=> $username
		);
		
		$user = get_users($args);
		$user = array_shift($user);
		if (!isset($user->ID)) return;
		return $user->ID;
		
	}
	
	function product_url_in_string($string) {
		$urls = $this->get_urls_string($string);
		return $this->product_url_in_array($urls);
	}
	
	function product_url_in_array($urls) {
		foreach ($urls as $_url) :
			if (!is_string($_url)) :
				if (isset($_url['expanded_url'])) $_url = $_url['expanded_url'];
				else if (isset($_url['url'])) $_url = $_url['url']; 
			endif;
			$product_url = $this->url_is_product_url($_url);
			if ($product_url) return $product_url;
		endforeach;
		return;
	}
	
	function url_is_product_url($_url) {
		$is_valid_link = false;
		$_url = trailingslashit($_url);
		if (strpos($_url,get_bloginfo('url')) === false) :
			$_url = $this->get_final_url($_url);
			if (strpos($_url,get_bloginfo('url')) === false) :
				return;
			else :
				$is_valid_link = true;
			endif;
		else :
			$is_valid_link = true;
		endif;
		
		if ($is_valid_link) :
		
			$slug = '';
			
			//TODO Make this dynamic, so we can set this in the stores/shop.php files
			
			//get product_var from wptsc_ecommerce
			
			//products/product-name/?x=1
			$parts 	= parse_url(str_replace(get_bloginfo('url'),'',$_url));
			if ($parts['path']) :
				$path 	= str_replace('/','',$parts['path']);
				if (!empty($path)) :
					$slug = $path;
				endif;
			endif;
			
			if(!$slug && (strpos($_url,'product=') || strpos($_url,'shopp_product=') || strpos($_url,'wpsc-product=') || strpos($_url,'page='))) :
				parse_str(parse_url($_url, PHP_URL_QUERY), $vars);
				
				if (isset($vars['page'])) :
					$slug = $vars['page'];
				endif;
				
				//WooCommerce
				if (isset($vars['product'])) :
					$slug = $vars['product'];
				endif;
				
				//Shopp
				if (isset($vars['shopp_product'])) :
					$slug = $vars['product'];
				endif;
				
				//WP e-Commerce
				if (isset($vars['wpsc_product'])) :
					$slug = $vars['product'];
				endif;
			endif;
				
			if (!empty($slug)) :
				$exists = $this->product_exists_by_slug($slug);
				if ($exists) return $exists;
			endif;
		endif;
		return ;
	}
	
	function product_exists_by_slug($slug) {
		//post_type any doesnt work for WooCommerce because product CPT isnt retrieved with any
		$args = array(
		  'name' 			=> trim($slug),
		  'post_type' 		=> array('post','page','product','wpsc-product','shopp_product'),
		  'post_status' 	=> 'publish',
		  'numberposts' 	=> 1
		);
		$post = get_posts($args);
		if (!isset($post[0]->ID)) return false;
		$post = array_shift($post);
		if (isset($post->ID)) return $post->ID;
		return false;
	}
	
	function product_exists_by_id($id) {
		$post = get_post($id);
		if (!isset($post->ID)) return false;
		return $post->ID;
	}
	
	/**
	 * get_redirect_url()
	 * Gets the address that the provided URL redirects to,
	 * or FALSE if there's no redirect. 
	 *
	 * @param string $url
	 * @return string
	 * via: http://w-shadow.com/blog/2008/07/05/how-to-get-redirect-url-in-php/
	 */
	function get_redirect_url($url){
		$redirect_url = null; 
	
		$url_parts = @parse_url($url);
		if (!$url_parts) return false;
		if (!isset($url_parts['host'])) return false; //can't process relative URLs
		if (!isset($url_parts['path'])) $url_parts['path'] = '/';
	
		$sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
		if (!$sock) return false;
	
		$request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n"; 
		$request .= 'Host: ' . $url_parts['host'] . "\r\n"; 
		$request .= "Connection: Close\r\n\r\n"; 
		fwrite($sock, $request);
		$response = '';
		while(!feof($sock)) $response .= fread($sock, 8192);
		fclose($sock);
	
		if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
			if ( substr($matches[1], 0, 1) == "/" )
				return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
			else
				return trim($matches[1]);
	
		} else {
			return false;
		}
	
	}
	
	/**
	 * get_all_redirects()
	 * Follows and collects all redirects, in order, for the given URL. 
	 *
	 * @param string $url
	 * @return array
	 * via: http://w-shadow.com/blog/2008/07/05/how-to-get-redirect-url-in-php/
	 */
	function get_all_redirects($url){
		$redirects = array();
		while ($newurl = $this->get_redirect_url($url)){
			if (in_array($newurl, $redirects)){
				break;
			}
			$redirects[] = $newurl;
			$url = $newurl;
		}
		return $redirects;
	}
	
	/**
	 * get_final_url()
	 * Gets the address that the URL ultimately leads to. 
	 * Returns $url itself if it isn't a redirect.
	 *
	 * @param string $url
	 * @return string
	 * via: http://w-shadow.com/blog/2008/07/05/how-to-get-redirect-url-in-php/
	 */
	function get_final_url($url){
		$redirects = $this->get_all_redirects($url);
		if (count($redirects)>0){
			return array_pop($redirects);
		} else {
			return $url;
		}
	}
	
	/**
     *
     * @get URLs from string (string maybe a url)
     *
     * @param string $string
     * @return array
     *
     */
    function get_urls_string($string) {
		$regex = '/https?\:\/\/[^\" ]+/i';
		preg_match_all($regex, $string, $matches);
		//return (array_reverse($matches[0]));
		return ($matches[0]);
	}
	
	/* FRONTEND */
	function do_wptsc_frontend() {
		
		//TODO make it even smarter that it is not executed on every page refresh
		//maybe with extra cookie and timeinterval of 5 minutes
		//make option on settings page to set time interval, default is 5 minutes
		
		if (!is_user_logged_in()) return;
		$user_tweets = $this->user_has_tweets();
		if (!$user_tweets) return;
		
		//TODO This can be prettier
		global $wptsc_ecommerce;
		$wptsc_ecommerce = new wptsc_ecommerce();
		
		foreach ($user_tweets as $_tweet) :
			$product_id = $_tweet->post_id;
			$wptsc_ecommerce->add_to_cart($product_id);
			
			//set insert_date
			$this->set_insert_date($_tweet->id);
		endforeach;
	}
	
	function set_insert_date($id) {
		global $wpdb;
		$table_name = $wpdb->prefix . $this->table_name;
		$insert_date 			= date('Y-m-d H:i:s',time());
		$wpdb->update( 
			$table_name, 
			array( 
				'insert_date' => $insert_date,
			), 
			array( 'id' => $id ), 
			array( 
				'%s'
			), 
			array( '%d' ) 
		);
	}
	
	function user_has_tweets($user_id = '') {
		if (empty($user_id)) $user_id = get_current_user_id();
		
		global $wpdb;
		$table_name = $wpdb->prefix . $this->table_name;
		$sql 		= "SELECT * FROM $table_name WHERE user_id = '".$user_id."' AND insert_date IS NULL ORDER BY id ASC";
		$tweets 		= $wpdb->get_results( $sql );
		return $tweets;
	}
	
	/* NOTIFICATIONS */
	function add_notification($notification) {
		$this->notifications[] = $notification;
	}
	
	function show_notifications() {
		if (!isset($this->notifications) || (isset($this->notifications) && !count($this->notifications))) :
			return;
		endif;	
		$notifications = $this->notifications;
		foreach ($notifications as $_notification) :
			//TODO output notification
		endforeach;
	}
	
}


///15 * * * wget -q -O – http://yourdomain.com/wp-cron.php?doing_wp_cron tip page with tutorial to set this manually in crontab on server. Add checkbox to disable wp cron afterwards

/* The library has been tested with PHP 5.3+ and relies on CURL and hash_hmac. The vast majority of hosting providers include these libraries and run with PHP 5.1+.

The code makes use of hash_hmac, which was introduced in PHP 5.1.2. If your version of PHP is lower than this you should ask your hosting provider for an update.

de twitter scraper werkt alleen bij deze configuratie

//mention that to add twitter app there needs to be a callback URL, just use the same URL as the regular URL.

//support for Twitter Profile Field plugin

//step by step guide how to add a Twitter App

//We have to make a addtoshoppingvart.com website where you can register
//There you will set hashtag only and install base plugin, in plugin you set userid of service
//plugin requests url from our server to get tweets
//that way it is also easy to transfer

//TODO: Notifications on cart/main page that new products have been added to cart
//if user visits webshop and after login we have to show a message that there is a new item in cart

//TODO: Not automaticcally add product to shopping cart, but manually

*/
global $wptsc;
$wptsc = new wptsc();
?>