<?php
	class wptsc_twitter {
	
		private $twitter_api_key = '';
		private $twitter_api_secret = '';
		private $twitter_access_token = '';
		private $twitter_access_token_secret = '';
		public $operational = false;
	
		function find_parent_tweet() {
			
		}
		
		function __construct() {
			
			$this->get_options();
			$this->set_defaults();
			
			if ($this->is_operational()) :
				require(wptsc_PATH.'includes/tmhOAuth/tmhOAuth.php');
				require(wptsc_PATH.'includes/tmhOAuth/tmhUtilities.php');
			endif;
			
			$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    => $this->twitter_api_key,
				'consumer_secret' => $this->twitter_api_secret,
				'user_token'      => $this->twitter_access_token,
				'user_secret'     => $this->twitter_access_token_secret
			));
			$this->OAuth = $tmhOAuth;
		}
		
		function find_tweets($hashtag, $args = array()) {			
			if (!$this->is_operational()) return;
			
			$num = 100;
			//TODO Think about how many results we want back
			//TODO we only want to get tweets from people that are a member of this website, if a spammer is posting a lot of tweets with the hashtag we get a result from this and everything takes longer than it has to.
			
			$since_id = (isset($args['since_id']) && is_numeric($args['since_id'])) ? $args['since_id'] : '' ;
			
			$code = $this->OAuth->request('GET', $this->OAuth->url('1.1/search/tweets'), array(
				'q' => $hashtag,
				'count'=>$num,				
				'since_id'=> $since_id	
			));
			
			if ($code != 200) {

				global $user_ID;
		
				if( $user_ID ) {
			
				}
			} else {
				$output = json_decode($this->OAuth->response['response'],true);
				return $output['statuses'];
			}
		}
		
		function get_tweet_by_id($id) {
			$code = $this->OAuth->request('GET', $this->OAuth->url('1.1/statuses/show'), array(
				'id' => $id
			));
			
			if ($code != 200) {

				global $user_ID;
		
				if( $user_ID ) {
			
				}
			} else {
				$output = json_decode($this->OAuth->response['response'],true);
				return $output;
			}
		}
		
		function set_defaults() {
			if (!isset($this->options)) return;
			
			$this->twitter_api_key = $this->get_option('twitter_api_key');
			$this->twitter_api_secret = $this->get_option('twitter_api_secret');
			$this->twitter_access_token = $this->get_option('twitter_access_token');
			$this->twitter_access_token_secret = $this->get_option('twitter_access_token_secret');
			
			if (!empty($this->twitter_api_key) && !empty($this->twitter_api_secret) && !empty($this->twitter_access_token) && !empty($this->twitter_access_token_secret)) :
				$this->operational = true;
			endif;
		}
		
		public function is_operational() {
			return $this->operational;	
		}
		
		/* OPTIONS */
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
		 * Retrieve all options for this plugin
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
			return $options;
		}
		
		/**
		 * Save all options to WP Options database
		 */
		function save_options() {
			global $wptsc;
			$wptsc->save_options();
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
		
	}
	global $wptsc_twitter;
	$wptsc_twitter = new wptsc_twitter();
	
?>