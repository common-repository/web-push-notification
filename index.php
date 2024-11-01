<?php   
	/*
		Plugin Name: Web Push Notification
		Description: Boost your readers engagement and send a smart Web Push Notification to your users each time you have new posts.
		Plugin URI: https://wordpress.org/plugins/web-push-notification
		Version: 1.1
		Author: Bassem Rabia
		Author URI: mailto:bassem.rabia@gmail.com
		License: GPLv2
	*/
	
	require_once(dirname(__FILE__).'/wpn/wpn.php');
	$wpn = new wpn();
	function language(){
		load_plugin_textdomain('web-push-notifications', false, basename(dirname(__FILE__) ).'/wpn/lang'); 
	}
	add_action('plugins_loaded', 'language');
?>