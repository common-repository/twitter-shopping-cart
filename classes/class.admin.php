<?php
class wptsc_admin extends wptsc {
	
	function __construct() {
		
		add_action('admin_menu', array(&$this, 'add_management_page') ) ;
	
	}
	
	function add_management_page() {
		add_management_page( 'Twitter Shopping Cart', 'WPTSC', 'manage_options', 'wptsc', array(&$this, 'wptsc_settings') );
	}
	
	/* ADMIN */
	/**
	 * The settings page where you can edit the options of this plugin
	 */
	function wptsc_settings() {
		
		global $table_prefix;
		global $plugin_admin;
		
			
			$plugin_admin->admin_header(true, 'wptsc_settings', 'wptsc_settings');
				
			//TODO hide default message if alle twitter keys and secrets are correct
			//possible solution is when the first tweet is found set an option first_tweet_found and and use that
			$content = '<p>To make Twitter Shopping Cart work you need to make a Twitter App. It is really easy. We need the information of that Twitter App before we can find those product tweets and replies.</p>';		
			
			$content .= '<p>You can make an App on the <a href="https://apps.twitter.com" target="_blank">Twitter Apps Area</a>. This area lets you configure your Twitter App and (if you want) reset your consumer key and secret. You can also create a new application. Fill out the application name, description and use <strong>'.get_bloginfo('url').'</strong> as the website.</p><p>If you do not know how to find all this information, <a href="http://www.youtube.com/watch?v=CVz1MjqTXMg">this video</a> will help you.</p>';
        
			$content_wptsc = '';
			$content_wptsc .= $plugin_admin->textinput('twitter_api_key',__('Twitter API Key'));
			$content_wptsc .= '<p>The "API secret" will remain secret. This key is never human-readable on your website.</p>';
			$content_wptsc .= $plugin_admin->textinput('twitter_api_secret',__('Twitter API Secret')).'<br/>';
			$content_wptsc .= $plugin_admin->textinput('twitter_access_token',__('Twitter Access Token')).'';
			$content_wptsc .= '<p>Do not share your access token secret with anyone.</p>';
			$content_wptsc .= $plugin_admin->textinput('twitter_access_token_secret',__('Twitter Access Token Secret')).'<br/><br/>';
			
			$content .= $content_wptsc;
			
			
			$plugin_admin->postbox( 'wptsc_twitter_settings', __( 'Twitter App Settings', 'wptsc' ), $content );
			
			$content = '<p>If you set a hashtag there will be an automatic process that will add tweets to a table in your database. When a user of your website logs in or revisits the website. We check if that user has a Twitter username set. If so we collect the tweets and add the products to the shopping cart.</p>';
			
			$content_wptsc = '';
        
			$content_wptsc .= $plugin_admin->textinput('hashtag',__('The hashtag people can use in their tweets')).'<br/><br/>';
			
			$content_wptsc .= $plugin_admin->textinput('twitter_custom_post_field',__('We search for a couple of Twitter Custom Post Fields, but you can define your own here. We will look for that custom post field for a user and use that as Twitter user name.')).'<br/><br/>';
			
			$content_wptsc .= $plugin_admin->checkbox('use_wp_cron',__('Don\'t use WP Cron to collect tweets from Twitter? You want to use this if you have set your own crontab on your server.')).'<br/><br/>';
			
			$content = $content_wptsc;
			
			$plugin_admin->postbox( 'wptsc_settings', __( 'WPTSC Settings', 'wptsc' ), $content );
			
			
			$plugin_admin->admin_footer();
	}
    
}
$wptsc_admin = new wptsc_admin();
?>